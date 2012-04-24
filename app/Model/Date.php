<?php

class Date extends AppModel{
	var $name = "Date";
	
	var $belongsTo = array( 
         // 'Entry' => array( 
             // 'className' => 'Entry', 
             // 'foreignKey' => 'entry_id'
         // ), 
         'DateType' => array( 
             'className' => 'DateType', 
             'foreignKey' => 'date_type_id' 
         ));	
		 
}

?>



