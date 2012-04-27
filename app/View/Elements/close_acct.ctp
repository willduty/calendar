
	Warning! This will permanently close your account and delete all entries and information associated with the account. Proceed?
<br>
<br>

<?php

	echo $this->Form->create("UserCloseAcctForm", array('url' => '/users/delete/'));
	echo '<label></label>';
	
	echo $this->Form->button('Close Account', array('type'=>'submit', 'style'=>'background:red;'));
	echo "&nbsp;&nbsp;&nbsp;";
	echo $this->Form->button('Don\'t Close Account', array('type'=>'button', 'id'=>'closeCloseDlgBtn'));
?>


