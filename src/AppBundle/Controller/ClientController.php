<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 27/11/17
 * Time: 14:01.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Controller\Api\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends Controller
{
    public function articleAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Post::class)->findAll();

        return $this->render('AppBundle:Client:articles/article.html.twig', [
            'data' => $data,
        ]);
    }

    public function loginAuthLegacyAction(Request $request)
    {
    }

    public function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);

        return  $launch;
    }

    public function activateAction(Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $api = new ApiController();
            $targetUrl = $this->container->getParameter('api_target').'/activate';

            $options = ['key' => $request->get('key')];

            $response = $api->doRequest('GET', $targetUrl, $options);

            if (true == $response['status']) {
                return $this->redirectToRoute('popem_client_login_warp');
            }
        }

        return $this->render('AppBundle:Client:defaults/activate.html.twig');
    }

    public function dummyAction(Request $request)
    {
        $request->getSession()->getFlashBag()->add('message', 'Permintaan validasi telah terkirim');

        return $this->redirectToRoute('popem_client_dashboard');
    }
}
