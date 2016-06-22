<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

$container->register('sql_container', \Illuminate\Container\Container::class)
    ->setPublic(false);

$container->register('sql_dispatcher', \Illuminate\Events\Dispatcher::class)
    ->addArgument(new Reference('sql_container'))
    ->setPublic(false);

$container->register('sql', \Illuminate\Database\Capsule\Manager::class)
    ->setFactory('Scanner\Service\SqlManagerFactory::getSqlManager')
    ->addArgument(new Reference('config'))
    ->addMethodCall('setEventDispatcher', [new Reference('sql_dispatcher')])
    ->addMethodCall('setAsGlobal')
    ->addMethodCall('bootEloquent');

$container->register('file', \Symfony\Component\Filesystem\Filesystem::class);

$container->register('http', \GuzzleHttp\Client::class);

$container->register('iterator', \Scanner\Service\Iterator::class)
    ->addArgument(new Reference('iterator_filter'));

$container->register('iterator_filter', \Scanner\Service\IteratorFilter::class)
    ->addArgument(new Reference('directory_iterator'))
    ->addMethodCall(
        'setFilters',
        [
            [
                '.idea',
                '.vendor',
                'app',
                'node_modules',
                'coverage',
                'documents',
                'vendor',
                'src'
            ]
        ]
    );

$container->register('directory_iterator', \Scanner\Service\DirectoryIterator::class)
    ->setFactory('Scanner\Service\DirectoryIteratorFactory::getDirectoryIterator')
    ->addArgument(new Reference('config'));

$container->register('config', \Scanner\Service\Config::class);

$container->register('console', Symfony\Component\Console\Application::class)
    ->addMethodCall('add', [new Reference('rename_console_command')]);

$container->register('rename_console_command', \Scanner\Command\RenameTransValues::class)
    ->addArgument(new Reference('service_container'));

return $container;