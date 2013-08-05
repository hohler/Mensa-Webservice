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
		
		$this->menuplanBuilder = new MenuplanBuilder();
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
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensa';
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
		$sql = 'SELECT id,name,street,plz,lat,lon FROM mensa WHERE id=:id';
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
		$menu = implode('|',$menu);
		$sql = 'INSERT INTO menu (mensa_id,title,date,menu,created)'
				.' VALUES (:mensa_id,:title,:date,:menu,NOW())';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId,PDO::PARAM_INT);
		$stmt->bindValue(':title',$title,PDO::PARAM_STR);
		$stmt->bindValue(':menu',$menu,PDO::PARAM_STR);
		$stmt->bindValue(':date',$date,PDO::PARAM_STR);
		return $stmt->execute();
	}
	
	public function doesMenuExist($id,$menuTitle,$dateString){
		$sql = 'SELECT EXISTS('
				.' SELECT 1 FROM `menu`'
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
		$sql = 'SELECT `title`,`date`,`menu`,`name` FROM `menu`'
				.' INNER JOIN `mensa` ON mensa.id = menu.mensa_id'
				.' WHERE mensa_id=:mensa_id and `date`=:date';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->bindValue(':date',$date, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$plan = $this->menuplanBuilder->buildDailyplan($rows);
		return $plan;
	}
	
	public function queryWeeklyMenuplan($mensaId){
		$sql = "SELECT `title`, `menu`,  `date`, `name`, WEEK(  `date` ) AS  `week`,"
				." DATE_FORMAT(  `date` ,  '%W' ) AS  `day` " 
				." FROM  `menu` INNER JOIN mensa ON mensa.id = menu.mensa_id" 
				." WHERE mensa_id =:mensa_id HAVING  `week` = WEEK( CURDATE( ) )";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':mensa_id',$mensaId, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$plan = $this->menuplanBuilder->buildWeeklyplan($rows);
		return $plan;
	}
}
?>