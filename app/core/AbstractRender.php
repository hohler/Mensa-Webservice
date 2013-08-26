<?php
/**
 * file: Render.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;
abstract class AbstractRender implements Render {
	
	protected $response;
	
	function __construct($slim) {
		$this->slim = $slim;
	}
	
	/**
	 * Hook method.
	 * @param unknown $object
	 */
	abstract public function handle($object);
	
	public function render($object){
		//make response object accessible for later changes
		$this->response = $this->slim->response();
		$rendered = $this->handle($object);
		$this->slim->response ()->body ($rendered);
	}
	
}
?>