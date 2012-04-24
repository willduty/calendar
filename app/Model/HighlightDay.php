
<?php

class HightlightDay extends AppModel{
	var $belongsTo = array(
		'User'
	);
	var $hasOne = 'Date';
}

?>
