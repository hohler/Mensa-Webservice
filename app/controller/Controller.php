<?php
namespace app\controller;

use app\core\MainController;
class Controller extends MainController{
	
	function test($request){
		return array(1,2,3,4,5,6,7);
	}
	
	function getMensas($request){
		return array(1,2,3);
	}
	
}
?>