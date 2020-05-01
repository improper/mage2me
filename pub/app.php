<?php

require __DIR__ . '/../vendor/autoload.php';

use App\DownloadCommand;
use Symfony\Component\Console\Application;

$application = new Application('Mage2Me', '1.0.0');
$application->add(new DownloadCommand());
$application->run();

