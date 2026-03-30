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
        $isEnablePublic = $entity && $entity->isEnablePublic();
        $isEnableRegister = $entity && $entity->isEnableRegister();
        $isForm = in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT]);

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection())
            ->setIcon('settings');
        $enablePublic = FieldGenerator::switch('enablePublic')
            ->setLabel($this->transEntityField('enablePublic'))
            ->isSwitch($isForm)
            ->setHtmlAttribute('data-hf-parent', 'enablePublic')
            ->setColumns(12);
        $enableResetPassword = FieldGenerator::switch('enableResetPassword')
            ->setLabel($this->transEntityField('enableResetPassword'))
            ->isSwitch($isForm)
            ->setHtmlAttribute('data-hf-child', 'enablePublic')
            ->displayIf($isForm || $isEnablePublic)
            ->setColumns(12);
        $enableRegister = FieldGenerator::switch('enableRegister')
            ->setLabel($this->transEntityField('enableRegister'))
            ->isSwitch($isForm)
            ->setHtmlAttribute('data-hf-child', 'enablePublic')
            ->setHtmlAttribute('data-hf-parent', 'enableRegister')
            ->displayIf($isForm || $isEnablePublic)
            ->setColumns(12);
        $roleDefaultRegister = FieldGenerator::association('roleDefaultRegister')
            ->setLabel($this->transEntityField('roleDefaultRegister'))
            ->setHtmlAttribute('data-hf-child', 'enableRegister')
            ->displayIf($isForm || $isEnableRegister)
            ->isRequired();

        /*** Privacy ***/
        $privacyPanel = FieldGenerator::panel($this->transEntitySection('privacy'))
            ->setIcon('settings')
            ->displayIf($isForm || $isEnablePublic);
        $enableCookies = FieldGenerator::switch('enableCookies')
            ->setLabel($this->transEntityField('enableCookies'))
            ->isSwitch($isForm)
            ->setHtmlAttribute('data-hf-child', 'enablePublic')
            ->displayIf($isForm || $isEnablePublic)
            ->setColumns(12);

        yield $dataPanel;
        yield $enablePublic;
        yield $enableResetPassword;
        yield $enableRegister;
        yield $roleDefaultRegister;
        yield $privacyPanel;
        yield $enableCookies;
    }

    public function configureActions(Actions $actions): Actions
    {
        $config = $this->em()->getRepository(Config::class)->get();
        $hasPermission = $this->hasPermissionCrud();

        $denied = [Action::INDEX, Action::DELETE, Action::BATCH_DELETE];
        array_merge($denied, $config || !$hasPermission ? [Action::NEW] : []);
        array_merge($denied, !$hasPermission ? [Action::DETAIL, Action::EDIT] : []);

        $actions->setPermissions(array_fill_keys($denied, 'NOPERMISSION_ACTION'));

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
