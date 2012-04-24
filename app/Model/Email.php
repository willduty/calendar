<?php

class Email extends AppModel{
	var $name = "Email";
	
	
	// var $belongsTo = array('DateType');	
	// var $belongsTo = array('Entry', 'DateType');	
	
	
	var $belongsTo = array( 
         'Entry' => array( 
             'className' => 'Entry', 
             'foreignKey' => 'entry_id'
         ));	
		 
}

?>



