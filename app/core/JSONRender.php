<?php
namespace app\core;

class JSONRender implements Render {
	function __construct($slim) {
		$this->slim = $slim;
	}
	
	public function render($object) {
		$options = 0;
		if (version_compare ( PHP_VERSION, '5.4.0' ) >= 0) {
			$options = JSON_PRETTY_PRINT;
		}
		$res = $this->slim->response();
		$res['Content-type'] = 'application/json; charset=utf-8';
		$rendered = json_encode ( $object, $options );
		$this->slim->response ()->body ( Helper::json_pretty_string($rendered)) ;
	}
}

?>