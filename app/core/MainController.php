<?php
namespace app\core;
class MainController {
	protected $ds;
	
	function __construct(){
		$this->ds = DataSource::getInstance();
	}
	
}
?>