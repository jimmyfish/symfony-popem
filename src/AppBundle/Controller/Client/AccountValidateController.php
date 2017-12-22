<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:02.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccountValidateController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        $information['broker'] = $api->doRequest('GET', $this->container->getParameter('api_target').'/broker-list');

        if ('POST' === $request->getMethod()) {
            $img = $request->files->get('file');

            if (!(is_dir($this->getParameter('tmp_directory')['resource']))) {
                @mkdir($this->getParameter('tmp_directory')['resource'], 0777, true);
            }

            $dirname = $this->getParameter('tmp_directory')['resource'];
            $filename = md5(uniqid()).'.'.$img->guessExtension();

            $img->move($dirname, $filename);

            if (file_exists($dirname.'/'.$filename)) {
                $options = [
                    [
                        'name' => 'broker_id',
                        'contents' => (int) $request->get('broker_id'),
                    ],
                    [
                        'name' => 'login',
                        'contents' => (int) $request->get('login'),
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
                        'contents' => fopen($dirname.'/'.$filename, 'r'),
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

                $response = $api->doRequest(
                    'POST',
                    $this->container->getParameter('api_target').'/validation-account',
                    $options,
                    'multipart'
                );

                if ($response['status']) {
                    $request->getSession()->getFlashBag()->add(
                        'message_success',
                        'Pengajuan validasi akun telah berhasil ditambahkan.'
                    );

                    return $this->redirectToRoute('popem_client_dashboard');
                } else {
                    $request->getSession()->getFlashBag()->add(
                        'message_error',
                        $response['data']
                    );

                    return $this->redirect($request->headers->get('referer'));
                }
            }
        }

        return $this->render('AppBundle:Client:member/validate.account.html.twig', [
            'information' => $information,
        ]);
    }
}
