<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 28/12/2017
 * Time: 15:16
 */

namespace AppBundle\Controller\Client;


use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransferClientController extends Controller
{

    public function transferClientAction(Request $request)
    {
        $api = new ApiController();

        $options = [
            'amount' => $request->get('transfer_client_amount'),
            'user_id' => $request->get('transfer_client_user_id')
        ];

        $response = $api->doRequest(
            'POST',
            $this->container->getParameter('api_target').'/transfer-balance',
            $options
        );

        if(true == $response['status']) {
            $request->getSession()->getFlashBag()->add(
                'message_success',
                'transfer telah berhasil'
            );
        }else{
            $request->getSession()->getFlashBag()->add(
                'message_error',
                $response['data']['message']
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

}