
<?php
echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 	
?>

<script type='text/javascript'>
	$(document).ready(function(){
		$("#UserLoginForm").find('input[type=submit]').button();
	})

</script>

<style type="text/css">
	label { float:left; width:6em; font-weight:bold; }
	input, textarea { width:10em; margin-bottom:5px; }
	textarea { width:250px;height:150px; }
	.boxes { width:1em; }
	br { clear:left; }
</style>

<?php
	
	echo $this->Session->flash() . '<br>';
    echo $this->Form->create('User', array('action' => 'login'));
    echo $this->Form->input('username', array('style' => 'padding:0px'));
    echo $this->Form->input('password', array('style' => 'padding:0px'));
    echo $this->Form->end('Login');
	
	echo "<br>";
	echo $this->Html->link('Forget password?', array('action'=>'passwordLookUp'));
	echo " | ";
	echo $this->Html->link('Create Account &raquo;', array('action'=>'register'), array('escape'=>false));
	
?>
