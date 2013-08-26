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
	protected $response;
	protected $config;
	
	function __construct($config){
		$this->config = $config;
		$this->ds = DataSource::getInstance();
		$this->request = \Slim\Slim::getInstance()->request();
		$this->response = \Slim\Slim::getInstance()->response();
	}
	
}
?>