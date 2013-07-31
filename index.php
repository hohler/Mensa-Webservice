<?php 
require 'vendor/autoload.php';
require 'app/config.php';
require 'app/routes.php';
use app\service\JSONRender;
use app\service\Controller;
use app\service\DataSource;

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
$controller = new Controller(DataSource::getInstance($config));

foreach($routes as $route){
	$request = $slim->request();
	$handler = $route['handler'];
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