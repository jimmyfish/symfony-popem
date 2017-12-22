<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 14:08
 */

namespace AppBundle\Controller\Dashboard;


use AppBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{

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

}