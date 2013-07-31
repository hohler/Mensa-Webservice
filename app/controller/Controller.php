<?php
namespace app\controller;

use app\core\MainController;
use app\core\Helper;
use app\core\Response;
class Controller extends MainController{
	
	function getMensas($request){
		$canteens = $this->ds->queryMensas();
		$canteens = Helper::utf8_string_array_decode($canteens);
		return new Response($canteens);
	}
	
	function getMensa($params){
		$canteen = $this->ds->queryMensaById($params['id']);
		$canteen = Helper::utf8_string_array_decode($canteen);	
		return new Response($canteen);
	}
	
}
?>