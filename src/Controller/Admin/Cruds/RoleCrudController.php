<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\Role;
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
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RoleCrudController extends AbstractCrudController
{
    private $rolePermissions;

    public function __construct(RolePermissions $rolePermissions)
    {
        parent::__construct();
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

        $dataPanel = FieldGenerator::panel($this->transEntitySection())->setIcon('lock');
        $displayName = FieldGenerator::text('displayName')->setLabel($this->transEntityField('displayName'));
        $isAdmin = FieldGenerator::switch('isAdmin')->setLabel($this->transEntityField('isAdmin'))
            ->setHtmlAttribute('data-hf-parent', 'isAdmin');

        $permissionsPanel = FieldGenerator::panel($this->transEntitySection('permissions'))->setIcon('lock');
        $crudPermissions = $this->rolePermissions->getCrudPermissions();
        $crudPermissionsFields = [];
        $this->rolePermissions->loopPermissions($crudPermissions, function ($permission, $parentPermission) use (&$crudPermissionsFields) {
            $crudPermissionsFields[] = $this->generatePermissionField($permission, $permission, $parentPermission);
        });

        $nameUsers = $this->config()->enablePublic && $entity && $entity->isAdmin() ? 'admin' : 'user';
        $iconUsers = $this->config()->enablePublic && $entity && $entity->isAdmin() ? 'user-shield' : 'user';
        $usersPanel = FieldGenerator::panel($this->transEntityPlural($nameUsers))->setIcon('' . $iconUsers);
        $users = FieldGenerator::association('users')->setLabel($this->transEntityPlural('user'));

        if ($pageName == Crud::PAGE_INDEX) {
            yield $displayName;
            if ($this->config()->enablePublic) {
                yield $isAdmin->renderAsSwitch(false);
            }
            yield $users->addCssClass('w-1')->setTextAlign('center');
        } elseif ($pageName == Crud::PAGE_DETAIL) {
            yield $dataPanel;
            yield $displayName;
            if ($this->config()->enablePublic) {
                yield $isAdmin;
            }
            $haveAnyCrudPermissions = false;
            foreach ($crudPermissionsFields as $crudPermissionsField) {
                if ($this->hasPermission($crudPermissionsField->getAsDto()->getProperty())) {
                    $haveAnyCrudPermissions = true;
                    break;
                }
            }
            if ($haveAnyCrudPermissions) {
                yield $permissionsPanel;
                foreach ($crudPermissionsFields as $crudPermissionsField) {
                    yield $crudPermissionsField;
                }
            }
            yield $usersPanel;
            yield $users->setLabel(false);
        } elseif ($pageName == Crud::PAGE_NEW || $pageName == Crud::PAGE_EDIT) {
            yield $dataPanel;
            yield $displayName;
            if ($this->config()->enablePublic) {
                yield $isAdmin;
            }
            $haveAnyCrudPermissions = false;
            foreach ($crudPermissionsFields as $crudPermissionsField) {
                if ($this->hasPermission($crudPermissionsField->getAsDto()->getProperty())) {
                    $haveAnyCrudPermissions = true;
                    break;
                }
            }
            if ($haveAnyCrudPermissions) {
                yield $permissionsPanel;
                foreach ($crudPermissionsFields as $crudPermissionsField) {
                    yield $crudPermissionsField;
                }
            }
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

            $hasPermissionEdit = $this->hasPermissionAction(Action::EDIT);
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

            $hasPermissionDelete = $this->hasPermissionAction(Action::DELETE);
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

            $hasPermissionToAdmins = $this->hasPermissionCrud('admin');
            $admins = Action::new('admins', $this->transEntityPlural('admin'))->setIcon('user-shield')
                ->linkToUrl(function ($entity) {
                    return $this->adminUrl()->setController(AdminCrudController::class)->setAction(Action::INDEX)
                        ->set(EA::FILTERS, [
                            'role' => ['comparison' => ComparisonType::EQ, 'value' => $entity->getId()],
                            'hidden_filters' => ['role' => true],
                        ])
                        ->setEntityId(null)
                        ->generateUrl();
                })
                ->displayIf(static function ($entity) use ($hasPermissionToAdmins) {
                    return $entity->isAdmin() && $hasPermissionToAdmins;
                });
            $actions->add(Crud::PAGE_INDEX, $admins);
            $actions->add(Crud::PAGE_DETAIL, $admins);

            $hasPermissionToUsers = $this->hasPermissionCrud('user');
            $users = Action::new('users', $this->transEntityPlural('user'))->setIcon('user')
                ->linkToUrl(function ($entity) {
                    return $this->adminUrl()->setController(UserCrudController::class)->setAction(Action::INDEX)
                        ->set(EA::FILTERS, [
                            'role' => ['comparison' => ComparisonType::EQ, 'value' => $entity->getId()],
                            'hidden_filters' => ['role' => true],
                        ])
                        ->setEntityId(null)
                        ->generateUrl();
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

            $permissions = [];
            $filesInsideCrudsFolder = array_diff(scandir(__DIR__), ['..', '.']);
            foreach ($filesInsideCrudsFolder as $fileName) {
                $crudName = str_replace('CrudController.php', '', $fileName);
                if (!preg_match('/CrudController.php$/', $fileName)) {
                    continue;
                }
                $permName = 'crud' . ucfirst($crudName);
                $permissions[$permName] = $form->has($permName) ? $form->get($permName)->getData() : false;
                $actionNames = [Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE];
                $actionNames = $crudName == "Config" ? [Action::EDIT] : $actionNames;
                foreach ($actionNames as $actionName) {
                    $subPermName = 'crud' . $crudName . ucfirst($actionName);
                    $permissions[$subPermName] = $form->has($subPermName) ? $form->get($subPermName)->getData() : false;
                }
            }
            $role->setPermissions($permissions);
        });
    }

    private function generatePermissionField(string $permission, string $label, ?string $parentPermission = null): FieldInterface
    {
        $entity = $this->entity();
        $permissionValue = $entity && $entity->getPermission($permission) ? true : false;

        $permission = FieldGenerator::switch($permission)->setLabel($label)
            ->setFormTypeOption('mapped', false)
            ->setFormTypeOption('data', $permissionValue)
            ->setValue($permissionValue)
            ->setFormattedValue($permissionValue)
            ->setHtmlAttribute('data-hf-parent', 'perm_' . $permission)
            ->setHtmlAttribute('data-hf-child', $parentPermission ? 'perm_' . $parentPermission : 'isAdmin')
            ->setHtmlAttribute('data-hf-save-value', 'true');
        return $permission;
    }
}
