<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/18/2017
 * Time: 11:38 AM
 */

namespace Magpiehunt\Controllers\Webapp;

use Magpiehunt\Controllers\Controller as Controller;
use Slim\Views\Twig as view;

class CMSController extends Controller
{

    public function login($request, $response)
    {
        return $this->view->render($response, 'login.twig');
    }
    public function welcome($request, $response)
    {
        return $this->view->render($response, 'welcome.twig');
    }
    public function collections($request, $response)
    {
        return $this->view->render($response, 'collections.twig');
    }
    public function create($request, $response)
    {
        return $this->view->render($response, 'create.twig');
    }
    function contact($request, $response) {
        return $this->view->render($response, 'contact.twig');
    }
    function legal($request, $response) {
        return $this->view->render($response, 'legal.twig');
    }
    function account($request, $response) {
        return $this->view->render($response, 'account.twig');
    }
    function logout($request, $response) {

    }
    function authCheck($path, $app, Request $request, Response $response, $args)
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
    }
}