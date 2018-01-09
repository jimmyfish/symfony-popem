<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:07.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccountAddController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!1 == $request->getSession()->get('isLogin')) {
            return $this->redirectToRoute('popem_client_login_warp');
        }

        $api = new ApiController();
        $information['broker'] = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/broker-list'
        );

        if ('POST' === $request->getMethod()) {
            $options = [
                'login' => (int) $request->get('a_login'),
                'broker_id' => (int) $request->get('a_broker_id'),
                'phone_password' => $request->get('a_phone_password'),
            ];

            $response = $api->doRequest(
                'POST',
                $this->container->getParameter('api_target').'/account-add',
                $options
            );

            if (true == $response['status']) {
                $request->getSession()->getFlashBag()->add(
                    'message_success',
                    'Penambahan akun telah berhasil di proses'
                );

                return $this->redirectToRoute('popem_client_add_account');
            } else {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    $response['data']['message']
                );

                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('AppBundle:Client:member/add.account.html.twig', [
            'information' => $information,
        ]);
    }
}
