<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 13:54.
 */

namespace AppBundle\Controller\Dashboard;

use AppBundle\Entity\Category;
use AppBundle\Entity\ImageResize;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function newPostAction(Request $request)
    {
        $session = $request->getSession();

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $tag = $em->getRepository(Tag::class)->findAll();

        $category = $em->getRepository(Category::class)->findAll();

        $post = $em->getRepository(Post::class)->findAll();

        $slugify = new Slugify();

        $newData = [];

        if ('POST' == $request->getMethod()) {
            $data = new Post();
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($request->get('title')));
            $data->setBody($request->get('body'));
            $data->setStatus(0);

            if (!(is_dir($this->getParameter('post_directory')['resource']))) {
                @mkdir($this->getParameter('post_directory')['resource'], 0777, true);
            }

            if (!(empty($request->files->get('image')))) {
                $file = $request->files->get('image');
                $nama1 = md5(uniqid()).'.'.$file->guessExtension();
                $exAllowed = array('jpg', 'png', 'jpeg', 'svg');
                $ex = pathinfo($nama1, PATHINFO_EXTENSION);

                if (in_array($ex, $exAllowed)) {
                    if ($file instanceof UploadedFile) {
                        if (!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('image')->getPathName()
                            )->saveTo($this->getParameter('post_directory')['resource'].'/'.$nama1, 30, true);
                            $data->setImage($nama1);
//                            $data->setImage($this->getParameter('post_directory')['resource'] . '/' . $nama1);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'message_error',
                                'ukuran file tidak boleh lebih dari 1 mb'
                            );

                            return $this->redirect($this->generateUrl('popem_admin_new_post'));
                        }
                    }
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'file harus berextensi .jpg, .jpeg, .png, .svg'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_list_post'));
                }
            }

            $data->setCategoryId($em->getRepository(Category::class)->find($request->get('category')));

            $data->setTag(serialize($request->get('tag')));

            $data->setMetaKeyword($request->get('meta-keyword'));
            $data->setMetaDescription($request->get('meta-description'));
            $data->setPublishedAt(new \DateTime());
            $data->setUpdatedAt(new \DateTime());

            array_push($newData, $data);

            foreach ($newData as $key => $item) {
                foreach ($post as $keyPost => $itemPost) {
                    if ($itemPost->getSlug() === $item->getSlug()) {
                        $item->setSlug($slugify->slugify($request->get('title')).'-'.$itemPost->getId());
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
            } catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'data tidak berhasil disimpan'
                );
            }

            return $this->redirect($this->generateUrl('popem_admin_list_post'));
        }

        return $this->render(
            'AppBundle:backend:post/new-post.html.twig',
            [
            'tag' => $tag,
            'category' => $category,
            ]
        );
    }

    public function listPostAction(Request $request)
    {
        $session = $request->getSession();

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Post::class)->findAll();

        return $this->render(
            'AppBundle:backend:post/list-post.html.twig',
            [
            'data' => $data,
            ]
        );
    }

    public function updatePostAction(Request $request, $id)
    {
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();

        $slugify = new Slugify();

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $data = $em->getRepository(Post::class)->find($id);

        $tag = $em->getRepository(Tag::class)->findAll();

        $category = $em->getRepository(Category::class)->findAll();

        $post = $em->getRepository(Post::class)->findAll();

        $newData = [];

        if ('POST' == $request->getMethod()) {
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($request->get('title')));
            $data->setBody($request->get('body'));

            if (!(empty($request->files->get('image')))) {
                $file = $request->files->get('image');
                $name1 = md5(uniqid()).'.'.$file->guessExtension();
                $exAllowed = array('jpg', 'png', 'jpeg', 'svg');
                $ex = pathinfo($file, PATHINFO_EXTENSION);

                if (in_array($ex, $exAllowed)) {
                    if ($file instanceof UploadedFile) {
                        if (!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('image')->getPathName()
                            )->saveTo($this->getParameter('post_directory')['resource'].'/'.$name1, 30, true);
                            $data->setImage($name1);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'message_error',
                                'file tidak boleh lebih dari 1 mb'
                            );

                            return $this->redirect($this->generateUrl('popem_admin_update_post', ['id' => $data->getId]));
                        }
                    }
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'extension file harus .jpg, .png, .jpeg, .svg'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_update_post', ['id' => $data->getId()]));
                }
            }

            $arrNewTag = [];

            foreach ($request->get('tag') as $item) {
                array_push($arrNewTag, $item);
            }

            $data->setTag(serialize($arrNewTag));

            $arrNewCategory = [];

            foreach ($request->get('category') as $item) {
                array_push($arrNewCategory, $item);
            }

            $data->setCategory(serialize($arrNewCategory));

            $data->setMetaKeyword($request->get('meta-keyword'));
            $data->setMetaDescription($request->get('meta-description'));
            $data->setStatus($request->get('status'));
            $data->setUpdatedAt(new \DateTime());

            array_push($newData, $data);

            foreach ($newData as $key => $item) {
                foreach ($post as $keyPost => $itemPost) {
                    if ($itemPost->getSlug() === $item->getSlug()) {
                        $item->setSlug($slugify->slugify($request->get('title')).'-'.$itemPost->getId());
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
            } catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add(
                    'message_error',
                    'data tidak boleh sama'
                );
            }

            return $this->redirect($this->generateUrl('popem_admin_list_post'));
        }

        return $this->render(
            'AppBundle:backend:post/update-post.html.twig',
            [
            'data' => $data,
            'tag' => $tag,
            'category' => $category,
            ]
        );
    }
}
