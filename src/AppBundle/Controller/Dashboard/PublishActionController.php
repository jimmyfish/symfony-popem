<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 13:58.
 */

namespace AppBundle\Controller\Dashboard;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublishActionController extends Controller
{
    public function publishPostAction(Request $request, $id)
    {
        $session = $request->getSession();

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Post::class)->find($id);

        if ($data instanceof Post) {
            $data->setStatus(2);
        }

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('popem_admin_list_post'));
    }

    public function unpublishPostAction(Request $request, $id)
    {
        $session = $request->getSession();

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(Post::class)->find($id);

        $data->setStatus(1);

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('popem_admin_list_post'));
    }
}
