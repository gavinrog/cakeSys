<?php

class SystemController extends Controller {

	public $components = array(
		'Session'
	);
	public $viewClass = 'System';
	public $_pseudoActions = array();

	public function __construct($request = null, $response = null) {

		if (isset($request->params['admin'])) {
			$this->components[] = 'Admin';
		}

		parent::__construct($request, $response);
	}

	public function isAuthorized() {
		return true;
	}

	public function invokeAction(CakeRequest $request) {
		try {
			parent::invokeAction($request);
		} catch (Exception $e) {
			if (isset($this->_pseudoActions[$request->params['action']])) {
				$action = $this->_pseudoActions[$request->params['action']];
				$args = $action['args'];
				return call_user_func_array($action['method'], $args);
			}
			throw $e;
		}
	}

	public function addPseudoAction($name, $method, $args = array()) {
		if (is_callable($method)) {
			$this->_pseudoActions[$name] = compact('method', 'args');
			return true;
		}
	}

}
