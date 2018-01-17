<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/


$app->middleware([
    Illuminate\Session\Middleware\StartSession::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'validate' => App\Http\Middleware\ValidateMiddleware::class,
    'maintain' => App\Http\Middleware\MaintainMiddleware::class,
    'feature' => App\Http\Middleware\FeatureMiddleware::class,
    'admin_level' => App\Http\Middleware\AdminLevelMiddleware::class
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\MultipleEssayProviders::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Illuminate\Session\SessionServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    //require __DIR__ . '/../routes/web.php';
    require __DIR__ . '/../routes/routes.php';
    //require __DIR__ . '/../routes/other_festival.php';
    //require __DIR__ . '/../routes/routes_backend.php';
    //require __DIR__ . '/../routes/routes_front.php';
});

/*
|--------------------------------------------------------------------------
| Register config
|--------------------------------------------------------------------------
|
| Now we will register the config in the service.
|
*/

switch (app()->environment()) {
    case 'local':
        $app->configure('setting/local');
        break;
    case 'qatest':
        $app->configure('setting/qatest');
        break;
    case  'develop':
        $app->configure('setting/develop');
        break;
    case  'release':
        $app->configure('setting/release');
        break;
    case  'master':
        $app->configure('setting/master');
        break;
}

$app->configure('define');
//session
$app->configure('session');
// 设置session别名
$app->alias('session', 'Illuminate\Session\SessionManager');

$app->configure('cache');

$app->alias('cache', 'Illuminate\Cache\CacheManager');

$app->configureMonologUsing(function (Monolog\Logger $monoLog) use ($app) {
    return $monoLog->pushHandler(
        new \Monolog\Handler\RotatingFileHandler($app->storagePath() . '/logs/lumen.log', 7)
    );
});
return $app;
