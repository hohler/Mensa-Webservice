<?php

namespace app\service;

class JSONRender implements Render {
	function __construct() {
	}
	
	public function render($object) {
		$options = 0;
		
		if (version_compare ( PHP_VERSION, '5.4.0' ) >= 0) {
			$options = JSON_PRETTY_PRINT;
		}
		
		return json_encode ( $object, $options );
	}
}

?>