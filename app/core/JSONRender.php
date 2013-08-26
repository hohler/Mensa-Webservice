<?php
/**
 * file: JSONRender.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;

class JSONRender extends AbstractRender {
	
	function __construct($slim) {
		$this->slim = $slim;
	}
	
	public function handle($object) {
		$json = json_encode ( $object);
		$rendered = Helper::json_pretty_string($json);
		$callback = $this->slim->request()->params('callback');
		
		//add support for jsonp!
		if(!empty($callback)){
			$rendered = $callback.'('.$rendered.');';
		}
		return $rendered;
	}
}

?>