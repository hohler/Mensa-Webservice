<?php
namespace app\core;
use PDO;
class DataSource {
	private $db;
	private static $instance = null;
	
	private function __construct($config){
		$driver = $config['driver'];
		$host = $config['host'];
		$dbname = $config['dbname'];
		$user = $config['user'];
		$pass = $config['password'];
		$this->db = new PDO("{$driver}:host={$host};dbname={$dbname}", $user, $pass);
	}
	
	function __destruct(){
		$this->db = null;
	}
	
	function getInstance($config=null){
		if(self::$instance==null && $config!=null)
			self::$instance = new DataSource($config['db']);
		return self::$instance;
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function queryMensas(){
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensas';
		$stmt = $this->db->query($sql);
		$mensas = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $mensas;
	}
	
	/**
	 * 
	 * @param $id
	 * @return mixed
	 */
	public function queryMensaById($id){
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensas WHERE id=:id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':id',$id, PDO::PARAM_INT);
		$stmt->execute();
		$mensa = $stmt->fetch(PDO::FETCH_ASSOC);
		return $mensa;
	}
	
	/**
	 * 
	 * @param $name
	 * @return mixed
	 */
	public function queryMensaByName($name){
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensas'
				.' WHERE id_name LIKE :name OR name LIKE :name LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':name',"%{$name}%", PDO::PARAM_STR);
		$stmt->execute();
		$mensa = $stmt->fetch(PDO::FETCH_ASSOC);
		return $mensa;
	}
	
	public function createMenu($mensaId,$title,$date,$menu,$price){
		$menu = implode('|',$menu);
		$sql = 'INSERT INTO menus (mensa_id,title,date,menu,price)'
				.' VALUES (:mensa_id,:title,:date,:menu,:price)';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId,PDO::PARAM_INT);
		$stmt->bindValue(':title',$title,PDO::PARAM_STR);
		$stmt->bindValue(':menu',$menu,PDO::PARAM_STR);
		$stmt->bindValue(':date',$date,PDO::PARAM_STR);
		$stmt->bindValue(':price',$price,PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	public function doesMenuExist($id,$menuTitle,$dateString){
		$sql = 'SELECT EXISTS('
				.' SELECT 1 FROM `menus`'
				.' WHERE mensa_id=:mensa_id and `date`=:date_str and title=:title)'
				.' AS `result`';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$id,PDO::PARAM_INT);
		$stmt->bindValue(':date_str',$dateString,PDO::PARAM_STR);
		$stmt->bindValue(':title',$menuTitle,PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['result']==1 ? true: false; 
	}
	
	public function queryDailyMenuplan($mensaId,$date){
		$sql = 'SELECT `title`,`date`,`price`,`menu`,`name` AS `mensa` FROM `menus`'
				.' INNER JOIN `mensas` ON mensas.id = menus.mensa_id'
				.' WHERE mensa_id=:mensa_id and `date`=:date';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->bindValue(':date',$date, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = array();
		$menus = array();
		$i = 0;
		foreach($rows as $menu){
			if($i==0){
				$plan['mensa'] = $menu['mensa'];
				$plan['date'] = $menu['date'];	
			}
			$menu = array(
				'title' => $menu['title'],
				'menu' => explode('|',$menu['menu']),
				'price' => $menu['price']
			);
			array_push($menus,$menu);
			$i++;
		}
		$plan['menus'] = $menus;
		return $plan;
	}
	
	public function queryWeeklyMenuplan($mensaId){
		$sql = "SELECT `title`, `menu`, `price`,  `date` , `name` AS `mensa`, WEEK(  `date` ) AS  `week` , DATE_FORMAT(  `date` ,  '%W' ) AS  `day` 
				FROM  `menus` INNER JOIN mensas ON mensas.id = menus.mensa_id WHERE mensa_id =:mensa_id HAVING  `week` = WEEK( CURDATE( ) )";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = array();
		$menus = array ("Monday"=>array(),"Tuesday"=>array(),"Wednesday"=>array(),"Thursday"=>array(),"Friday"=>array());
		$i = 0;
		foreach($rows as $row){
			if($i==0){
				$plan['mensa'] = $row['mensa'];
				$plan['week'] = $row['week'];
			}
			
			$menu = array(
				'title'=>$row['title'],
				'date' => $row['date'],
				'menu' => explode('|',$row['menu']),
				'price' => $row['price']
			);
			array_push($menus[$row['day']],$menu);
			$i++;
		}
		$filter = function($menu){
			return count($menu)!=0;
		};
		$menus = array_filter($menus,$filter);
		$plan['menus'] = $menus;
		return $plan;
	}
	
	
	
}

?>