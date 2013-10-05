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
	
	function __construct($config,$tokens,$ds,$request,$response){
		$this->config = $config;
		$this->tokens = $tokens;
		$this->ds = $ds;
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * check if token is okay
	 */
	public function checkToken(){
		$tok = $this->request->params('tok');
		$found = false;
		foreach($this->tokens as $token){
			if(strpos($token,$tok)!==false)
				return true;
		}
		return $found;
	}
	
}
?>
