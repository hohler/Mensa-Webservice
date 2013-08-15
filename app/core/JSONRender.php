<?php
/**
 * file: JSONRender.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;

class JSONRender implements Render {
	function __construct($slim) {
		$this->slim = $slim;
	}
	
	public function render($object) {
		$res = $this->slim->response();
		$res['Content-type'] = 'application/json; charset=utf-8';
		$rendered = json_encode ( $object);
		$this->slim->response ()->body ( Helper::json_pretty_string($rendered)) ;
	}
}

?>