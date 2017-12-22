<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 23/11/17
 * Time: 16:49.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ('POST' == $request->getMethod()) {
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

        if ($session->has('token')) {
            return $this->redirect($this->generateUrl('popem_admin_home'));
        }
        $em = $this->getDoctrine()->getManager();

        if ('POST' == $request->getMethod()) {
            $username = $request->get('username');
            $password = sha1(md5($request->get('password')));

            $data = $em->getRepository(User::class)->findOneBy(['username' => $username]);

            $name = str_replace('', '_', strtolower($data->getUsername()));
            $id = $data->getId();

            $token = sha1(md5($name.'_'.$id));

            if (null != $data) {
                if ($data->getPassword() == $password) {
                    $session->set('token', ['value' => $token]);
                    $session->set('uname', ['value' => $data->getUsername()]);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'username/password salah'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_login'));
                }
            } else {
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

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        return $this->render('AppBundle:backend:home/home.html.twig');
    }

    public function messageAction()
    {
        return $this->render('AppBundle:backend:configuration/message.html.twig');
    }
}
