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
	
	public function getCanteens(){
		$sql = 'SELECT * FROM mensa';
		$stmt = $this->db->query($sql);
		$canteens = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $canteens;
	}
	
	public function getCanteen($id){
		$sql = 'SELECT * FROM mensa WHERE id=:id';
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':id',$id, PDO::PARAM_INT);
		$stmt->execute();
		$canteen = $stmt->fetch(PDO::FETCH_ASSOC);
		return $canteen;
	}
	
}

?>