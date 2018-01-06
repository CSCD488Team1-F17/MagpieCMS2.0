<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/24/2017
 * Time: 1:05 PM
 */

use Magpiehunt\Controllers\Api\DatabaseController;


$container = $app->getContainer();

//getting database objects
$app->get('/api/collections', DatabaseController::class . ':getCollections');
$app->get('/api/collection/{cid}', DatabaseController::class . ':getCollection');
$app->get('/api/landmark/{cid}', DatabaseController::class . ':getLandmarks');
$app->get('/api/collectionimages/{cid}', DatabaseController::class . ':getCollectionsImage');
$app->get('/api/landmarkimages/{lid}', DatabaseController::class . ':getLandmarkImage');
$app->get('/api/award{cid}', DatabaseController::class . ':getAward');

//updating data in the database
$app->put('/api/collection/disable/{cid}', DatabaseController::class . ':collectionDisble');
$app->put('/api/collection/enable/{cid}', DatabaseController::class . ':collectionEnable');
$app->put('/api/collection/approve/{cid}', DatabaseController::class . ':collectionApprove');

/*
 * The functions will require a request to be built by ajax see landmark.js or landmarks.twig
 */
$app->post('/api/collection', DatabaseController::class . ':addCollection');
$app->post('/api/landmark', DatabaseController::class . ':addLandmark');
$app->post('/api/landmarkimages/{lid}', DatabaseController::class . ':addLandmarkImage');
$app->post('/api/collectionimages/{cid}', DatabaseController::class . ':addCollectionsImage');
$app->post('/api/collectionowner/{cid}', DatabaseController::class . ':addCollectionOwner');


//connect to database used from android
$app->get('/api/connect_db', DatabaseController::class . ':connect_db');
