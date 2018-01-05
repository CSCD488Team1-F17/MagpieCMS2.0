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
$app->get('/api/collections', CollectionController::class . ':getCollections');
$app->get('/api/collection/{cid}', CollectionController::class . ':getCollection');
$app->get('/api/landmark/{cid}', LandmarkController::class . ':getLandmarks');
$app->get('/api/collectionimages/{cid}', DatabaseController::class . ':getCollectionsImage');
$app->get('/api/landmarkimages/{lid}', DatabaseController::class . ':getLandmarkImage');
$app->get('/api/award{cid}', DatabaseController::class . ':getAward');

//updating data in the database
$app->put('/api/collection/disable/{cid}', CollectionController::class . ':collectionDisble');
$app->put('/api/collection/enable/{cid}', CollectionController::class . ':collectionEnable');

/*
 * The functions will require a request to be built by ajax see landmark.js or landmarks.twig
 */
$app->post('/api/collection', CollectionController::class . ':addCollection');
$app->post('/api/landmark', LandmarkController::class . ':addLandmark');
$app->post('/api/landmarkimages/{lid}', DatabaseController::class . ':addLandmarkImage');
$app->post('/api/collectionimages/{cid}', DatabaseController::class . ':addCollectionsImage');
$app->post('/api/collectionowner/{cid}', DatabaseController::class . ':addCollectionOwner');


//connect to database used from android
$app->get('/api/connect_db', DBConnect::class . ':connect_db');
