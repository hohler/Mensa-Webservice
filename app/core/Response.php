<?php
namespace app\core;
class Response {
	public $response;
	
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
		$content = Helper::utf8_string_array_decode($content);
		$this->response = array('content'=>$content,'code'=>$code,'msg'=>$msg);
	}
}
?>