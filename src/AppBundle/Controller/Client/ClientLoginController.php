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
use AppBundle\Controller\Api\ApiController;

class ClientLoginController extends Controller
{
    public function indexAction(Request $request)
    {
        if (true == $request->getSession()->get('isLogin')) {
            return $this->redirectToRoute('popem_client_dashboard');
        }

        if('POST' == $request->getMethod()) {
        	$api = new ApiController();
        	$options = [
        		'username' => $request->get('username'),
        		'password' => $request->get('password'),
        	];

        	$response = $api->doRequest(
        		'POST',
        		$this->container->getParameter('api_target').'/login',
        		$options
        	);

        	if(true === $response['status']) {
        		return $this->redirectToRoute('popem_client_dashboard');
        	}else{
        		$request->getSession()->getFlashBag()->add(
        			'message',
        			$response['data']['message']
        		);

        		return $this->redirect($request->headers->get('referer'));
        	}
        }

        return $this->render('AppBundle:Client:defaults/login.html.twig');
    }
}
