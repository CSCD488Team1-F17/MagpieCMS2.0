<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 1/5/2018
 * Time: 1:19 PM
 */

namespace Magpiehunt\Controllers\Api;


use Magpiehunt\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DB_connect extends Controller
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
}