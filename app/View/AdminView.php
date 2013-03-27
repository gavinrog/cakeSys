<?php

App::uses('SystemView', 'View');

class AdminView extends SystemView {

	protected function _getViewFileName($name = null) {
				
		if ($name === null) {
			$name = $this->action;
		}
		$name = Inflector::underscore($name);
		$prefixes = Configure::read('Routing.prefixes');

		if (!empty($prefixes)) {
			foreach ($prefixes as $prefix) {
				if (strpos($name, $prefix . '_') !== false) {
					$name = substr($name, strlen($prefix) + 1);
					break;
				}
			}
		}

		if ($name === 'add' || $name == 'edit') {
			$name = 'form';
		}

		$scaffoldAction = 'scaffold.' . $name;

		if (!is_null($this->subDir)) {
			$subDir = strtolower($this->subDir) . DS;
		} else {
			$subDir = null;
		}

		$names[] = $this->viewPath . DS . $subDir . $scaffoldAction;
		$names[] = 'Scaffolds' . DS . $subDir . $name;

		$paths = $this->_paths($this->plugin);
		$exts = array($this->ext);
		if ($this->ext !== '.ctp') {
			array_push($exts, '.ctp');
		}
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				foreach ($names as $name) {
					if (file_exists($path . $name . $ext)) {
						return $path . $name . $ext;
					}
				}
			}
		}

		if ($name === 'Scaffolds' . DS . $subDir . 'error') {
			return CAKE . 'View' . DS . 'Errors' . DS . 'scaffold_error.ctp';
		}

		throw new MissingViewException($paths[0] . $name . $this->ext);
	}

}

