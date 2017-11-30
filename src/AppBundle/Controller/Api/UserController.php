<?php

namespace AppBundle\Controller\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{

    public function loginAction(Request $request)
    {
        $client = new Client();

        $response = array();

        $status = true;

        $targetSite = $this->container->getParameter('api_target');

        try {
            $response = $client->request('POST', $targetSite . '/login', [
                'auth' => ['popem_auth', 'Blink182'],
                'form_params' => [
                    'username' => $request->get('username'),
                    'password' => $request->get('password'),
                ],
            ]);

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $status = false;
        }

        $data = [
            'status' => $status,
            'status_code' => $response->getStatusCode(),
            'content-type' => $response->getHeaderLine('content-type'),
            'body' => \GuzzleHttp\json_decode($response->getBody(), true),
        ];

        echo json_encode($data);

        return new Response();
    }

}
