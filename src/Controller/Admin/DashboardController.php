<?php

namespace App\Controller\Admin;

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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Security\Core\User\UserInterface;

use function Symfony\Component\Translation\t;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setRoute('admin_home')->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        $session = $this->container->get('request_stack')->getSession();

        $dashboard = Dashboard::new();

        $dashboard->setTitle('<img src="' . $session->get('config')->appLogo . '" class="mx-auto d-block">');
        $dashboard->setFaviconPath($session->get('config')->appFavicon);

        $localesStr = explode('|', $this->getParameter('locales'));
        $locales = [];
        foreach ($localesStr as $localeStr) {
            if ($localeStr) {
                $locales[] = Locale::new($localeStr, ucfirst(Languages::getName($localeStr)) . ' (' . $localeStr . ')', 'language');
            }
        }
        if (count($locales) > 1) {
            $dashboard->setLocales($locales);
        }

        return $dashboard;
    }

    public function configureCrud(): Crud
    {
        $session = $this->container->get('request_stack')->getSession();

        $crud = Crud::new();

        $crud->setTimezone($session->get('config')->appTimezone);

        return $crud;
    }

    public function configureAssets(): Assets
    {
        $assets = Assets::new();

        $assets->useCustomIconSet('tabler');

        return $assets;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'home');
    }

    public function configureUserMenu(UserInterface $userInterface): UserMenu
    {
        /** @var User $user */
        $user = $userInterface;
        $userMenu = parent::configureUserMenu($userInterface);

        if ($user->getAvatar()) {
            $userMenu->setAvatarUrl($user->getAvatar());
        }

        $menuItems = [];
        if ($this->isGranted('IS_IMPERSONATOR')) {
            $menuItems[] = MenuItem::linkToExitImpersonation(t('user.exit_impersonation', [], 'EasyAdminBundle'), 'user-x');
        } else {
            $menuItems[] = MenuItem::linkToLogout(t('user.sign_out', [], 'EasyAdminBundle'), 'logout');
        }
        $userMenu->setMenuItems($menuItems);

        return $userMenu;
    }

	public function configureActions(): Actions
	{
		$actions = Actions::new();

		$actions->addBatchAction(Action::BATCH_DELETE);
		$actions->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, function (Action $action) {return $action->setIcon('trash');});

		$actions->add(Crud::PAGE_INDEX, Action::NEW);
		$actions->add(Crud::PAGE_INDEX, Action::DETAIL);
		$actions->add(Crud::PAGE_INDEX, Action::EDIT);
		$actions->add(Crud::PAGE_INDEX, Action::DELETE);
		$actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {return $action->setIcon('plus');});
		$actions->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {return $action->setIcon('eye');});
		$actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {return $action->setIcon('edit');});
		$actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {return $action->setIcon('trash');});

		$actions->add(Crud::PAGE_NEW, Action::INDEX);
		$actions->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN);
		$actions->update(Crud::PAGE_NEW, Action::INDEX, function (Action $action) {return $action->setIcon('chevron-left');});
		$actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {return $action->setIcon('device-floppy');});

		$actions->add(Crud::PAGE_DETAIL, Action::INDEX);
		$actions->add(Crud::PAGE_DETAIL, Action::DELETE);
		$actions->add(Crud::PAGE_DETAIL, Action::EDIT);
		$actions->update(Crud::PAGE_DETAIL, Action::INDEX, function (Action $action) {return $action->setIcon('chevron-left');});
		$actions->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {return $action->setIcon('trash');});
		$actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {return $action->setIcon('edit');});

		$actions->add(Crud::PAGE_EDIT, Action::INDEX);
		$actions->add(Crud::PAGE_EDIT, Action::DELETE);
		$actions->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN);
		$actions->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $action) {return $action->setIcon('chevron-left');});
		$actions->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $action) {return $action->setIcon('trash');});
		$actions->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {return $action->setIcon('device-floppy');});
			
		$actions->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
		$actions->reorder(Crud::PAGE_NEW, [Action::INDEX, Action::SAVE_AND_RETURN]);
		$actions->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::DELETE, Action::EDIT]);
		$actions->reorder(Crud::PAGE_EDIT, [Action::INDEX, Action::DELETE, Action::SAVE_AND_RETURN]);

		return $actions;
	}
}
