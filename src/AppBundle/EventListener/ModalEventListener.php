<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 20/12/2017
 * Time: 15:27
 */

namespace AppBundle\EventListener;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Yaml\Parser;

class ModalEventListener implements EventSubscriberInterface
{

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => array(
            array('doTwig',0)
        ));
    }

    public function doTwig(FilterControllerEvent $event)
    {
        $yaml = new Parser();

        $arrNewFiles = [];

        $file = [];

        $subMenu = [];

        $test = [];

        $files = $yaml->parse(file_get_contents(dirname(__DIR__) . '/Resources/config/routing/admin/menu.yml'));

        foreach ($files as $item) {
            array_push($arrNewFiles,$item);
        }

        for($i=0;$i<count($arrNewFiles); $i++) {
            if(isset($arrNewFiles[$i]['submenu'])) {
                $file[] = [
                    'path' => $arrNewFiles[$i]['path'],
                    'label' => $arrNewFiles[$i]['label'],
                    'submenu' => $arrNewFiles[$i]['submenu']
                ];
                foreach ($arrNewFiles[$i]['submenu'] as $item) {
                    array_push($test,$item);
                }
            }else{
                $file[] = [
                    'path' => $arrNewFiles[$i]['path'],
                    'label' => $arrNewFiles[$i]['label']
                ];
            }
        }

        for($j = 0; $j < count($test); $j++) {
            $subMenu[] = [
                'path' => $test[$j]['path'],
                'label' => $test[$j]['label']
            ];
        }

        $this->container->get('twig')->addGlobal('submenu',$subMenu);

        $this->container->get('twig')->addGlobal('files', $files);
    }
}