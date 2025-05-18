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

use function Symfony\Component\Translation\t;

class AdminCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $rolePermissions;

    public function __construct(UserPasswordHasherInterface $passwordHasher, RolePermissions $rolePermissions)
    {
        parent::__construct();
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

        $entity = $this->entity();
        if ($entity && $entity == $this->getUser()) {
            $crud->setPageTitle(Crud::PAGE_DETAIL, t('ea.profile.title'));
            $crud->setPageTitle(Crud::PAGE_EDIT, t('ea.profile.title'));
        }

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        /** @var User $user */
        $user = $this->getUser();
        $entity = $this->entity();

        $dataPanelName = $entity && $entity == $user ? t('ea.profile.data') : $this->transEntitySection('data', 'user');
        $dataPanel = FieldGenerator::panel($dataPanelName)->setIcon('user' . ($this->config()->enablePublic ? '-shield' : ''));
        $fullname = FieldGenerator::text('fullname')->setLabel($this->transEntityField('name', 'user'));
        $name = FieldGenerator::text('name')->setLabel($this->transEntityField('name', 'user'))->setColumns(2);
        $lastname = FieldGenerator::text('lastname')->setLabel($this->transEntityField('lastname', 'user'))->setColumns(3);
        $email = FieldGenerator::email('email')->setLabel($this->transEntityField('email', 'user'))->setColumns(4);
        $phone = FieldGenerator::phone('phone')->setLabel($this->transEntityField('phone', 'user'))->setColumns(3);
        $birthdate = FieldGenerator::date('birthdate')->setLabel($this->transEntityField('birthdate', 'user'))->setColumns(2);
        $gender = FieldGenerator::choice('gender')->setLabel($this->transEntityField('gender', 'user'))->setColumns(2)
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', UserGender::class);
        $avatar = FieldGenerator::avatar('avatar')->setLabel($this->transEntityField('avatar', 'user'))->setColumns(8);
        $roles = $this->em()->getRepository(Role::class)->getAdminIsUp($user->getRole());
        $role = FieldGenerator::association('role')->setLabel($this->transEntitySingular('role'))
            ->setRequired(true)
            ->setQueryBuilder(function ($qb) use ($roles) {
                $rolesIds = array_map(function ($role) { return $role->getId(); }, $roles);
                return !count($rolesIds) ? $qb->andWhere('entity.id IS NULL') : $qb->andWhere('entity.id IN (' . implode(',', $rolesIds) . ')');
            });
        if (count($roles) == 1) {
            $role->setFormTypeOption('data', $roles[0])->setColumns('d-none');
        } elseif ($this->filterHidden('role')) {
            $role->setFormTypeOption('data', $this->em()->getRepository(Role::class)->find($this->filterHidden('role')['value']))->setColumns('d-none');
        }
        $active = FieldGenerator::switch('active')->setLabel($this->transEntityField('active', 'user'));
        $createdAt = FieldGenerator::datetime('createdAt')->setLabel($this->transEntityField('createdAt', 'user'))->setColumns(6);

        $passwordPanel = FieldGenerator::panel($this->transEntitySection('password', 'user'))->setIcon('key');
        $password = FieldGenerator::confirmPassword('plainPassword')
            ->setFormTypeOption('first_options.label', $this->transEntityField('password', 'user'))
            ->setFormTypeOption('second_options.label', $this->transEntityField('repeatPassword', 'user'));

        if ($pageName == Crud::PAGE_INDEX) {
            yield $avatar->addCssClass('w-1');
            yield $fullname;
            yield $email;
            if (count($roles) > 1 && !$this->filterHidden('role')) {
                yield $role;
            }
            yield $active->renderAsSwitch(false)->addCssClass('w-1');
        } elseif ($pageName == Crud::PAGE_DETAIL) {
            yield $dataPanel;
            yield $avatar->setColumns(12);
            yield $name;
            yield $lastname;
            yield $email;
            yield $phone;
            yield $birthdate;
            yield $gender;
            if (count($roles) > 1 && !$this->filterHidden('role')) {
                yield $role->setColumns(2);
            }
            if ($entity !== $user) {
                yield $active->setColumns(2);
            }
            yield $createdAt;
        } elseif ($pageName == Crud::PAGE_NEW || $pageName == Crud::PAGE_EDIT) {
            yield $dataPanel;
            yield $name;
            yield $lastname;
            yield $email;
            yield $phone;
            yield $birthdate;
            yield $gender;
            yield $avatar;
            if ($entity !== $user) {
                yield $role;
            }
            if ($entity !== $user) {
                yield $active;
            }
            yield $passwordPanel;
            yield $password->setRequired($pageName == Crud::PAGE_NEW);
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.role', 'r')
            ->andWhere("entity.verified = true")
            ->andWhere("r.isAdmin = true");
        return $queryBuilder;
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

            $hasPermissionEdit = $this->hasPermissionAction(Action::EDIT);
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

            $hasPermissionDelete = $this->hasPermissionAction(Action::DELETE);
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

            $hasPermissionImpersonate = $this->hasPermissionAction('impersonate');
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
