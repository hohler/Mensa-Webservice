<?php
namespace app\service;
class Controller {
	
	private $ds;
	
	function __construct($ds){
		$this->ds = $ds;
	}
	
	function test($request){
		return array(1,2,3,4,5,6,7);
	}
	
	function getMensas($request){
		return array(1,2,3);
	}
	
}
?>