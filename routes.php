<?php
$routes = array (
		array (
				'method' => array (
						'GET',
						'POST' 
				),
				'path' => '/',
				'handler' => 'test',
				'controller' => '\app\controller\Controller' 
		),
		array (
				'method' => 'GET',
				'path' => '/mensas',
				'handler' => 'getMensas',
				'controller' => '\app\controller\Controller'
		) 
);
?>