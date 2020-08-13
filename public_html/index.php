<?php

$appConfig = require __DIR__ . '/../config/application.config.php';
require __DIR__.'/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

function pvar($var){
	echo "<pre>";
	print_r($var);
	echo '</pre>';
}

$loader = new ClassLoader();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
	$r->addRoute('GET', '/', 'Task/renderTaskList');
	$r->addRoute('POST','/','Task/updateTask');
	$r->addRoute('GET','/login','User/renderAuth');
	$r->addRoute('POST','/auth' ,'User/auth');
	$r->addRoute('GET','/logout','User/logout');
	$r->addRoute(['GET', 'POST'],'/form','Task/CreateTask');
	$r->addRoute(['GET','POST'],'/edit/','Task/editTask');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
\RedBeanPHP\R::setup('sqlite:'.__DIR__.'/../DB.db');

session_start();
switch ($routeInfo[0]) {
	case FastRoute\Dispatcher::NOT_FOUND:

		break;
	case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
		$allowedMethods = $routeInfo[1];

		break;
	case FastRoute\Dispatcher::FOUND:
		$handler = $routeInfo[1];
		$vars = $routeInfo[2];


		list($class, $method) = explode("/", $handler, 2);


		$loader->addPsr4('',  APP_PATH.'Controller/'.$class);
		$loader->addPsr4('',APP_PATH.'Model/'.$class);

		$loader->register();
		list($result,$action) = call_user_func_array(array(new $class, $method), $vars);
		require APP_PATH."View/Init.php";
		new View($class,$result,$action);

		break;
}



