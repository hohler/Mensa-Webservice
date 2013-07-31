<?php 
require 'vendor/autoload.php';
use app\service\JSONRender;

//autoload function
$autoload = function ($className){
	$array = explode('\\', $className);
	$fileName = array_pop($array).'.php';
	$dir = implode('/', $array);
	$inc = $dir.'/'.$fileName;
	include $inc;
};

spl_autoload_register($autoload);



$app = new \Slim\Slim();
$jsonRender = new JSONRender();

$app->get('/',function() use($app,$jsonRender){
	$response = array(
		'result' => array('content' => array(1,2,3,4,5,6))
	);
	$app->response()['Content-type'] = 'application/json; charset=utf-8';
	$rendered = $jsonRender->render($response);
	$app->response()->body($rendered);
	
});

$app->run();

?>