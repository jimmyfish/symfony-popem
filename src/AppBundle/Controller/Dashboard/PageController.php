<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 14:12
 */

namespace AppBundle\Controller\Dashboard;


use AppBundle\Entity\ImageResize;
use AppBundle\Entity\Page;
use AppBundle\Entity\Tag;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{

    public function newPageAction(Request $request,$id=0)
    {
        $session = $request->getSession();
        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $tag = $em->getRepository(Tag::class)->findAll();

        $slugify = new Slugify();

        $page = $em->getRepository(Page::class)->findAll();

        $newData = [];

        if($request->getMethod() == 'POST') {
            $data = new Page();
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($request->get('title')));
            $data->setBody($request->get('body'));
            $data->setStatus(0);

            if(!is_dir($this->getParameter('page_directory')['resource'])) {
                @mkdir($this->getParameter('page_directory')['resource'],0777,true);
            }

            if(!(empty($request->files->get('image')))) {
                $file = $request->files->get('image');
                $name1 = md5(uniqid()) . '.' . $file->guessExtension();
                $exAllowed = array('jpg','png','jpeg','svg');
                $ex = pathinfo($name1, PATHINFO_EXTENSION);
                if(in_array($ex,$exAllowed)) {
                    if($file instanceof UploadedFile) {
                        if(!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('image')->getPathName()
                            )->saveTo($this->getParameter('page_directory')['resource'] . '/' . $name1,20,true);
//                            move_uploaded_file($name1,$this->getParameter('post_directory')['resource']);
                            $data->setImage($name1);
                        }else {
                            $this->get('session')->getFlashBag()->add(
                                'message_error',
                                'ukuran file tidak boleh lebih dari 1 mb'
                            );

                            return $this->redirect($this->generateUrl('popem_admin_new_page'));
                        }
                    }
                }else{
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'extension file harus .jpg, .png , .svg , .jpeg'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_new_page'));
                }
            }else {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'file gambar belum dimasukkan'
                );

                return $this->redirect($this->generateUrl('popem_admin_new_page'));
            }

            $arrNewTag = [];

            foreach ($request->get('tag') as $item) {
                array_push($arrNewTag, $item);
            }

            $data->setTag(serialize($arrNewTag));
            $data->setMetaKeyword($request->get('meta-keyword'));
            $data->setMetaDescription($request->get('meta-description'));

            array_push($newData,$data);

            foreach ($newData as $key => $item) {
                foreach ($page as $keyPage => $itemPage) {
                    if($itemPage->getSlug() === $item->getSlug()) {
                        $item->setSlug($slugify->slugify($request->get('title')) . '-' . $itemPage->getId());
                    }
                }
            }

            foreach ($newData as $data) {
                $em->persist($data);
            }

            try {
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'message_success',
                    'data berhasil disimpan'
                );
            }catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'data tidak berhasil disimpan'
                );
            }

            return $this->redirect($this->generateUrl('popem_admin_list_page'));

        }

        return $this->render('AppBundle:backend:page/new-page.html.twig' , [
            'tag' =>$tag
        ]);
    }

    public function updatePageAction(Request $request,$id)
    {
        $session = $request->getSession();

        $slugify = new Slugify();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Page::class)->find($id);

        $tag = $em->getRepository(Tag::class)->findAll();

        $page = $em->getRepository(Page::class)->findAll();

        $newData = [];

        if($request->getMethod() == 'POST') {
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($request->get('title')));
            $data->setBody($request->get('body'));
            if(!(empty($request->files->get('image')))) {
                $file = $request->files->get('image');
                $name1 = md5(uniqid()) . '.' . $file->guessExtension();
                $exAllowed = array('jpg','png','jpeg','svg');
                $ex = pathinfo($file, PATHINFO_EXTENSION);

                if(in_array($ex,$exAllowed)) {
                    if($file instanceof UploadedFile) {
                        if(!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('image')->getPathName()
                            )->saveTo($this->getParameter('page_directory')['resource'] . '/' . $name1,30,true);
                            $data->setImage($name1);
//                            $data->setImage($this->getParameter('page_directory')['resource'] . '/' . $name1);
                        }else {
                            $this->get('session')->getFlashBag()->add(
                                'message_error',
                                'file tidak boleh lebih dari 1 mb'
                            );

                            return $this->redirect($this->generateUrl('popem_admin_update_page',['id'=>$data->getId()]));
                        }
                    }
                }else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'extension file harus .jpg, .jpeg, .png, .svg'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_update_page',['id'=>$data->getId()]));
                }
            }

            $arrNewtag = [];

            foreach ($request->get('tag') as $item) {
                array_push($arrNewtag, $item);
            }

            $data->setTag(serialize($arrNewtag));

            $data->setMetaKeyword($request->get('meta-keyword'));
            $data->setMetaDescription($request->get('meta-description'));
            $data->setStatus($request->get('status'));

            array_push($newData,$data);

            foreach ($newData as $key => $item) {
                foreach ($page as $keyPage => $itemPage) {
                    if($itemPage->getSlug() == $item->getSlug()) {
                        $item->setSlug($slugify->slugify($request->get('title')) . '-' . $itemPage->getId());
                    }
                }
            }

            foreach ($newData as $data) {
                $em->persist($data);
            }

            try {
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'message_success',
                    'data berhasil di update'
                );
            }catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'data tidak boleh sama'
                );
            }

            return $this->redirect($this->generateUrl('popem_admin_list_page'));
        }

        return $this->render('AppBundle:backend:page/update-page.html.twig',[
            'data'=>$data,
            'tag' => $tag
        ]);
    }

    public function listPageAction(Request $request)
    {
        $session = $request->getSession();
        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Page::class)->findAll();

        return $this->render('AppBundle:backend:page/list-page.html.twig',['data'=>$data]);
    }

}