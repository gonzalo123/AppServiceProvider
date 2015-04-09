<?php
namespace G;

use Silex\ControllerProviderInterface;
use Silex\Application;

class AuthControllerProvider implements ControllerProviderInterface
{
    private $security;
    private $postRoutes;

    public function __construct(SecurityIface $security)
    {
        $this->security   = $security;
        $this->postRoutes = [];
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        foreach ($this->postRoutes as $route => $callback) {
            $controllers->post($route, $callback);
        }

        return $controllers;
    }

    public function registerPostRoute($route, Callable $callback)
    {
        $this->postRoutes[$route] = $callback;
    }
}