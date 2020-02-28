<?php

namespace Laminas\ApiTools\OAuth2\Doctrine;

use Interop\Container\ContainerInterface;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Config\Config;
use Laminas\Mvc\MvcEvent;
use Laminas\Loader\StandardAutoloader;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ApiTools\OAuth2\Doctrine\Adapter\DoctrineAdapterFactory;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface
{
    /**
     * Retrieve autoloader configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [StandardAutoloader::class => ['namespaces' => [
            __NAMESPACE__ => __DIR__,
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
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getDependencyConfig(),
            'api-tools-doctrine-query-create-filter' => $provider->getQueryCreateFilterConfig(),
            'api-tools-doctrine-query-provider' => $provider->getQueryProviderConfig(),
        ];
    }

    public function onBootstrap(MvcEvent $e)
    {
        /** @var ServiceLocatorInterface $serviceManager */
        $serviceManager = $e->getParam('application')->getServiceManager();
        $serviceManager->get('oauth2.doctrineadapter.default')->bootstrap($e);
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'oauth2.doctrineadapter.default' => function ($serviceManager) {
                    /** @var ServiceLocatorInterface | ContainerInterface $serviceManager */
                    $globalConfig = $serviceManager->get('Config');
                    $config = new Config($globalConfig['api-tools-oauth2-doctrine']['default']);
                    /** @var DoctrineAdapterFactory $factory */
                    $factory = $serviceManager->get(DoctrineAdapterFactory::class);
                    $factory->setConfig($config);

                    return $factory->createService($serviceManager);
                }
            ],
        ];
    }
}
