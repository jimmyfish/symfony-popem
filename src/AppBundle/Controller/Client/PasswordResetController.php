<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 18:57.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PasswordResetController extends Controller
{
    public function passwordResetAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $api = new ApiController();

            $options = [
                'key' => $request->get('key'),
                'password' => $request->get('password'),
                'passconf' => $request->get('confirm-password'),
            ];

            $response = $api->doRequest(
                'POST',
                $this->container->getParameter('api_target').'/reset-password',
                $options
            );

            if (true == $response['status']) {
                $request->getSession()->getFlashBag()->add(
                    'message_success',
                    'Password telah berhasil dirubah, silahkan login menggunakan password anda yang baru.'
                );

                return $this->redirectToRoute('popem_client_login_warp');
            } else {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    $response['data']['message']
                );

                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('AppBundle:Client:account/reset.password.html.twig', ['key' => $request->get('key')]);
    }
}
