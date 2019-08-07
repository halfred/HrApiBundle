<?php

namespace Hr\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HrApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
          $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');


//         $this->addAnnotatedClassesToCompile([
//        // you can define the fully qualified class names...
//        'App\\Controller\\DefaultController',
//        // ... but glob patterns are also supported:
//        '**Bundle\\Controller\\',
//
//        // ...
//    ]);
    }
}