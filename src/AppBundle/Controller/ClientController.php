<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 27/11/17
 * Time: 14:01
 */

namespace AppBundle\Controller;


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
        return new Response('BLOG VIEW');
    }

    public function articleAction(Request $request)
    {
        return $this->render('AppBundle:Client:articles/article.html.twig');
    }

    public function dashboardClientAction()
    {
        return $this->render('AppBundle:Client:defaults/dashboard.html.twig');
    }
}