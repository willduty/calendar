<?php

class UsersController extends AppController{
	var $helpers = array('Html', 'Js');
	var $name = 'Users';

	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('register', 'welcome', 'confirm_registration', 'forgot_pwd');
	}
	
	function login(){
		
       	if ($this->request->is('post')) {
			if($this->Auth->login()) {
				// clear user session items (todo, make this an option?)
				CakeSession::delete('UserValues.view');
				CakeSession::delete('UserValues.categoryId');
				
				// main calendar page
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
			
			// save new user
			$this->request->data['User']['user_group_id'] = 2;
			
			
			// validate password
			
			$json = $this->validatePasswords($this->data['User']['password1'], $this->data['User']['password2']	);
			$obj = json_decode($json);
			if(!$obj->success){
				$this->Session->setFlash($obj->errMsg, 'flashElem');
				return;
			}
			
			// set the password in the pending column and an activation token
			// later when the user responds to the activation email, the token will be deleted
			// and pending_password will be moved to password so it will be visible to login mechanism
			$encrypted_password = $this->Auth->password($this->data['User']['password1']);
			$this->request->data['User']['pending_password'] = $encrypted_password;
			$reg_token = $this->makeRegToken();
			$this->request->data['User']['registration_token'] = $reg_token;
			
			
			// save user and send registration
			if($this->User->save($this->request->data)){
				
				$user = $this->User->read();
		
				$to      = $this->request->data['User']['email'];					
				$subject = 'El Calendario Registration';
				$message = 'Hello,	
							To complete El Calendario registration, simply click the link below:<br><br>
							<a href="http://cakecalendar.phpfogapp.com/users/confirm_registration/'.$reg_token.
							'" target=_blank>-- El Calendario registration confirmation -- </a>
							<br><br>Thank you for registering. -Webmaster, El Calendario';
							
				$headers = 'From: webmaster@cakecalendar.com' . "\r\n" .
					'Reply-To: webmaster@cakecalendar.com' . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";					
					
				mail($to, $subject, $message, $headers);

				$this->redirect(array('action'=>'welcome'));
			}
		}
	}
	
	function welcome(){
		
	}
	
	function confirm_registration($reg_token){
		$user = $this->User->findByRegistrationToken($reg_token);
		$userFound = $user || FALSE;
		$this->set('userFound', $userFound);
		if($userFound){
			$user = array('User'=>$user['User']);
			$date = new DateTime();
			$this->User->id = $user['User']['id'];
			
			// make pending password official (move to password). 
			// delete activate token and set activated date
			$user['User']['password'] = $user['User']['pending_password'];
			$user['User']['pending_password'] = NULL;
			$user['User']['activated'] = $date->format('Y-m-d h:i:s');
			$user['User']['registration_token'] = NULL;
			
			$this->User->save($user);
			
			// log user in
			unset($user['User']['password']);
			$this->Auth->login($user);
			
		}

	}

	
	function view($id){
	}
	
	function edit(){
		
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		if(empty($this->data)){
			$this->data = $this->User->read();
			$this->set('user', $this->User->read());
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
				$json = $this->validatePasswords($newPwd1, $newPwd2);
				$obj = json_decode($json);
				if(!$obj->success){
					echo $json;
				}else{				
					// password ok, try to save password
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
	
	function forgot_pwd(){
	
		$this->set('new_password_created', false);
	
		if(!empty($this->request->data)){
			$data = $this->request->data;
			
			// find if user exists
			$user = $this->User->findByEmail($data['User']['email']);
		
			if(!is_array($user) || count($user) < 1){
				$this->Session->setFlash('User not found', 'flashElem');
			}
			else{
			
				$userId = $user['User']['id'];
			
				// validate new pwd
				$newPwd1 = $this->data['User']['newPassword1'];
				$newPwd2 = $this->data['User']['newPassword2'];
				$json = $this->validatePasswords($newPwd1, $newPwd2);
				$obj = json_decode($json);
				if(!$obj->success){	
					$this->Session->setFlash($obj->errMsg, 'flashElem');
				}else{				
				
					// new pwd ok
					// save new password to pending new pwd
					$hash = $this->Auth->password($newPwd1);
					$this->User->id = $userId;
					if($this->User->saveField('pending_password', $hash)){
						
						$reg_token = $this->makeRegToken();				
						$this->User->saveField('registration_token', $reg_token);
						
					//	echo $hash;
					//	echo '<br>';
					//	echo $userId;
						
						// SEND EMAIL
						//
						$user = $this->User->read();
				
						$to      = $data['User']['email'];					
						$subject = 'El Calendario Password Reset';
						$message = 'Hello,	
									To complete your El Calendario reset, click the link below:<br><br>
									<a href="http://cakecalendar.phpfogapp.com/users/confirm_registration/'.$reg_token.
									'" target=_blank>-- El Calendario Password Reset-- </a>
									<br><br>-Webmaster, El Calendario';
									
						$headers = 'From: webmaster@cakecalendar.com' . "\r\n" .
							'Reply-To: webmaster@cakecalendar.com' . "\r\n";
						$headers .= "MIME-Version: 1.0\r\n";
						$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";					
							
						mail($to, $subject, $message, $headers);
					
						$this->set('new_password_created', true);
					}
				}
			}
		}
	}
	
	
	function delete(){
		$userId = $this->Auth->user('id');
		$user = $this->User->findById($userId);
		
		if($user['User']['user_group_id'] == 1){
			echo 'you can\'t close your account, you are an admin.';
			die();
		}
		
		// logout first
		$this->Auth->logout($userId);
		
		// delete the user...
		$this->User->delete($userId, true);
		
		// the end
	}
	
	
	function validatePasswords($newPwd1, $newPwd2){
	
		// regex: alphanumeric only & at least one num & one letter
		$regex = '/^[A-Z0-9]*[0-9][A-Z][A-Z0-9]*$|^[A-Z0-9]*[A-Z][0-9][A-Z0-9]*$/i';
	
		if(trim($newPwd1) == '' || trim($newPwd2) == '' ){
			$arr = array('success' => false, 'errMsg' => "Passwords cannot be blank");
		}
	
		// check new passwords match
		else if($newPwd1 != $newPwd2){
			$arr = array('success' => false, 'errMsg' => "New passwords do not match");
		}
		
		// validate
		else if(strlen($newPwd1) < 8 || strlen($newPwd1) > 20
			|| !preg_match($regex, $newPwd1, $matches)){
			$arr = array('success' => false, 
				'errMsg' => "Password must be between 8 and 20 characters and contain at least one number.");
		}
		else
			$arr = array('success' => true);
		
		$json = json_encode($arr);
		return $json;

	}
	
	
	function makeRegToken(){
		// random token
		$reg_token = '';
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$size = strlen( $chars );
		for( $i = 0; $i < 25; $i++ ) {
			$reg_token .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $reg_token;
	}
	
}


?>
