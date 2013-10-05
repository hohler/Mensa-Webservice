<?php 
/**
 * file: index.php
 * @author: Alexander Rüedlinger
 * 
 * This file acts as frontcontroller.
 * 
 */
require 'vendor/autoload.php';
require 'config.php';
require 'routes.php';
require 'tokens.php';
use app\core\JSONRender;
use app\core\JSONPRender;
use app\core\Helper;
use app\model\DataSource;
use app\core\Response;

//autoload function
$autoload = function ($className){
	$array = explode('\\', $className);
	$fileName = array_pop($array).'.php';
	$dir = implode('/', $array);
	$inc = $dir.'/'.$fileName;
	include $inc;
};

spl_autoload_register($autoload);


$slim = new \Slim\Slim();
$dataSource = DataSource::createInstance($config); //create instance
$request = $slim->request();
$response = $slim->response();

$jsonRender = new JSONRender($request,$response);
$renders = array( '' => $jsonRender, '.json' => $jsonRender); //url endings .json, .xml or blank
//note: blank ending -> json render!

//map each route with an anonymous function that binds the controller methods
//in the slim framework.
foreach($routes as $route){
	$handler = $route['handler'];
	$controllerName = $route['controller'];
	$controller = new $controllerName($config,$tokens,$dataSource,$request,$response);
	$path = $route['path'];
	
	//register each route for different renders
	foreach($renders as $format=>$render){
		
		//anonymous function
		$func = function () use ($controller,$handler,$path,$render) {
			
			//create named parameters according the routes.php file
			$arguments = func_get_args();
			$paramNames = Helper::getParamNames($path);
			
			//ensure that both array have the same length and that its length is greater than 0!
			if(count($paramNames)==count($arguments) && count($paramNames)!=0){
				//make an associative array.
				$params = array_combine($paramNames,$arguments);
			} else { //otherwise just use the array
				$params = $arguments;
			}
			
			//token validation
			if($controller->checkToken()){
				//call the controller method and render the response
				$render->render($controller->$handler($params));
			} else {
				$errorResponse = new Response(array(),400);
				$errorResponse->setReason('Invalid token!');
				$render->render($errorResponse);
			}
		};
		
		// map route with a handler and set http methods POST,GET,PUT etc.
		$map = $slim->map($route['path'].$format,$func); //append format to the url ending!
		if(is_array($route['method'])){	
			foreach($route['method'] as $m){
				$map->via($m);
			}
		} else {
			$map->via($route['method']);
		}
		
		// set conditions
		if(array_key_exists('conditions',$route) && is_array($route['conditions'])){
			$map->conditions($route['conditions']);
		}
	}
}

$slim->run ();
?>
