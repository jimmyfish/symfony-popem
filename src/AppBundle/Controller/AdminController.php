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

    public function publishPageAction(Request $request,$id)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Page::class)->find($id);

        $data->setStatus(2);

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('popem_admin_list_page'));

    }

    public function unpublishPageAction(Request $request,$id)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Page::class)->find($id);

        $data->setStatus(1);

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('popem_admin_list_page'));
    }


    public function homeAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        return $this->render('AppBundle:backend:home/home.html.twig');
    }

    public function newCategoryAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST') {
            $data = new Category();
            $data->setNameCategory($request->get('category'));

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('popem_admin_list_category'));
        }

        return $this->render('AppBundle:backend:category/new-category.html.twig');
    }

    public function listCategoryAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Category::class)->findAll();

        return $this->render('AppBundle:backend:category/list-category.html.twig',[
            'data' => $data
        ]);
    }
    
    public function updateCategoryAction(Request $request,$id)
    {
        $session = $request->getSession();
        
        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }
        
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Category::class)->find($id);

        if($request->getMethod() == 'POST') {
            if($data instanceof Category) {
                $data->setNameCategory($request->get('name-category'));
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('popem_admin_list_category'));
        }

        return $this->render('AppBundle:backend:category/update-category.html.twig',[
            'data' => $data
        ]);
    }

    public function deleteCategoryAction(Request $request, $id)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Category::class)->find($id);

        $em->remove($data);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'message_success',
            'data berhasil dihapus'
        );

        return $this->redirect($this->generateUrl('popem_admin_list_category'));
    }


    public function newTagAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST') {
            $data = new Tag();
            $data->setNameTag($request->get('name-tag'));

            $em->persist($data);
            $em->flush();

           return $this->redirect($this->generateUrl('popem_admin_list_tag'));
        }

        return $this->render('AppBundle:backend:tag/new-tag.html.twig');
    }

    public function listTagAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Tag::class)->findAll();

        return $this->render('AppBundle:backend:tag/list-tag.html.twig',[
            'data' => $data
        ]);
    }

    public function updateTagAction(Request $request, $id)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Tag::class)->find($id);

        if($request->getMethod() == 'POST') {
            if($data instanceof Tag) {
                $data->setNameTag($request->get('name-tag'));
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('popem_admin_list_tag'));
        }

        return $this->render('AppBundle:backend:tag/update-tag.html.twig',[
            'data' => $data
        ]);
    }

    public function deleteTagAction(Request $request,$id)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Tag::class)->find($id);

        $em->remove($data);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'message_success',
            'data berhasil dihapus'
        );

        return $this->redirect($this->generateUrl('popem_admin_list_tag'));
    }


    public function newPostAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $tag = $em->getRepository(Tag::class)->findAll();

        $category = $em->getRepository(Category::class)->findAll();

        $post = $em->getRepository(Post::class)->findAll();

        $slugify = new Slugify();

        $newData = [];

        if($request->getMethod() == 'POST') {
            $data = new Post();
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($request->get('title')));
            $data->setBody($request->get('body'));
            $data->setStatus(0);

            if(!(is_dir($this->getParameter('post_directory')['resource']))) {
                @mkdir($this->getParameter('post_directory')['resource'],0777,true);
            }

            if(!(empty($request->files->get('image')))) {
                $file = $request->files->get('image');
                $nama1 = md5(uniqid()) . '.' . $file->guessExtension();
                $exAllowed = array('jpg','png','jpeg','svg');
                $ex = pathinfo($nama1,PATHINFO_EXTENSION);

                if(in_array($ex,$exAllowed)) {
                    if($file instanceof UploadedFile) {
                        if(!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('image')->getPathName()
                            )->saveTo($this->getParameter('post_directory')['resource'] . '/' . $nama1,30,true);
                            $data->setImage($nama1);
//                            $data->setImage($this->getParameter('post_directory')['resource'] . '/' . $nama1);
                        }else {
                            $this->get('session')->getFlashBag()->add(
                                'message_error',
                                'ukuran file tidak boleh lebih dari 1 mb'
                            );

                            return $this->redirect($this->generateUrl('popem_admin_new_post'));
                        }
                    }
                }else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'file harus berextensi .jpg, .jpeg, .png, .svg'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_list_post'));
                }
            }

            $arrNewCategory = [];

            foreach ($request->get('category') as $item) {
                array_push($arrNewCategory, $item);
            }

            $data->setCategory(serialize($arrNewCategory));

            $arrNewTag = [];

            foreach ($request->get('tag') as $item) {
                array_push($arrNewTag, $item);
            }

            $data->setTag(serialize($arrNewTag));
            
            $data->setMetaKeyword($request->get('meta-keyword'));
            $data->setMetaDescription($request->get('meta-description'));
            
            array_push($newData,$data);

            foreach ($newData as $key => $item) {
                foreach ($post as $keyPost => $itemPost) {
                    if($itemPost->getSlug() === $item->getSlug()) {
                        $item->setSlug($slugify->slugify($request->get('title')) . '-' . $itemPost->getId());
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
            
            return $this->redirect($this->generateUrl('popem_admin_list_post'));
        }
        
        return $this->render('AppBundle:backend:post/new-post.html.twig',[
            'tag' => $tag,
            'category' => $category
        ]);
    }

    public function listPostAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Post::class)->findAll();

        return $this->render('AppBundle:backend:post/list-post.html.twig',[
            'data' => $data
        ]);
    }

    public function updatePostAction(Request $request,$id)
    {
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();

        $slugify = new Slugify();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $data = $em->getRepository(Post::class)->find($id);

        $tag = $em->getRepository(Tag::class)->findAll();

        $category = $em->getRepository(Category::class)->findAll();

        $post = $em->getRepository(Post::class)->findAll();

        $newData = [];

        if($request->getMethod() == 'POST') {
            $data->setTitle($request->get('title'));
            $data->setSlug($slugify->slugify($request->get('title')));
            $data->setBody($request->get('body'));

            if(!(empty($request->files->get('image')))) {
                $file = $request->files->get('image');
                $name1 = md5(uniqid()) . '.' . $file->guessExtension();
                $exAllowed = array('jpg','png','jpeg','svg');
                $ex = pathinfo($file,PATHINFO_EXTENSION);

                if(in_array($ex,$exAllowed)) {
                    if($file instanceof UploadedFile) {
                        if(!($file->getClientSize() > (1024 * 1024 * 1))) {
                            ImageResize::createFromFile(
                                $request->files->get('image')->getPathName()
                            )->saveTo($this->getParameter('post_directory')['resource'] . '/' . $name1,30,true);
                            $data->setImage($name1);
//                            $data->setImage($this->getParameter('post_directory')['resource'] . '/' . $name1);
                        }else {
                            $this->get('session')->getFlashBag()->add(
                                'message_error',
                                'file tidak boleh lebih dari 1 mb'
                            );
                            return $this->redirect($this->generateUrl('popem_admin_update_post',['id'=>$data->getId]));
                        }
                    }
                }else {
                    $this->get('session')->getFlashBag()->add(
                        'message_error',
                        'extension file harus .jpg, .png, .jpeg, .svg'
                    );

                    return $this->redirect($this->generateUrl('popem_admin_update_post',['id'=>$data->getId()]));
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

            array_push($newData,$data);

            foreach ($newData as $key => $item) {
                foreach ($post as $keyPost => $itemPost) {
                    if($itemPost->getSlug() === $item->getSlug()) {
                        $item->setSlug($slugify->slugify($request->get('title')) . '-' . $itemPost->getId());
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
                    'data tidak boleh sama'
                );
            }

            return $this->redirect($this->generateUrl('popem_admin_list_post'));
        }
        return $this->render('AppBundle:backend:post/update-post.html.twig',[
            'data' => $data,
            'tag' => $tag,
            'category' => $category
        ]);
    }

    public function publishPostAction(Request $request,$id)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Post::class)->find($id);

        if($data instanceof Post) {
            $data->setStatus(2);
        }

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('popem_admin_list_post'));
    }

    public function unpublishPostAction(Request $request,$id)
    {
        $session = $request->getSession();
        
        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }
        
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Post::class)->find($id);

        $data->setStatus(1);

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('popem_admin_list_post'));
    }

    public function configurationAction(Request $request)
    {
        $session = $request->getSession();

        if(!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }
        $files = file_get_contents(dirname(__DIR__) . '/Resources/config/routing/admin/menu.yml');

        if($request->getMethod() == 'POST') {

            $data = $request->get('code');

            $open = fopen(file_put_contents(dirname(__DIR__) . '/Resources/config/routing/admin/menu.yml',$data),'w');

            fwrite($open, $data);

            $this->get('session')->getFlashBag()->add(
                'message_success',
                'menu berhasil di tambahkan'
            );

            return $this->redirect($this->generateUrl('popem_admin_message'));
        }

        return $this->render('AppBundle:backend:configuration/configuration.html.twig',['files'=>$files]);
    }

    public function messageAction()
    {
        return $this->render('AppBundle:backend:configuration/message.html.twig');
    }
}