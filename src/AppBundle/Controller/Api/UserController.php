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
        $apiCont = new ApiController();

        $targetUrl = $this->container->getParameter('api_target');

        $response = $apiCont->doRequest('GET', $targetUrl . '/user-info');

        if ($response['status'] == false) {
            $request->getSession()->clear();
            $this->get('session')->getFlashBag()->add('message', 'Sesi anda telah berakhir, silahkan login kembali');
            return $this->redirectToRoute('popem_client_login_warp');
        }

        return new JsonResponse($response);
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
            $dataUser = $response->doRequest('GET', $targetUrl .'/user-info');
            $session->set('userLog', $dataUser['data']['data']['name']);
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

    public function clientWithdrawalAccountAction(Request $request)
    {
        $api = new ApiController();

        $targetUrl = $this->container->getParameter('api_target');

        $options = [
            'broker_id' => $request->get('broker_id'),
            'login' => $request->get('login'),
            'phone_password' => $request->get('email'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'bank_name' => $request->get('bank_name'),
            'bank_account' => $request->get('bank_account'),
            'bank_beneficiary_name' => $request->get('bank_beneficiary_name'),
            'amount' => $request->get('amount'),
        ];

        $response = $api->doRequest('POST', $targetUrl . '/withdrawal-account', $options);

        return new JsonResponse($response);
    }

    public function clientValidationAction(Request $request)
    {
        $api = new ApiController();
        $img = $request->files->get('file');

        if(!(is_dir($this->getParameter('tmp_directory')['resource']))) {
            @mkdir($this->getParameter('tmp_directory')['resource'],0777,true);
        }

        $dirName = $this->getParameter('tmp_directory')['resource'];

        $filename = md5(uniqid()) . '.' . $img->guessExtension();

        $img->move($dirName, $filename);

        if (file_exists($dirName . '/' . $filename)) {
            $options = [
                [
                    'name' => 'name',
                    'contents' => $request->get('name'),
                ],
                [
                    'name' => 'phone',
                    'contents' => $request->get('phone'),
                ],
                [
                    'name' => 'address',
                    'contents' => $request->get('address'),
                ],
                [
                    'name' => 'city',
                    'contents' => $request->get('city'),
                ],
                [
                    'name' => 'postal_code',
                    'contents' => $request->get('postal_code'),
                ],
                [
                    'name' => 'state',
                    'contents' => $request->get('state'),
                ],
                [
                    'name' => 'country',
                    'contents' => $request->get('country'),
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($dirName . '/' . $filename,'r'),
                ],
                [
                    'name' => 'bank_name',
                    'contents' => $request->get('bank_name'),
                ],
                [
                    'name' => 'bank_account',
                    'contents' => $request->get('bank_account'),
                ],
                [
                    'name' => 'bank_beneficiary_name',
                    'contents' => $request->get('bank_beneficiary_name'),
                ]
            ];

            $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/validation-user', $options, 'multipart');

            return new JsonResponse($response);
        }
    }

    public function accountValidationAction(Request $request)
    {
        $api = new ApiController();

        $img = $request->files->get('file');

        if(!(is_dir($this->getParameter('tmp_directory')['resource']))) {
            @mkdir($this->getParameter('tmp_directory')['resource'],0777,true);
        }

        $dirName = $this->getParameter('tmp_directory')['resource'];

        $filename = md5(uniqid()) . '.' . $img->guessExtension();

        $img->move($dirName, $filename);

        if (file_exists($dirName . '/' . $filename)) {
            $options = [
                [
                    'name' => 'broker_id',
                    'contents' => $request->get('broker_id'),
                ],
                [
                    'name' => 'login',
                    'contents' => $request->get('login'),
                ],
                [
                    'name' => 'phone_password',
                    'contents' => $request->get('phone_password'),
                ],
                [
                    'name' => 'email',
                    'contents' => $request->get('email'),
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($dirName . '/' . $filename, 'r')
                ],
                [
                    'name' => 'bank_name',
                    'contents' => $request->get('bank_name'),
                ],
                [
                    'name' => 'bank_account',
                    'contents' => $request->get('bank_account'),
                ],
                [
                    'name' => 'bank_beneficiary_name',
                    'contents' => $request->get('bank_beneficiary_name'),
                ],
            ];

            $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/validation_account', $options, 'multipart');
            unlink($dirName . '/' . $filename);
            return new JsonResponse($response);
        } else {
            return new JsonResponse('Image upload not successful');
        }
    }

    public function addAccountAction(Request $request)
    {
        $api = new ApiController();

        $options = [
            'login' => $request->get('login'),
            'broker_id' => $request->get('broker_id'),
            'phone_password' => $request->get('phone_password'),
        ];

        $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/account-add', $options);

        return new JsonResponse($response);
    }

    public function listAccountAction()
    {
        $api = new ApiController();

        $response = $api->doRequest('GET', $this->container->getParameter('api_target').'/list-account');

        return new JsonResponse($response);
    }

}
