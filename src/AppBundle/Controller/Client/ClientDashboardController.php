<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:10.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientDashboardController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();
        $targetUrl = $this->container->getParameter('api_target');

        $response = $api->doRequest('GET', $targetUrl.'/user-info');
        $information['broker'] = $api->doRequest('GET', $targetUrl.'/broker-list');

        return $this->render('AppBundle:Client:defaults/dashboard.html.twig', [
            'data' => $response['data']['data'],
            'information' => $information,
        ]);
    }
}
