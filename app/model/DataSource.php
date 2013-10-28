<?php
/**
 * file: Datasource.php
 * @author Alexander Rüedlinger
 * @date 2013
 *
 */
namespace app\model;
use PDO;
class DataSource {
	private $db;
	private static $instance = null;
	
	/**
	 * Create a data source object by means of the specified 
	 * config values in $config.
	 */
	private function __construct($config){
		$driver = $config['driver'];
		$host = $config['host'];
		$dbname = $config['dbname'];
		$user = $config['user'];
		$pass = $config['password'];
		
		$this->db = new PDO ( "{$driver}:host={$host};dbname={$dbname}", $user, $pass, array(
				PDO::ATTR_PERSISTENT => true 
		));
	}
	
	function __destruct(){
		$this->db = null;
	}
	
	/**
	 * Get a data source instance.
	 */
	function getInstance(){
		if(self::$instance==null){
			throw new \Exception('Datasource instance has not been created!');
		}
		return self::$instance;
	}
	
	/**
	 * Create an instance of a data source object.
	 * 
	 * @param $config a config array
	 * @return a data source object
	 */
	function createInstance($config){
		if($config==null){
			throw new \Exception('Invalid config parameter!');
		}
		
		if(self::$instance==null && $config!=null){
			try {
				self::$instance = new DataSource($config['db']);
			}catch(PDOException $e){
				throw new \Exception('Cannot connect to database!');
			}
		}
		return self::$instance;
	}
	
	/**
	 * Returns a list of canteens.
	 * 
	 * @return array
	 */
	public function queryMensas(){
		$sql = 'SELECT * FROM view_mensa';
		$stmt = $this->db->query($sql);
		$mensas = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $mensas;
	}
	
	/**
	 * Returns a canteen by its id.
	 * 
	 * @param $id
	 * @return mixed
	 */
	public function queryMensaById($id){
		$sql = 'SELECT * FROM view_mensa WHERE id = :id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':id',$id, PDO::PARAM_INT);
		$stmt->execute();
		$mensa = $stmt->fetch(PDO::FETCH_ASSOC);
		return $mensa;
	}
	
	/**
	 * Returns a canteen by its name.
	 * 
	 * @param $name
	 * @return mixed
	 */
	public function queryMensaByName($name){
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensa'
				.' WHERE id_name LIKE :name OR name LIKE :name LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':name',"%{$name}%", PDO::PARAM_STR);
		$stmt->execute();
		$mensa = $stmt->fetch(PDO::FETCH_ASSOC);
		return $mensa;
	}
	
	/**
	 * Creates a menu entry. 
	 */
	public function createMenu($mensaId,$title,$date,$menu){
		$menu = implode('|',array_map('trim',$menu));
		$sql = 'INSERT INTO menu (mensa_id, title, `date`, menu, created, modified)'
				.' VALUES (:mensa_id, :title, :date, :menu, NOW(), NOW())';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId,PDO::PARAM_INT);
		$stmt->bindValue(':title',$title,PDO::PARAM_STR);
		$stmt->bindValue(':menu',$menu,PDO::PARAM_STR);
		$stmt->bindValue(':date',$date,PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	/**
	 * Updates a menu entry.
	 */
	public function updateMenu($menuId,$menu){
		$menu = implode('|',array_map('trim',$menu));
		$sql = 'UPDATE menu SET menu = :menu, modified = NOW()'
				.' WHERE id = :menu_id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':menu_id',$menuId,PDO::PARAM_INT);
		$stmt->bindValue(':menu',$menu,PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	/**
	 * Checks if a menu already exists.
	 */
	public function doesMenuExist($id,$menuTitle,$dateString){
		$sql = 'SELECT EXISTS( SELECT 1 FROM `menu` WHERE mensa_id = :mensa_id AND `date` = :date_str AND title = :title) AS `result`';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$id,PDO::PARAM_INT);
		$stmt->bindValue(':date_str',$dateString,PDO::PARAM_STR);
		$stmt->bindValue(':title',$menuTitle,PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['result']==1 ? true: false; 
	}
	
	/**
	 * Queries a menu given by a menu title and date string.
	 * 
	 * @param $id canteen id
	 * @param $menuTitle menu title
	 * @param $date a date e.g. 2013-12-12
	 */
	public function queryMenu($id,$menuTitle,$date){
		$sql = 'SELECT * FROM `menu` WHERE mensa_id = :mensa_id AND `date` = :date AND title = :title';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$id,PDO::PARAM_INT);
		$stmt->bindValue(':date_str',$date,PDO::PARAM_STR);
		$stmt->bindValue(':title',$menuTitle,PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}
	
	/**
	 * Queries a menu plan given by a date.
	 * 
	 * @param $mensaId canteen id
	 * @param $date e.g.: 2013-12-12
	 */
	public function queryDailyMenuplanByDate($mensaId,$date){
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id AND `date` = :date';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->bindValue(':date',$date, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildDailyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns the previous daily meal plan.
	 *
	 * @param $mensaId canteen id
	 * @return array
	 */
	public function queryPreviousDailyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_prev_menu WHERE mensa_id = :mensa_id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildDailyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns the current weekly meal plan as an array.
	 * 
	 * @param $mensaId canteen id
	 * @return array
	 */
	public function queryWeeklyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id AND `yearweek` = YEARWEEK( CURDATE( ),1)';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns the weekly meal plan on a specific week.
	 * 
	 * @param $mensaId canteen id
	 * @param $week day of week e.g.: monday
	 * @return array
	 */
	public function queryWeeklyMenuplanByWeek($mensaId,$week){
		$yearweek = intval(date("Y").$week);
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id AND `yearweek` = :yearweek';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->bindValue(':yearweek',$yearweek, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns the weekly meal plan on a specific week and day.
	 * 
	 * @param $mensaId
	 * @param $week e.g.: 44
	 * @param $day e.g.: monday
	 * @return array
	 */
	public function queryWeeklyMenuplanByWeekAndDay($mensaId,$week,$day){
		$yearweek = intval(date("Y").$week);
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id AND `yearweek` = :yearweek AND day=:day';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->bindValue(':yearweek',$yearweek, PDO::PARAM_INT);
		$stmt->bindValue(':day',$day, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns the previous weekly meal plan.
	 *
	 * @param $mensaId canteen id
	 * @return array
	 */
	public function queryPreviousWeeklyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_prev_menu WHERE mensa_id = :mensa_id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns the next weekly meal plan.
	 *
	 * @param $mensaId canteen id
	 * @return array
	 */
	public function queryNextWeeklyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_next_menu WHERE mensa_id = :mensa_id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
	
	/**
	 * Returns a list of update statuses.
	 * 
	 */
	public function queryMensasUpdates(){
		$sql = 'SELECT * FROM view_update';
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$updates = array();
		foreach ($rows as $row){
			$timestamp = max(strtotime($row['created']),strtotime($row['modified']));
			$datetime = date('Y-m-d H:i:s',$timestamp);
			$update = array(
				'id' => $row['id'],
				'mensa' => $row['name'],
				'timestamp' => $timestamp,
				'datetime' => $datetime,
				'active' => $row['active']
			);
			array_push($updates, $update);
		}
		return $updates;
	}
}
?>
