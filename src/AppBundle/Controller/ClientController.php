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

    public function blogAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Post::class)->findAll();

        return $this->render('AppBundle:Client:blog/list.html.twig',[
            'data' => $data
        ]);
    }

    public function detailBlogAction($slug)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Post::class)->findOneBy(['slug'=>$slug]);

        return $this->render('AppBundle:Client:articles/article.html.twig',[
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

    public function blogCategoryAction($category)
    {
        $manager = $this->getDoctrine()->getManager();

        $query = $manager->getRepository(Post::class)->findOneBy(['categoryId' => $category]);

        return var_dump($query);
    }

    public function dashboardClientAction(Request $request)
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

    public function dummyAction(Request $request)
    {
        $api = new ApiController();

        $targetUrl = $this->container->getParameter('api_target');

        $information['broker'] = $api->doRequest('GET', $targetUrl . '/broker-list');
        $information['bank'] = $api->doRequest('GET', $targetUrl . '/bank-list');

        return $this->render('AppBundle:Client:defaults/dummy.html.twig', [
            'information' => $information,
        ]);
    }
}
