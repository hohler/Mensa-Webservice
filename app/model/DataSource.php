<?php
/**
 * file: Datasource.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\model;
use PDO;
class DataSource {
	private $db;
	private static $instance = null;
	private $menuplanBuilder;
	
	private function __construct($config){
		$driver = $config['driver'];
		$host = $config['host'];
		$dbname = $config['dbname'];
		$user = $config['user'];
		$pass = $config['password'];
		$this->db = new PDO ( "{$driver}:host={$host};dbname={$dbname}", $user, $pass, array (
				PDO::ATTR_PERSISTENT => true 
		) );
	}
	
	function __destruct(){
		$this->db = null;
	}
	
	function getInstance(){
		if(self::$instance==null)
			throw new \Exception("Datasource instance has no been created!");
		return self::$instance;
	}
	
	function createInstance($config){
		if($config==null)
			throw new \Exception('$config parameter is null!');
		
		if(self::$instance==null && $config!=null)
			self::$instance = new DataSource($config['db']);
		return self::$instance;
	}
	
	/**
	 * 
	 * @return multitype:
	 */
	public function queryMensas(){
		$sql = 'SELECT * FROM view_mensa';
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
		$sql = 'SELECT * FROM view_mensa WHERE id = :id';
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
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensa'
				.' WHERE id_name LIKE :name OR name LIKE :name LIMIT 1';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':name',"%{$name}%", PDO::PARAM_STR);
		$stmt->execute();
		
		$mensa = $stmt->fetch(PDO::FETCH_ASSOC);
		return $mensa;
	}
	
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
	
	public function updateMenu($menuId,$menu){
		$menu = implode('|',array_map('trim',$menu));
		$sql = 'UPDATE menu SET menu = :menu, modified = NOW()'
				.' WHERE id = :menu_id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':menu_id',$menuId,PDO::PARAM_INT);
		$stmt->bindValue(':menu',$menu,PDO::PARAM_STR);
		
		return $stmt->execute();
	}
	
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
	
	public function queryMenu($id,$menuTitle,$dateString){
		$sql =  'SELECT * FROM `menu` WHERE mensa_id = :mensa_id AND `date` = :date_str AND title = :title';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$id,PDO::PARAM_INT);
		$stmt->bindValue(':date_str',$dateString,PDO::PARAM_STR);
		$stmt->bindValue(':title',$menuTitle,PDO::PARAM_STR);
		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}
	
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
	
	public function queryLatestDailyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id HAVING `date` = (SELECT MAX(`date`) FROM view_menu WHERE mensa_id = :mensa_id) ' ;
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$plan = MenuplanBuilder::buildDailyplan($rows);
		return $plan;
	}
	
	public function queryWeeklyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id AND `yearweek` = YEARWEEK( CURDATE( ),1)';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
	
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
	
	public function queryLatestWeeklyMenuplan($mensaId){
		$sql = 'SELECT * FROM view_menu WHERE mensa_id = :mensa_id HAVING `yearweek` = (SELECT MAX(YEARWEEK(date,1)) FROM view_menu WHERE mensa_id = :mensa_id)';
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$plan = MenuplanBuilder::buildWeeklyplan($rows);
		return $plan;
	}
}
?>