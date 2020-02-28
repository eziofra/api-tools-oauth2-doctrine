<?php

namespace ZFTest\OAuth2\Doctrine;

use Laminas\EventManager\EventManager;
use Laminas\ModuleManager\ModuleManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ApiTools\OAuth2\Doctrine\EventListener\DynamicMappingSubscriber;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Laminas\Mvc\MvcEvent;

class Module
{
    /**
     * Retrieve autoloader configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return ['Laminas\Loader\StandardAutoloader' => ['namespaces' => [
            __NAMESPACE__ => __DIR__ . '/src/',
        ]
        ]
        ];
    }

    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $doctrineAdapter = $serviceManager = $e->getParam('application')
            ->getServiceManager()
            ->get('oauth2.doctrineadapter.default')
            ;

        $listenerAggregate = new \ZFTest\OAuth2\Doctrine\Listener\TestEvents($doctrineAdapter);
        /** @var ServiceManager $serviceManager */
        $serviceManager = $e->getParam('application')->getServiceManager();
        /** @var EventManager $eventManager */
        $eventManager = $serviceManager->get('oauth2.doctrineadapter.default')->getEventManager();
        $listenerAggregate->attach($eventManager);
    }
}
