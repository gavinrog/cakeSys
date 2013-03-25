<?php

class SystemController extends Controller {

	public $components = array(
		'Admin',
		'Auth' => array(
			'authenticate' => array(
				'Form'
			),
			'authorize' => array(
				'Controller'
			)
		),
		'Session'
	);
	public $viewClass = 'System';

	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
	}
	
	public function beforeFilter(){
		$this->Auth->loginAction = array(
			'controller' => 'users',
			'action' => 'login',
			'admin' => $this->Admin->isAdmin()
		);
	}

	public function isAuthorized() {
		return true;
	}

	public function invokeAction(CakeRequest $request) {
		try {
			parent::invokeAction($request);
		} catch (Exception $e) {
			if ($this->Admin->isAdmin()) {
				return true;
			}
			throw $e;
		}
	}

}
