<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:01.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountListController extends Controller
{
    public function indexAction()
    {
        $api = new ApiController();

        $response = $api->doRequest('GET', $this->container->getParameter('api_target').'/list-account');

        return $this->render('AppBundle:Client:member/list.account.html.twig', [
            'data' => $response['data']['data'],
        ]);
    }
}
