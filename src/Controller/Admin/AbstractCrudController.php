<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Model\AppConfig;
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
 * Base EasyAdmin CRUD controller: role-based action visibility, filter/query helpers,
 * translation keys for entities, and shortcuts to common services.
 */
abstract class AbstractCrudController extends EasyAbstractCrudController
{
    /** Translation domain key for this CRUD. */
    public string $transEntity;

    public function __construct(
        public EntityManagerInterface $em,
        public TranslatorInterface $translator,
        public ConfigService $configService,
        public RolePermissions $rolePermissions,
    ) {
        $this->transEntity = $this->transEntity ?? $this->crud();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular(fn ($entity) => $entity ? $this->transEntitySingular().': '.(string) $entity : $this->transEntitySingular());
        $crud->setEntityLabelInPlural($this->transEntityPlural());

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

    public function config(): ?AppConfig
    {
        return $this->configService->get();
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
        $request = $this->request()->getCurrentRequest();
        $entityId = $request->get(EA::ENTITY_ID);
        if ($entityId) {
            return $this->em->getRepository($this->getEntityFqcn())->find($entityId);
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

    public function isIndex(): bool
    {
        return $this->action() === Action::INDEX;
    }

    public function isDetail(): bool
    {
        return $this->action() === Action::DETAIL;
    }

    public function isNew(): bool
    {
        return $this->action() === Action::NEW;
    }

    public function isEdit(): bool
    {
        return $this->action() === Action::EDIT;
    }

    public function isForm(): bool
    {
        return $this->isNew() || $this->isEdit();
    }

    public function filters(bool $withHiddenFilters = false): array
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

    public function filter(string $name): array|string|null
    {
        $filters = $this->filters();

        return $filters[$name] ?? null;
    }

    public function filterShown(string $name): array|string|null
    {
        $filters = $this->filtersShown();

        return $filters[$name] ?? null;
    }

    public function filterHidden(string $name): array|string|null
    {
        $filters = $this->filtersHidden();

        return $filters[$name] ?? null;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->rolePermissions->userHasPermission($this->user(), $permission);
    }

    public function hasPermissionCrud(?string $crud = null): bool
    {
        return $this->rolePermissions->userHasPermissionCrud($this->user(), $crud ?? $this->crud());
    }

    public function hasPermissionCrudAction(string $action, ?string $crud = null): bool
    {
        return $this->rolePermissions->userHasPermissionCrudAction($this->user(), $crud ?? $this->crud(), $action);
    }

    public function transEntitySingular(?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.singular');
    }

    public function transEntityPlural(?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.plural');
    }

    public function transEntitySection(string $section = 'data', ?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.sections.'.$section);
    }

    public function transEntityAction(string $action, ?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.actions.'.$action);
    }

    public function transEntityField(string $field, ?string $entity = null): string
    {
        $entity = $entity ?? $this->transEntity;

        return $this->translator->trans('entities.'.$entity.'.fields.'.$field);
    }
}
