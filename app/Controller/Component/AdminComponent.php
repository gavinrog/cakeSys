<?php

class AdminComponent extends Component {

	private $_controller;
	private $_prefix = 'admin';
	private $_adminModel;
	private $_redirect = array(
		'action' => 'index'
	);
	private $_request;
	private $_actions = array(
		'login',
		'add',
		'delete',
		'edit',
		'index',
		'view',
	);

	public function initialize(Controller &$controller) {
		$this->_controller = $controller;
		$this->_request = $controller->request;
		if (!empty($controller->modelClass)) {
			$this->_adminModel = $controller->{$controller->modelClass};
			foreach ($this->_adminModel->Behaviors->enabled() as $behavior) {
				switch ($behavior) {
					case 'Tree' :
						$this->_actions[] = 'move_up';
						$this->_actions[] = 'move_down';
						break;
				}
			}
		}
		$this->_addControllerActions();
		$this->_admin();
	}

	private function _admin() {

		$this->_controller->viewClass = 'Admin';
		$this->_controller->set(array(
			'title_for_layout' => Inflector::humanize($this->_request->action),
			'modelClass' => $this->_controller->modelClass,
			'primaryKey' => $this->_adminModel->primaryKey,
			'displayField' => $this->_adminModel->displayField,
			'singularVar' => Inflector::variable($this->_controller->modelClass),
			'pluralVar' => Inflector::variable($this->_controller->name),
			'singularHumanName' => Inflector::humanize($this->_controller->modelClass),
			'pluralHumanName' => Inflector::humanize($this->_controller->name),
			'scaffoldFields' => array_keys($this->_adminModel->schema()),
			'associations' => $this->_associations()
				)
		);
	}

	private function _addControllerActions() {
		foreach ($this->_actions as $action) {
			switch ($action) {
				case 'add' :
				case 'edit':
					$this->_controller->addPseudoAction($this->_prefix . '_' . $action, array($this, Inflector::variable($this->_prefix . '_' . 'save')), array($action));
					break;
				default:
					$this->_controller->addPseudoAction($this->_prefix . '_' . $action, array($this, Inflector::variable($this->_prefix . '_' . 'save')));
					break;
			}
		}
	}

	public function adminLogin() {
		
	}

	public function adminIndex() {
		$pages = $this->_adminModel->find('all');
		$this->_controller->set(compact('pages'));
	}

	public function adminView() {
		
	}

	public function adminSave($type) {
		if ($type == 'edit') {
			$this->_adminModel->id = $this->_controller->request->params['pass'][0];
			if (!$data = $this->_adminModel->read()) {
				
			}
			$this->_controller->data = $data;
		} else {
			$this->_adminModel->create();
		}
	}

	public function adminDelete() {
		
	}

	private function _associations() {
		$keys = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
		$associations = array();
		foreach ($keys as $key => $type) {
			foreach ($this->_adminModel->{$type} as $assocKey => $assocData) {
				$associations[$type][$assocKey]['primaryKey'] =
						$this->_adminModel->{$assocKey}->primaryKey;

				$associations[$type][$assocKey]['displayField'] =
						$this->_adminModel->{$assocKey}->displayField;

				$associations[$type][$assocKey]['foreignKey'] =
						$assocData['foreignKey'];

				$associations[$type][$assocKey]['controller'] =
						Inflector::pluralize(Inflector::underscore($assocData['className']));

				if ($type == 'hasAndBelongsToMany') {
					$associations[$type][$assocKey]['with'] = $assocData['with'];
				}
			}
		}
		return $associations;
	}

}
