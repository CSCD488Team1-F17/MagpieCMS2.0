<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 1:57 PM
 */

//namespace Magpiehunt\Services;

use Magpiehunt\Controllers\Webapp\HomeController;
use Magpiehunt\Controllers\Webapp\CMSController;
use Magpiehunt\Controllers\Api\DatabaseController;
use Magpiehunt\Controllers\Api\FirebaseController;


$container = $app->getContainer();


$container['HomeController'] = function($container){
    return new HomeController($container);
};
$container['CMSController'] = function($container){
    return new CMSController($container);
};
$container['DatabaseController'] = function($container){
    return new DatabaseController($container);
};
$container['FirebaseController'] = function($container){
    return new FirebaseController($container);
};
