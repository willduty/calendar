

<?php
	echo $this->Form->create("UserResetPwdForm", array('url' => '/users/reset_pwd/'.$user['User']['id'], 'id' => 'UserResetPwdForm'));
	echo $this->Form->input("oldPassword", array('type' => 'password', 'id'=>'oldPassword', 'label'=>'old password:'));
	echo $this->Form->input("newPassword1", array('type' => 'password', 'id'=>'newPassword1', 'label'=>'new password:'));
	echo $this->Form->input("newPassword2", array('type' => 'password', 'id'=>'newPassword2', 'label'=>'confirm password:'));
	echo $this->Form->end('submit');
?>


