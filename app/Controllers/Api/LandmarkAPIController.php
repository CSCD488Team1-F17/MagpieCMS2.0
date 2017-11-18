<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 2:27 PM
 */

namespace Magpiehunt\Controllers\Api;


class LandmarkAPIController
{
    function lid(Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $lid = (int)$request->getAttribute('lid');
        $stmt = $conn->prepare("SELECT DISTINCT * FROM Landmarks INNER JOIN LandmarkDescription ON LandmarkDescription.DesID = Landmarks.DescID AND Landmarks.LID = ? AND LandmarkDescription.LID = ?;");
        $stmt->execute([$lid, $lid]);
        while($row = $stmt->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    }
    function all(Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT * FROM Landmarks LEFT JOIN LandmarkDescription ON LandmarkDescription.DesID = Landmarks.DescID INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
        $stmt->execute([$cid]);
        while($row = $stmt->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    }
}