<?php
/**
 * This file contains a very crude bootstrap and routing configuration
 */

define('APP_ROOT', realpath(__DIR__ . '/../'));

include APP_ROOT.'/vendor/autoload.php';

use League\Container\ReflectionContainer;
use League\Plates\Engine;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
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
    return new Engine(APP_ROOT.'/src/templates', 'phtml');
});


$collection = new RouteCollection($container);

$collection->group('application', function (RouteGroup $group) {
    $group->get('connect', '\App\Controller\ApplicationController::connect');
});


$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$response = $collection->dispatch($request, new Response());

$emitter = new SapiEmitter();
$emitter->emit($response);