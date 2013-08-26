<?php
/**
 * file: Render.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;
abstract class AbstractRender implements Render {
	
	abstract public function handle($object);
	
	public function render($object){
		$res = $this->slim->response();
		$res['Content-type'] = 'application/json; charset=utf-8';
		$rendered = $this->handle($object);
		$this->slim->response ()->body ($rendered);
	}
	
}
?>