<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 1/5/2018
 * Time: 1:36 PM
 */

namespace Magpiehunt\Controllers\Api;


class LandmarkController
{
    //gets all landmarks with given cid
    function getLandmarks( ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $dbController = new DatabaseController();
        $conn = $dbController->db->connect_db();
        $output = $conn->prepare("SELECT * FROM Landmarks WHERE cid = $cid ORDER BY 'Order' asc");
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
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

        $dbController = new DatabaseController();
        $conn = $dbController->db->connect_db();

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