<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 2:13 PM
 */

namespace Magpiehunt\Controllers\Api;

use PDO;

use Magpiehunt\Controllers\Controller as Controller;

$config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

class DatabaseController extends Controller
{


    function connect_db(){
        $config = require dirname(__FILE__, 3) . '/bootstrap/config.php';
        $connection = new PDO("mysql:host=$config->server;dbname=$config->database" ,$config->username, $config->password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }
    function collection(Request $request){
        $cid =(int)$request->getParam("cid");
        $name = $request->getParam("name");
        $abv = $request->getParam("abv");
        $description = $request->getParam("summary");
        $numberOfLandmarks = (int)$request->getParam("numBadge");
        $isOrdered = (int)$request->getParam("ordered");
        $idToken = $request->getParam("idToken");

        error_log(print_r($idToken, TRUE));

        $conn = connect_db();
        $stmt = $conn->prepare("INSERT INTO Collections (Name, Abbreviation, Description, NumberOfLandMarks, IsOrder) VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([$name, $abv, $description, $numberOfLandmarks, $isOrdered]);

        $picID = (int)superbadgeUpload($request);
        $stmt = $conn->prepare("UPDATE Collections SET PicID = ? WHERE CID = ?");
        $stmt->execute([$picID, $cid]);

        $conn = null;

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($idToken);
        if ($payload) {
            $userid = $payload['sub'];
            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();
            error_log(print_r($output['UserID'], TRUE));
            if($output['UID'] == $userid){
                $stmt = $conn->prepare("INSERT INTO UserMadeCollectionList (UserID, CollectionID) VALUES (?, ?);");
                $stmt->execute([$output['UserID'], $cid]);
            }

            $conn = null;
        } else {
            return $response->withStatus(300);
        }

        echo("success");
    }

}