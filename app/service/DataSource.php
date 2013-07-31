<?php
namespace app\service;
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
}

?>