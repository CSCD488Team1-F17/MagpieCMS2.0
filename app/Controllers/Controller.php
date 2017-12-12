<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/15/2017
 * Time: 3:36 PM
 */
namespace Magpiehunt\Controllers;


class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    public function __get($property)
    {
        if($this->container->{$property}){
            return $this->container->{$property};
        }
    }
   /* public function canEdit(Response $response, $cid)
    {
        //A session must be started in order for Google' oauth2callback to work
        session_start();

        //Get our config object and fetch our firebase-admin-credentials.json file
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        //If we have an access_token then we can fetch our user, else we must fetch one.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $token = $client->getAccessToken();
            $client->setAccessToken($token);

            //Verify token with Google. If we fetch data then we can continue, otherwise something failed.
            if ($token_data = $client->verifyIdToken()) {
                //Token data contains our unique userID in the array field "sub"
                $userid = $token_data['sub'];

                $conn = connect_db();

                $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
                $stmt->execute([$userid]);
                $output = $stmt->fetch();

                //Check if our user exists
                if ($output['UID'] == $userid) {
                    //Check if our userID matches the UserID associated with the provided CID. If so, they own it and can edit it.
                    $stmt = $conn->prepare("SELECT * FROM UserMadeCollectionList WHERE (UserID = ? AND CollectionID = ?);");
                    $stmt->execute([$output['UserID'], $cid]);
                    if ($stmt->fetch()) {
                        return true;
                    }
                    // UserID 0 is a special ID that belongs to the admin. If the UserID is 0 then they can edit the collection.
                    else if($output['UserID'] == 0){
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            if ($response->withRedirect('/oauth2callback')) {
                //Get access token
                $client->setAccessToken($_SESSION['access_token']);
                $token = $client->getAccessToken();
                $client->setAccessToken($token);

                if ($token_data = $client->verifyIdToken()) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    public function authCheck($path, $app, Request $request, Response $response, $args)
    {
        //A session must be started in order for Google' oauth2callback to work
        session_start();

        //Get our config object and fetch our firebase-admin-credentials.json file
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        //If we have an access_token then we can render the page. Else we redirect to our oauth2callback endpoint.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            return $app->view->render($response, $path, $args);
        } else {
            return $response->withRedirect('/oauth2callback');
        }
    }*/
}