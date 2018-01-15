<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 1/6/2018
 * Time: 2:17 PM
 */

namespace Magpiehunt\Controllers\Api;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LandmarkController extends DatabaseController
{
    //gets all landmarks with given cid
    function getLandmarks( ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $dbconn = new DBConnection($this->container);
        $conn = $dbconn->connect_db();
        $output = $conn->prepare("SELECT * FROM Landmarks WHERE cid = $cid ORDER BY 'Order' asc");
        $output->execute();
        $res = $output->fetchAll();
        $arr = array("result_type" => 'Landmark', "Landmarks"=>$res);

        echo json_encode($arr);
        $conn = null;
    }
    //TODO: Error checking and default values
    function addLandmark(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = $request->getParam('cid');
        $name = $request->getParam('name');
        $lat = (double)$request->getParam('lat');
        $long = (double)$request->getParam('long');
        $desc = $request->getParam('desc');
        $qr = $request->getParam('qr');
        $pic = (int)$request->getParam('pic');
        $badge = (int)$request->getParam('badge');
        $order = (int)$request->getParam('order');

        $dbconn = new DBConnection($this->container);
        $conn = $dbconn->connect_db();

        $query = "INSERT INTO Landmarks (CID, LandmarkName
               ,Latitude
               ,Longitude
               ,LandmarkDescription
               ,QRCode
               ,PicID
               ,BadgeID
               ,OrderNum) VALUES ('$cid', '$name', '$lat', '$long', '$desc', '$qr', '$pic', '$badge', '$order')";
        $output = $conn->prepare($query);
        $output->execute();
        $conn = null;
    }
}