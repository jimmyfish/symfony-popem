<?php
/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 23/11/17
 * Time: 16:49
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        return $this->render('AppBundle:backend:register/register.html.twig');
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST') {
            $username = $request->get('username');
            $password = md5($request->get('password'));
            
            
            $data = $em->getRepository(User::class)->findOneBy(['username'=>$username]);

            if($data != null) {
                if($data->getPassword() == $password) {
                    $session->set('uname',['value'=>$data->getUsername()]);
                }else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'username/password salah'
                    );
                    return $this->redirect($this->generateUrl('popem_login'));
                }
            }else {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'data tidak ditemukan'
                );
                return $this->redirect($this->generateUrl('popem_login'));
            }
            return $this->redirect($this->generateUrl('popem_admin_home'));
        }

        return $this->render('AppBundle:backend:login/login.html.twig');
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();

        $session->clear();

        return $this->redirect($this->generateUrl(''));
    }

    public function postAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $slugify = new Slugify();

        if($request->getMethod() == 'POST') {
            $data = new Post();
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($data->setTitle($request->get('title'))));
            $data->setBody($request->get('body'));

            if(!empty($request->files->get('image'))) {
                $file = $request->files->get('image');
                $name1 = md5(uniqid()) . '.' . $file->guessExtension();
                $exAllowed = array('jpg','png','jpeg','svg');
                $ex = pathinfo($name1, PATHINFO_EXTENSION);
                if(in_array($ex,$exAllowed)) {
                    if($file instanceof UploadedFile) {
                        if(!$file->getClientSize() > (1024 * 1024 * 1)) {
                            $data->setImage($name1);
                        }else {
                            return 'data tidak boleh lebih dari 1 mb';
                        }
                    }
                }else{
                    return 'extension harus .jpg, .png, .jpeg, .svg';
                }
            }

            $arrNewTag = [];

            foreach ($request->get('tag') as $item) {
                array_push($arrNewTag, $item);
            }

            $data->setTag(serialize($arrNewTag));
            $data->setMetaKeyword($request->get('meta-keyword'));
            $data->setMetaDescription($request->get('meta-description'));

            $em->persist($data);
            $em->flush();

            return 'data berhasil disimpan';
        }

        return $this->render('AppBundle:backend:post/new-post.html.twig');
    }

    public function listPostAction()
    {
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(User::class)->findAll();

        return $this->render('AppBundle:backend:post/list-post.html.twig',['data'=>$data]);
    }

    public function homeAction()
    {
        return $this->render('AppBundle:backend:home/home.html.twig');
    }

}