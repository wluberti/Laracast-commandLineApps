#! /usr/bin/env php

<?php

use Symfony\Component\Console\Application;

require 'vendor/autoload.php';


$app = new Application('Laracast Demo', '1.0');

$app->add(new Acme\SayHelloCommand());
$app->add(new Acme\NewCommand(new \GuzzleHttp\Client()));
$app->add(new Acme\RenderCommand());

$app->run();
