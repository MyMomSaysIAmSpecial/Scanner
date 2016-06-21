<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

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