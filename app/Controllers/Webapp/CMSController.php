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
}