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

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection())
            ->setIcon('settings');
        $enablePublic = FieldGenerator::switch('enablePublic')
            ->setLabel($this->transEntityField('enablePublic'))
            ->setHtmlAttribute('data-hf-parent', 'enablePublic')
            ->setColumns(12);
        $enableResetPassword = FieldGenerator::switch('enableResetPassword')
            ->setLabel($this->transEntityField('enableResetPassword'))
            ->setHtmlAttribute('data-hf-child', 'enablePublic')
            ->setColumns(12);
        $enableRegister = FieldGenerator::switch('enableRegister')
            ->setLabel($this->transEntityField('enableRegister'))
            ->setHtmlAttribute('data-hf-child', 'enablePublic')
            ->setColumns(12);

        /*** Privacy ***/
        $privacyPanel = FieldGenerator::panel($this->transEntitySection('privacy'))
            ->setIcon('settings');
        $enableCookies = FieldGenerator::switch('enableCookies')
            ->setLabel($this->transEntityField('enableCookies'))
            ->setHtmlAttribute('data-hf-child', 'enablePublic')
            ->setColumns(12);

        yield from $this->yieldFields([
            $dataPanel,
            $enablePublic->renderAsSwitch($pageName !== Crud::PAGE_INDEX),
            ...($pageName !== Crud::PAGE_DETAIL || ($entity && $entity->isEnablePublic()) ? [
                $enableResetPassword->renderAsSwitch($pageName !== Crud::PAGE_INDEX),
                $enableRegister->renderAsSwitch($pageName !== Crud::PAGE_INDEX),
                $privacyPanel,
                $enableCookies->renderAsSwitch($pageName !== Crud::PAGE_INDEX),
            ] : []),
        ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        if ($this->hasPermissionCrud()) {
            $actions->remove(Crud::PAGE_NEW, Action::INDEX);
            $actions->remove(Crud::PAGE_DETAIL, Action::INDEX);
            $actions->remove(Crud::PAGE_EDIT, Action::INDEX);

            $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->displayIf(fn () => true);
            });
            $actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {
                return $action->displayIf(fn () => true);
            });
            $actions->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->displayIf(fn () => true);
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
