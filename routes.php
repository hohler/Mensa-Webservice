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
		array (
				'method' => 'GET',
				'path' => '/mensas/:id',
				'handler' => 'getMensa',
				'controller' => '\app\controller\Controller' 
		),
		array (
				'method' => 'POST',
				'path' => '/mensas/menus',
				'handler' => 'addMenus',
				'controller' => '\app\controller\Controller' 
		),
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/dailyplan/:date',
				'handler' => 'getDailyMenuplan',
				'controller' => '\app\controller\Controller'
		),
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/dailyplan',
				'handler' => 'getDailyMenuplan',
				'controller' => '\app\controller\Controller'
		),
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/weeklyplan',
				'handler' => 'getWeeklyMenuplan',
				'controller' => '\app\controller\Controller'
		),
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/weeklyplan/:day',
				'handler' => 'getWeeklyMenuplan',
				'controller' => '\app\controller\Controller'
		)
		
);
?>