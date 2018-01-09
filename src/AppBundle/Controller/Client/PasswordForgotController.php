<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 18:54.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PasswordForgotController extends Controller
{
    public function passwordForgotAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $api = new ApiController();

            $options = [
                'username' => $request->get('username'),
            ];

            $response = $api->doRequest(
                'POST',
                $this->container->getParameter('api_target').'/forgot-password',
                $options
            );

            if (true == $response['status']) {
                $request->getSession()->getFlashBag()->add(
                    'message_success',
                    'Email konfirmasi telah dikirimkan ke email anda.'
                );

                return $this->redirectToRoute('popem_client_login_warp');
            } else {
                $request->getSession()->getFlashBag()->add(
                    'message_error',
                    $response['data']['message']
                );

                return $this->redirectToRoute('popem_client_forgot_password');
            }
        }

        return $this->render('AppBundle:Client:defaults/forgot-password.html.twig');
    }
}
