

<?php
	echo $this->Form->create("UserResetPwdForm", array('url' => '/users/reset_pwd/'.$user['User']['id'], 'id' => 'UserResetPwdForm', 'class'=>'neatForm'));
	echo $this->Form->input("oldPassword", array('type' => 'password', 'id'=>'oldPassword', 'label'=>'Old Password:'));
	echo $this->Form->input("newPassword1", array('type' => 'password', 'id'=>'newPassword1', 'label'=>'New Password:'));
	echo $this->Form->input("newPassword2", array('type' => 'password', 'id'=>'newPassword2', 'label'=>'Confirm:'));
	echo '<label></label>';
	echo $this->Form->end('submit');
?>


