<?php

namespace App\Controller\Admin;

use function Symfony\Component\Translation\t;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as EasyAbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

abstract class AbstractCrudController extends EasyAbstractCrudController
{
	public $em;
	public $adminUrlGenerator;
	public $transEntity;

	public function __construct(EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator)
	{
		$this->em = $em;
		$this->adminUrlGenerator = $adminUrlGenerator;
		$this->transEntity = $this->transEntity ?? $this->currentCrud();
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
			if ($this->getUser() !== $this->entity()) {
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

	public function currentCrud(): string
	{
		$className = get_called_class();
		$crudClassNameParts = explode('\\', $className);
		$crudClassName = end($crudClassNameParts);
		$crud = str_replace('CrudController' , '', $crudClassName);
		return lcfirst($crud);
	}

	public function entityId(): ?int
	{
		return filter_input(INPUT_GET, EA::ENTITY_ID, FILTER_SANITIZE_URL);
	}

	public function entity(): ?object
	{
		$entityId = $this->entityId();
		return $entityId ? $this->em->getRepository($this->getEntityFqcn())->find($entityId) : null;
	}

	public function crudAction(): ?string
	{
		return filter_input(INPUT_GET, EA::CRUD_ACTION, FILTER_SANITIZE_URL);
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

	public function config(): ?object
	{
		return $this->container->get('request_stack')->getSession()->get('config');
	}

	public function hasPermission($permission): bool
	{
		/** @var User $user */
		$user = $this->getUser();
		return $user->hasPermission($permission);
	}

	public function hasPermissionCrud($crud = null): bool
	{
		/** @var User $user */
		$user = $this->getUser();
		$crud = $crud ?? $this->currentCrud();
		return $user->hasPermissionCrud($crud);
	}

	public function hasPermissionAction($action, $crud = null): bool
	{
		/** @var User $user */
		$user = $this->getUser();
		$crud = $crud ?? $this->currentCrud();
		return $user->hasPermissionAction($action, $crud);
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
