<?php
/**
 * file: MenuplanBuilder.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\model;

class MenuplanBuilder {
	
	private function buildPlan($rows, $each) {
		$plan = array ();
		$i = 0;
		$menus = array ();
		foreach ( $rows as $row ) {
			$menu = $each ( $plan, $row, $i );
			array_push ( $menus, $menu );
			$i ++;
		}
		usort($menus,function($a,$b) {
			return $a['hash'] - $b['hash'];
		});
		$plan['menus'] = $menus;
		$menus = array();
		$i=0;
		foreach($plan['menus'] as $menu){
			$menu['num'] = $i;
			array_push($menus,$menu);
			$i++;
		}
		$plan['menus'] = $menus;
		return $plan;
	}
	
	public function buildDailyplan($rows) {
		$each = function (&$plan, $row, $index) {
			if ($index == 0) {
				$plan ['mensa'] = $row ['mensa'];
				$plan ['date'] = $row ['date'];
				$plan ['day'] = $row ['day'];
			}
			return array (
					'num' => 0,
					'hash' => $this->makeNumber($row),
					'title' => $row ['title'],
					'menu' => explode ( '|', $row ['menu'] ) 
			);
		};
		return $this->buildPlan ( $rows, $each );
	}
	
	public function buildWeeklyplan($rows) {
		$each = function (&$plan, $row, $index) {
			if ($index == 0) {
				$plan ['mensa'] = $row ['mensa'];
				$plan ['week'] = $row ['week'];
			}
			return array (
					'num' => 0,
					'hash' => $this->makeNumber($row),
					'title' => $row ['title'],
					'date' => $row ['date'],
					'day' => $row ['day'],
					'menu' => explode ( '|', $row ['menu'] ) 
			);
		};
		return $this->buildPlan ( $rows, $each );
	}
	/**
	 * This function generates a hash number based on the content of the menu (day,date,title).
	 * @param $menu
	 * @return hash number
	 */
	public function makeNumber($menu){
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
		
		$num = intval($time.$day_num.$q);
		return $num;
	}
}
?>