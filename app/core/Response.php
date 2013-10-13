<?php
/**
 * file: Response.php
 * @author Alexander Rüedlinger
 *
 */
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
	
	public function setCode($code){
		$this->result['code'] = $code;
	}
	
	public function setContent($content){
		$this->result['content'] = $content;
	}
	
	public function setMsg($msg){
		$this->result['msg'] = $msg;
	}
	
	public function setReason($reason){
		$this->result['reason'] = $reason;
	}
}

?>
