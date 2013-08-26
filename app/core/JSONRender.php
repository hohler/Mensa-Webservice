<?php
/**
 * file: JSONRender.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;

class JSONRender extends AbstractRender {
	
	public function handle($object) {
		//set content type
		$this->response['Content-type'] = 'application/json; charset=utf-8';
		
		$json = json_encode($object);
		$rendered = Helper::json_pretty_string($json);
		$callback = $this->slim->request()->params('callback');
		
		//add support for jsonp!
		if(!empty($callback))
			$rendered = $callback.'('.$rendered.');';
		
		return $rendered;
	}
}

?>