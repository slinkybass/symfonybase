<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\RolePermissions;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as EasyAbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractCrudController extends EasyAbstractCrudController
{
    public TranslatorInterface $translator;
    public RolePermissions $rolePermissions;

    public string $transEntity;

    public function __construct(TranslatorInterface $translator, RolePermissions $rolePermissions)
    {
        $this->translator = $translator;
        $this->rolePermissions = $rolePermissions;

        $this->transEntity = $this->transEntity ?? $this->crud();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular($this->transEntitySingular());
        $crud->setEntityLabelInPlural($this->transEntityPlural());

        $entity = $this->entity();
        if ($entity) {
            $pageTitle = $this->transEntitySingular() . ': ' . $entity;
            $editTitle = $this->translator->trans('page_title.edit', ['%entity_label_singular%' => $pageTitle], 'EasyAdminBundle');
            $crud->setPageTitle(Crud::PAGE_DETAIL, $pageTitle);
            $crud->setPageTitle(Crud::PAGE_EDIT, $editTitle);
        }

        $crud->setDefaultSort(['id' => 'DESC']);

        $crud->addFormTheme('@ArkounayUxCollection/ux_collection_form_theme.html.twig');
        $crud->addFormTheme('@ArkounayUxMedia/ux_media_form_theme.html.twig');

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $hasPermission = $this->hasPermissionCrud();
        $hasPermissionNew = $this->hasPermissionCrudAction(Action::NEW);
        $hasPermissionDetail = $this->hasPermissionCrudAction(Action::DETAIL);
        $hasPermissionEdit = $this->hasPermissionCrudAction(Action::EDIT);
        $hasPermissionDelete = $this->hasPermissionCrudAction(Action::DELETE);
        $isOwnUser = $this->user() === $this->entity();

        $denied = match(true) {
            !$hasPermission && !$isOwnUser => [Action::INDEX, Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE, Action::BATCH_DELETE],
            !$hasPermission && $isOwnUser  => [Action::INDEX, Action::NEW, Action::DETAIL, Action::DELETE, Action::BATCH_DELETE],
            default                        => array_filter([
                !$hasPermissionNew ? Action::NEW : null,
                !$hasPermissionDetail ? Action::DETAIL : null,
                !$hasPermissionEdit && !$isOwnUser ? Action::EDIT : null,
                !$hasPermissionDelete || $isOwnUser ? Action::DELETE : null,
                !$hasPermissionDelete || $isOwnUser ? Action::BATCH_DELETE : null,
            ]),
        };

        $actions->setPermissions(array_fill_keys($denied, 'NOPERMISSION_ACTION'));

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
        $entity = $this->getContext()?->getEntity()?->getInstance();
        if (is_object($entity)) {
            return $entity;
        }
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $entityId = $request->get(EA::ENTITY_ID);
        if ($entityId) {
            return $this->em()->getRepository($this->getEntityFqcn())->find($entityId);
        }
        return null;
    }

    public function crud(): string
    {
        $className = get_class($this);
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
        return $this->rolePermissions->userHasPermission($this->user(), $permission);
    }

    public function hasPermissionCrud($crud = null): bool
    {
        return $this->rolePermissions->userHasPermissionCrud($this->user(), $crud ?? $this->crud());
    }

    public function hasPermissionCrudAction($action, $crud = null): bool
    {
        return $this->rolePermissions->userHasPermissionCrudAction($this->user(), $crud ?? $this->crud(), $action);
    }

    public function transEntitySingular($entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return $this->translator->trans('entities.' . $entity . '.singular');
    }

    public function transEntityPlural($entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return $this->translator->trans('entities.' . $entity . '.plural');
    }

    public function transEntitySection($section = 'data', $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return $this->translator->trans('entities.' . $entity . '.sections.' . $section);
    }

    public function transEntityAction($action, $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return $this->translator->trans('entities.' . $entity . '.actions.' . $action);
    }

    public function transEntityField($field, $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;
        return $this->translator->trans('entities.' . $entity . '.fields.' . $field);
    }
}
