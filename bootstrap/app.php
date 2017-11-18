<?php
/*
app.php

This file does most of application setup and configuration.
After setup, it loads our URL routes and starts up the app.
*/
//Load Slim App class
use Magpiehunt\Controllers\Webapp\HomeController;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

require __DIR__ . '/../vendor/autoload.php';

//Set up our settings variables
$slimSettings = require 'settings.php';
$config = require 'config.php';

//Define our app
$app = new \Slim\App($slimSettings);

/*
Here we add additional dependencies through Slim Frameworks container variable.

Our only additional dependency that we load is the Twig template engine for our web pages.

Please note that we could have used this for our database connection, which might have made things
simpler and cleaned up code. It is suggested to do so in the future.

This comes straight from Slim Framework's website:
https://www.slimframework.com/docs/features/templates.html
*/
$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false
    ]);

    //$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension(
        $container['router'], $container->request->getUri()
        //$basePath
    ));

    return $view;
};

//initialize Firebase
/*$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/firebase_credentials.json');
$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->create();
*/
//Loading point for our routes
require __DIR__ . '/../app/routes.php';

//Run the app
$app->run();

?>