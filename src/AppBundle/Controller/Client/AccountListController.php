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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AccountListController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        $response = $api->doRequest('GET', $this->container->getParameter('api_target').'/list-account');

        if(false === $response['data']['status']) {
            $request->getSession()->getFlashBag()->add(
                'message_error',
                $response['data']['message']
            );
        }

        return $this->render('AppBundle:Client:member/list.account.html.twig', [
            'data' => $response['data']['data'],
        ]);
    }
}
