<?php

namespace App\Controller\Admin;

use App\Entity\Config;
use App\Security\AdminUserTrait;
use App\Security\VirtualPermission;
use App\Service\ConfigService;
use App\Service\RolePermissions;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Twig\Component\Option\ButtonStyle;
use EasyCorp\Bundle\EasyAdminBundle\Twig\Component\Option\ButtonVariant;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AdminDashboard(routePath: '/admin/{_locale}', routeName: 'admin')]
final class DashboardController extends AbstractDashboardController
{
    use AdminUserTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly ConfigService $configService,
        private readonly RolePermissions $rolePermissions,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/home.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        $config = $this->configService->get();

        $dashboard = Dashboard::new();

        $dashboard->setTitle('<img src="'.$config->appLogo.'" class="mx-auto d-block">');
        $dashboard->setFaviconPath($config->appFavicon);
        $dashboard->setDefaultColorScheme('light');
        $dashboard->renderContentMaximized();

        $localesStr = explode('|', $this->getParameter('locales'));
        $locales = [];
        foreach ($localesStr as $localeStr) {
            if ($localeStr) {
                $locales[] = Locale::new($localeStr, ucfirst(Locales::getName($localeStr)).' ('.$localeStr.')', 'language');
            }
        }
        if (count($locales) > 1) {
            $dashboard->setLocales($locales);
        }

        return $dashboard;
    }

    public function configureCrud(): Crud
    {
        $config = $this->configService->get();

        $crud = Crud::new();

        $crud->setTimezone($config->appTimezone);
        $crud->setDefaultRowAction(Action::DETAIL);

        return $crud;
    }

    public function configureAssets(): Assets
    {
        $assets = Assets::new();

        $assets->useCustomIconSet('tabler');
        $assets->addAssetMapperEntry('app');
        $assets->addAssetMapperEntry('admin');

        return $assets;
    }

    public function configureMenuItems(): iterable
    {
        $user = $this->user();
        $configId = $this->em->getRepository(Config::class)->filterFirst()?->getId();
        $config = $this->configService->get();
        $labelAdmin = $config->enablePublic ? 'admin' : 'user';
        $iconAdmin = $config->enablePublic ? 'user-shield' : 'user';

        yield MenuItem::linkToDashboard($this->translator->trans('admin.home.title'), 'home');

        $userItems = [];
        if ($config->enablePublic) {
            $userItems[] = MenuItem::linkTo(Cruds\UserCrudController::class, $this->translator->trans('entities.user.plural'), 'user')
                ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermissionCrud($user, 'user')));
        }
        $userItems[] = MenuItem::linkTo(Cruds\AdminCrudController::class, $this->translator->trans('entities.'.$labelAdmin.'.plural'), $iconAdmin)
            ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermissionCrud($user, 'admin')));
        $userItems[] = MenuItem::linkTo(Cruds\RoleCrudController::class, $this->translator->trans('entities.role.plural'), 'lock')
            ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermissionCrud($user, 'role')));
        $userItemsAvailable = array_filter($userItems, fn ($item) => !VirtualPermission::isDenied($item->getAsDto()->getPermission()));
        if (count($userItemsAvailable) <= 1) {
            yield from $userItems;
        } else {
            yield MenuItem::subMenu($this->translator->trans('entities.user.plural'), 'users')->setSubItems($userItems);
        }

        yield MenuItem::linkToRoute($this->translator->trans('entities.media.plural'), 'file', 'admin_media')
            ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermission($user, 'media')));

        $configItems = [];
        $settingsLink = MenuItem::linkTo(Cruds\SettingsCrudController::class, $this->translator->trans('entities.settings.singular'), 'tool')
            ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermissionCrud($user, 'settings')));
        $configItems[] = $configId ? $settingsLink->setAction(Crud::PAGE_DETAIL)->setEntityId($configId) : $settingsLink->setAction(Crud::PAGE_NEW);
        $configLink = MenuItem::linkTo(Cruds\ConfigCrudController::class, $this->translator->trans('entities.config.singular'), 'settings')
            ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermissionCrud($user, 'config')));
        $configItems[] = $configId ? $configLink->setAction(Crud::PAGE_DETAIL)->setEntityId($configId) : $configLink->setAction(Crud::PAGE_NEW);
        if (class_exists('App\\Entity\\DemoEntity')) {
            $configItems[] = MenuItem::linkTo('App\\Controller\\Admin\\Cruds\\DemoEntityCrudController', $this->translator->trans('entities.demoEntity.singular'), 'flask')
                ->setPermission(VirtualPermission::allowed($this->rolePermissions->userHasPermissionCrud($user, 'demoEntity')));
        }
        $configItemsAvailable = array_filter($configItems, fn ($item) => !VirtualPermission::isDenied($item->getAsDto()->getPermission()));
        if (count($configItemsAvailable) <= 1) {
            yield from $configItems;
        } else {
            yield MenuItem::subMenu($this->translator->trans('entities.config.singular'), 'settings')->setSubItems($configItems);
        }
    }

    public function configureUserMenu(UserInterface $userInterface): UserMenu
    {
        $user = $this->user();
        $userMenu = parent::configureUserMenu($userInterface);

        $userMenu->setMenuItems([
            MenuItem::linkTo(Cruds\AdminCrudController::class, $this->translator->trans('admin.profile.title'), 'user')
                ->setAction(Crud::PAGE_DETAIL)->setEntityId($user->getId()),
            MenuItem::section(),
            MenuItem::linkToExitImpersonation($this->translator->trans('user.exit_impersonation', [], 'EasyAdminBundle'), 'user-x')
                ->setPermission(VirtualPermission::allowed($this->isGranted('IS_IMPERSONATOR'))),
            MenuItem::linkToLogout($this->translator->trans('user.sign_out', [], 'EasyAdminBundle'), 'logout')
                ->setPermission(VirtualPermission::allowed(!$this->isGranted('IS_IMPERSONATOR'))),
        ]);

        return $userMenu;
    }

    public function configureActions(): Actions
    {
        $actions = Actions::new();

        $actions->addBatchAction(Action::BATCH_DELETE);
        $actions->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, function (Action $action) { return $action->setIcon('trash'); });

        $actions->add(Crud::PAGE_INDEX, Action::NEW);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->add(Crud::PAGE_INDEX, Action::EDIT);
        $actions->add(Crud::PAGE_INDEX, Action::DELETE);
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) { return $action->setIcon('plus'); });
        $actions->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) { return $action->setIcon('eye'); });
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) { return $action->setIcon('edit'); });
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) { return $action->setIcon('trash'); });

        $actions->add(Crud::PAGE_NEW, Action::INDEX);
        $actions->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN);
        $actions->update(Crud::PAGE_NEW, Action::INDEX, function (Action $action) { return $action->setIcon('chevron-left')->addCssClass('btn-animate-icon btn-animate-icon-move-start'); });
        $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
            $action->getAsDto()->setVariant(ButtonVariant::Success);

            return $action->setIcon('device-floppy');
        });

        $actions->add(Crud::PAGE_DETAIL, Action::INDEX);
        $actions->add(Crud::PAGE_DETAIL, Action::DELETE);
        $actions->add(Crud::PAGE_DETAIL, Action::EDIT);
        $actions->update(Crud::PAGE_DETAIL, Action::INDEX, function (Action $action) { return $action->setIcon('chevron-left')->addCssClass('btn-animate-icon btn-animate-icon-move-start'); });
        $actions->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
            $action->getAsDto()->setStyle(ButtonStyle::Solid);

            return $action->setIcon('trash');
        });
        $actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) { return $action->setIcon('edit'); });

        $actions->add(Crud::PAGE_EDIT, Action::INDEX);
        $actions->add(Crud::PAGE_EDIT, Action::DELETE);
        $actions->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN);
        $actions->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $action) { return $action->setIcon('chevron-left')->addCssClass('btn-animate-icon btn-animate-icon-move-start'); });
        $actions->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $action) {
            $action->getAsDto()->setStyle(ButtonStyle::Solid);

            return $action->setIcon('trash');
        });
        $actions->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
            $action->getAsDto()->setVariant(ButtonVariant::Success);

            return $action->setIcon('device-floppy');
        });

        $actions->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
        $actions->reorder(Crud::PAGE_NEW, [Action::SAVE_AND_RETURN, Action::INDEX]);
        $actions->reorder(Crud::PAGE_DETAIL, [Action::EDIT, Action::DELETE, Action::INDEX]);
        $actions->reorder(Crud::PAGE_EDIT, [Action::SAVE_AND_RETURN, Action::DELETE, Action::INDEX]);

        return $actions;
    }
}
