<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Languages;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private TranslatorInterface $translator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, TranslatorInterface $translator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->translator = $translator;
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

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    }
}
