<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:59.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Respect\Validation\Validator as v;

class ClientWithdrawalController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        $balance = $api->doRequestDataOnly(
            'GET',
            $this->container->getParameter('api_target').'/user-balance'
        );

        if (1 == !$balance['status']) {
            return $this->redirectToRoute('popem_client_login_warp');
        }

        return $this->render('AppBundle:Client:member/client.withdrawal.html.twig', [
            'balance' => $balance['data'],
        ]);
    }

    public function postAction(Request $request)
    {
        $message = '';

        $api = new ApiController();

        $options = [];

        $valid = v::numeric()->validate($request->get('withdrawal-amount'));

        return new JsonResponse($valid);

        $response = $api->doRequest(
            'POST',
            $this->container->getParameter('api_target').'/withdrawal-balance',
            $options
        );
    }
}
