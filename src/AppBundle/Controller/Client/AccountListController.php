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

        $data = [];

//        return new JsonResponse($response);

        if ($response['data']['status'] === false) {
            $request->getSession()->getFlashBag()->add(
                'message_error',
                $response['data']['message']
            );
        } else {
            $data = $response['data']['data'];
        }

        return $this->render('AppBundle:Client:member/list.account.html.twig', [
            'data' => $data,
        ]);
    }
}
