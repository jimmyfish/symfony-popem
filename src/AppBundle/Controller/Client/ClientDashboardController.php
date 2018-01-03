<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:10.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientDashboardController extends Controller
{
    public function indexAction(Request $request)
    {
//        if (true == $request->getSession()->get('isLogin')) {
//
//        } else {
//            return $this->redirectToRoute('popem_client_login_warp');
//        }
        $api = new ApiController();
        $targetUrl = $this->container->getParameter('api_target');

        $response = $api->doRequest('GET', $targetUrl.'/user-info');
        $information['broker'] = $api->doRequest('GET', $targetUrl.'/broker-list');

//        if (false == $response['status']) {
//            $request->getSession()->clear();
//            $this->get('session')->getFlashBag()->add(
//                'message',
//                'Sesi anda telah berakhir, silahkan login kembali'
//            );
//
//            return $this->redirectToRoute('popem_client_login_warp');
//        }

        return $this->render('AppBundle:Client:defaults/dashboard.html.twig', [
            'data' => $response['data']['data'],
            'information' => $information,
        ]);
    }
}
