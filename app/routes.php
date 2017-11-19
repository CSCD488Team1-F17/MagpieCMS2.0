<?php
/*
routes.php

This file loads all of the other route files for our application.

We split the routes into two parts: one for our web app and one for the backend api.
This helped keep us stay organized and keep the code as clean as possible.
*/
    //require 'webapp.php';
    //require 'api.php';

    use Magpiehunt\Controllers\Webapp\HomeController;
    use Magpiehunt\Controllers\Webapp\LoginController;
    use Magpiehunt\Controllers\Webapp\ContactController;
    use Magpiehunt\Controllers\Webapp\CMSController;

    require __DIR__ . '/services/controllers.php';

    $app->get('/', HomeController::class . ':index');
    $app->get('/login', CMSController::class . ':login');
    $app->get('/welcome', CMSController::class . ':welcome');
    $app->get('/contact', CMSController::class . ':contact');
    $app->get('/collections', CMSController::class . ':collections');
    $app->get('/create', CMSController::class . ':create');
    $app->get('/legal', CMSController::class . ':legal');
    $app->get('/account', CMSController::class . ':account');


    //not implemented yet
    //$app->get('/logout', CMSController::class . ':logout');


?>