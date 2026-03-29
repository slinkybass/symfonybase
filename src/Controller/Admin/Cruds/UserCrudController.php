<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\Enum\UserGender;
use App\Entity\Role;
use App\Entity\User;
use App\Field\FieldGenerator;
use App\Repository\RoleRepository;
use App\Service\RolePermissions;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(TranslatorInterface $translator, RolePermissions $rolePermissions, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($translator, $rolePermissions);
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud->setDefaultSort(['name' => 'ASC', 'lastname' => 'ASC']);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $filterHiddenRole = $this->filterHidden('role');
        $roles = $this->em()->getRepository(Role::class)->getAdmin(false);
        $roleDefaultValue = count($roles) == 1 ? $roles[0] : (
            $filterHiddenRole ? $this->em()->getRepository(Role::class)->find($filterHiddenRole['value']) : null
        );

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection())
            ->setIcon('user');
        $fullname = FieldGenerator::text('fullname')
            ->setLabel($this->transEntityField('name'));
        $name = FieldGenerator::text('name')
            ->setLabel($this->transEntityField('name'))
            ->setColumns(2);
        $lastname = FieldGenerator::text('lastname')
            ->setLabel($this->transEntityField('lastname'))
            ->setColumns(3);
        $email = FieldGenerator::email('email')
            ->setLabel($this->transEntityField('email'))
            ->setColumns(4);
        $phone = FieldGenerator::phone('phone')
            ->setLabel($this->transEntityField('phone'))
            ->setColumns(3);
        $birthdate = FieldGenerator::date('birthdate')
            ->setLabel($this->transEntityField('birthdate'))
            ->setColumns(2);
        $gender = FieldGenerator::enum('gender')
            ->setLabel($this->transEntityField('gender'))
            ->setColumns(2);
        $avatar = FieldGenerator::media('avatar')
            ->setLabel($this->transEntityField('avatar'))
            ->setConf('public_user_images')
            ->setColumns(8);
        $role = FieldGenerator::association('role')
            ->setLabel($this->transEntitySingular('role'))
            ->isRequired()
            ->setQueryBuilder(function ($qb) use ($roles) {
                $rolesIds = array_map(fn ($r) => $r->getId(), $roles);
                return empty($rolesIds) ? $qb->andWhere('entity.id IS NULL') : $qb->andWhere('entity.id IN (' . implode(',', $rolesIds) . ')');
            });
        if ($roleDefaultValue) {
            $role->setFormTypeOption('data', $roleDefaultValue)->setColumns('d-none');
        }
        $active = FieldGenerator::switch('active')
            ->setLabel($this->transEntityField('active'));
        $createdAt = FieldGenerator::datetime('createdAt')
            ->setLabel($this->transEntityField('createdAt'))
            ->setColumns(6);

        /*** Password ***/
        $passwordPanel = FieldGenerator::panel($this->transEntitySection('password'))
            ->setIcon('key');
        $password = FieldGenerator::password('plainPassword')
            ->isRepeated()
            ->setFirstLabel($this->transEntityField('password', 'user'))
            ->setSecondLabel($this->transEntityField('repeatPassword', 'user'));

        if ($pageName == Crud::PAGE_INDEX) {
            yield $avatar->addCssClass('w-1');
            yield $fullname;
            yield $email;
            yield $role->displayIf(count($roles) > 1 && !$filterHiddenRole);
            yield $active->isSwitch(false)->addCssClass('w-1');
        } elseif ($pageName == Crud::PAGE_DETAIL) {
            yield $dataPanel;
            yield $avatar->setColumns(12);
            yield $name;
            yield $lastname;
            yield $email;
            yield $phone;
            yield $birthdate;
            yield $gender;
            yield $role->displayIf(count($roles) > 1 && !$filterHiddenRole)->setColumns(2);
            yield $active->setColumns(2);
            yield $createdAt;
        } elseif (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT])) {
            yield $dataPanel;
            yield $name;
            yield $lastname;
            yield $email;
            yield $phone;
            yield $birthdate;
            yield $gender;
            yield $avatar;
            yield $role;
            yield $active;
            yield $passwordPanel;
            yield $password->isRequired($pageName == Crud::PAGE_NEW);
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->em()->getRepository($this->getEntityFqcn())->findAdminsSentence(
            $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters), false
        );
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add(DateTimeFilter::new('birthdate', $this->transEntityField('birthdate')));
        $filters->add(ChoiceFilter::new('gender', $this->transEntityField('gender'))
            ->setChoices(UserGender::choices())
            ->setFormTypeOption('translation_domain', 'messages')
        );

        $roles = $this->em()->getRepository(Role::class)->getAdmin(false);
        if (count($roles) > 1) {
            $filters->add(EntityFilter::new('role', $this->transEntitySingular('role'))
                ->setFormTypeOption('value_type_options.query_builder', static fn (RoleRepository $rep) => $rep->getAdminQB(false)));
        }

        $filters->add(BooleanFilter::new('active', $this->transEntityField('active')));

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        if ($this->hasPermissionCrud()) {
            $hasPermissionImpersonate = $this->hasPermissionCrudAction('impersonate');
            $impersonate = Action::new('impersonate', $this->transEntityAction('impersonate'))->setIcon('user-search')
                ->linkToUrl(function ($entity) {
                    return $this->generateUrl('home', ['_switch_user' => $entity->getEmail()]);
                })->displayIf(static function ($entity) use ($hasPermissionImpersonate) {
                    return $hasPermissionImpersonate && $entity->isActive();
                })->asPrimaryAction()->addCssClass('btn-outline');
            $actions->add(Crud::PAGE_INDEX, $impersonate);
            $actions->add(Crud::PAGE_DETAIL, $impersonate);

            $actions->reorder(Crud::PAGE_INDEX, [ Action::DETAIL, 'impersonate', Action::EDIT, Action::DELETE ]);
            $actions->reorder(Crud::PAGE_DETAIL, [ Action::EDIT, Action::DELETE, 'impersonate', Action::INDEX ]);
        }

        return $actions;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        $this->addEncodePasswordEventListener($formBuilder);
        return $formBuilder;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $keyValueStore, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $keyValueStore, $context);
        $this->addEncodePasswordEventListener($formBuilder);
        return $formBuilder;
    }

    public function addEncodePasswordEventListener(FormBuilderInterface $formBuilder)
    {
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $user = $event->getData();
            if ($user->getPlainPassword()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            }
        });
    }
}
