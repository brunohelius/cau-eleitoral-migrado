<?php

use SwaggerLume\ServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

/*
 * |--------------------------------------------------------------------------
 * | Create The Application
 * |--------------------------------------------------------------------------
 * |
 * | Here we will load the environment and create the application instance
 * | that serves as the central piece of this framework. We'll use this
 * | application as an "IoC" container and router for this framework.
 * |
 */
$app = new Laravel\Lumen\Application(dirname(__DIR__));
$app->withFacades();
$app->withEloquent();

// Doctrine
if (!class_exists('EntityManager')) class_alias('LaravelDoctrine\ORM\Facades\EntityManager', 'EntityManager');
if (!class_exists('Registry')) class_alias('LaravelDoctrine\ORM\Facades\Registry', 'Registry');
if (!class_exists('Doctrine')) class_alias('LaravelDoctrine\ORM\Facades\Doctrine', 'Doctrine');
$app->alias(\Doctrine\Common\Persistence\ManagerRegistry::class, \Doctrine\Persistence\ManagerRegistry::class);

class_alias(Barryvdh\Snappy\Facades\SnappyPdf::class, 'PDF');
class_alias(Barryvdh\Snappy\Facades\SnappyImage::class, 'SnappyImage');


// Swagger
$app->configure('swagger-lume');

// Mail
$app->configure('mail');

// DomPDF
$app->configure('dompdf');

// JWT
$app->configure('jwt');

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);

$app->make('translator')->setLocale('pt-BR');
$app->make('queue');

/*
 * |--------------------------------------------------------------------------
 * | Register Container Bindings
 * |--------------------------------------------------------------------------
 * |
 * | Now we will register a few bindings in the service container. We will
 * | register the exception handler and the console kernel. You may add
 * | your own bindings here if you like or you can make another file.
 * |
 */

$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);


/*
 * |--------------------------------------------------------------------------
 * | Register Middleware
 * |--------------------------------------------------------------------------
 * |
 * | Next, we will register the middleware with the application. These can
 * | be global middleware that run before and after each request into a
 * | route or middleware that'll be assigned to some specific routes.
 * |
 */

$app->middleware([
    App\Http\Middleware\Cors::class,
    \Illuminate\Http\Middleware\FrameGuard::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

/*
 * |--------------------------------------------------------------------------
 * | Register Service Providers
 * |--------------------------------------------------------------------------
 * |
 * | Here we will register all of the application's service providers which
 * | are used to bind services into the container. Service providers are
 * | totally optional, so you are not required to uncomment this line.
 * |
 */

$app->register(LaravelDoctrine\ORM\DoctrineServiceProvider::class);
$app->register(App\Security\Providers\AuthProvider::class);
$app->register(App\Providers\AppServiceProvider::class);
$app->register(ServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(MarvinLabs\DiscordLogger\ServiceProvider::class);
$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
$app->register(Illuminate\Translation\TranslationServiceProvider::class);
$app->register(\Barryvdh\DomPDF\ServiceProvider::class);
$app->register(\Intervention\Image\ImageServiceProviderLumen::class);
$app->register(Barryvdh\Snappy\LumenServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

/*
 * |--------------------------------------------------------------------------
 * | Load The Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Next we will include the routes file so that they can all be added to
 * | the application. This will provide all of the URLs the application
 * | can respond to, as well as the controllers that may handle them.
 * |
 */

$app->router->group([
    'namespace' => 'App\Http\Controllers'
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
