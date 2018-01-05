<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 1/5/2018
 * Time: 1:33 PM
 */

namespace Magpiehunt\Controllers\Api;


class CollectionController
{
    //gets all collections
    function getCollections(ServerRequestInterface $request){
        $dbController = new DatabaseController();
        $conn = $dbController->db->connect_db();
        $query = "SELECT * FROM Collections WHERE Available = 1";
        $output = $conn->prepare($query);
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function getCollection(ServerRequestInterface $request){
        $cid = (int)$request->getAttribute('cid');
        $dbController = new DatabaseController();
        $conn = $dbController->db->connect_db();
        $query = "SELECT * FROM Collections WHERE cid = $cid";
        $output = $conn->prepare($query);
        $output->execute();
        $res = $output->fetch();
        echo json_encode($res);
        $conn = null;
    }
    function collectionEnable(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $dbController = new DatabaseController();
        $conn = $dbController->db->connect_db();
        $output = $conn->prepare("UPDATE Collections SET Available = 1 WHERE CID = $cid");
        $output->execute();
        $conn = null;
    }
    function collectionDisble(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $conn = $this->db->connect_db();
        error_log(print_r($cid, TRUE));
        $output = $conn->prepare("UPDATE Collections SET Available = 0 WHERE CID = $cid");
        $output->execute();
        $conn = null;
    }
    //TODO: Error checking and default values
    function addCollection(ServerRequestInterface $request, ResponseInterface $response)
    {
        // global $app;
        //$req = $app->request();
        //$body = json_decode($req->getBody());

        $name = $request->getParam('name');
        //if(empty($name)) throw new \UnexpectedValueException("Name must not be null");
        $city = $request->getParam('city');
        //if(empty($city)) $city = null;
        $state = $request->getParam('state');
        //if(empty($state)) $state = null;
        $zip = $request->getParam('zipcode');
        //if(empty($zip)) $zip = null;
        $desc = $request->getParam('description');
        //if(empty($desc)) throw new \UnexpectedValueException("Description must not be null");
        $ord = $request->getParam('ordered');
        //if(empty($ord)) $ord = null;
        $abv = $request->getParam('abbreviation');
        //if(empty($abv)) throw new \UnexpectedValueException("abbreviation must not be null");
        $avail = $request->getParam('available');
        if($avail == ''){ $avail = 1; }
        if($avail != '0' && $avail != '1') throw new \UnexpectedValueException("Value for avail must be 0 or 1");
        $rating = $request->getParam('rating');
        //if(empty($rating)) $rating = null;
        $sponsor = $request->getParam('sponsor');
        //if(empty($sponsor)) $sponsor = null;

        $dbController = new DatabaseController();
        $conn = $dbController->db->connect_db();

        $query = "INSERT INTO Collections (Available, Name
               ,City
               ,State
               ,ZipCode
               ,Rating
               ,Description
               ,Ordered
               ,Abbreviation
               ,Sponsor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $output = $conn->prepare($query);
        $output->execute([$avail, $name, $city, $state, $zip, $rating, $desc, $ord, $abv, $sponsor]);
        $conn = null;
    }
    function getCollectionRowCount(ServerRequestInterface $request, ResponseInterface $response)
    {

    }
}