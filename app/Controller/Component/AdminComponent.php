<?php

class AdminComponent extends Component {

	public $controller;
	public $scaffold = 'admin';
	private $adminModel;
	private $_actions = array(
		'add',
		'edit',
		'delete',
		'index'
	);

	public function initialize(Controller &$controller) {
		$this->controller = $controller;
		if (!empty($controller->modelClass)) {
			$this->controller->scaffold = $this->scaffold;
			$this->adminModel = $controller->{$controller->modelClass};			
			foreach($this->adminModel->Behaviors->enabled() as $behavior){
				switch($behavior){
					case 'Tree' : 
						$this->_actions[] = 'move_up';
						$this->_actions[] = 'move_down';
					break;
				}
			}
			if($this->isAdmin()){
				//$this->controller->layout = 'admin';
			}
		}
	}

	public function isAdmin() {		
		if(!empty($this->controller->params['admin'])){
			return $this->controller->params['admin'];
		}
		return false;		
	}

}
