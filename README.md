SilexService Provider


```php
<?php

include __DIR__ . "/../vendor/autoload.php";

use Silex\Application;
use G\AppServiceProvider;
use G\AngularPostRequestServiceProvider;
use G\SecurityIface;

class Security implements SecurityIface
{
    public function validateCredentials($user, $pass)
    {
        if ($user == 'gonzalo') {
            $out = [
                'status' => true,
                'token'  => md5("gonzalo")
            ];
        } else {
            $out = [
                'status'  => false,
                'message' => "Not valid credentials"
            ];
        }

        return $out;
    }

    public function getUserFromToken($token)
    {
        if (md5("gonzalo") == $token) {
            return 'gonzalo';
        } else {
            return false;
        }
    }
}
$app = new Application([
    'debug'                                        => true,
    'version'                                      => 1,
    AppServiceProvider::AUTH_MOUNT                 => '/auth',
    AppServiceProvider::VALIDATE_CREDENTIALS_ROUTE => '/validateCredentials',
]);

$app->register(new AngularPostRequestServiceProvider());
$app->register(new AppServiceProvider(new Security()));

$app->get('/hello', function (Application $app) {
    return $app->json(['Hello']);
});

$app->run();

```