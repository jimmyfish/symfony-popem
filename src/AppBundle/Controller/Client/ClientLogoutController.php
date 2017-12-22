<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:54.
 */

namespace AppBundle\Controller\Client;

use GuzzleHttp\Cookie\SessionCookieJar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ClientLogoutController extends Controller
{
    public function logoutAction(Request $request, Session $session)
    {
        $session->clear();

        $cookie = new SessionCookieJar('SESSION_STORAGE');

        $cookie->clear();

        return $this->redirect($request->headers->get('referer'));
    }
}
