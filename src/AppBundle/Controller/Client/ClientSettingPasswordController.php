<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 21/12/17
 * Time: 18:40.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientSettingPasswordController extends Controller
{
    public function indexAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $api = new ApiController();

            if ($request->get('new-password') == $request->get('password-confirm')) {
                $options = [
                    'old_password' => $request->get('old-password'),
                    'password' => $request->get('new-password'),
                    'passconf' => $request->get('password-confirm'),
                ];
            }

            $response = $api->doRequest(
                'POST',
                $this->container->getParameter('api_target').'/setting-password',
                $options
            );
        }

        return $this->render('AppBundle:Client:member/setting.html.twig');
    }
}
