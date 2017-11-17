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
}