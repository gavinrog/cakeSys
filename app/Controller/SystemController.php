<?php

class SystemController extends Controller {

	public $components = array('Admin');
	
	public $viewClass = 'System';

	public function __construct($request = null, $response = null) {


		parent::__construct($request, $response);
	}


}
