<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 14:04
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
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

}