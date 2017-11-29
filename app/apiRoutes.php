<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/24/2017
 * Time: 1:05 PM
 */

use Magpiehunt\Controllers\Api\DatabaseController;


$container = $app->getContainer();

//$container['dbconnection'] = $app->get('/connect_db', DatabaseController::class . ':connect_db');
$app->get('/collectionPull', DatabaseController::class . ':collectionPull');
$app->get('/landmarkPull', DatabaseController::class . ':landmarkPull');
