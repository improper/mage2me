<?php

require __DIR__ . '/../vendor/autoload.php';

use App\DownloadCommand;
use Symfony\Component\Console\Application;

$application = new Application('mage2me', 'v1.1.0');
$application->add(new DownloadCommand());
$application->setDefaultCommand('download');
$application->run();

