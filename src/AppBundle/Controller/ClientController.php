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
use AppBundle\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function homeAction(Request $request)
    {
        return $this->render('AppBundle:Client:home/index.html.twig');
    }

    public function blogAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Page::class)->findAll();

        return new Response('BLOG VIEW');
    }

    public function articleAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Post::class)->findAll();

        return $this->render('AppBundle:Client:articles/article.html.twig');
    }

    public function dashboardClientAction()
    {
        return $this->render('AppBundle:Client:defaults/dashboard.html.twig');
    }

    public function loginAuthLegacyAction(Request $request)
    {

    }

    public function dummyAction(Request $request)
    {
        return $this->render('AppBundle:Client:defaults/login.html.twig');
    }
}