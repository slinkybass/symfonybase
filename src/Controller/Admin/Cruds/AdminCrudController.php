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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $rolePermissions;

    public function __construct(TranslatorInterface $translator, UserPasswordHasherInterface $passwordHasher, RolePermissions $rolePermissions)
    {
        parent::__construct($translator);
        $this->passwordHasher = $passwordHasher;
        $this->rolePermissions = $rolePermissions;
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
        /** @var User $user */
        $user = $this->getUser();
        $entity = $this->entity();
        $roles = $this->em()->getRepository(Role::class)->getAdminIsUp($user->getRole());

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection('data', 'user'))
            ->setIcon('user' . ($this->config()->enablePublic ? '-shield' : ''));
        $fullname = FieldGenerator::text('fullname')
            ->setLabel($this->transEntityField('name', 'user'));
        $name = FieldGenerator::text('name')
            ->setLabel($this->transEntityField('name', 'user'))
            ->setColumns(2);
        $lastname = FieldGenerator::text('lastname')
            ->setLabel($this->transEntityField('lastname', 'user'))
            ->setColumns(3);
        $email = FieldGenerator::email('email')
            ->setLabel($this->transEntityField('email', 'user'))
            ->setColumns(4);
        $phone = FieldGenerator::phone('phone')
            ->setLabel($this->transEntityField('phone', 'user'))
            ->setColumns(3);
        $birthdate = FieldGenerator::date('birthdate')
            ->setLabel($this->transEntityField('birthdate', 'user'))
            ->setColumns(2);
        $gender = FieldGenerator::choice('gender')
            ->setLabel($this->transEntityField('gender', 'user'))
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', UserGender::class)
            ->setColumns(2);
        $avatar = FieldGenerator::media('avatar')
            ->setLabel($this->transEntityField('avatar', 'user'))
            ->conf('public_user_images')
            ->setColumns(8);
        $role = FieldGenerator::association('role')
            ->setLabel($this->transEntitySingular('role'))
            ->required()
            ->setQueryBuilder(function ($qb) use ($roles) {
                $rolesIds = array_map(fn ($r) => $r->getId(), $roles);
                return empty($rolesIds)
                    ? $qb->andWhere('entity.id IS NULL')
                    : $qb->andWhere('entity.id IN (' . implode(',', $rolesIds) . ')');
            })
            ->setColumns('d-none');
        if (count($roles) == 1) {
            $role->setFormTypeOption('data', $roles[0]);
        } elseif ($this->filterHidden('role')) {
            $role->setFormTypeOption('data', $this->em()->getRepository(Role::class)->find($this->filterHidden('role')['value']));
        }
        $active = FieldGenerator::switch('active')
            ->setLabel($this->transEntityField('active', 'user'));
        $createdAt = FieldGenerator::datetime('createdAt')
            ->setLabel($this->transEntityField('createdAt', 'user'))
            ->setColumns(6);

        /*** Password ***/
        $passwordPanel = FieldGenerator::panel($this->transEntitySection('password', 'user'))
            ->setIcon('key');
        $password = FieldGenerator::password('plainPassword')
            ->repeated()
            ->setRequired($pageName == Crud::PAGE_NEW)
            ->setFirstLabel($this->transEntityField('password', 'user'))
            ->setSecondLabel($this->transEntityField('repeatPassword', 'user'));

        if ($pageName == Crud::PAGE_INDEX) {
            yield from $this->yieldFields([
                $avatar->addCssClass('w-1'),
                $fullname,
                $email,
                ...(count($roles) && !$this->filterHidden('role') ? [
                    $role,
                ] : []),
                $active->renderAsSwitch(false)->addCssClass('w-1'),
            ]);
        } elseif ($pageName == Crud::PAGE_DETAIL) {
            yield from $this->yieldFields([
                $dataPanel,
                $avatar->setColumns(12),
                $name,
                $lastname,
                $email,
                $phone,
                $birthdate,
                $gender,
                ...(count($roles) && !$this->filterHidden('role') ? [
                    $role->setColumns(2),
                ] : []),
                ...($entity !== $user ? [
                    $active->setColumns(2),
                ] : []),
                $createdAt,
            ]);
        } elseif (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT])) {
            yield from $this->yieldFields([
                $dataPanel,
                $name,
                $lastname,
                $email,
                $phone,
                $birthdate,
                $gender,
                $avatar,
                ...($entity !== $user ? [
                    $role,
                    $active,
                ] : []),
                $passwordPanel,
                $password->setRequired($pageName == Crud::PAGE_NEW),
            ]);
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
        /** @var User $user */
        $user = $this->getUser();

        $filters->add(ChoiceFilter::new('gender', $this->transEntityField('gender', 'user'))
            ->setChoices(UserGender::getChoices())
            ->setFormTypeOption('translation_domain', 'messages')
        );

        $roles = $this->em()->getRepository(Role::class)->getAdminIsUp($user->getRole());
        if (count($roles) > 1) {
            $filters->add(EntityFilter::new('role', $this->transEntitySingular('role'))
                ->setFormTypeOption('value_type_options.query_builder', static fn (RoleRepository $rep) => $rep->getAdminQB()));
        }

        $filters->add(BooleanFilter::new('active', $this->transEntityField('active', 'user')));

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        if ($this->hasPermissionCrud()) {
            /** @var User $user */
            $user = $this->getUser();
            $rolePermissions = $this->rolePermissions;

            $hasPermissionEdit = $this->hasPermissionCrudAction(Action::EDIT);
            $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) use ($hasPermissionEdit, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionEdit, $rolePermissions, $user) {
                    return ($hasPermissionEdit || $entity === $user) && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            });
            $actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) use ($hasPermissionEdit, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionEdit, $rolePermissions, $user) {
                    return ($hasPermissionEdit || $entity === $user) && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            });
            $actions->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) use ($hasPermissionEdit, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionEdit, $rolePermissions, $user) {
                    return ($hasPermissionEdit || $entity === $user) && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            });

            $hasPermissionDelete = $this->hasPermissionCrudAction(Action::DELETE);
            $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) use ($hasPermissionDelete, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionDelete, $rolePermissions, $user) {
                    return $hasPermissionDelete && $entity !== $user && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            });
            $actions->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) use ($hasPermissionDelete, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionDelete, $rolePermissions, $user) {
                    return $hasPermissionDelete && $entity !== $user && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            });
            $actions->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $action) use ($hasPermissionDelete, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionDelete, $rolePermissions, $user) {
                    return $hasPermissionDelete && $entity !== $user && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            });

            $hasPermissionImpersonate = $this->hasPermissionCrudAction('impersonate');
            $impersonate = Action::new('impersonate', $this->transEntityAction('impersonate', 'user'))->setIcon('user-search')
                ->linkToUrl(function ($entity) {
                    return $this->generateUrl('home', ['_switch_user' => $entity->getEmail()]);
                })->displayIf(static function ($entity) use ($hasPermissionImpersonate, $rolePermissions, $user) {
                    return $hasPermissionImpersonate && $entity !== $user && $rolePermissions->isUp($user->getRole(), $entity->getRole());
                });
            $actions->add(Crud::PAGE_INDEX, $impersonate);
            $actions->add(Crud::PAGE_DETAIL, $impersonate);
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

    public function edit(AdminContext $context)
    {
        $redirect = parent::edit($context);
        if ($redirect instanceof RedirectResponse) {
            if (!$this->hasPermissionCrud()) {
                $url = $this->adminUrl()->setRoute('admin_home')->generateUrl();
                return $this->redirect($url);
            }
        }
        return $redirect;
    }
}
