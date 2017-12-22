<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 14:14.
 */

namespace AppBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationController extends Controller
{
    public function configurationAction(Request $request)
    {
        $session = $request->getSession();

        if (!($session->has('token'))) {
            return $this->redirect($this->generateUrl('popem_admin_login'));
        }
        $files = file_get_contents(dirname(__DIR__).'/Resources/config/routing/admin/menu.yml');

        if ('POST' == $request->getMethod()) {
            $data = $request->get('code');

            $open = fopen(file_put_contents(dirname(__DIR__).'/Resources/config/routing/admin/menu.yml', $data), 'w');

            fwrite($open, $data);

            $this->get('session')->getFlashBag()->add(
                'message_success',
                'menu berhasil di tambahkan'
            );

            return $this->redirect($this->generateUrl('popem_admin_message'));
        }

        return $this->render('AppBundle:backend:configuration/configuration.html.twig', ['files' => $files]);
    }
}
