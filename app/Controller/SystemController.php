<?php

abstract class SystemController extends Controller {

	public $components = array('Admin');
	public $viewClass = 'System';

	public function __construct($request = null, $response = null) {


		parent::__construct($request, $response);
	}

	public function invokeAction(CakeRequest $request) {
		try {
			parent::invokeAction($request);
		} catch (Exception $e) {
			
		}
	}

}
