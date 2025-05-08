<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as EasyAbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

use function Symfony\Component\Translation\t;

abstract class AbstractCrudController extends EasyAbstractCrudController
{
    public $transEntity;

    public function __construct()
    {
        $this->transEntity = $this->transEntity ?? $this->crud();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular($this->transEntitySingular());
        $crud->setEntityLabelInPlural($this->transEntityPlural());

        $entity = $this->entity();
        if ($entity) {
            $pageTitle = $this->transEntitySingular() . ': ' . $entity;
            $editTitle = t('page_title.edit', ['%entity_label_singular%' => $pageTitle], 'EasyAdminBundle');
            $crud->setPageTitle(Crud::PAGE_DETAIL, $pageTitle);
            $crud->setPageTitle(Crud::PAGE_EDIT, $editTitle);
        }

        $crud->setDefaultSort(['id' => 'DESC']);

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->hasPermissionCrud()) {
            if ($this->user() !== $this->entity()) {
                $actions = Actions::new();
            } else {
                $actions->remove(Crud::PAGE_DETAIL, Action::INDEX);
                $actions->remove(Crud::PAGE_DETAIL, Action::DELETE);
                $actions->remove(Crud::PAGE_EDIT, Action::INDEX);
                $actions->remove(Crud::PAGE_EDIT, Action::DELETE);
            }
        } else {
            $hasPermissionNew = $this->hasPermissionAction(Action::NEW);
            $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) use ($hasPermissionNew) {
                return $action->displayIf(static function () use ($hasPermissionNew) {
                    return $hasPermissionNew;
                });
            });
            $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) use ($hasPermissionNew) {
                return $action->displayIf(static function () use ($hasPermissionNew) {
                    return $hasPermissionNew;
                });
            });

            $hasPermissionDetail = $this->hasPermissionAction(Action::DETAIL);
            $actions->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) use ($hasPermissionDetail) {
                return $action->displayIf(static function () use ($hasPermissionDetail) {
                    return $hasPermissionDetail;
                });
            });

            $hasPermissionEdit = $this->hasPermissionAction(Action::EDIT);
            $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) use ($hasPermissionEdit) {
                return $action->displayIf(static function () use ($hasPermissionEdit) {
                    return $hasPermissionEdit;
                });
            });
            $actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) use ($hasPermissionEdit) {
                return $action->displayIf(static function () use ($hasPermissionEdit) {
                    return $hasPermissionEdit;
                });
            });
            $actions->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) use ($hasPermissionEdit) {
                return $action->displayIf(static function () use ($hasPermissionEdit) {
                    return $hasPermissionEdit;
                });
            });

            $hasPermissionDelete = $this->hasPermissionAction(Action::DELETE);
            $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) use ($hasPermissionDelete) {
                return $action->displayIf(static function () use ($hasPermissionDelete) {
                    return $hasPermissionDelete;
                });
            });
            $actions->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, function (Action $action) use ($hasPermissionDelete) {
                return $action->displayIf(static function () use ($hasPermissionDelete) {
                    return $hasPermissionDelete;
                });
            });
            $actions->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) use ($hasPermissionDelete) {
                return $action->displayIf(static function () use ($hasPermissionDelete) {
                    return $hasPermissionDelete;
                });
            });
            $actions->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $action) use ($hasPermissionDelete) {
                return $action->displayIf(static function () use ($hasPermissionDelete) {
                    return $hasPermissionDelete;
                });
            });
        }

        return $actions;
    }

    public function em(): EntityManagerInterface
    {
        return $this->container->get('doctrine')->getManager();
    }

    public function adminUrl(): AdminUrlGenerator
    {
        return $this->container->get(AdminUrlGenerator::class);
    }

    public function request(): RequestStack
    {
        return $this->container->get('request_stack');
    }

    public function session(): ?Session
    {
        return $this->request()?->getSession();
    }

    public function config(): ?\stdClass
    {
        return $this->session()?->get('config');
    }

    public function user(): ?User
    {
        return $this->getUser();
    }

    public function entity(): ?object
    {
        return $this->getContext()?->getEntity()?->getInstance();
    }

    public function crud(): string
    {
        $className = $this->getEntityFqcn();
        $crudClassNameParts = explode('\\', $className);
        $crudClassName = end($crudClassNameParts);
        $crud = str_replace('CrudController', '', $crudClassName);
        return lcfirst($crud);
    }

    public function action(): ?string
    {
        return $this->getContext()?->getCrud()?->getCurrentAction();
    }

    public function filters($withHiddenFilters = false): array
    {
        $filters = filter_input(INPUT_GET, EA::FILTERS, FILTER_SANITIZE_URL, FILTER_REQUIRE_ARRAY) ?? [];
        if (!$withHiddenFilters) {
            unset($filters['hidden_filters']);
        }
        return $filters;
    }

    public function filtersShown(): array
    {
        $filters = $this->filters(true);
        $hiddenFilters = $filters['hidden_filters'] ?? [];
        $filters = array_diff_key($filters, array_flip(array_keys($hiddenFilters)));
        unset($filters['hidden_filters']);
        return $filters;
    }

    public function filtersHidden(): array
    {
        $filters = $this->filters(true);
        $hiddenFilters = $filters['hidden_filters'] ?? [];
        $filters = array_intersect_key($filters, array_flip(array_keys($hiddenFilters)));
        unset($filters['hidden_filters']);
        return $filters;
    }

    public function filter($name): array|string|null
    {
        $filters = $this->filters();
        return $filters[$name] ?? null;
    }

    public function filterShown($name): array|string|null
    {
        $filters = $this->filtersShown();
        return $filters[$name] ?? null;
    }

    public function filterHidden($name): array|string|null
    {
        $filters = $this->filtersHidden();
        return $filters[$name] ?? null;
    }

    public function hasPermission($permission): bool
    {
        return $this->user()->hasPermission($permission);
    }

    public function hasPermissionCrud($crud = null): bool
    {
        return $this->user()->hasPermissionCrud($crud ?? $this->crud());
    }

    public function hasPermissionAction($action, $crud = null): bool
    {
        return $this->user()->hasPermissionAction($action, $crud ?? $this->crud());
    }

    public function transEntitySingular($entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return t('entities.' . $entity . '.singular');
    }

    public function transEntityPlural($entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return t('entities.' . $entity . '.plural');
    }

    public function transEntitySection($section = 'data', $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return t('entities.' . $entity . '.sections.' . $section);
    }

    public function transEntityAction($action, $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return t('entities.' . $entity . '.actions.' . $action);
    }

    public function transEntityField($field, $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return t('entities.' . $entity . '.fields.' . $field);
    }
}
