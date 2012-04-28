




<?php if($new_password_created): ?>

	Your password has been reset but now needs to be activated. Check your email and click the link to activate the new password.
	

<?php else:?>




	Enter your email and new password below. An email will be sent to you to activate your new password.
	<br><br>
	<?php
			echo $this->Form->create('User', array('url' => '/users/forgot_pwd/', 'class'=>'neatForm'));
			echo $this->Form->input('email', array('id'=>'newPassword1', 'label'=>'Email'));
			echo $this->Form->input("newPassword1", array('type' => 'password', 'id'=>'newPassword1', 'label'=>'New Password:'));
			echo $this->Form->input("newPassword2", array('type' => 'password', 'id'=>'newPassword2', 'label'=>'Confirm:'));
			echo '<label></label>';
			echo $this->Form->end('submit');
			
			echo $this->Session->flash();
			
	?>



<?php endif; ?>
