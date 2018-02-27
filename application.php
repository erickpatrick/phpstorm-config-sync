#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use Nintendo\Translator\Command\MagentoTranslation;
use Symfony\Component\Console\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load('services.yaml');

$application = new Application();

try {
    $application->add(new MagentoTranslation($container));
    $application->run();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}