<?php
$routes = array (
		array (
				'method' => array (
						'GET',
						'POST' 
				),
				'path' => '/',
				'handler' => 'test' 
		),
		array (
				'method' => 'GET',
				'path' => '/mensas',
				'handler' => 'getMensas' 
		) 
);
?>