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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$config = require __DIR__ . '/../../../bootstrap/config.php';


class DatabaseController extends Controller
{

    public function __construct($container)
    {
        parent::__construct($container);
    }

    function connect_db(){
        $config = require __DIR__ . '/../../../bootstrap/config.php';
        $server = $config->server;
        //echo $server;
        $user = $config->username;
        //echo $user;
        $pass = $config->password;
        //echo $pass;
        $database = $config->database;
        //echo $database;
        $connection = new PDO("mysql:host=" . $server . ";dbname=" . $database, $user, $pass);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $connection;
    }
    //gets all collections
    function collectionPull(ServerRequestInterface $request){
        $ara = array();
        $conn = $this->connect_db();
        $query = "SELECT * FROM Collections WHERE Available = 1";
        $output = $conn->prepare($query);
        $output->execute();
        /*while($row = $output->fetchObject()) {
            $ara[] = $row;
        }*/
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    //gets all landmarks with given cid
    function landmarkPull( ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $cid = (int)$request->getAttribute('cid');
        $ara = array();
        $conn = $this->connect_db();
        $output = $conn->prepare("SELECT * FROM Landmarks WHERE cid = :cid ORDER BY 'Order' asc");
        $output->bindParam("cid", $args['cid']);
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function landmarkImg($request)
    {
        $lid = (int)$request->getParam('lid');

    }
}