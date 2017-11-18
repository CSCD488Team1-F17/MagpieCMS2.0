<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 1:57 PM
 */

use Magpiehunt\Controllers\Webapp\HomeController;
use Magpiehunt\Controllers\Webapp\LoginController;
use Magpiehunt\Controllers\Webapp\ContactController;
use Magpiehunt\Controllers\Webapp\CMSController;

$container = $app->getContainer();


$container['HomeController'] = function($container){
    return new HomeController($container);
};
$container['LoginController'] = function($container){
    return new LoginController($container);
};
$container['ContactController'] = function($container){
    return new ContactController($container);
};
$container['CMSController'] = function($container){
    return new CMSController($container);
};