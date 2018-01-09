<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 09/01/18
 * Time: 16:49.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientActivateController extends Controller
{
    public function activateAction(Request $request)
    {
        $api = new ApiController();

        if ($request->get('key') && 'GET' == $request->getMethod()) {
            $options = [
                'key' => $request->get('key'),
            ];

            $response = $api->doRequest(
                'GET',
                $this->container->getParameter('api_target').'/activate',
                $options
            );

            if (true == $response['status']) {
                $this->addFlash(
                    'message_success',
                    'Aktivasi telah berhasil,'.
                    'silahkan login menggunakan username dan password anda'
                );
            } else {
                $this->addFlash(
                    'message',
                    $response['data']['message']
                );
            }

            return $this->redirectToRoute('popem_client_login_warp');
        }

        $this->addFlash(
            'message',
            'Maaf halaman tersebut tidak dapat diakses'
        );

        return $this->redirectToRoute('popem_client_login_warp');
    }
}
