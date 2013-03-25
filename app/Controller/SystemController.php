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
			),
			'loginAction' => array(
				'controller' => 'users',
				'action' => 'login',
				'admin' => false
			),
			'logoutUrl' => '/'
		),
		'Session'
	);
	public $viewClass = 'System';

	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
	}
	
	public function beforeRender(){
		$this->Auth->loginAction = array(
			'controller' => 'users',
			'action' => 'loginf',
			'admin' => false
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
