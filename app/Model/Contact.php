<?php

class Contact extends AppModel{
	var $name = "Contact";
	
	var $belongsTo = array( 
         'ContactList' => array( 
             'className' => 'ContactList', 
             'foreignKey' => 'contact_list_id'
         ));
		 
}

?>



