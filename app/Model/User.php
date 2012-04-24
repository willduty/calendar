
<?php

class User extends AppModel{
	var $name = "User";
	
	// var $hasMany = array('Entry' => array( 'className'  => 'Entry'));

	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 3),
			'allowEmpty' => false),
		
		'email' => array(
			'rule'=>'email',
			'message'=>'invalid email',
			'allowEmpty'=>false)
	);
	
	function mustMatch(){
		if($this->data['User']['password1'] != $this->data['User']['password2']){
		//	debug($this->data);
			return false;
		}
		return true;
	}
	
}


?>