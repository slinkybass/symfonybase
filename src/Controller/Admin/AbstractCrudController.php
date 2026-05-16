<?php

namespace App\Controller\Admin;

use App\Model\AppConfig;
use App\Security\AdminUserTrait;
use App\Security\VirtualPermission;
use App\Service\ConfigService;
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

/**
 * Base EasyAdmin CRUD controller for this app: wires shared services, applies
 * RolePermissions to CRUD actions, and exposes helpers for request/session,
 * cached AppConfig, current entity, and translation keys under `entities.*`.
 *
 * Uses {@see \App\Security\AdminUserTrait}: under `/admin` the security principal is {@see \App\Entity\User}.
 */
abstract class AbstractCrudController extends EasyAbstractCrudController
{
    use AdminUserTrait;

    /**
     * Translation entity segment (e.g. `user` → `entities.user.*`).
     * Subclasses may set explicitly; otherwise the constructor falls back to `crud()`.
     */
    public string $transEntity;

    /**
     * @param EntityManagerInterface $em            Persisted entities for `entity()` fallback by id
     * @param TranslatorInterface    $translator    `entities.{transEntity}.*` keys
     * @param ConfigService          $configService Cached application config
     * @param RolePermissions        $rolePermissions CRUD/action permission checks
     */
    public function __construct(
        public EntityManagerInterface $em,
        public TranslatorInterface $translator,
        public ConfigService $configService,
        public RolePermissions $rolePermissions,
    ) {
        $this->transEntity = $this->transEntity ?? $this->crud();
    }

    /**
     * Default labels, sort, and form themes shared by admin CRUDs.
     */
    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular(fn ($entity) => $entity ? $this->transEntitySingular().': '.(string) $entity : $this->transEntitySingular());
        $crud->setEntityLabelInPlural($this->transEntityPlural());

        $crud->setDefaultSort(['id' => 'DESC']);

        $crud->addFormTheme('@ArkounayUxCollection/ux_collection_form_theme.html.twig');
        $crud->addFormTheme('@ArkounayUxMedia/ux_media_form_theme.html.twig');

        return $crud;
    }

    /**
     * Maps denied CRUD actions to an impossible Symfony permission so EasyAdmin hides them.
     */
    public function configureActions(Actions $actions): Actions
    {
        $hasPermission = $this->hasPermissionCrud();
        $hasPermissionNew = $this->hasPermissionCrudAction(Action::NEW);
        $hasPermissionDetail = $this->hasPermissionCrudAction(Action::DETAIL);
        $hasPermissionEdit = $this->hasPermissionCrudAction(Action::EDIT);
        $hasPermissionDelete = $this->hasPermissionCrudAction(Action::DELETE);

        $denied = match (true) {
            !$hasPermission => [Action::INDEX, Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE, Action::BATCH_DELETE],
            default => array_filter([
                !$hasPermissionNew ? Action::NEW : null,
                !$hasPermissionDetail ? Action::DETAIL : null,
                !$hasPermissionEdit ? Action::EDIT : null,
                !$hasPermissionDelete ? Action::DELETE : null,
                !$hasPermissionDelete ? Action::BATCH_DELETE : null,
            ]),
        };

        $actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));

        return $actions;
    }

    /**
     * Admin URL generator from the container (EasyAdmin legacy access pattern).
     */
    public function adminUrl(): AdminUrlGenerator
    {
        return $this->container->get(AdminUrlGenerator::class);
    }

    /**
     * Request stack from the container (EasyAdmin legacy access pattern).
     */
    public function request(): RequestStack
    {
        return $this->container->get('request_stack');
    }

    /**
     * Current session if the request stack exposes one.
     */
    public function session(): ?Session
    {
        return $this->request()?->getSession();
    }

    /**
     * Resolved application configuration (may be null if not yet available).
     */
    public function config(): ?AppConfig
    {
        return $this->configService->get();
    }

    /**
     * Active entity instance from EasyAdmin context, or loaded by `EA::ENTITY_ID` on the current request.
     */
    public function entity(): ?object
    {
        $entity = $this->getContext()?->getEntity()?->getInstance();
        if (is_object($entity)) {
            return $entity;
        }
        $request = $this->request()->getCurrentRequest();
        if ($request === null) {
            return null;
        }
        $entityId = $request->get(EA::ENTITY_ID);
        if ($entityId) {
            return $this->em->getRepository($this->getEntityFqcn())->find($entityId);
        }

        return null;
    }

    /**
     * Short entity key derived from the concrete controller class name (`FooCrudController` → `foo`).
     */
    public function crud(): string
    {
        $className = get_class($this);
        $crudClassNameParts = explode('\\', $className);
        $crudClassName = end($crudClassNameParts);
        $crud = str_replace('CrudController', '', $crudClassName);

        return lcfirst($crud);
    }

    /**
     * Current EasyAdmin CRUD action name, if any.
     */
    public function action(): ?string
    {
        return $this->getContext()?->getCrud()?->getCurrentAction();
    }

    /** True when the current action is the list (index). */
    public function isIndex(): bool
    {
        return $this->action() === Action::INDEX;
    }

    /** True when the current action is detail view. */
    public function isDetail(): bool
    {
        return $this->action() === Action::DETAIL;
    }

    /** True when the current action is create (new entity form). */
    public function isNew(): bool
    {
        return $this->action() === Action::NEW;
    }

    /** True when the current action is edit. */
    public function isEdit(): bool
    {
        return $this->action() === Action::EDIT;
    }

    /** True on new or edit (any screen that shows the entity form). */
    public function isForm(): bool
    {
        return $this->isNew() || $this->isEdit();
    }

    /**
     * EasyAdmin filter payload from the query string (`EA::FILTERS`).
     *
     * @param bool $withHiddenFilters When true, keeps the `hidden_filters` meta entry used by EA
     */
    public function filters(bool $withHiddenFilters = false): array
    {
        $request = $this->request()->getCurrentRequest();
        $value = $request?->query->get(EA::FILTERS);
        $filters = \is_array($value) ? $value : [];
        if (!$withHiddenFilters) {
            unset($filters['hidden_filters']);
        }

        return $filters;
    }

    /** Filter values visible in the UI (excludes keys listed under `hidden_filters`). */
    public function filtersShown(): array
    {
        $filters = $this->filters(true);
        $hiddenFilters = $filters['hidden_filters'] ?? [];
        $filters = array_diff_key($filters, array_flip(array_keys($hiddenFilters)));
        unset($filters['hidden_filters']);

        return $filters;
    }

    /** Filter values that are applied but hidden from the filter bar. */
    public function filtersHidden(): array
    {
        $filters = $this->filters(true);
        $hiddenFilters = $filters['hidden_filters'] ?? [];
        $filters = array_intersect_key($filters, array_flip(array_keys($hiddenFilters)));
        unset($filters['hidden_filters']);

        return $filters;
    }

    /** Single filter value from the non-hidden filter set. */
    public function filter(string $name): array|string|null
    {
        $filters = $this->filters();

        return $filters[$name] ?? null;
    }

    /** Single filter value from the visible subset only. */
    public function filterShown(string $name): array|string|null
    {
        $filters = $this->filtersShown();

        return $filters[$name] ?? null;
    }

    /** Single filter value from the hidden subset only. */
    public function filterHidden(string $name): array|string|null
    {
        $filters = $this->filtersHidden();

        return $filters[$name] ?? null;
    }

    /** Whether the current user holds an arbitrary application permission. */
    public function hasPermission(string $permission): bool
    {
        return $this->rolePermissions->userHasPermission($this->user(), $permission);
    }

    /**
     * Whether the user may access this CRUD (or the given `$crud` key) at all.
     */
    public function hasPermissionCrud(?string $crud = null): bool
    {
        return $this->rolePermissions->userHasPermissionCrud($this->user(), $crud ?? $this->crud());
    }

    /**
     * Whether the user may run a specific CRUD action (`Action::*` constant or equivalent string).
     */
    public function hasPermissionCrudAction(string $action, ?string $crud = null): bool
    {
        return $this->rolePermissions->userHasPermissionCrudAction($this->user(), $crud ?? $this->crud(), $action);
    }

    /** Singular entity label (`entities.{entity}.singular`). */
    public function transEntitySingular(?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.singular');
    }

    /** Plural entity label (`entities.{entity}.plural`). */
    public function transEntityPlural(?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.plural');
    }

    /** Section heading (`entities.{entity}.sections.{section}`). */
    public function transEntitySection(string $section = 'data', ?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.sections.'.$section);
    }

    /** Custom action label (`entities.{entity}.actions.{action}`). */
    public function transEntityAction(string $action, ?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.actions.'.$action);
    }

    /** Field label (`entities.{entity}.fields.{field}`). */
    public function transEntityField(string $field, ?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.fields.'.$field);
    }
}
