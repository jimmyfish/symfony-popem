<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 04/01/18
 * Time: 18:01.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientHomeController extends Controller
{
    public function indexAction()
    {
        $api = new ApiController();

        $targetUrl = $this->container->getParameter('api_target');

        $information['broker'] = $api->doRequest('GET', $targetUrl.'/broker-list');
        $information['bank'] = $api->doRequest('GET', $targetUrl.'/bank-list');

        return $this->render('AppBundle:Client:home/index.html.twig', [
            'information' => $information,
        ]);
    }
}
