<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 03/01/18
 * Time: 14:43.
 */

namespace AppBundle\EventListener;

use Doctrine\Common\Util\Debug;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AuthEventListener implements EventSubscriberInterface
{
    private $session;
    private $container;

    public function __construct(Session $session, ContainerInterface $container)
    {
        $this->session = $session;
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $route = 'popem_client_login_warp';

        $exception = [
            'popem_client_dashboard',
            'popem_client_list_account',
            'popem_client_validate_client',
            'popem_client_add_account',
            'popem_client_validate_account',
            'popem_client_transfer_account',
            'popem_client_transfer_from',
            'popem_client_transfer_to',
            'popem_client_validate_client',
            'popem_client_withdrawal_client',
            'popem_client_withdrawal_client_post',
        ];

        $url = $this->container->get('router')->generate($route);

        foreach ($exception as $value) {
            if ($value != $event->getRequest()->get('_route')) {
                return;
            }

            if (false == $this->session->get('isLogin')) {
                $this->session->getFlashBag()->add(
                    'auth_error',
                    'Anda harus masuk untuk mengakses halaman tersebut'
                );

                $response = new RedirectResponse($url);
                $event->setResponse($response);
            }
        }

        return;

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
