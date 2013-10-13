<?php
/**
 * file: Controller.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\controller;

use app\core\MainController;
use app\core\Helper;
use app\core\Response;

class Controller extends MainController {
	
	/**
	 * HTTP Method: GET
	 * get a list of mensas
	 * 
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
	 * HTTP Method: GET
	 * get a specific mensa
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getMensa($params){
		$canteen = $this->ds->queryMensaById($params['id']);
		return new Response($canteen);
	}
	
	/**
	 * HTTP Method: POST
	 * create menus
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function addMenus($params){
		//TODO: refactor this controller method
		// -> write a PushMessageRader class
		$body = $this->request->getBody();
		
		if(!empty($body)){
			$code = 200;
			// read content of the push message
			$message = json_decode($body,true);
			
			if(strcmp($message['token'],$this->getPushToken())==0){
				$menus = $message['menus'];
				$menus = Helper::utf8_string_array_decode($menus);
				$created = array();
				
				foreach($menus as $menuData){
					$mensa = $this->ds->queryMensaByName($menuData['mensa']);
					$mensaId = $mensa['id'];
					$title = $menuData['title'];
					$date = $menuData['date'];
					$menu = $menuData['menu'];
					if(!$this->ds->doesMenuExist($mensaId, $title, $date)){
						$success = $this->ds->createMenu($mensaId,$title,$date,$menu);
						if($success){
							array_push($created,$menu);
						}
					}
				}
				
				if(count($created)!=0){
					$code = 201;
				}
				
				$this->response->status($code);
			}
			$response = new Response($created,$code);
		} else {
			$response = new Response(array(),400);
			$response->setReason('No JSON Body send!');
		}
		
		return $response;
	}
	
	/**
	 * HTTP Method: PUT
	 * update menus
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function updateMenus($params){
		//TODO: refactor this controller method
		// -> write a PushMessageRader class
		$body = $this->request->getBody();
		
		if(!empty($body)){
			$code = 200;
			// read content of the push message
			$message = json_decode($body,true);
			
			if(strcmp($message['token'],$this->getPushToken())==0){
				$menus = $message['menus'];
				$menus = Helper::utf8_string_array_decode($menus);
				$created = array();
				
				foreach($menus as $menuData){
					$mensa = $this->ds->queryMensaByName($menuData['mensa']);
					$mensaId = $mensa['id'];
					$title = $menuData['title'];
					$date = $menuData['date'];
					$menu = $menuData['menu'];
					
					if($this->ds->doesMenuExist($mensaId, $title, $date)){
						$old_menu = $this->ds->queryMenu($mensaId, $title, $date);
						$success = $this->ds->updateMenu($old_menu['id'],$menu);
						
						if($success){
							array_push($created,array('title'=>$title,'mensa_id'=>$mensaId,'date'=>$date,'old'=>$old_menu,'update'=>$menuData));
						}
					} else {
						$success = $this->ds->createMenu($mensaId, $title, $date, $menu);
						if($success){
							array_push($created,array('title'=>$title,'mensa_id'=>$mensaId,'date'=>$date,'created'=>$menuData));
						}
					}
				}
				
				if(count($created)!=0) {
					$code = 201;
				}
				
				$this->response->status($code);
				$response = new Response($created,$code);
			}
		} else {
			$response = new Response(array(),400);
			$response->setReason('No JSON Body send!');
		}
		
		return $response;
	}
	
	/**
	 * HTTP Method: GET
	 * get daily meal plan
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getDailyMenuplan($params){
		$mensaId = $params['id'];
		$plan = $this->ds->queryDailyMenuplanByDate($mensaId,date('Y-m-d'));
		
		if(count($plan['menus'])==0){
			$plan = $this->ds->queryLatestDailyMenuplan($mensaId);
		}
		
		if(count($plan['menus'])==0){
			$code = 404;
		} else { 
			$code = 200;
		}
		
		return new Response($plan,$code);
	}
	
	/**
	 * HTTP Method: GET
	 * get daily meal plan on a specific date
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getDailyMenuplanByDate($params){
		$mensaId = $params['id'];
		$date = $params['date'];
		$plan = $this->ds->queryDailyMenuplanByDate($mensaId, $date);
	
		if(count($plan['menus'])==0){
			$code = 404;
		} else {
			$code = 200;
		}
		
		return new Response($plan,$code);
	}
	
	/**
	 * HTTP Method: GET
	 * return the latest weekly menuplan
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getWeeklyMenuplan($params){
		$mensaId = $params['id'];
		$plan = $this->ds->queryWeeklyMenuplan($mensaId);
		
		//fallback!
		if(count($plan['menus'])==0){
			$plan = $this->ds->queryLatestWeeklyMenuplan($mensaId);
		}
		
		if(count($plan['menus'])==0){
			$code = 404;
		} else { 
			$code = 200;
		}
		
		return new Response($plan,$code);
	}
	
	/**
	 * HTTP Method: GET
	 * Return the weekly menuplan on a specific week
	 *
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getWeeklyMenuplanByWeek($params){
		$mensaId = $params['id'];
		$week = $params['week'];
		$plan = $this->ds->queryWeeklyMenuplanByWeek($mensaId,$week);
	
		if(count($plan['menus'])==0){
			$code = 404;
		} else {
			$code = 200;
		}
		return new Response($plan,$code);
	}
	
	/**
	 * HTTP Method: GET
	 * Return the daily menuplan on a specific week and days
	 *
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getDailyMenuplanByWeekAndDay($params){
		$mensaId = $params['id'];
		$week = $params['week'];
		$day = $day = Helper::formatDayString($params['day']);
		$plan = $this->ds->queryWeeklyMenuplanByWeekAndDay($mensaId,$week,$day);
	
		if(count($plan['menus'])==0){
			$code = 404;
		} else {
			$code = 200;
		}
		
		return new Response($plan,$code);
	}
	
	/**
	 * HTTP Method: GET
	 * Return the latest daily menuplan filtered by the weekly menuplan
	 * 
	 * @param $params
	 * @return \app\core\Response
	 */
	public function getWeeklyMenuplanFilteredByDay($params){
		$mensaId = $params['id'];
		$day = Helper::formatDayString($params['day']);
		
		if(Helper::isDay($day)){
			$plan = $this->ds->queryWeeklyMenuplan($mensaId);
			// fallback!
			if(count($plan['menus'])==0){
				$plan = $this->ds->queryLatestWeeklyMenuplan($mensaId);
			}
			
			$filter = function ($menu) use($day) {
				return strpos ( $menu ['day'], $day ) !== false;
			};
			
			$plan ['menus'] = array_values(array_filter($plan['menus'],$filter));
			
			if(count($plan['menus'])==0){
				$code = 404;
			} else {
				$code = 200;
			}
		} else {
			$code = 400;
			$plan = array();
		}
		
		return new Response($plan,$code);
	}
	
	public function getMensasUpdates(){
		$updates = $this->ds->queryMensasUpdates();
		return new Response($updates);
	}	
}

?>
