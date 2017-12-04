<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 04/12/17
 * Time: 14:21
 */

namespace AppBundle\Controller\Api;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller
{
    public static function makeRequest($method, $targetURL, $options)
    {
        $client = new Client();

        $response = array();

        $status = true;

        try {
            $response = $client->request($method, $targetURL, $options);

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

        return $data;
    }
}