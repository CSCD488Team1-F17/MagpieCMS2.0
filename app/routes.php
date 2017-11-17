<?php
/*
routes.php

This file loads all of the other route files for our application.

We split the routes into two parts: one for our web app and one for the backend api.
This helped keep us stay organized and keep the code as clean as possible.
*/
    //require 'webapp.php';
    //require 'api.php';

    $app->get('/', 'HomeController:index');
    /*$app->group('/', function(){
        $this->group('/auth', function(){
            $this->map(['GET', 'POST'], '/login', 'Magpiehunt\Controllers\AuthController:login');
        });
        $this->group('/events', function (){

        });
    });
    */

?>