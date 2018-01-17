<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 17/01/18
 * Time: 16:58.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientDepositHistoryController extends Controller
{
    public function indexAction()
    {
        $api = new ApiController();

        $response = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/deposit-history'
        );

        return $this->render(
            'AppBundle:Client:member/history/deposit.html.twig',
            [
                'info' => $response,
            ]
        );
    }
}
