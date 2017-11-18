<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 2:25 PM
 */

namespace Magpiehunt\Controllers\Api;


class CollectionAPIController
{

    function collection(Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $output = $conn->query("SELECT * FROM Collections WHERE STATUS = 1;");
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    }
    function cid(Request $request, Response $response){
        $conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
        $stmt->execute([$cid]);
        $output = $stmt->fetch();
        echo json_encode($output);
        $conn = null;
    }
    function disable(Request $request, Response $response){
        $conn = connect_db();
        $json = $request->getBody();
        $data = json_decode($json, true);

        $cid = $data['cid'];
        error_log(print_r($cid, TRUE));
        $stmt = $conn->prepare("UPDATE Collections SET Status = ? WHERE CID = ?");
        $stmt->execute([0, $cid]);
        $conn = null;
    }
    function enable(Request $request, Response $response){
        $conn = connect_db();
        $json = $request->getBody();
        $data = json_decode($json, true);

        $cid = $data['cid'];
        error_log(print_r($cid, TRUE));
        $stmt = $conn->prepare("UPDATE Collections SET Status = ? WHERE CID = ?");
        $stmt->execute([1, $cid]);
        $conn = null;
    }
    function approve(Request $request, Response $response){
        $conn = connect_db();
        $json = $request->getBody();
        $data = json_decode($json, true);

        $cid = $data['cid'];
        error_log(print_r($cid, TRUE));
        $stmt = $conn->prepare("UPDATE Collections SET Status = ? WHERE CID = ?");
        $stmt->execute([2, $cid]);
        $conn = null;
    }
}