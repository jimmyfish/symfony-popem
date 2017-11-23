<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 23/11/17
 * Time: 16:49
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST') {
            $data = new User();
            $data->setUsername($request->get('username'));
            $data->setPassword($request->get('password'));

            $em->persist($data);
            $em->flush();

            return 'data berhasil disimpan';
        }
    }

    public function loginAction()
    {
        
    }

}