<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 14:10
 */

namespace AppBundle\Controller\Dashboard;


use AppBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublishPageActionController extends Controller
{

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

}