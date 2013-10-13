<?php
/**
 * file: MainController.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;
use app\model\DataSource;

class MainController {
	protected $ds;
	protected $request;
	protected $response;
	protected $config;
	private $pullTokens;
	private $pushTokens;
	
	function __construct($config,$pullTokens,$ds,$request,$response){
		$this->config = $config;
		$this->pushTokens = $config['tokens']; //FIX THAT!
		$this->pullTokens = $pullTokens;
		$this->ds = $ds;
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * return push token 
	 */
	public function getPushToken(){
		$wday = intval(date('w'));
		return $this->pushTokens[$wday];
	}
	
	/**
	 * check if token is okay
	 */
	public function checkToken(){
		if($this->request->isGet()){
			$tok = $this->request->params('tok');
			$found = false;
			foreach($this->pullTokens as $token){
				if(strpos($token,$tok)!==false)
					return true;
			}
		} else { //post, put, head or patch handle token validation by itself!
				 //FIX THAT!
			return true;
		}
		return $found;
	}
}

?>
