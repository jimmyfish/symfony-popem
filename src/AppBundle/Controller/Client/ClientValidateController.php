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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ClientValidateController extends Controller
{
    public function indexAction(Request $request)
    {
        $api = new ApiController();

        if ('POST' === $request->getMethod()) {
            $img = $request->files->get('v_client_file');

            $dirName = $this->getParameter('tmp_directory')['resource'];

            if (!(is_dir($dirName))) {
                @mkdir($dirName, 0777, true);
            }

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
                    $request->getSession()->getFlashBag()->add(
                        'message',
                        'Permintaan validasi telah terkirim'
                    );

                    return $this->redirectToRoute('popem_client_dashboard');
                } else {
                    $request->getSession()->getFlashBag()->add(
                        'message_error',
                        $response['data']['message']
                    );

                    return $this->redirect($request->headers->get('referer'));
                }
            }
        }

        $options['history']['order'] = [['field' => 'created_at', 'value' => 'desc']];

        $information['history'] = $api->doRequest(
            'GET',
            $this->container->getParameter('api_target').'/validation-history?order='.
            urlencode(serialize($options['history']['order']))
        );

        return new JsonResponse(var_dump($information['history']));

        return $this->render('AppBundle:Client:member/validate.client.html.twig', [
            'info' => $information,
        ]);
    }
}
