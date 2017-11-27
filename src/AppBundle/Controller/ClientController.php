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

class ClientController extends Controller
{
    public function homeAction(Request $request)
    {
        return $this->render('AppBundle:Client:home/index.html.twig');
    }
}