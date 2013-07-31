<?php
$routes = array (
		array (
				'method' => 'GET',
				'path' => '/',
				'handler' => 'getMensas',
				'controller' => '\app\controller\Controller' 
		),
		array (
				'method' => 'GET',
				'path' => '/mensas',
				'handler' => 'getMensas',
				'controller' => '\app\controller\Controller'
		),
		array(
				'method' => 'GET',
				'path' => '/mensas/:id',
				'handler' => 'getMensa',
				'controller' => '\app\controller\Controller'
		)
);
?>