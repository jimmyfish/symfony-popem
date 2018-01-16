<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 12/01/18
 * Time: 16:35.
 */

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ReportFinanceController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        $options = array(
            'login' => $request->get('login'),
            'phone_password' => $request->get('phone_password'),
            'broker_id' => $request->get('broker_id'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        );

        $response = $api->doRequest(
            'POST',
            $this->container->getParameter('api_target').'/finance-history',
            $options
        );

        return new JsonResponse($response);
    }
}
