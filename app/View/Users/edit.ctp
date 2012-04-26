
<?php 
	
	echo $this->Html->script('jquery-1.7.1.min.js'); 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 

?>


<script type='text/javascript'>
	
	$(document).ready(function(){
	
		// set up reset pwd dialog
		$('#resetPwdDlg').hide();
		
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

<div id="resetPwdBtn" class='simpleSection hand'>Reset Password &raquo;</div>
<br>

<div id="resetPwdDlg">
	<?php echo $this->element('reset_pwd', array('user'=>$this->data)); ?>
	<div id='resetStatus'></div>
	<div id='closeResetBtn'>
		<a href=# id=closeResetDlgBtn>close</a>
	</div>
	
</div>

<div id="contacts/emails" class=simpleSection>contacts/emails &raquo;</div>



<br>
	
	
	
<?php
	$arr = Router::parse("/", $_SERVER['HTTP_REFERER']);
	unset($arr['pass']);
	echo $this->Html->link('done', $arr, array('id'=>'cancelBtn'));
?>

