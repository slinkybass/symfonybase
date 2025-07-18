<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\Role;
use App\Field\BooleanField;
use App\Field\FieldGenerator;
use App\Service\RolePermissions;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoleCrudController extends AbstractCrudController
{
    private $rolePermissions;

    public function __construct(TranslatorInterface $translator, RolePermissions $rolePermissions)
    {
        parent::__construct($translator);
        $this->rolePermissions = $rolePermissions;
    }

    public static function getEntityFqcn(): string
    {
        return Role::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud->setDefaultSort(['displayName' => 'ASC']);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $entity = $this->entity();
        $entityIsAdmin = $entity && $entity->isAdmin();
        $publicEnabled = $this->config()->enablePublic;

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection())
            ->setIcon('lock');
        $displayName = FieldGenerator::text('displayName')
            ->setLabel($this->transEntityField('displayName'));
        $isAdmin = FieldGenerator::switch('isAdmin')
            ->setLabel($this->transEntityField('isAdmin'))
            ->setHtmlAttribute('data-hf-parent', 'isAdmin');

        /*** Permissions ***/
        $permissionsPanel = FieldGenerator::panel($this->transEntitySection('permissions'))
            ->setIcon('lock');
        $permissions = $this->rolePermissions->getGroupedPermissions();
        $permissionsFields = [];
        $this->rolePermissions->loopPermissions($permissions, function ($permission, $parentPermission, $level) use (&$permissionsFields) {
            if ($this->hasPermission($permission)) {
                $isCrudPermission = strpos($permission, 'crud') !== false;
                if ($isCrudPermission) {
                    $permissionWithoutCrud = str_replace('crud', '', $permission);
                    $entity = lcfirst(preg_split('/(?=[A-Z])/', $permissionWithoutCrud)[1]);
                    $action = lcfirst(str_replace(ucfirst($entity), '', $permissionWithoutCrud));

                    $entityLabel = $this->translator->trans('entities.' . $entity . '.plural');
                    if (!$action) {
                        $permissionLabel = $entityLabel;
                    } elseif (in_array($action, [Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE])) {
                        $permissionLabel = $this->translator->trans('action.' . $action, [], 'EasyAdminBundle');
                        $permissionLabel = str_replace(['%entity_label_singular%', '%entity_label_plural%'], [$entityLabel, $entityLabel], $permissionLabel);
                    } else {
                        $permissionLabel = $this->translator->trans('entities.' . $entity . '.actions.' . $action);
                    }
                } else {
                    $permissionLabel = $this->translator->trans('entities.role.permissions.' . $permission);
                }

                $permissionsFields[] = $this->generatePermissionField($permission, $permissionLabel, $parentPermission, $level);
            }
        });
        $haveAnyPermissions = false;
        foreach ($permissionsFields as $permissionsField) {
            if ($this->hasPermission($permissionsField->getAsDto()->getProperty())) {
                $haveAnyPermissions = true;
                break;
            }
        }

        /*** Users ***/
        $nameUsers = $publicEnabled && $entityIsAdmin ? 'admin' : 'user';
        $iconUsers = $publicEnabled && $entityIsAdmin ? 'user-shield' : 'user';
        $usersPanel = FieldGenerator::panel($this->transEntityPlural($nameUsers))
            ->setIcon('' . $iconUsers);
        $users = FieldGenerator::association('users')
            ->setLabel($this->transEntityPlural('user'));

        if ($pageName == Crud::PAGE_INDEX) {
            yield from $this->yieldFields([
                $displayName,
                ...($publicEnabled ? [
                    $isAdmin->renderAsSwitch(false),
                ] : []),
                $users->addCssClass('w-1')->setTextAlign('center'),
            ]);
        } elseif ($pageName == Crud::PAGE_DETAIL) {
            yield from $this->yieldFields([
                $dataPanel,
                $displayName,
                ...($publicEnabled ? [
                    $isAdmin,
                ] : []),
                ...($haveAnyPermissions ? [
                    $permissionsPanel,
                    ...$permissionsFields,
                ] : []),
                $usersPanel,
                $users->setLabel(false),
            ]);
        } elseif ($pageName == Crud::PAGE_NEW || $pageName == Crud::PAGE_EDIT) {
            yield from $this->yieldFields([
                $dataPanel,
                $displayName,
                ...($publicEnabled ? [
                    $isAdmin,
                ] : []),
                ...($haveAnyPermissions ? [
                    $permissionsPanel,
                    ...$permissionsFields,
                ] : []),
            ]);
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if (!$this->config()->enablePublic) {
            $queryBuilder->andWhere("entity.isAdmin = true");
        }
        return $queryBuilder;
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
                    return $hasPermissionEdit && (!$entity->isAdmin() || ($entity->isAdmin() && $rolePermissions->isUp($user->getRole(), $entity)));
                });
            });
            $actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) use ($hasPermissionEdit, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionEdit, $rolePermissions, $user) {
                    return $hasPermissionEdit && (!$entity->isAdmin() || ($entity->isAdmin() && $rolePermissions->isUp($user->getRole(), $entity)));
                });
            });

            $hasPermissionDelete = $this->hasPermissionCrudAction(Action::DELETE);
            $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) use ($hasPermissionDelete, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionDelete, $rolePermissions, $user) {
                    return $hasPermissionDelete && (!$entity->isAdmin() || ($entity->isAdmin() && $rolePermissions->isUp($user->getRole(), $entity)));
                });
            });
            $actions->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) use ($hasPermissionDelete, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionDelete, $rolePermissions, $user) {
                    return $hasPermissionDelete && (!$entity->isAdmin() || ($entity->isAdmin() && $rolePermissions->isUp($user->getRole(), $entity)));
                });
            });
            $actions->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $action) use ($hasPermissionDelete, $rolePermissions, $user) {
                return $action->displayIf(static function ($entity) use ($hasPermissionDelete, $rolePermissions, $user) {
                    return $hasPermissionDelete && (!$entity->isAdmin() || ($entity->isAdmin() && $rolePermissions->isUp($user->getRole(), $entity)));
                });
            });

            $rolesAdmin = $this->em()->getRepository(Role::class)->getAdmin();
            $rolesUser = $this->em()->getRepository(Role::class)->getAdmin(false);

            $hasPermissionToAdmins = $this->hasPermissionCrud('admin');
            $admins = Action::new('admins', $this->transEntityPlural('admin'))->setIcon('user-shield')
                ->linkToUrl(function ($entity) use ($rolesAdmin) {
                    $url = $this->adminUrl()->setController(AdminCrudController::class)->setAction(Action::INDEX)
                        ->setEntityId(null);
                    if (count($rolesAdmin) > 1) {
                        $url->set(EA::FILTERS, [
                            'role' => ['comparison' => ComparisonType::EQ, 'value' => $entity->getId()],
                            'hidden_filters' => ['role' => true],
                        ]);
                    }
                    return $url->generateUrl();
                })
                ->displayIf(static function ($entity) use ($hasPermissionToAdmins) {
                    return $entity->isAdmin() && $hasPermissionToAdmins;
                });
            $actions->add(Crud::PAGE_INDEX, $admins);
            $actions->add(Crud::PAGE_DETAIL, $admins);

            $hasPermissionToUsers = $this->hasPermissionCrud('user');
            $users = Action::new('users', $this->transEntityPlural('user'))->setIcon('user')
                ->linkToUrl(function ($entity) use ($rolesUser) {
                    $url = $this->adminUrl()->setController(UserCrudController::class)->setAction(Action::INDEX)
                        ->setEntityId(null);
                    if (count($rolesUser) > 1) {
                        $url->set(EA::FILTERS, [
                            'role' => ['comparison' => ComparisonType::EQ, 'value' => $entity->getId()],
                            'hidden_filters' => ['role' => true],
                        ]);
                    }
                    return $url->generateUrl();
                })
                ->displayIf(static function ($entity) use ($hasPermissionToUsers) {
                    return !$entity->isAdmin() && $hasPermissionToUsers;
                });
            $actions->add(Crud::PAGE_INDEX, $users);
            $actions->add(Crud::PAGE_DETAIL, $users);
        }

        return $actions;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        if (!$this->config()->enablePublic) {
            $this->addIsAdminEventListener($formBuilder);
        }
        $this->addPermissionsEventListener($formBuilder);
        return $formBuilder;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $keyValueStore, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $keyValueStore, $context);
        if (!$this->config()->enablePublic) {
            $this->addIsAdminEventListener($formBuilder);
        }
        $this->addPermissionsEventListener($formBuilder);
        return $formBuilder;
    }

    public function addIsAdminEventListener(FormBuilderInterface $formBuilder)
    {
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $role = $event->getData();
            $role->setIsAdmin(true);
        });
    }

    public function addPermissionsEventListener(FormBuilderInterface $formBuilder)
    {
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $role = $event->getData();
            $form = $event->getForm();

            $permissions = $this->rolePermissions->getGroupedPermissions();
            $permissionsValues = [];
            $this->rolePermissions->loopPermissions($permissions, function ($permission) use (&$permissionsValues, $form) {
                $permissionsValues[$permission] = $form->has($permission) ? $form->get($permission)->getData() : false;
            });

            $role->setPermissions($permissionsValues);
        });
    }

    private function generatePermissionField(string $permission, string $label, ?string $parentPermission = null, ?int $level = 0): BooleanField
    {
        $entity = $this->entity();
        $permissionValue = $entity && $entity->getPermission($permission) ? true : false;
        $label = "<span data-bs-toggle='tooltip' data-bs-placement='top' data-bs-title='$permission'>$label</span>";

        $permission = FieldGenerator::switch($permission)->setLabel($label)
            ->setFormTypeOption('mapped', false)
            ->setFormTypeOption('data', $permissionValue)
            ->setValue($permissionValue)
            ->setFormattedValue($permissionValue)
            ->setHtmlAttribute('data-hf-parent', 'perm_' . $permission)
            ->setHtmlAttribute('data-hf-child', $parentPermission ? 'perm_' . $parentPermission : 'isAdmin')
            ->setFormTypeOption('row_attr.style', 'margin-left: ' . ($level * 1.5) . 'rem;');
        return $permission;
    }
}
