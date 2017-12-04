<?php

namespace AppBundle\Controller\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{

    public function loginAction(Request $request, Session $session)
    {
        $options = array(
            'auth' => ['popem_auth', 'Blink182'],
            'form_params' => [
                'username' => $request->get('username'),
                'password' => $request->get('password'),
            ],
        );

        $targetSite = $this->container->getParameter('api_target');

        $response = ApiController::makeRequest('POST', $targetSite . '/login', $options);

        if ($response['body']['status'] == true) {
            $session->set('_token', ['value' => base64_encode($request->get('username'))]);

        }

        return new JsonResponse($response);
    }

}
