<?php

use Symfony\Component\Console\Application;
use Acme\UI\Cli\CalculateCommissionCommand;
use Symfony\Component\Console\Input\InputArgument;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function () {
    $command = new CalculateCommissionCommand('calculate commission');
    $command->addArgument(
        'file',
        InputArgument::REQUIRED,
        'What file do you want to calculate?',
    );
    $command->addArgument(
        'exchange-rate-uri',
        InputArgument::REQUIRED,
        'Please, provide URI for currency exchange rates',
    );

    $app = new Application();
    $app->add($command);
    $app->setDefaultCommand('calculate commission', true);

    return $app;
};
