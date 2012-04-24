

<?php 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 
?>


<script type='text/javascript'>
	
	$(document).ready(function(){
	
		// set up reset pwd dialog
		$('#resetPwdDlg').hide();
		$("#resetSuccessElem").hide();
		$('#closeResetDlgBtn').click(function(){
			$('#resetPwdDlg').dialog('close');
			return false;
		})
		
		// show reset dlg 
		$('#resetPwdBtn').click(function(){
			$('#resetPwdDlg').dialog({title:'Reset Password', width:"25em"});
			
			// on submitting the form in the dialog, post by ajax
			$('#UserResetPwdForm').submit(function(){
			
				// validate form
				var pwd1 = $('#UserResetPwdForm').find('#newPassword1').val();
				
				if(pwd1 == ""){
					alert("please enter a new password");
					return false;
				}
				
				// send reset by ajax 
				$.post($('#UserResetPwdForm').attr('action'),
					$('#UserResetPwdForm').serialize(),
					
					function(ajaxResponse){	
						// response comes as json obj of type ajaxResponse
						// adjust the dialog html depending on result
						var respObj = $.parseJSON(ajaxResponse);
						if(respObj.success){
							$('#UserResetPwdForm').hide();
							$("#resetSuccessElem").show();
						}
						else{
							$('#resetStatus').css("background", "red");
							$('#resetStatus').html("Error: "+respObj.errMsg);
						}
					});
				return false;
			})
		})
		
		$('#resetPwdBtn').button();
		$('#cancelBtn').button();
		
	});
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
	echo $this->Form->input('username', array('style' => 'padding:0px' ));
	echo $this->Form->input('password1', array('style' => 'padding:0px', 'type'=>'password', 'label'=>'password'));
	echo $this->Form->input('password2', array('style' => 'padding:0px', 'type'=>'password', 'label'=>'confirm password'));
	echo $this->Form->input('email', array('style' => 'padding:0px' ));
	echo $this->Form->input('about', array('style' => 'padding:0px;'));
	echo $this->Form->end('create account', array('id'=>'editFormSubmitBtn'));
	
?>	

<br>
	
	
	
<?php
	// $arr = Router::parse("/", $_SERVER['HTTP_REFERER']);
	// unset($arr['pass']);
	// echo $this->Html->link('cancel', $arr, array('id'=>'cancelBtn'));
?>

