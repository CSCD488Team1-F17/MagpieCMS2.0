<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/15/2017
 * Time: 2:39 PM
 */

namespace Magpiehunt\Controllers\Webapp;
use Magpiehunt\Controllers\Controller as Controller;
use Slim\Views\Twig as view;

class HomeController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'index.twig');
    }
}
