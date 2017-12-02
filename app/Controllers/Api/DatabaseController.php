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

$config = require __DIR__ . '/../../../bootstrap/config.php';


class DatabaseController extends Controller
{

    public function __construct($container)
    {
        parent::__construct($container);
    }

    function connect_db(){
        $config = require __DIR__ . '/../../../bootstrap/config.php';
        $connection = new PDO("mysql:host=$config->server;dbname=$config->database" ,$config->username, $config->password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }
    //gets all collections
    function collectionPull($request, $response){
        $ara = array();
        $conn = $this->connect_db();
        $output = $conn->query("SELECT * FROM Collections WHERE Available = 1;");
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    }
    //gets all landmarks with given cid
    function landmarkPull($request, $response)
    {

        $cid = (int)$request->getParam('cid');
        $ara = array();
        $conn = $this->connect_db();
        //cid given
        if($cid != null)
        {
            $output = $conn->query("SELECT * FROM Landmarks WHERE cid = $cid ORDER BY 'Order' asc ;");
        }
        //no cid specified
        else {
            $output = $conn->query("SELECT * FROM Landmarks ORDER BY 'Order' asc ;");
        }
        while ($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    }

}