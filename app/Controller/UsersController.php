<?php

class UsersController extends AppController {

	public function beforeFilter(){
		$allowed = array('login','logout');
		foreach($allowed as $allow){
			$this->Auth->allow($allow);
		}
	}
	
	public function login(){
		if($this->request->data){
			if($this->Auth->login()){
				return $this->redirect($this->Auth->redirectUrl);
			}
			$this->Session->setFlash('wrong details');
		}
	}
	
	public function logout(){
		$this->Auth->logout();
		$this->redirect($this->Auth->logoutUrl);
	}

}
