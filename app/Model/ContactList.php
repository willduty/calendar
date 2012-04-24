<?php

class ContactList extends AppModel{
	var $name = "ContactList";
	
	
	var $hasMany = array('Contact');
	
	var $hasAndBelongsToMany = array('Reminder');	
	
	var $belongsTo = array( 
         'User' => array( 
             'className' => 'User', 
             'foreignKey' => 'user_id'
         ));
	
}

?>



