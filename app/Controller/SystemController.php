<?php

class SystemController extends Controller {

	public $components = array(
		'Admin',
		'Auth' => array(
			'authorize' => array(
				'Controller'
			),
		)
	);
	
	public $viewClass = 'System';

	public function __construct($request = null, $response = null) {
		
		parent::__construct($request, $response);		
;	}
	
	
	
	public function isAuthorized(){
		die('test');
	}
	
	
	public function invokeAction(CakeRequest $request) {
		try {
			parent::invokeAction($request);			
		} catch (Exception $e) {
			if($this->Admin->isAdmin()){
				return true;
			}
			throw $e;
		}
	}

}
