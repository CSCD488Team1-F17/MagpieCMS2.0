<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 1/6/2018
 * Time: 1:59 PM
 */

namespace Magpiehunt\Controllers\Api;


use Magpiehunt\Controllers\Controller;
use PDO;

class DBConnection extends Controller
{
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
}