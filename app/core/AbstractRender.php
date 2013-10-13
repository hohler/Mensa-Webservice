<?php
/**
 * file: AbstractRender.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;
use app\core\Render;
abstract class AbstractRender implements Render {
	
	protected $request;
	protected $response;
	
	function __construct($request,$response) {
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * Hook method.
	 * @param unknown $object
	 */
	abstract public function handle($object);
	
	public function render($object){
		$rendered = $this->handle($object);
		$this->response->body ($rendered);
	}
	
}
?>
