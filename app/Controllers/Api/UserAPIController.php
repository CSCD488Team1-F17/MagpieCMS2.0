<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 2:29 PM
 */

namespace Magpiehunt\Controllers\Api;


class UserAPIController
{
    function app(Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client(); //Composer is used to set up Google_Client() configuration stuff
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();

            if($output['UID'] != $userid){
                $stmt = $conn->prepare("INSERT INTO AppUserData (UID) VALUES (?)");
                $stmt->execute([$userid]);
            } else{
                echo "found em!";
            }
        } else {
            return $response->withStatus(300);
        }

        $conn = null;
        return $response->withStatus(200);
    }
    function progressPull(Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userID = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userID]);
            $output = $stmt->fetch();

            if($output['UID'] != $userID){
                echo "you dont exist!";
            } else{
                $uid = $output['UserID'];
                $stmt = $conn->prepare("SELECT CollectionID FROM UserCollectionInprogress WHERE UserID = ?;");
                $stmt->execute([$uid]);

                $collections = array();
                while($row = $stmt->fetch()) {
                    $cid = $row['CollectionID'];
                    array_push($collections, $cid);
                }

                $landmarks = array();
                $i = 0;
                foreach($collections as $cid){
                    $landmarks[$i] = array();

                    $stmt = $conn->prepare("SELECT LandmarkID FROM UserLandmarksSeen WHERE UserID = ? AND CollectionID = ?;");
                    $stmt->execute([$uid, $cid]);
                    while($row = $stmt->fetch()) {
                        $lid = $row['LandmarkID'];
                        array_push($landmarks[$i], $lid);
                    }
                    $i += 1;
                }

                $objArray = array();
                $i = 0;
                foreach($collections as $cid){
                    $objArray[$i] = (object) ["cid" => $cid, "landmarks" => $landmarks[$i]];
                }

                return $response->withJson($objArray);
            }
        } else {
            error_log(print_r("Sending back a 300", TRUE));
            return $response->withStatus(300);
        }

        $conn = null;
        return $response->withStatus(200);
    }
    function progressPush(Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);
        $collections = $data['collections'];

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userID = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userID]);
            $output = $stmt->fetch();

            if($output['UID'] != $userID){
                echo "you dont exist!";
            } else{
                $uid = $output['UserID'];
                $stmt = $conn->prepare("SELECT CollectionID FROM UserCollectionInprogress WHERE UserID = ?;");
                $stmt->execute([$uid]);

                $array = array();
                while($row = $stmt->fetch()) {
                    $cid = $row['CollectionID'];
                    array_push($array, $cid);
                }

                foreach($collections as $c){
                    if(in_array($c['cid'], $array)){
                        //do an update
                        $landmarks = $c['landmarks'];
                        $i = 1;
                        foreach($landmarks as $seen){
                            if($seen){
                                $stmt = $conn->prepare("INSERT IGNORE INTO UserLandmarksSeen (UserID, LandmarkID, CollectionID) VALUES (?, ?, ?)");
                                $stmt->execute([$uid, $i, $c['cid']]);
                            }
                            $i += 1;
                        }
                    } else {
                        //do an insert
                        $stmt = $conn->prepare("INSERT INTO UserCollectionInprogress (UserID, CollectionID, NumberOfLandmarksSeen) VALUES (?, ?, ?)");
                        $stmt->execute([$uid, $c['cid'], 0]);

                        $landmarks = $c['landmarks'];
                        $i = 1;
                        foreach($landmarks as $seen){
                            if($seen){
                                $stmt = $conn->prepare("INSERT INTO UserLandmarksSeen (UserID, LandmarkID, CollectionID) VALUES (?, ?, ?)");
                                $stmt->execute([$uid, $i, $c['cid']]);
                            }
                            $i += 1;
                        }
                    }
                }
            }
        } else {
            error_log(print_r("Sending back a 300", TRUE));
            return $response->withStatus(300);
        }

        $conn = null;
        return $response->withStatus(200);
    }
    function web(Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();

            if($output['UID'] == $userid){
                $conn = null;
                return $response->withJson($output);
            } else{
                return $response->withStatus(300);
            }
        } else {
            return $response->withStatus(300);
        }
    }
    function webCollections(Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $userID = $data['UserID'];
        error_log(print_r($userID, TRUE));
        $conn = connect_db();
        if($userID == 0){
            $stmt = $conn->prepare("SELECT CollectionID FROM UserMadeCollectionList;");
            $stmt->execute([$userID]);
        } else {
            $stmt = $conn->prepare("SELECT CollectionID FROM UserMadeCollectionList WHERE UserID = ?;");
            $stmt->execute([$userID]);
        }

        $result = array();
        while($row = $stmt->fetch()) {
            $cid = $row['CollectionID'];
            $stmt2 = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
            $stmt2->execute([$cid]);
            $output = $stmt2->fetch();
            array_push($result, $output);
        }

        return $response->withJson($result);
    }
}