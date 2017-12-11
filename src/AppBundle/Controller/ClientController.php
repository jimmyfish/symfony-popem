<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 27/11/17
 * Time: 14:01
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Controller\Api\ApiController;
use AppBundle\Form\LoginType;
use Doctrine\Common\Util\Debug;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use GuzzleHttp\Cookie\SessionCookieJar;


class ClientController extends Controller
{
    public function homeAction()
    {
        return $this->render('AppBundle:Client:home/index.html.twig');
    }

    public function blogAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Page::class)->findAll();

        return new Response('BLOG VIEW');
    }

    public function detailBlogAction($slug)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Page::class)->findOneBy(['slug'=>$slug]);

        return $this->render('',[
            'data' => $data
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

    public function detailArticleAction($slug)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Post::class)->findOneBy(['slug'=>$slug]);

        return $this->render('', [
            'data' => $data
        ]);
    }


    public function dashboardClientAction()
    {
        $api = new ApiController();
        $targetUrl = $this->container->getParameter('api_target') . '/user-info';

        $response = $api->doRequest($request, 'GET', $targetUrl);

        if ($response['status'] == false) {
            if ($request->getSession()->get('isLogin') == true) {
                $request->getSession()->clear();
            }
            return $this->redirectToRoute('popem_client_login_warp');
        }

        return $this->render('AppBundle:Client:defaults/dashboard.html.twig', [
            'data' => $response['data']['data'],
        ]);
    }

    public function loginAuthLegacyAction(Request $request)
    {

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

        return $this->redirect($request->headers->get('referer'));
    }

    public function activateAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $api = new ApiController();
            $targetUrl = $this->container->getParameter('api_target') . '/activate';

            $options = ['key' => $request->get('key')];

            $response = $api->doRequest($request,'GET', $targetUrl, $options);

//            return new JsonResponse($response);

            if ($response['status'] == true) {
                return $this->redirectToRoute('popem_client_login_warp');
            }
        }

        return $this->render('AppBundle:Client:defaults/activate.html.twig');
    }
}
