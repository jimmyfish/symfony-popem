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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        return $this->render('AppBundle:Client:defaults/dashboard.html.twig');
    }

    public function loginAuthLegacyAction(Request $request)
    {

    }

    public function dummyAction()
    {
        return $this->render('AppBundle:Client:defaults/login.html.twig');
    }
}