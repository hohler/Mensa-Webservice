<?php
/**
 * file: routes.php
 * @author: Alexander Rüedlinger
 * 
 * This file defines the routes for the froncontroller index.php
 */
$routes = array (
		/**
		 * Route matches: /mensas
		 */
		array (
				'method' => 'GET',
				'path' => '/',
				'handler' => 'getMensas',
				'controller' => '\app\controller\Controller' 
		),
		
		/**
		 * Route matches: /mensas
		 */
		array (
				'method' => 'GET',
				'path' => '/mensas',
				'handler' => 'getMensas',
				'controller' => '\app\controller\Controller' 
		),
		
		/**
		 * Route matches: /mensas/1
		 */
		array (
				'method' => 'GET',
				'path' => '/mensas/:id',
				'handler' => 'getMensa',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+') 
		),

		/**
		 * Route matches: /mensas/1/dailyplan/2013-08-09
		 */
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/dailyplan/:date',
				'handler' => 'getDailyMenuplanByDate',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+','date'=>'\d\d\d\d-(\d\d|\d)-(\d\d|\d)') 
				// allow for date: 2013-8-9, 2013-08-09, 2013-8-09, 2013-08-9
		),
		
		/**
		 * Route matches: /mensas/1/dailyplan
		 */
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/dailyplan',
				'handler' => 'getDailyMenuplan',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+')
		),
		/**
		 * Route matches: /mensas/1/weeklyplan/32/monday
		 */
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/weeklyplan/:week/:day',
				'handler' => 'getDailyMenuplanByWeekAndDay',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+','week'=>'(\d|\d\d)','day'=>'\w+')
		),
		/**
		 * Route matches: /mensas/1/weeklyplan/32
		 */
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/weeklyplan/:week',
				'handler' => 'getWeeklyMenuplanByWeek',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+','week'=>'(\d|\d\d)')
		),
		
		/**
		 * Route matches: /mensas/1/weeklyplan/monday|tuesday etc.
		 */
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/weeklyplan/:day',
				'handler' => 'getWeeklyMenuplanFilteredByDay',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+','day'=>'\w+')
		),
		
		/**
		 * Route matches: /mensas/1/weeklyplan
		 */
		array(
				'method' => 'GET',
				'path' => '/mensas/:id/weeklyplan',
				'handler' => 'getWeeklyMenuplan',
				'controller' => '\app\controller\Controller',
				'conditions' => array('id'=>'\d+')
		),
		
		/**
		 * Create and update routes.
		 */
		
		/**
		 * Route matches: /mensas/menus
		 */
		array (
				'method' => 'PUT',
				'path' => '/mensas/menus',
				'handler' => 'updateMenus',
				'controller' => '\app\controller\Controller'
		),
		/**
		 * Route matches: /mensas/menus
		*/
		array (
				'method' => 'POST',
				'path' => '/mensas/menus',
				'handler' => 'addMenus',
				'controller' => '\app\controller\Controller'
		),
		
);

?>