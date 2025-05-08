<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\Config;
use App\Field\FieldGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\RedirectResponse;

use function Symfony\Component\Translation\t;

class ConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud->setPageTitle(Crud::PAGE_INDEX, $this->transEntitySingular());
        $crud->setPageTitle(Crud::PAGE_DETAIL, $this->transEntitySingular());
        $crud->setPageTitle(Crud::PAGE_NEW, $this->transEntitySingular());
        $crud->setPageTitle(Crud::PAGE_EDIT, $this->transEntitySingular());
        $crud->setSearchFields(null);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $entity = $this->entity();

        $dataPanel = FieldGenerator::panel($this->transEntitySection())->setIcon('settings');
		$appName = FieldGenerator::text('appName')->setLabel($this->transEntityField('appName'))->setColumns(6);
		$appColor = FieldGenerator::color('appColor')->setLabel($this->transEntityField('appColor'))->setColumns(6);
		$appLogo = FieldGenerator::text('appLogo')->setLabel($this->transEntityField('appLogo'))->setColumns(6);
		$appFavicon = FieldGenerator::text('appFavicon')->setLabel($this->transEntityField('appFavicon'))->setColumns(6);
		$appTimezone = FieldGenerator::timezone('appTimezone')->setLabel($this->transEntityField('appTimezone'))->setColumns(6);
        $enablePublic = FieldGenerator::switch('enablePublic')->setLabel($this->transEntityField('enablePublic'))->setColumns(12);
		$appDescription = FieldGenerator::textarea('appDescription')->setLabel($this->transEntityField('appDescription'))->setColumns(6);
		$appKeywords = FieldGenerator::textarea('appKeywords')->setLabel($this->transEntityField('appKeywords'))->setColumns(6);

        yield $dataPanel;
		yield $appName;
		yield $appColor;
		yield $appLogo;
		yield $appFavicon;
		yield $appTimezone;
        yield $enablePublic;
		if (($entity && $entity->isEnablePublic() && $pageName == Crud::PAGE_DETAIL) || $pageName !== Crud::PAGE_DETAIL) {
			yield $appDescription;
			yield $appKeywords;
		}
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        if ($this->hasPermissionCrud()) {
            $actions->remove(Crud::PAGE_NEW, Action::INDEX);
            $actions->remove(Crud::PAGE_DETAIL, Action::INDEX);
            $actions->remove(Crud::PAGE_EDIT, Action::INDEX);

            $hasPermissionEdit = $this->hasPermissionAction(Action::EDIT);
            $actions->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE);
            $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE, function (Action $action) use ($hasPermissionEdit) {
                return $action
                    ->setIcon('device-floppy')
					->addCssClass('btn-success')
                    ->setLabel(t('action.save', [], 'EasyAdminBundle'))
                    ->displayIf(static function () use ($hasPermissionEdit) {
                        return $hasPermissionEdit;
                    });
            });
        }
        return $actions;
    }

    public function new(AdminContext $context)
    {
        $redirect = parent::new($context);
        if ($redirect instanceof RedirectResponse) {
            $config = $this->em()->getRepository($this->getEntityFqcn())->get();
            $url = $this->adminUrl()
                ->setAction(Crud::PAGE_DETAIL)
                ->setEntityId($config->getId())
                ->generateUrl();
            return $this->redirect($url);
        }
        return $redirect;
    }

    public function edit(AdminContext $context)
    {
        $redirect = parent::edit($context);
        if ($redirect instanceof RedirectResponse) {
            $url = $this->adminUrl()
                ->setAction(Crud::PAGE_DETAIL)
                ->setEntityId($this->entity()->getId())
                ->generateUrl();
            return $this->redirect($url);
        }
        return $redirect;
    }
}
