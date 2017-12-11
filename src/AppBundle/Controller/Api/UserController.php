<?php

namespace AppBundle\Controller\Api;

use Doctrine\Common\Util\Debug;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    public function dummyAction(Session $session)
    {
        return $session->get('SESSION_STORAGE');
    }

    public function loginCheckAction(Request $request)
    {
        if ($request->getSession()->get('isLogin') == true) {
            $apiCont = new ApiController();

            $targetUrl = $this->container->getParameter('api_target');

            $response = $apiCont->doRequest($request,'GET', $targetUrl . '/user-info');

            if ($response['status'] == false) {
                $request->getSession()->clear();
                return $this->redirectToRoute('popem_client_dummy');
            }

            return new JsonResponse($response);
        } else {
            return $this->redirectToRoute('popem_client_dummy');
        }
    }

    public function loginAction(Request $request, Session $session)
    {
        $response = new ApiController();

        $options = [
            'username' => $request->get('username'),
            'password' => $request->get('password'),
        ];

        $targetUrl = $this->container->getParameter('api_target');

        $hasil = $response->doRequest($request, 'POST', $targetUrl . '/login', $options);

        return new JsonResponse($hasil);
    }

    public function registerAction(Request $request)
    {
        $formContent = [
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'passconf' => $request->get('passconf'),
        ];

        $targetURL = $this->container->getParameter('api_target') . '/register';

        $api = new ApiController();

        $response = $api->doRequest($request, 'POST', $targetURL, $formContent);

        return new JsonResponse($response);
    }

}
