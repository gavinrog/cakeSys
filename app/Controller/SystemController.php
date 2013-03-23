<?php

abstract class SystemController extends AppController {
	
	
	public $components = array('Admin');
	
	
	public function __construct($request = null, $response = null){				
		
		
		parent::__construct($request, $response);
	}
	
	
}
