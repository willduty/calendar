<?php

class UsersController extends AppController{
	var $helpers = array('Html', 'Js');
	var $name = 'Users';

	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('register');
	}
	
	function login(){
		
       		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				// $this->Session->setFlash(__('Username or password is incorrect'), 'flashElem', array(), 'auth');
				$this->Session->setFlash("Login Failed", 'flashElem');
				
			}
		}
 	}

	function logout(){
		$this->redirect($this->Auth->logout());
	}
	
	function index(){
		$this->set('users', $this->User->find('all'));
	}
	
	function add(){
	}
	
	function register(){
		if(!empty($this->data)){
			
			$this->request->data['User']['user_group_id'] = 2;
			$this->request->data['User']['password'] = $this->Auth->password($this->data['User']['password1']);
			
			if($this->User->save($this->request->data)){
				$this->redirect(array('action'=>'welcome'));
			}
		}
	}
	
	function welcome(){
		
	}
	
	function view($id){
	}
	
	function edit(){
		
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		if(empty($this->data)){
			$this->data = $this->User->read();
		}
		else{
			$this->User->save($this->data);
			$this->Session->setFlash('user changes saved');
			$this->redirect(array('action' => 'index'));
		}
	}
	
	
	// reset user password
	function reset_pwd(){
		$id = $this->Auth->user('id');
		
		$this->recursive = 0;
		$this->User->id = $id;
		$user = $this->User->read();
		
		if($this->RequestHandler->isAjax() && $_SERVER['REQUEST_METHOD'] == 'POST'){
			
			// check old password
			$oldPwdHash = $this->data['UserResetPwdForm']['oldPassword'];
			if($this->Auth->password($oldPwdHash) != $user['User']['password']){
				$arr = array('success' => false, 'errMsg' => "old password invalid");
				$json = json_encode($arr);
				echo $json;
			}
			else{
				// check new passwords match
				$newPwd1 = $this->data['UserResetPwdForm']['newPassword1'];
				$newPwd2 = $this->data['UserResetPwdForm']['newPassword2'];
				if($newPwd1 != $newPwd2){
					$arr = array('success' => false, 'errMsg' => "new passwords do not match");
					$json = json_encode($arr);
					echo $json;
				}
				else{
					// validate new password
					
					
					
					
					
					/*
					
					
					
					'password1' => array(
						'alphaNumeric' => array(
							'rule' => 'alphaNumeric',
							'required' => true,
							'message' => 'Password must contain letters and numbers only'
						),
						'between' => array(
							'rule' => array('between', 8, 12),
							'message' => 'Password must be between 8 and 12 characters'
						)
					),
					
					'password2' => array(
						'alphaNumeric' => array(
							'rule' => 'alphaNumeric',
							'required' => true,
							'message' => 'Password must contain letters and numbers only'
						),
						'between' => array(
							'rule' => array('between', 6, 12),
							'message' => 'Password must be between 6 and 12 characters'
						),
						'mustMatch' => array(
							'rule' => 'mustMatch',
							'message' => 'Passwords do not match.'
						)
					)
				
					*/
					
					
					
					
					$user['User']['password'] = $this->Auth->password($newPwd1);
					if($this->User->save($user)){
						$arr = array('success' => true, 'errMsg' => "");
						$json = json_encode($arr);
						echo $json;
					}
					else{
						$arr = array('success' => false, 'errMsg' => "could not save password" . nl2br(print_r($user, true)));
						$json = json_encode($arr);
						echo $json;
					}
				}
			}
		}
		die();
	}
	
	
	function delete($id){

	}
	
}


?>
