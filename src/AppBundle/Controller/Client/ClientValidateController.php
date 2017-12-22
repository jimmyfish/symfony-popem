<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 22/12/17
 * Time: 19:09.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientValidateController extends Controller
{
    public function indexAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $api = new ApiController();
            $img = $request->files->get('v_client_file');

            if (!(is_dir($this->getParameter('tmp_directory')['resource']))) {
                @mkdir($this->getParameter('tmp_directory')['resource'], 0777, true);
            }

            $dirName = $this->getParameter('tmp_directory')['resource'];

            $filename = md5(uniqid()).'.'.$img->guessExtension();

            $img->move($dirName, $filename);

            if (file_exists($dirName.'/'.$filename)) {
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
                        'contents' => fopen($dirName.'/'.$filename, 'r'),
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
                    ],
                ];

                $response = $api->doRequest(
                    'POST',
                    $this->container->getParameter('api_target').'/validation-user',
                    $formData,
                    'multipart'
                );

                if (true == $response['status']) {
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
}
