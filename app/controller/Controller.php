<?php
namespace app\controller;

use app\core\MainController;
use app\core\Helper;
use app\core\Response;
class Controller extends MainController{
	private static $token = 'Tm6eePnrnrrT9eLjin5z';
	
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
		$message = json_decode($body,true);
		if(strcmp($message['token'],self::$token)==0){
			$menus = $message['menus'];
			$menus = Helper::utf8_string_array_decode($menus);
			$created = array();
			foreach($menus as $menuData){
				$mensa = $this->ds->queryMensaByName($menuData['mensa']);
				$mensaId = $mensa['id'];
				$title = $menuData['title'];
				$date = $menuData['date'];
				$menu = $menuData['menu'];
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
			$this->response->status($code);
		}
		return new Response($created,$code);
	}
	
	/**
	 * GET
	 * @param $params
	 * @return \app\core\Response
	 */
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
	
	/**
	 * GET
	 * @param $params
	 * @return boolean|\app\core\Response
	 */
	public function getWeeklyMenuplan($params){
		$mensaId = $params['id'];
		$day = null;
		if(array_key_exists('day',$params)){
			$day = ucfirst(strtolower($params['day']));
		}
		$plan = $this->ds->queryWeeklyMenuplan($mensaId);
		
		//check if the weekly plan should be filtered
		if($day!=null){
			$filter = function($key) use ($day){
				return strcmp($key,$day)==0;
			};
			$menus = array();
			
			//copy menus with a day field equals to $day
			foreach($plan['menus'] as $dayKey=>$menusOfDay){
				if(strcmp($dayKey,$day)==0){
					$i=0;
					//remove for each menu its date field
					foreach($menusOfDay as &$menuOfDay){ //give a reference to the sub array
						if($i==0){
							$plan['date'] = substr($menuOfDay['date'],0); //copy string
						}
						unset($menuOfDay['date']); //remove field
						$i++;
					}
					array_push($menus,$menusOfDay);
				}
				
			}
			$plan['menus'] = $menus;
		}
		
		if(count($plan['menus'])==0){
			$code = 404;
		} else { 
			$code = 200;
		}
		return new Response($plan,$code);
	}
	
}
?>