<?php
namespace app\controller;

use app\core\MainController;
use app\core\Helper;
use app\core\Response;
class Controller extends MainController{
	
	function getMensas($request){
		$canteens = $this->ds->getCanteens();
		$canteens = Helper::utf8_string_array_decode($canteens);
		return new Response($canteens);
	}
	
	function getMensa($params){
		$canteen = $this->ds->getCanteen($params['id']);
		$canteen = Helper::utf8_string_array_decode($canteen);	
		return new Response($canteen);
	}
	
}
?>