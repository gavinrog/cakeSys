<?php

class AdminComponent extends Component {

	public $controller;
	private $_prefix = 'admin';
	private $_adminModel;
	
	private $_redirext = array(
		'action' => 'index'
	);
	
	private $_actions = array(
		'add',
		'delete',
		'edit',
		'index',
		'view'
	);

	public function initialize(Controller &$controller) {
		$this->controller = $controller;
		if (!empty($controller->modelClass)) {
			$this->adminModel = $controller->{$controller->modelClass};
			foreach ($this->adminModel->Behaviors->enabled() as $behavior) {
				switch ($behavior) {
					case 'Tree' :
						$this->_actions[] = 'move_up';
						$this->_actions[] = 'move_down';
						break;
				}
			}
			if ($this->isAdmin()) {
				$this->controller->viewClass = 'Admin';
				$this->_admin();
			}
		}
	}

	private function _admin() {

		$count = count($this->_passedVars);
		for ($j = 0; $j < $count; $j++) {
			$var = $this->_passedVars[$j];
			$this->{$var} = $controller->{$var};
		}

		$this->redirect = array('action' => 'index');

		$this->modelClass = $this->controller->modelClass;
		$this->modelKey = $this->controller->modelKey;

		if (!is_object($this->controller->{$this->modelClass})) {
			throw new MissingModelException($this->modelClass);
		}
		$title = Inflector::humanize($this->controller->request->action) . ' :: ' . $this->scaffoldTitle;
		$modelClass = $this->controller->modelClass;
		$primaryKey = $this->adminModel->primaryKey;
		$displayField = $this->adminModel->displayField;
		$singularVar = Inflector::variable($modelClass);
		$pluralVar = Inflector::variable($this->controller->name);
		$singularHumanName = Inflector::humanize(Inflector::underscore($modelClass));
		$pluralHumanName = Inflector::humanize(Inflector::underscore($this->controller->name));
		$scaffoldFields = array_keys($this->adminModel->schema());
		$associations = $this->_associations();
		$this->controller->set(compact(
						'title_for_layout', 'modelClass', 'primaryKey', 'displayField', 'singularVar', 'pluralVar', 'singularHumanName', 'pluralHumanName', 'scaffoldFields', 'associations'
				));
		$this->controller->set('title_for_layout', $title);

		if ($this->controller->viewClass) {
			$this->controller->viewClass = 'Scaffold';
		}
		$this->_validSession = (
				isset($this->controller->Session) && $this->controller->Session->valid() != false
				);

		$this->_adminAction();
	}

	private function _adminAction() {
		$request = $this->controller->request;
		//strip out admin prefix
		$action = str_replace($this->_prefix . '_', '', $request->params['action']);
		if (in_array($action, $this->_actions)) {
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
				'controller' => $this->controller->name,
				'action' => $request->action
			));
		}
	}

	private function _adminIndex() {
		
	}

	private function _adminView() {
		
	}

	private function _adminSave($type) {
		die($type);
	}

	private function _adminDelete() {
		
	}

	public function isAdmin() {
		if (!empty($this->controller->params[$this->_prefix])) {
			return $this->controller->params[$this->_prefix];
		}
		return false;
	}

	private function _associations() {
		$keys = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
		$associations = array();

		foreach ($keys as $key => $type) {
			foreach ($this->adminModel->{$type} as $assocKey => $assocData) {
				$associations[$type][$assocKey]['primaryKey'] =
						$this->adminModel->{$assocKey}->primaryKey;

				$associations[$type][$assocKey]['displayField'] =
						$this->adminModel->{$assocKey}->displayField;

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
