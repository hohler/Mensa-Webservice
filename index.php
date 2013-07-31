<?php 
require 'vendor/autoload.php';
require 'app/config.php';
use app\service\JSONRender;
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
$dataSource = new DataSource($config);

$slim->get ( '/', function () use($slim,$render) {
	$response = array (
			'result' => array (
					'content' => array (
							1,
							2,
							3,
							4,
							5,
							6 
					) 
			) 
	);
	$render->render ( $response );
} );



$slim->run ();


?>