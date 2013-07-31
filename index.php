<?php 
require 'vendor/autoload.php';
require 'config.php';
require 'routes.php';
use app\core\JSONRender;
use app\core\DataSource;
use app\core\Helper;

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
	$path = $route['path'];
	
	$func = function () use ($controller,$handler,$path,$request,$render) {
		$arguments = func_get_args();
		$paramNames = Helper::getParamNames($path);
		$params = array_combine($paramNames,$arguments);
		$render->render($controller->$handler($params));
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