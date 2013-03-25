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
		'add',
		'delete',
		'edit',
		'index',
		'view'
	);

	public function initialize(Controller &$controller) {
		$this->_controller = $controller;
		$this->_request = $controller->request;
		if ($this->isAdmin()) {
			//sets scaffold to true for auth component startup checks//
			$this->_controller->scaffold = true;
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
			$this->_admin();
		}
	}

	private function _admin() {
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
		));

		$this->_adminAction();
	}

	private function _adminAction() {
		$action = str_replace($this->_prefix . '_', '', $this->_request->params['action']);
		if (in_array($action, $this->_actions)) {
			$this->_controller->viewClass = 'Admin';
			switch ($action) {
				case 'add':
				case 'edit' :
					$this->_adminSave($action);
					break;
				case 'delete':
					$this->_adminDelete();
					break;
				case 'index' :
					$this->_adminIndex();
					break;
				case 'view' :
					$this->_adminView();
					break;
			}
		} else {
			throw new MissingActionException(array(
				'controller' => $this->_controller->name,
				'action' => $this->_request->action
			));
		}
	}

	private function _adminIndex() {
		
	}

	private function _adminView() {
		
	}

	private function _adminSave($type) {
		
	}

	private function _adminDelete() {
		
	}

	public function isAdmin() {
		if (!empty($this->_controller->params[$this->_prefix])) {
			return $this->_controller->params[$this->_prefix];
		}
		return false;
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
