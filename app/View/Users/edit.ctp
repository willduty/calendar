
<?php 
	
	echo $this->Html->script('jquery-1.7.1.min.js'); 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 

?>


<script type='text/javascript'>
	
	$(document).ready(function(){
	
		// set up reset pwd dialog
		$('#resetPwdDlg').hide();
		$('#closeAcctDlg').hide();
		
		$("#closeResetBtn").hide();
		$('#closeResetDlgBtn').click(function(){
			$('#resetPwdDlg').dialog('close');
			return false;
		})
		
		// show reset dlg 
		$('#resetPwdBtn').click(function(){
			$('#resetPwdDlg').dialog({title:'Reset Password', width:"25em"});
			
			$('#UserResetPwdForm')
				.find('input[type=password]').val('')
				.end().show();
				
				$("#resetStatus").empty();
				$("#closeResetBtn").hide();		
		})
		
		
		// close acct dlg
		$('#closeAcctBtn').click(function(){
			$('#closeAcctDlg').dialog({title:'Close Account', width:"25em"});
			$('#UserCloseAcctForm').show();	
			$('#closeCloseDlgBtn').click(function(){$('#closeAcctDlg').dialog('close')})
		})
		
		
		
		// on submitting the form in the dialog, post by ajax
		$('#UserResetPwdForm').submit(function(){
		
			// validate form
			var bEmpty = false;
			$('#UserResetPwdForm input[type=password]').each(function(){
					if(this.value == "")
						bEmpty = true;
			});
			if(bEmpty){
				alert('all fields must be filled out');
				return false;
			}
			
			// send reset by ajax 
			$.post($('#UserResetPwdForm').attr('action'),
				$('#UserResetPwdForm').serialize(),
				
				function(ajaxResponse){	
					try{
					// adjust the dialog html depending on result
					var respObj = $.parseJSON(ajaxResponse);
					if(respObj.success){	
						$('#UserResetPwdForm').hide();
						$("#closeResetBtn").show();
						$('#resetStatus').removeClass().addClass("msgSuccess").html("New Password Saved");
					}
					else{
						$('#resetStatus').removeClass().addClass("msgErr").html("Error: "+respObj.errMsg);
					}
					}catch(e){alert('response err: '+e)}
				});
			return false;
		})
	
		
		// $('#resetPwdBtn').button();
		$('#cancelBtn').button();
		
	});
</script>

<style type="text/css">
	form.neatForm input, textarea{width:180px;margin-bottom:5px;}
	form.neatForm textarea{width:250px;height:75px;}
	.boxes{width:1em;}
	#editFormSubmitBtn{margin-left:120px; margin-top:5px; width:90px;}
	br{clear:left;}
</style>



<h2>Profile:</h2>
<div class=simpleSection>
<?php
	echo $this->Form->create('User', array('action' => 'edit', 'class'=>'neatForm'));
	echo $this->Form->input('username', array('style' => 'padding:0px'));
	echo $this->Form->input('email', array('style' => 'padding:0px' ));
	echo $this->Form->input('about', array('style' => 'padding:0px;'));
	echo '<label></label>';
	echo $this->Form->end('save changes', array('id'=>'editFormSubmitBtn'));
?>	
<?php echo $this->Session->Flash(); ?>
</div>
<br>



<h2>Summary:</h2>
<div class=simpleSection>
	<div style='font:bold 12px arial;'>
	Acct Created: <span class=data><?php echo $user['User']['created']; ?></span><br>
	Calendar Entries: <span class=data><?php echo count($user['Entry']); ?></span><br>
	Active Alerts: <span class=data>todo</span><br>
	</div>
</div>
<br>

<div id="resetPwdBtn" class='simpleSection hand'>Reset Password &raquo;</div>
<br>

<div id="contacts_emails" class='simpleSection hand'>Contacts/Emails &raquo;</div>

<br>
<div id="closeAcctBtn" class='simpleSection hand'>Close Account &raquo;</div>




<div id="resetPwdDlg">
	<?php echo $this->element('reset_pwd', array('user'=>$this->data)); ?>
	<div id='resetStatus'></div>
	<div id='closeResetBtn'>
		<a href=# id=closeResetDlgBtn>close</a>
	</div>
</div>


<div id="closeAcctDlg">
	<?php echo $this->element('close_acct', array('user'=>$this->data)); ?>	
</div>


<br>
	
	
	
<?php

	// back to whereever we came from, or entries controller
	if(isset($_SERVER['HTTP_REFERER'])){
		$arr = Router::parse("/", $_SERVER['HTTP_REFERER']);
		unset($arr['pass']);
	}
	else{
		$arr = array('controller'=>'entries');
	}
	echo $this->Html->link('done', $arr, array('id'=>'cancelBtn'));
?>

