<?php

namespace G;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppServiceProvider implements ServiceProviderInterface
{
    private $security;

    const AUTH_MOUNT = 'app.mount.on';
    const VALIDATE_CREDENTIALS_ROUTE = 'app.route.validate.credentials';

    public function __construct(SecurityIface $security)
    {
        $this->security = $security;
    }

    public function register(Application $app)
    {
        $check = function (Request $request) use ($app) {
            if ($request->getMethod() != 'OPTIONS') {
                $this->versionCheck($request);
                $this->tokenCheck($app, $request);
            }
        };

        $authControllerProvider = new AuthControllerProvider($this->security);
        $authControllerProvider->registerPostRoute($app[self::VALIDATE_CREDENTIALS_ROUTE],
            function (Application $app, Request $request) {
                $user = $request->get('user');
                $pass = $request->get('password');

                return $app->json($this->security->validateCredentials($user, $pass));
            });
        $app->mount($app[self::AUTH_MOUNT], $authControllerProvider);
        $app->before($check, 0);
    }

    private function versionCheck(Request $request)
    {
        if (isset($app['version'])) {
            if ($request->get('_version') != $app['version']) {
                throw new HttpException(412, "Wrong version");
            }
        }
    }

    private function tokenCheck(Application $app, Request $request)
    {
        $pathsAllowedWithoutToken = [
            $app[self::AUTH_MOUNT] . $app[self::VALIDATE_CREDENTIALS_ROUTE],
        ];

        if (!in_array($request->getPathInfo(), $pathsAllowedWithoutToken)) {
            $token       = $request->get('_token', '');
            $app['user'] = $this->security->getUserFromToken($token);
            if ($app['user'] === false) {
                throw new AccessDeniedHttpException('Access Denied');
            }
        }
    }

    public function boot(Application $app)
    {
    }
}