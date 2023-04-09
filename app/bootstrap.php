<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
@mkdir(__DIR__ . '/../temp/log');
$configurator->enableDebugger(__DIR__ . '/../temp/log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->setDebugMode(true);
$configurator->enableTracy(__DIR__ . '/../log');
$container = $configurator->createContainer();

return $container;
