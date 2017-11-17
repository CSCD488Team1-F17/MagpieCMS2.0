<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/15/2017
 * Time: 3:45 PM
 */

namespace Magpiehunt\Controllers;

use Slim\Views\Twig as View;

class LoginController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'login.twig');
    }
}