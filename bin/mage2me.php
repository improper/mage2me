<?php

require __DIR__ . '/../vendor/autoload.php';

use App\DownloadCommand;
use Symfony\Component\Console\Application;

$application = new Application('mage2me', 'v1.0.0-test2');
$application->add(new DownloadCommand());
$application->run();

