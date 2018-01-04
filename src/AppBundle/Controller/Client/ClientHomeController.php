<?php
/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 04/01/18
 * Time: 18:01.
 */

namespace AppBundle\Controller\Client;

use AppBundle\Controller\Api\ApiController;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientHomeController extends Controller
{
    public function indexAction()
    {
        $api = new ApiController();

        $client = new Client();

        $crawler = $client->request('GET', 'https://www.proinsta.com/');

        $kursTitle = $crawler->filterXPath('//table[@id="collapseKurs"]')
            ->filter('tbody')->each(function ($tbody, $i) {
                return $tbody->filter('tr')->each(function ($tr, $i) {
                    return $tr->filter('th')->each(function ($th, $i) {
                        return trim($th->text());
                    });
                });
            });

        $kursData = $crawler->filterXPath('//table[@id="collapseKurs"]')
            ->filter('tbody')->each(function ($tbody, $i) {
                return $tbody->filter('tr')->each(function ($tr, $i) {
                    return $tr->filter('td')->each(function ($td, $i) {
                        return trim($td->text());
                    });
                });
            });

        $i = 0;

        foreach ($kursTitle[0] as $key => $value) {
            foreach ($kursData[0] as $kursKey => $kursValue) {
                $kurs[$value[0]] = $kursData[0][$i];
            }
            ++$i;
        }

        $targetUrl = $this->container->getParameter('api_target');

        $information['broker'] = $api->doRequest('GET', $targetUrl.'/broker-list');
        $information['bank'] = $api->doRequest('GET', $targetUrl.'/bank-list');

        return $this->render('AppBundle:Client:home/index.html.twig', [
            'information' => $information,
            'kurs' => $kurs,
        ]);
    }
}
