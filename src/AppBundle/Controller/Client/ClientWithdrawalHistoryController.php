<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 17/01/18
 * Time: 16:12.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientWithdrawalHistoryController extends Controller
{
    public function indexAction()
    {
        $api = new ApiController();

        $response = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/withdrawal-history'
        );

        return $this->render(
            'AppBundle:Client:member/history/withdrawal.html.twig',
            [
                'info' => $response,
            ]
        );
    }
}
