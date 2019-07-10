<?php

namespace BioSounds\Controller;

use BioSounds\Exception\InvalidActionException;
use Twig\Environment;

class ApiController
{
    /**
     * @param Environment $twig
     * @param array $route
     * @return mixed
     * @throws InvalidActionException
     */
    public function route(Environment $twig, array $route)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET,POST,DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $controllerName =  __NAMESPACE__ . '\\' . ucfirst($route[0]) . 'Controller';
        $controller = new $controllerName($twig);

        if (!method_exists($controller, $route[1]) || !is_callable([$controller, $route[1]])) {
            throw new InvalidActionException($route[1]);
        }
        return call_user_func_array([$controller, $route[1]], array_slice($route, 2));
    }
}
