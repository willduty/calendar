
<?php

class Entry extends AppModel{
	
	var $name = "Entry";
	// var $belongsTo = "Category, User";
	var $belongsTo = "Category";
	
	var $hasMany = array(
		"Date" => array(
			'className' => 'Date',
			'dependent' => true),
		"Reminder" => array(
			'className' => 'Reminder',
			'dependent' => true)
			
		);
	
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'allowEmpty' => false),
		'email' => array(
			'rule'=>'email',
			'message'=>'invalid email',
			'allowEmpty'=>true)
	);
	
}


?>
