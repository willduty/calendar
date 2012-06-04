
<?php

class Calendar extends AppModel{
	var $name = "Calendar";
	
	var $hasMany = array(
		"Entry" => array(
			'className' => 'Entry',
			'dependent' => true),
		"Category" => array(
			'className' => 'Category',
			'dependent' => true)	
			
		);
	
	
	
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 3),
			'allowEmpty' => false),
	);
		
}


?>
