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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminCrudController extends AbstractCrudController
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
        $this->transEntity = $this->config()->enablePublic ? $this->transEntity : 'user';

        $crud = parent::configureCrud($crud);
        $crud->setDefaultSort(['name' => 'ASC', 'lastname' => 'ASC']);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $this->transEntity = 'user';

        /** @var User $user */
        $user = $this->getUser();
        $entity = $this->entity();
        $filterHiddenRole = $this->filterHidden('role');
        $roles = $this->em()->getRepository(Role::class)->getAdminIsUp($user->getRole());
        $roleDefaultValue = count($roles) == 1 ? $roles[0] : (
            $filterHiddenRole ? $this->em()->getRepository(Role::class)->find($filterHiddenRole['value']) : null
        );

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection('data'))
            ->setIcon('user' . ($this->config()->enablePublic ? '-shield' : ''));
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
            ->isRequired($pageName == Crud::PAGE_NEW)
            ->setFirstLabel($this->transEntityField('password'))
            ->setSecondLabel($this->transEntityField('repeatPassword'));

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
            yield $role->displayIf($entity !== $user);
            yield $active->displayIf($entity !== $user);
            yield $passwordPanel;
            yield $password->isRequired($pageName == Crud::PAGE_NEW);
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->em()->getRepository($this->getEntityFqcn())->findAdminsSentence(
            $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters), true
        );
    }

    public function configureFilters(Filters $filters): Filters
    {
        $this->transEntity = 'user';

        /** @var User $user */
        $user = $this->getUser();

        $filters->add(DateTimeFilter::new('birthdate', $this->transEntityField('birthdate')));
        $filters->add(ChoiceFilter::new('gender', $this->transEntityField('gender'))
            ->setChoices(UserGender::choices())
            ->setFormTypeOption('translation_domain', 'messages')
        );

        $roles = $this->em()->getRepository(Role::class)->getAdminIsUp($user->getRole());
        if (count($roles) > 1) {
            $filters->add(EntityFilter::new('role', $this->transEntitySingular('role'))
                ->setFormTypeOption('value_type_options.query_builder', static fn (RoleRepository $rep) => $rep->getAdminQB()));
        }

        $filters->add(BooleanFilter::new('active', $this->transEntityField('active')));

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->transEntity = 'user';

        /** @var User $user */
        $user = $this->getUser();
        $entity = $this->entity();
        $isOwnUser = $user === $entity;
        $isUp = $entity && $this->rolePermissions->isUp($user->getRole(), $entity->getRole());
        $hasPermission = $this->hasPermissionCrud();
        $hasPermissionNew = $this->hasPermissionCrudAction(Action::NEW);
        $hasPermissionDetail = $this->hasPermissionCrudAction(Action::DETAIL);
        $hasPermissionEdit = $this->hasPermissionCrudAction(Action::EDIT);
        $hasPermissionDelete = $this->hasPermissionCrudAction(Action::DELETE);

        $actions->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE);

        $actions->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action) =>
            $action->displayIf(fn (User $u) => $user === $u || $hasPermissionDetail)
        );
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) =>
            $action->displayIf(fn (User $u) => $user === $u || ($hasPermissionEdit && $this->rolePermissions->isUp($user->getRole(), $u->getRole())))
        );
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) =>
            $action->displayIf(fn (User $u) => $user !== $u && ($hasPermissionDelete && $this->rolePermissions->isUp($user->getRole(), $u->getRole())))
        );

        $denied = !$hasPermission ? [Action::INDEX] : [];
        if (!$hasPermissionNew) $denied[] = Action::NEW;
        if ($entity) {
            if (!$isOwnUser && !$hasPermissionDetail) $denied[] = Action::DETAIL;
            if (!$isOwnUser && (!$hasPermissionEdit || !$isUp)) $denied[] = Action::EDIT;
            if ($isOwnUser || !$hasPermissionDelete || !$isUp) $denied[] = Action::DELETE;
        }
        $actions->setPermissions(array_fill_keys(array_unique($denied), 'NOPERMISSION_ACTION'));

        $hasPermissionImpersonate = $this->hasPermissionCrudAction('impersonate');
        $impersonate = Action::new('impersonate', $this->transEntityAction('impersonate'))->setIcon('user-search')
            ->linkToUrl(fn (User $u) => $this->generateUrl('home', ['_switch_user' => $u->getEmail()]))
            ->displayIf(fn (User $u) => $user !== $u && $u->isActive() && $this->rolePermissions->isUp($user->getRole(), $u->getRole()))
            ->asPrimaryAction()->addCssClass('btn-outline');
        $actions->add(Crud::PAGE_INDEX, $impersonate);
        $actions->add(Crud::PAGE_DETAIL, $impersonate);
        $actions->setPermission('impersonate', !$hasPermissionImpersonate ? 'NOPERMISSION_ACTION' : '');

        $actions->reorder(Crud::PAGE_INDEX, [ Action::DETAIL, 'impersonate', Action::EDIT, Action::DELETE ]);
        $actions->reorder(Crud::PAGE_DETAIL, [ Action::EDIT, Action::DELETE, 'impersonate', Action::INDEX ]);

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

    public function edit(AdminContext $context)
    {
        $redirect = parent::edit($context);
        if ($redirect instanceof RedirectResponse) {
            if (!$this->hasPermissionCrud()) {
                $url = $this->adminUrl()->setRoute('admin')->generateUrl();
                return $this->redirect($url);
            }
        }
        return $redirect;
    }
}
