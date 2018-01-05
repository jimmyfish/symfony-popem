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
use Respect\Validation\Validator as v;

class ClientDepositController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        $information['bank'] = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/bank-list'
        );

        $amount = v::numeric()->validate($request->get('amount'));
        $bankAccount = v::numeric()->validate($request->get('bank_account'));
        $bankName = v::alnum()->validate($request->get('bank_name'));
        $bankBeneficiary = v::alnum()->validate($request->get('bank_beneficiary_name'));

        if ('POST' === $request->getMethod()) {
            $options = [
                'bank_id' => $request->get('bank_id'),
                'amount' => $amount,
                'bank_name' => $bankName,
                'bank_account' => $bankAccount,
                'bank_beneficiary_name' => $bankBeneficiary,
            ];

            if(true === $amount && $bankAccount && $bankName && $bankBeneficiary) {
                $response = $api->doRequest(
                    'POST',
                    $this->container->getParameter('api_target').'/deposit-balance',
                    $options
                );
            }

            if(false === $amount) {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    'masukkan angka dengan benar'
                );

                return $this->redirect($request->headers->get('referer'));
            }

            if(false === $bankAccount) {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    'masukkan angka dengan benar'
                );

                return $this->redirect($request->headers->get('referer'));
            }

            if(false === $bankName) {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    'masukkan nama bank dengan benar'
                );

                return $this->redirect($request->headers->get('referer'));
            }

            if(false === $bankBeneficiary) {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    'masukkan nama Pemilik bank dengan benar'
                );

                return $this->redirect($request->headers->get('referer'));
            }

            if (true === $response['status']) {
                $request->getSession()->getFlashBag()->add(
                    'message',
                    'Deposit Sukses'
                );
            } else {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    $request['data']['message']
                );
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('AppBundle:Client:member/client.deposit.html.twig', [
            'information' => $information,
        ]);
    }
}
