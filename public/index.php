<?php
/**
 * This file contains a very crude bootstrap and routing configuration
 * Everything is currently in one file, but should be split out into providers
 */

define('APP_ROOT', realpath(__DIR__ . '/../'));

include APP_ROOT . '/vendor/autoload.php';

use App\Helper\XeroSessionStorage;
use League\Container\ReflectionContainer;
use League\Plates\Engine;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
use XeroPHP\Application\PublicApplication;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

$container = new League\Container\Container();

/**
 * Container has the power to automatically resolve objects and
 * all of their dependencies recursively by inspecting the type hints
 * of the constructor arguments
 *
 * @see  http://container.thephpleague.com/3.x/auto-wiring/
 */
$container->delegate(new ReflectionContainer());

//Share the template engine into controllers instantiated by the container
$container->share(Engine::class, function () {
    return new Engine(APP_ROOT . '/src/templates', 'phtml');
});

$container->share(XeroSessionStorage::class);

// This is where the Xero application is instantiated.
// This should happen wherever your services are registered
$container->share(PublicApplication::class, function () use ($container) {
    $config = include APP_ROOT . '/config/xero.php';
    $application = new PublicApplication($config);

    /** @var XeroSessionStorage $sessionStorage */
    $sessionStorage = $container->get(XeroSessionStorage::class);

    //If the session exists, register it on the application
    if (null !== $session = $sessionStorage->getSession()) {
        $application->getOAuthClient()
            ->setToken($session->token)
            ->setTokenSecret($session->token_secret);
    }

    return $application;
});


//Routes
$collection = new RouteCollection($container);

//This handles exceptions in HTML land
$collection->setStrategy(new \App\Helper\CustomExceptionStrategy());

\App\Controller\ApplicationController::registerRoutes($collection);
\App\Controller\AccountsController::registerRoutes($collection);


$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$response = $collection->dispatch($request, new Response());

$emitter = new SapiEmitter();
$emitter->emit($response);