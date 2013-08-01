<?php
namespace app\core;
class MainController {
	protected $ds;
	protected $request;
	
	function __construct(){
		$this->ds = DataSource::getInstance();
		$this->request = \Slim\Slim::getInstance()->request();
	}
	
}
?>