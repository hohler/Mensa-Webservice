<?php
/**
 * file: MenuplanBuilder.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\model;

class MenuplanBuilder {
	
	private static function buildPlan($rows, $each) {
		$plan = array ();
		$menus = array ();
		$count = count($rows);
		for($i=0; $i < $count; $i++) {
			$menu = $each($plan, $rows[$i], $i);
			array_push($menus, $menu);
		}
		
		usort($menus,function($a,$b) {
			return $a['hash'] - $b['hash'];
		});
		
		for($i=0; $i < count($menus); $i++){
			$menus[$i]['num'] = $i;
		}
		
		$plan['menus'] = $menus;
		return $plan;
	}
	
	public static function buildDailyplan($rows) {
		$each = function (&$plan, $row, $index) {
			if ($index == 0) {
				$plan['mensa'] = $row['mensa'];
				$plan['date'] = $row['date'];
				$plan['day'] = $row['day'];
			}
			return array (
					'num' => 0,
					'hash' => self::makeNumber($row),
					'title' => $row['title'],
					'menu' => explode ( '|', $row ['menu'] ) 
			);
		};
		return self::buildPlan ( $rows, $each );
	}
	
	public static function buildWeeklyplan($rows) {
		$each = function (&$plan, $row, $index) {
			if ($index == 0) {
				$plan ['mensa'] = $row ['mensa'];
				$plan ['week'] = $row ['week'];
			}
			return array (
					'num' => 0,
					'hash' => self::makeNumber($row),
					'title' => $row ['title'],
					'date' => $row ['date'],
					'day' => $row ['day'],
					'menu' => explode ( '|', $row ['menu'] ) 
			);
		};
		return self::buildPlan ( $rows, $each );
	}
	/**
	 * This function generates a hash number based on the content of the menu (day,date,title).
	 * @param $menu
	 * @return hash number
	 */
	public static function makeNumber($menu){
		$time = strtotime($menu['date']);
		$day_num = date('N',strtotime($menu['day']));
		$q = 0;
		if(strpos($menu['title'],'Tagesmen')!==false || strpos($menu['title'],'Men')!==false 
			|| strpos($menu['title'],'Tagesgericht')!==false || strpos($menu['title'],'Warmes')!==false){
			$q = 0;
		} elseif(strpos($menu['title'],'Vegimen')!==false || strpos($menu['title'],'Vegi')!==false 
				|| strpos($menu['title'],'Vege')!==false){
			$q = 1;
		} elseif(strpos($menu['title'],'Special')!==false || strpos($menu['title'],'Wochenhit')!==false){
			$q=2;
		} elseif(strpos($menu['title'],'Salat')!==false || strpos($menu['title'],'Free choice')!==false ){
			$q = 3;
		} else {
			$q = 4;
		}
		
		$num = $time+10*$day_num+$q;
		return $num;
	}
}
?>