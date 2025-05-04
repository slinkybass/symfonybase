<?php

namespace App\Controller\Admin;

use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
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
		$locales = array();
		foreach ($localesStr as $localeStr) {
            if ($localeStr) {
                $locales[] = Locale::new($localeStr, ucfirst(Languages::getName($localeStr)) . ' (' . $localeStr . ')', 'icon ti ti-language');
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

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    }

	public function configureUserMenu(UserInterface $userInterface): UserMenu
	{
        /** @var User $user */
        $user = $userInterface;
		$userMenu = parent::configureUserMenu($userInterface);

		if ($user->getAvatar()) {
			$userMenu->setAvatarUrl($user->getAvatar());
		}

		$menuItems = array();
		if ($this->isGranted('IS_IMPERSONATOR')) {
			$menuItems[] = MenuItem::linkToExitImpersonation(t('user.exit_impersonation', [], 'EasyAdminBundle'), 'icon ti ti-user-x');
		} else {
			$menuItems[] = MenuItem::linkToLogout(t('user.sign_out', [], 'EasyAdminBundle'), 'icon ti ti-logout');
		}
		$userMenu->setMenuItems($menuItems);

		return $userMenu;
	}
}
