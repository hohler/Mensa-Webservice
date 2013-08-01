<?php
namespace app\core;
class Response {
	public $result;
	
	private static $codes = array (
			200 => 'OK',
			201 => 'Created',
			404 => 'Not Found',
			400 => 'Bad Request' 
	);
	
	function __construct($content,$code=200,$msg=''){
		if(array_key_exists($code,self::$codes)){
			$msg = self::$codes[$code];
		} 
		$content = Helper::utf8_string_array_encode($content);
		$this->result = array('content'=>$content,'code'=>$code,'msg'=>$msg);
	}
}
?>