<?php

class UsersController extends AppController{
	var $helpers = array('Html', 'Js');
	var $name = 'Users';

	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('register', 'welcome');
	}
	
	function login(){
		
       	if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
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
			
				$user = $this->User->read();
							
				$to      = $this->request->data['User']['email'];
				$subject = 'el calendario registration';
				$message = 'Hello,	
							To complete your registration, simply click the link below:\n\n
							<a href="localhost/users/confirm_registration/'.$user['User']['id'].'" target=_blank>confirm link</a>
							\n\nThank you for registering. -Webmaster, El Calendario';
							
				$headers = 'From: webmaster@cakecalendar.com' . "\r\n" .
					'Reply-To: webmaster@cakecalendar.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);

				$this->redirect(array('action'=>'welcome'));
			}
		}
	}
	
	function welcome(){
		
	}
	
	function confirm_registration($id){
		echo 'hello dere';
		die();
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
			if($this->User->save($this->data)){
				$this->Session->setFlash('user changes saved', 'flashElem');
			}
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
			$oldPwd = $this->data['UserResetPwdForm']['oldPassword'];
			if($this->Auth->password($oldPwd) != $user['User']['password']){
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
					die();
				}
				
				// validate
				$regex = '/^[A-Z0-9]*[0-9][A-Z][A-Z0-9]*$|^[A-Z0-9]*[A-Z][0-9][A-Z0-9]*$/i';
				if(strlen($newPwd1) < 8 || strlen($newPwd1) > 20
					|| !preg_match($regex, $newPwd1, $matches)){
					$arr = array('success' => false, 
						'errMsg' => "Password must be between 8 and 20 characters and contain at least one number.");
					$json = json_encode($arr);
					echo $json;
					die();
				}
				
				// try to save password
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
		die();
	}
	
	
	function delete($id){

	}
	
}


?>
