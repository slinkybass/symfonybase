<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\Enum\UserGender;
use App\Entity\Role;
use App\Entity\User;
use App\Field\FieldGenerator;
use App\Repository\Filter\Role as RoleFilter;
use App\Repository\Filter\User as UserFilter;
use App\Repository\RoleRepository;
use App\Service\ConfigService;
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
    public function __construct(
        public TranslatorInterface $translator,
        public ConfigService $configService,
        public RolePermissions $rolePermissions,
        public readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($translator, $configService, $rolePermissions);
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
        $roles = $this->em()->getRepository(Role::class)->filter([new RoleFilter\IsAdminFilter(false)]);
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

                return empty($rolesIds) ? $qb->andWhere('entity.id IS NULL') : $qb->andWhere('entity.id IN ('.implode(',', $rolesIds).')');
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
            ->isRequired($this->isNew())
            ->setFirstLabel($this->transEntityField('password'))
            ->setSecondLabel($this->transEntityField('repeatPassword'));

        if ($this->isIndex()) {
            yield $avatar->addCssClass('w-1');
            yield $fullname;
            yield $email;
            yield $role->displayIf(count($roles) > 1 && !$filterHiddenRole);
            yield $active->isSwitch(false)->addCssClass('w-1');
        } elseif ($this->isDetail()) {
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
        } elseif ($this->isForm()) {
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
            yield $password;
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        (new UserFilter\IsVerifiedFilter())->apply($qb);
        (new UserFilter\IsAdminFilter(false))->apply($qb);

        return $qb;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add(DateTimeFilter::new('birthdate', $this->transEntityField('birthdate')));
        $filters->add(ChoiceFilter::new('gender', $this->transEntityField('gender'))
            ->setChoices(UserGender::choices())
            ->setFormTypeOption('translation_domain', 'messages')
        );

        $roles = $this->em()->getRepository(Role::class)->filter([new RoleFilter\IsAdminFilter(false)]);
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

        $hasPermissionImpersonate = $this->hasPermissionCrudAction('impersonate');
        $impersonate = Action::new('impersonate', $this->transEntityAction('impersonate'))->setIcon('user-search')
            ->linkToUrl(fn (User $u) => $this->generateUrl('home', ['_switch_user' => $u->getEmail()]))
            ->displayIf(fn (User $u) => $u->isActive())
            ->asPrimaryAction()->addCssClass('btn-outline');
        $actions->add(Crud::PAGE_INDEX, $impersonate);
        $actions->add(Crud::PAGE_DETAIL, $impersonate);
        $actions->setPermission('impersonate', !$hasPermissionImpersonate ? 'NOPERMISSION_ACTION' : '');

        $actions->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'impersonate', Action::EDIT, Action::DELETE]);
        $actions->reorder(Crud::PAGE_DETAIL, [Action::EDIT, Action::DELETE, 'impersonate', Action::INDEX]);

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
