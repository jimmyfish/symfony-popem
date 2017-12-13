<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 04/12/17
 * Time: 14:21
 */

namespace AppBundle\Controller\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function doRequest($method, $uri, array $data = null, $type = 'form_params')
    {

        if ($method === 'GET')
            $type = 'query';

        $options[$type] = $data;

        $httpResponse = new Response();

        $request = new Request();

        $session = $request->getSession();

        $options['auth'] = ['popem_auth', 'Blink182'];

        $options['headers'] = [
            'User-Agent' => $request->headers->get('User-Agent'),
            'Accept' => 'application/json',
        ];

        $cookieJar = new SessionCookieJar('SESSION_STORAGE', true);

        $client = new Client([
            'base_uri' => 'http://localhost:8000',
            'cookies' => $cookieJar,
            'timeout' => 100.0
        ]);

        $response = [];

        $status = true;

        try {
            $response = $client->request($method, $uri, $options);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $status = false;
        }


        $return = [
            'status' => $status,
            'status_code' => $response->getStatusCode(),
            'content_type' => $response->getHeaderLine('content-type'),
            'data' => \GuzzleHttp\json_decode($response->getBody(), true),
        ];

        return $return;
    }
}
