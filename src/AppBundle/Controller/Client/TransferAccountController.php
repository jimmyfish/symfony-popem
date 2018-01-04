<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 17:55.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TransferAccountController.
 */
class TransferAccountController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transferAccountAction(Request $request)
    {
        $api = new ApiController();
        $information['broker'] = $api->doRequest('GET', $this->container->getParameter('api_target').'/broker-list');

        return $this->render('AppBundle:Client:member/transfer.account.html.twig', [
            'information' => $information,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function transferFromAccountAction(Request $request)
    {
        $api = new ApiController();

        $options = [
            'amount' => $request->get('transfer_from_amount'),
            'account_id' => $request->get('transfer_from_account_id'),
        ];

        $response = $api->doRequest(
            'POST',
            $this->container->getParameter('api_target').'/transfer-from-account',
            $options
        );

        if (true === $response['status']) {
            $request->getSession()->getFlashBag()->add(
                'message_success',
                'Transaksi ke nomor akun '.$request->get('transfer_from_account_id').' telah berhasil'
            );
        } else {
            $request->getSession()->getFlashBag()->add(
                'message_error',
                $response['data']['message']
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function transferToAccountAction(Request $request)
    {
        $api = new ApiController();

        $options = [
            'amount' => $request->get('transfer_to_amount'),
            'account_id' => $request->get('transfer_to_account_id'),
        ];

        $response = $api->doRequest(
            'POST',
            $this->container->getParameter('api_target').'/transfer-to-account',
            $options
        );

        if (true === $response['status']) {
            $request->getSession()->getFlashBag()->add(
                'message_success',
                'Transaksi ke nomor akun '.$request->get('transfer_to_account_id').' telah berhasil'
            );
        } else {
            $request->getSession()->getFlashBag()->add('message_error', $response['data']['message']);
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
