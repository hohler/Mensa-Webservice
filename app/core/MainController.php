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
	
	function __construct($config,$ds,$request,$response){
		$this->config = $config;
		$this->ds = $ds;
		$this->request = $request;
		$this->response = $response;
	}
	
}
?>