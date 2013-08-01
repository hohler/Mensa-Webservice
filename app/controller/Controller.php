<?php
namespace app\controller;

use app\core\MainController;
use app\core\Helper;
use app\core\Response;
class Controller extends MainController{
	
	/**
	 * GET
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getMensas($params){
		$canteens = $this->ds->queryMensas();
		
		$q = $this->request->params('q');
		$field = $this->request->params('field');
		if(!empty($q) && !empty($field)){
			$canteens = $this->ds->queryMensas();
			$filter = function ($canteen) use($field,$q){
				return array_key_exists($field, $canteen) && strpos($canteen[$field],$q)!==false;
			};
			$canteens = array_filter($canteens,$filter);
		} 
		$canteens = Helper::utf8_string_array_decode($canteens);
		return new Response($canteens);
	}
	
	/**
	 * GET
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getMensa($params){
		$canteen = $this->ds->queryMensaById($params['id']);
		return new Response($canteen);
	}
	
	/**
	 * POST
	 * @param $params
	 */
	public function addMenus($params){
		$body = $this->request->getBody();
		$code = 200;
		$menus = json_decode($body,true);
		$menus = Helper::utf8_string_array_decode($menus);
		$created = array();
		foreach($menus as $menuData){
			$mensa = $this->ds->queryMensaByName($menuData['mensa']);
			$mensaId = $mensa['id'];
			$title = $menuData['title'];
			$date = $menuData['date'];
			$price = $menuData['price'];
			if(!$this->ds->doesMenuExist($mensaId, $title, $date)){
				$success = $this->ds->createMenu($mensaId,$title,$date,$menu,$price);
				if($success){
					array_push($created,$menu);
				}
			}
		}
		if(count($created)!=0)
			$code = 201;
		
		return new Response($created,$code);
	}
	
	public function getDailyMenuplan($params){
		$mensaId = $params['id'];
		
		if(array_key_exists('date',$params)){
			$date = $date = $params['date'];
		} else {
			$date = date('Y-m-d');
		}
		
		$plan = $this->ds->queryDailyMenuplan($mensaId, $date);
		if(count($plan['menus'])==0){
			$code = 404;
		} else { 
			$code = 200;
		}
		return new Response($plan,$code);
	}
	
	public function getWeeklyMenuplan($params){
		$mensaId = $params['id'];
		echo 'test';
		$plan = $this->ds->queryWeeklyMenuplan($mensaId);
		if(count($plan['menus'])!=0){
			$code = 200;
		} else {
			$code = 404;
		}
		return new Response($plan,$code);
	}
	
}
?>