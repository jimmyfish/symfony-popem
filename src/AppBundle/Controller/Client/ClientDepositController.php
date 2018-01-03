<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 03/01/18
 * Time: 19:20.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientDepositController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        $information['bank'] = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/bank-list'
        );

        if ('POST' === $request->getMethod()) {
            $options = [
                'bank_id' => $request->get('bank_id'),
                'amount' => (int) $request->get('amount'),
                'bank_name' => $request->get('bank_name'),
                'bank_account' => (int) $request->get('bank_account'),
                'bank_beneficiary_name' => $request->get('bank_beneficiary_name'),
            ];

            $response = $api->doRequest(
                'POST',
                $this->container->getParameter('api_target').'/deposit-balance',
                $options
            );

            if (true === $response['status']) {
                $request->getSession()->getFlashBag()->add(
                    'message',
                    'Deposit Sukses'
                );

                return $this->redirect($request->headers->get('referer'));
            } else {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    $request['data']['message']
                );

                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('AppBundle:Client:member/client.deposit.html.twig', [
            'information' => $information,
        ]);
    }
}
