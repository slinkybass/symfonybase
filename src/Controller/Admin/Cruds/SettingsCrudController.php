<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\Config;
use App\Field\FieldGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingsCrudController extends AbstractCrudController
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

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addAssetMapperEntry('settingsForm');

        return $assets;
    }

    public function configureFields(string $pageName): iterable
    {
        $entity = $this->entity();

        /*** Data ***/
        $dataPanel = FieldGenerator::panel($this->transEntitySection())
            ->setIcon('tool');
        $appName = FieldGenerator::text('appName')
            ->setLabel($this->transEntityField('appName'))
            ->setColumns(6);
        $appColor = FieldGenerator::color('appColor')
            ->setLabel($this->transEntityField('appColor'))
            ->setColumns(6);
        $appLogo = FieldGenerator::media('appLogo')
            ->setLabel($this->transEntityField('appLogo'))
            ->conf('public_config_images')
            ->setColumns(6);
        $appFavicon = FieldGenerator::media('appFavicon')
            ->setLabel($this->transEntityField('appFavicon'))
            ->conf('public_config_images')
            ->setColumns(6);
        $appTimezone = FieldGenerator::timezone('appTimezone')
            ->setLabel($this->transEntityField('appTimezone'))
            ->setColumns(6);
        $senderEmail = FieldGenerator::email('senderEmail')
            ->setLabel($this->transEntityField('senderEmail'))
            ->setColumns(6);
        $appDescription = FieldGenerator::textarea('appDescription')
            ->setLabel($this->transEntityField('appDescription'))
            ->setColumns(6);
        $appKeywords = FieldGenerator::textarea('appKeywords')
            ->setLabel($this->transEntityField('appKeywords'))
            ->setColumns(6);

        /*** Privacy ***/
        $privacyPanel = FieldGenerator::panel($this->transEntitySection('privacy'))
            ->setIcon('tool');
        $privacyText = FieldGenerator::texteditor('privacyText')
            ->setLabel($this->transEntityField('privacyText'));
        $cookiesText = FieldGenerator::texteditor('cookiesText')
            ->setLabel($this->transEntityField('cookiesText'));

        yield from $this->yieldFields([
            $dataPanel,
            $appName,
            $appColor,
            $appLogo,
            $appFavicon,
            $appTimezone,
            $senderEmail,
            ...(!$entity || $entity->isEnablePublic() ? [
                $appDescription,
                $appKeywords,
                $privacyPanel,
                $privacyText,
                ...(!$entity || $entity->isEnableCookies() ? [
                    $cookiesText,
                ] : []),
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
