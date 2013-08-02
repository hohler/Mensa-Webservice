<?php
/**
 * file: MainController.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;
use app\model\DataSource;
class MainController {
	protected $ds;
	protected $request;
	protected $config;
	
	function __construct(){
		$this->ds = DataSource::getInstance();
		$this->request = \Slim\Slim::getInstance()->request();
		$this->response = \Slim\Slim::getInstance()->response();
	}
	
}
?>