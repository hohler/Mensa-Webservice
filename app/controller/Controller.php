<?php
namespace app\controller;

use app\core\MainController;
use app\core\Helper;
use app\core\Response;
class Controller extends MainController{
	
	function test($request){
		return new Response(array(1,2,3,4,5,6,7));
	}
	
	function getMensas($request){
		$canteens = $this->ds->getCanteens();
		$canteens = Helper::utf8_string_array_decode($canteens);
		return new Response($canteens);
	}
	
}
?>