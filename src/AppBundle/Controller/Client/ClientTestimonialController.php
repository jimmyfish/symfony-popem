<?php

namespace AppBundle\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\Api\ApiController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Testimonial;

class ClientTestimonialController extends Controller
{
    public function testimonialAction(Request $request)
    {
        $api = new ApiController();

        $manager = $this->getDoctrine()->getManager();

        $targetUrl = $this->container->getParameter('api_target');

        $response = $api->doRequest('GET', $targetUrl.'/user-info');

        // return var_dump($response['data']['data']['username']);

        if ('POST' == $request->getMethod()) {
            $testimonial = new Testimonial();
            $testimonial->setName($response['data']['data']['username']);
            $testimonial->setNameTestimonial($request->get('testimonial'));

            $manager->persist($testimonial);
            $manager->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }
}
