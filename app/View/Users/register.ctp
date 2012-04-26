

<?php 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 
	echo $this->Html->script('jquery-1.7.1.min.js'); 
	
?>


<script type='text/javascript'>
/*
	$(document).ready(function(){
	
	
		$('#UserRegisterForm').submit(function(){
			bFail = false;
			$(this).find('input[type=password], input[type=text]').each(function(){
				if(this.value == ''){
					bFail = true;
					alert('please fill out all the fields');
					return false;
				}
			
			})
			if(bFail){
				return false;
				
			
			return false;
		})
	});
	*/
</script>

<style type="text/css">
	label{float:left;width:120px;font-weight:bold;}
	input, textarea{width:180px;margin-bottom:5px;}
	textarea{width:250px;height:150px;}
	.boxes{width:1em;}
	#editFormSubmitBtn{margin-left:120px; margin-top:5px; width:90px;}
	br{clear:left;}
</style>



<h2>Create Account:</h2>
<?php
	echo $this->Form->create('User', array('action' => 'register'));
	echo $this->Form->input('username', array('style' => 'padding:0px'));
	echo $this->Form->input('email', array('style' => 'padding:0px' ));
	echo $this->Form->input('password1', array('style' => 'padding:0px', 'type'=>'password', 'label'=>'password'));
	echo $this->Form->input('password2', array('style' => 'padding:0px', 'type'=>'password', 'label'=>'confirm password'));
	echo $this->Form->end('create account', array('id'=>'editFormSubmitBtn'));
	
?>	

<br>

<?php
	echo $this->Session->flash(); 
?>
	
	
<?php
	// $arr = Router::parse("/", $_SERVER['HTTP_REFERER']);
	// unset($arr['pass']);
	// echo $this->Html->link('cancel', $arr, array('id'=>'cancelBtn'));
?>

