<?php
namespace app\service;
use PDO;
class DataSource {
	private $db;
	
	function __construct($config){
		$host = $config['host'];
		$dbname = $config['dbname'];
		$user = $config['user'];
		$pass = $config['password'];
		$this->db = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
	}
	
	function __destruct(){
		$this->db = null;
	}
}

?>