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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountValidateController extends Controller
{
    public function indexAction(Request $request)
    {
        $data = [];
        $api = new ApiController();

        $information['broker'] = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/broker-list'
        );

        if ('POST' === $request->getMethod()) {
            return new Response('Should be here');
            $img = $request->files->get('file');

            $data = $request->request->get('login');

            $status = true;

            if ($img instanceof UploadedFile) {
                if ($img->getClientSize() > 1048576) {
                    $this->addFlash(
                        'file_error',
                        'Size file tidak boleh lebih dari 1 MB'
                    );

                    $status = false;
                }

                if (!(is_dir($this->getParameter('tmp_directory')['resource']))) {
                    @mkdir($this->getParameter('tmp_directory')['resource'], 0777, true);
                }

                $dirname = $this->getParameter('tmp_directory')['resource'];
                $filename = md5(uniqid()).'.'.$img->guessExtension();

                $img->move($dirname, $filename);

                if (file_exists($dirname.'/'.$filename)) {
                    $options = [
                        [
                            'name' => 'file',
                            'contents' => fopen($dirname.'/'.$filename, 'r'),
                        ],
                    ];
                }
            }

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

            if (false == $response['status']) {
                $this->addFlash(
                    'message_error',
                    $response['data']['message']
                );

                $status = false;
            }

            $this->addFlash(
                'message_success',
                'Pengajuan validasi akun telah berhasil ditambahkan.'
            );

            if (false == $status) {
                return $this->render('AppBundle:Client:member/validate.account.html.twig', [
                    'information' => $information,
                ]);
            } else {
                return $this->redirectToRoute('popem_client_dashboard');
            }
        }

        return $this->render('AppBundle:Client:member/validate.account.html.twig', [
            'information' => $information,
            'data' => $data,
        ]);
    }
}
