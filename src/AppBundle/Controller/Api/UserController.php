<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    public function dummyAction(Session $session)
    {
        return $session->get('SESSION_STORAGE');
    }

    public function loginCheckAction(Request $request)
    {
        if ($request->getSession()->get('isLogin') == true) {
            $apiCont = new ApiController();

            $targetUrl = $this->container->getParameter('api_target');

            $response = $apiCont->doRequest('GET', $targetUrl . '/user-info');

            if ($response['status'] == false) {
                $request->getSession()->clear();
                $this->get('session')->getFlashBag()->add('message', 'Sesi anda telah berakhir, silahkan login kembali');
                return $this->redirectToRoute('popem_client_login_warp');
            }

            return new JsonResponse($response);
        } else {
            return $this->redirectToRoute('popem_client_login_warp');
        }
    }

    public function loginAction(Request $request, Session $session)
    {
        $response = new ApiController();

        $options = [
            'username' => $request->get('username'),
            'password' => $request->get('password'),
        ];

        $targetUrl = $this->container->getParameter('api_target');

        $hasil = $response->doRequest('POST', $targetUrl . '/login', $options);

        if ($hasil['status'] == true) {
            $session->set('isLogin', true);
        }

        return new JsonResponse($hasil);
    }

    public function registerAction(Request $request)
    {
        $formContent = [
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'passconf' => $request->get('passconf'),
        ];

        $targetURL = $this->container->getParameter('api_target') . '/register';

        $api = new ApiController();

        $response = $api->doRequest('POST', $targetURL, $formContent);

        return new JsonResponse($response);
    }

    public function clientBalanceAction(Request $request)
    {
        if ($request->getSession()->get('isLogin') == true) {
            $api = new ApiController();

            $targetUrl = $this->container->getParameter('api_target') . '/user-balance';

            $response = $api->doRequest($request, 'GET', $targetUrl);

            if ($response['status'] == false) {
                $request->getSession()->clear();
                $this->get('session')->getFlashBag()->add('message', 'Sesi anda telah berakhir, silahkan login kembali');
                return $this->redirectToRoute('popem_client_login_warp');
            }

            return new JsonResponse($response);
        }

        return $this->redirectToRoute('popem_client_login_warp');
    }

    public function clientDepositAccountAction(Request $request)
    {
        $api = new ApiController();


        $targetUrl = $this->container->getParameter('api_target');

        $img = $request->files->get('file');

        if(!(is_dir($this->getParameter('tmp_directory')['resource']))) {
            @mkdir($this->getParameter('tmp_directory')['resource'],0777,true);
        }

        $dirName = $this->getParameter('tmp_directory')['resource'];

        $filename = md5(uniqid()) . '.' . $img->guessExtension();

        $img->move($dirName, $filename);

        if (file_exists($dirName . '/' . $filename)) {
            $formData = [
                [
                    'name' => 'broker_id',
                    'contents' => $request->get('broker_id')
                ],
                [
                    'name' => 'login',
                    'contents' => $request->get('login')
                ],
                [
                    'name' => 'email',
                    'contents' => $request->get('email')
                ],
                [
                    'name' => 'phone',
                    'contents' => $request->get('phone')
                ],
                [
                    'name' => 'bank_name',
                    'contents' => $request->get('bank_name')
                ],
                [
                    'name' => 'bank_account',
                    'contents' => $request->get('bank_account')
                ],
                [
                    'name' => 'bank_beneficiary_name',
                    'contents' => $request->get('bank_beneficiary_name')
                ],
                [
                    'name' => 'bank_id',
                    'contents' => $request->get('bank_id')
                ],
                [
                    'name' => 'amount',
                    'contents' => $request->get('amount')
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($dirName . '/' . $filename, 'r'),
                ]
            ];

            $response = $api->doRequest('POST', $targetUrl . '/deposit-account', $formData, 'multipart');

            unlink($dirName . '/' . $filename);

            return new JsonResponse($response);
        } else {
            return new JsonResponse(['data' => 'Image upload not successfull']);
        }
    }

}
