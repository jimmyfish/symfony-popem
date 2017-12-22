<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 23/11/17
 * Time: 16:49
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use AppBundle\Entity\ImageResize;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

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

        return $this->render('AppBundle:backend:register/register.html.twig');
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if($session->has('token')) {
            return $this->redirect($this->generateUrl('popem_admin_home'));
        }
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST') {
            $username = $request->get('username');
            $password = sha1(md5($request->get('password')));

            $data = $em->getRepository(User::class)->findOneBy(['username'=>$username]);

            $name = str_replace("","_",strtolower($data->getUsername()));
            $id = $data->getId();

            $token = sha1(md5($name . "_" . $id));

            if($data != null) {
                if($data->getPassword() == $password) {
                    $session->set('token',['value'=>$token]);
                    $session->set('uname',['value'=>$data->getUsername()]);
                }else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'username/password salah'
                    );
                    return $this->redirect($this->generateUrl('popem_admin_login'));
                }
            }else {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'data tidak ditemukan'
                );
                return $this->redirect($this->generateUrl('popem_admin_login'));
            }
            return $this->redirect($this->generateUrl('popem_admin_home'));
        }

        return $this->render('AppBundle:backend:login/login.html.twig');
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();

        $session->clear();

        return $this->redirect($this->generateUrl('popem_admin_login'));
    }

    public function homeAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        return $this->render('AppBundle:backend:home/home.html.twig');
    }

    public function messageAction()
    {
        return $this->render('AppBundle:backend:configuration/message.html.twig');
    }
}