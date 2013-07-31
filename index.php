<?php 
require 'vendor/autoload.php';
require 'config.php';
require 'routes.php';
use app\core\JSONRender;
use app\core\DataSource;

//autoload function
$autoload = function ($className){
	$array = explode('\\', $className);
	$fileName = array_pop($array).'.php';
	$dir = implode('/', $array);
	$inc = $dir.'/'.$fileName;
	include $inc;
};

spl_autoload_register($autoload);
$slim = new \Slim\Slim ();
$render = new JSONRender($slim);
DataSource::getInstance($config); #create/get instance

foreach($routes as $route){
	$request = $slim->request();
	$handler = $route['handler'];
	$controller = new $route['controller'];
	
	$func = function () use ($controller,$handler,$request,$render) {
		$render->render($controller->$handler($request));
	};
	// map route with a handler
	$map = $slim->map($route['path'],$func);
	if(is_array($route['method'])){	
		foreach($route['method'] as $m){
			$map->via($m);
		}
	} else {
		$map->via($route['method']);
	}
}

$slim->run ();
?>