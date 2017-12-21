<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 27/11/17
 * Time: 14:01
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Controller\Api\ApiController;
use AppBundle\Form\LoginType;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use GuzzleHttp\Cookie\SessionCookieJar;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Parser;


class ClientController extends Controller
{
    public function homeAction(Request $request)
    {
        $api = new ApiController();

        $targetUrl = $this->container->getParameter('api_target');

        $information['broker'] = $api->doRequest('GET', $targetUrl . '/broker-list');
        $information['bank'] = $api->doRequest('GET', $targetUrl . '/bank-list');

        return $this->render('AppBundle:Client:home/index.html.twig', [
            'information' => $information,
        ]);
    }

    public function articleAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Post::class)->findAll();

        return $this->render('AppBundle:Client:articles/article.html.twig',[
            'data' => $data
        ]);
    }

    public function dashboardClientAction(Request $request)
    {
        if ($request->getSession()->get('isLogin') == true) {
            $api = new ApiController();
            $targetUrl = $this->container->getParameter('api_target');

            $response = $api->doRequest('GET', $targetUrl . '/user-info');
            $information['broker'] = $api->doRequest('GET', $targetUrl . '/broker-list');

            if ($response['status'] == false) {
                $request->getSession()->clear();
                $this->get('session')->getFlashBag()->add('message', 'Sesi anda telah berakhir, silahkan login kembali');
                return $this->redirectToRoute('popem_client_login_warp');
            }

            return $this->render('AppBundle:Client:defaults/dashboard.html.twig', [
                'data' => $response['data']['data'],
                'information' => $information,
            ]);
        } else {
            return $this->redirectToRoute('popem_client_login_warp');
        }
    }

    public function loginAuthLegacyAction(Request $request)
    {

    }

    public function multiexplode ($delimiters,$string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }

    public function loginAction(Request $request)
    {
        if ($request->getSession()->get('isLogin') == true) {
            return $this->redirectToRoute('popem_client_dashboard');
        }

        return $this->render('AppBundle:Client:defaults/login.html.twig');
    }

    public function logoutAction(Request $request, Session $session)
    {
        $session->clear();

        $cookie = new SessionCookieJar('SESSION_STORAGE');

        $cookie->clear();

        return $this->redirect($request->headers->get('referer'));
    }

    public function activateAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $api = new ApiController();
            $targetUrl = $this->container->getParameter('api_target') . '/activate';

            $options = ['key' => $request->get('key')];

            $response = $api->doRequest($request,'GET', $targetUrl, $options);

            if ($response['status'] == true) {
                return $this->redirectToRoute('popem_client_login_warp');
            }
        }

        return $this->render('AppBundle:Client:defaults/activate.html.twig');
    }

    public function validateClientAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $api = new ApiController();
            $img = $request->files->get('v_client_file');

            if(!(is_dir($this->getParameter('tmp_directory')['resource']))) {
                @mkdir($this->getParameter('tmp_directory')['resource'],0777,true);
            }

            $dirName = $this->getParameter('tmp_directory')['resource'];

            $filename = md5(uniqid()) . '.' . $img->guessExtension();

            $img->move($dirName, $filename);

            if (file_exists($dirName . '/' . $filename)) {
                $formData = [
                    [
                        'name' => 'name',
                        'contents' => $request->get('v_client_name'),
                    ],
                    [
                        'name' => 'phone',
                        'contents' => $request->get('v_client_phone'),
                    ],
                    [
                        'name' => 'address',
                        'contents' => $request->get('v_client_address'),
                    ],
                    [
                        'name' => 'city',
                        'contents' => $request->get('v_client_city'),
                    ],
                    [
                        'name' => 'postal_code',
                        'contents' => $request->get('v_client_postal'),
                    ],
                    [
                        'name' => 'state',
                        'contents' => $request->get('v_client_state'),
                    ],
                    [
                        'name' => 'country',
                        'contents' => $request->get('v_client_country'),
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($dirName . '/' . $filename,'r'),
                    ],
                    [
                        'name' => 'bank_name',
                        'contents' => $request->get('v_client_bank_name'),
                    ],
                    [
                        'name' => 'bank_account',
                        'contents' => $request->get('v_client_bank_account'),
                    ],
                    [
                        'name' => 'bank_beneficiary_name',
                        'contents' => $request->get('v_client_beneficiary'),
                    ]
                ];

                $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/validation-user', $formData, 'multipart');

                if ($response['status'] == true) {
                    $request->getSession()->getFlashBag()->add('message', 'Permintaan validasi telah terkirim');
                    return $this->redirectToRoute('popem_client_dashboard');
                } else {
                    $request->getSession()->getFlashBag()->add('message_error', $response['data']);
                    return false;
                }
            }
        }

        return $this->render('AppBundle:Client:member/validate.client.html.twig');
    }

    public function addAccountAction(Request $request)
    {
        $api = new ApiController();
        $information['broker'] = $api->doRequest('GET', $this->container->getParameter('api_target').'/broker-list');

        if ($request->getMethod() === 'POST') {
            $options = [
                'login' => (int)$request->get('login'),
                'broker_id' => (int)$request->get('broker_id'),
                'phone_password' => $request->get('phone_password'),
            ];

            $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/account-add', $options);

            if ($response['status'] == true) {
                $request->getSession()->getFlashBag()->add('message_success', 'Penambahan akun telah berhasil di proses');
                return $this->redirectToRoute('popem_client_add_account');
            } else {
                $request->getSession()->getFlashBag()->add('message_error', $response['data']);
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render('AppBundle:Client:member/add.account.html.twig', [
            'information' => $information,
        ]);
    }

    public function validateAccountAction(Request $request)
    {
        $api = new ApiController();

        $information['broker'] = $api->doRequest('GET', $this->container->getParameter('api_target').'/broker-list');

        if ($request->getMethod() === 'POST') {
            $img = $request->files->get('file');

            if(!(is_dir($this->getParameter('tmp_directory')['resource']))) {
                @mkdir($this->getParameter('tmp_directory')['resource'],0777,true);
            }

            $dirname = $this->getParameter('tmp_directory')['resource'];
            $filename = md5(uniqid()) . '.' . $img->guessExtension();

            $img->move($dirname, $filename);

            if (file_exists($dirname . '/' . $filename)) {
                $options = [
                    [
                        'name' => 'broker_id',
                        'contents' => (int)$request->get('broker_id'),
                    ],
                    [
                        'name' => 'login',
                        'contents' => (int)$request->get('login'),
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
                        'contents' => fopen($dirname . '/' . $filename, 'r'),
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

                $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/validation-account', $options, 'multipart');

                if ($response['status']) {
                    $request->getSession()->getFlashBag()->add('message_success', 'Pengajuan validasi akun telah berhasil ditambahkan.');
                    return $this->redirectToRoute('popem_client_dashboard');
                } else {
                    $request->getSession()->getFlashBag()->add('message_error', $response['data']);
                    return $this->redirect($request->headers->get('referer'));
                }
            }
        }

        return $this->render('AppBundle:Client:member/validate.account.html.twig', [
            'information' => $information,
        ]);
    }

    public function transferAccountAction(Request $request)
    {
        $api = new ApiController();
        $information['broker'] = $api->doRequest('GET', $this->container->getParameter('api_target').'/broker-list');

        return $this->render('AppBundle:Client:member/transfer.account.html.twig', [
            'information' => $information,
        ]);
    }

    public function transferFromAccountAction(Request $request)
    {
        $api = new ApiController();

        $options = [
            'amount' => $request->get('transfer_from_amount'),
            'account_id' => $request->get('transfer_from_account_id'),
        ];

        $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/transfer-from-account', $options);

        if ($response['status'] === true) {
            $request->getSession()->getFlashBag()->add('message_success', 'Transaksi ke nomor akun ' . $request->get('transfer_from_account_id') . ' telah berhasil');
        } else {
            $request->getSession()->getFlashBag()->add('message_error', $response['data']['message']);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function transferToAccountAction(Request $request)
    {
        $api = new ApiController();

        $options = [
            'amount' => $request->get('transfer_from_amount'),
            'account_id' => $request->get('transfer_from_account_id'),
        ];

        $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/transfer-to-account', $options);

        if ($response['status'] === true) {
            $request->getSession()->getFlashBag()->add('message_success', 'Transaksi ke nomor akun ' . $request->get('transfer_from_account_id') . ' telah berhasil');
        } else {
            $request->getSession()->getFlashBag()->add('message_error', $response['data']['message']);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function listAccountAction()
    {
        $api = new ApiController();

        $response = $api->doRequest('GET', $this->container->getParameter('api_target').'/list-account');

        return $this->render('AppBundle:Client:member/list.account.html.twig', [
            'data' => $response['data']['data'],
        ]);
    }

    public function forgotPasswordAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $api = new ApiController();

            $options = [
                'username' => $request->get('username'),
            ];

            $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/forgot-password', $options);

            if ($response['status'] == true) {
                $request->getSession()->getFlashBag()->add('message', 'Email konfirmasi telah dikirimkan ke email anda.');

                return $this->redirectToRoute('popem_client_login_warp');
            }
        }

        return $this->render('AppBundle:Client:defaults/forgot-password.html.twig');
    }

    public function resetPasswordAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $api = new ApiController();

            $options = [
                'key' => $request->get('key'),
                'password' => $request->get('password'),
                'passconf' => $request->get('confirm-password'),
            ];

            $response = $api->doRequest('POST', $this->container->getParameter('api_target').'/reset-password', $options);

            if ($response['status'] == true) {
                $request->getSession()->getFlashBag()->add('message', 'Password telah berhasil dirubah, silahkan login menggunakan password anda yang baru.');

                return $this->redirectToRoute('popem_client_login_warp');
            }
        }

        return $this->render('AppBundle:Client:account/reset.password.html.twig', ['key' => $request->get('key')]);
    }

    public function dummyAction(Request $request)
    {
        $request->getSession()->getFlashBag()->add('message', 'Permintaan validasi telah terkirim');

        return $this->redirectToRoute('popem_client_dashboard');
    }
}