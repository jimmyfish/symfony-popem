<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:54.
 */

namespace AppBundle\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientLoginController extends Controller
{
    public function indexAction(Request $request)
    {
        if (true == $request->getSession()->get('isLogin')) {
            return $this->redirectToRoute('popem_client_dashboard');
        }

        return $this->render('AppBundle:Client:defaults/login.html.twig');
    }
}
