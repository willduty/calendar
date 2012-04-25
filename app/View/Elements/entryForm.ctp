
<?php 
	echo $this->Html->script('jquery-1.6.2.min.js'); 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 
	echo $this->Html->script('wd_TimePicker.js'); 
	echo $this->Html->script('TimePicker.js'); 
	echo $this->Html->script('wd_FormDropdown.js'); 
	echo $this->Html->script('GuiTree.js'); 
	echo $this->Html->css('wd_FormDropdown');
	echo $this->Html->css('wd_TimePicker');
	echo $this->Html->css('TimePicker');
	
	
	$EDIT = $this->action == 'edit' || FALSE;
	
?>




<script type=text/javascript>

	<?php
		if($EDIT)
			echo 'gEntryId = ' . $this->data['Entry']['id'] . ';';
		
		if(isset($date))
			echo 'gDate = "' . $date->format('m/d/Y') . '";';
	?>

		
	$(document).ready(function(){
		
		gui = new GuiTree();
		gui.setup();
		
		
		// jquery guis
		$("[name$=start_date\\\]]").datepicker();
		$("[name$=end_date\\\]]").datepicker();
		
		
		if(location.pathname.indexOf('edit') != -1)
			hideDateGui();
		else
			initDateGui();
		
		
		if(typeof gDate != 'undefined'){
			$('#Date0StartDate').val(gDate)
		}
		
		$('#additionalDetailsSwitch').click(function(){
			$('#additionalDetails').toggle();
		});
		

		new TimePicker($('[name=data\\\[Date\\\]\\\[0\\\]\\\[start_time\\\]]').get(0))
		
		new TimePicker($('[name=data\\\[Date\\\]\\\[0\\\]\\\[end_time\\\]]').get(0))
		
		
		
		// pre submit validation
		$('#EntryAddForm').bind('submit', validateForm);
		$('#EntryEditForm').bind('submit', validateForm);
		
		
	})
	
	
	function validateForm(){
		
		var name = $('#EntryName').val();
		if($.trim(name) == ""){
			alert('invalid title')
			return false;
		}
		
		var form = $('#EntryEditForm')
		
		var wp = form.find('[name$=weeks_pattern\\\]]:checked')
		
			
		if(wp.val() == 'nth_weekdays_of_month'){
			if(0 == form.find('[name$=weeks_of_month\\\]\\\[\\\]]:checked').length){
				alert('for Nth days of month you must select at least one day')
				return false;
			}
		}
		
		if(wp.val() == 'every_week'){
			form.find('[name$=weeks_of_month\\\]\\\[\\\]]').attr('checked', true);
		}
		
		return true;
	}
	
	
	function hideDateGui(){
		
		$('div[name=dateGuiPlaceholder]').insertAfter('div[name=newDate]');
		var gui = $('div[name=newDate]').detach();
		$('#hider').append(gui);
		
		return false;
	}
	
	function showDateGui(){
		var placeholder = $('div[name=dateGuiPlaceholder]');
		$('div[name=newDate]').detach().insertAfter(placeholder)
		$('#hider').append(placeholder.detach());
		
		initDateGui();
	}
	

	function initDateGui(){
		
		$('[name=dateType]').first().trigger('click');
			
	}
	
	
	function updateEntry(elem){
		$.post('/entries/updateEntryDetails/' + gEntryId, 
			$('form').serialize(), 
			function(resp){
				var result = $.parseJSON(resp).result;
				alert((result == 'ok') ? 'update succeeded.' : 'could not update.')
			})
	}
	
	
	
	// delete a date via ajax
	function removeDate(btn){
		var count = $('div[name=existingDate]').length;
		if(count == 1 && !confirm('This is the only date for this calendar entry. '+
					'If you delete it you will no longer see the entry in calendar views. '+
					'It will still be visible in a list view. \n\nContinue with delete?'))	
			return;
		
		var id = btn.value;
		$.get('/dates/delete/' + id + '/' + 0, function(resp){
			var result = $.parseJSON(resp).result;
			alert((result == 'ok') ? 'delete succeeded' : 'could not delete date')
			if(result == 'ok'){
			
				$('div[name=existingDate][value='+id+']').remove();
			}
		})
		return false;
	}
	
	// save a date via ajax
	function saveDate(btn){
		
		if(!validateForm())
			return;
			
		
		$.post('/dates/add/' + gEntryId, 
			$('form').serialize(), 
			function(resp){
				try{
				
				var result = $.parseJSON(resp)
				
				if(result.result != 'ok') {
					alert('save failed.')
				}
				else{
					$('#existingDates').append($("<div name=existingDate value='"+result.obj.Date.id+"' >"+
						"<b>Date: </b>"+ result.dateAsString +" &nbsp;&nbsp;"+
						"<button type=button onclick='return removeDate(this);' "+
						"value="+result.obj.Date.id+" class=small>delete</button></div>"));
					
					hideDateGui();
				}
				}catch(e){alert(e)}
			})
	}
	
	function timepicker(elem){
		var list = document.createElement('ul')
		
	}
		
</script>

<div id=tempdiv></div>








<!-- BEGIN FORM -->

<?php if($this->action == 'add'): ?>
	<h2>New Calendar Entry</h2>
<?php elseif ($EDIT): ?>	
	<h2>Entry Details</h2>
<?php endif; ?>

<?php

echo $this->Form->create('Entry');
echo $this->Form->input('name', array('label'=>array('text'=>'Title')));

?>


<div class=switch id=additionalDetailsSwitch style='width:10em;'>more options...</div>
<div id=additionalDetails style='display:none;'>
	<?php
		echo $this->Form->input('url', array('type'=>'text'));
		echo $this->Form->input('email', array('type'=>'text'));
		echo $this->Form->input('address', array('type'=>'text'));
		echo $this->Form->input('city', array('type'=>'text'));
		echo $this->Form->input('state', array('type'=>'text'));
		echo $this->Form->input('zip_code', array('type'=>'text'));
		
		
	?>
	
	
</div>


<br>


<?php if($EDIT): 
	echo $this->Form->input('id'); ?>
<button name='save' class='small' type=button onclick='return updateEntry(this);'>save</button>
<br><br>
<?php endif; ?>


<!-- LIST OF EXISTING DATES -->
<?php 

	if($EDIT):
	echo '<div style="padding:5px 0px 5px 0px;" id=existingDates ><h2>Dates</h2>'; 
	foreach($this->data['Date'] as $date):
?>

	<div name=existingDate value=<?php echo $date['id']; ?> >
		<b>Date: </b><?php echo $this->Calendar->dateAsString($date); ?> 
		&nbsp;&nbsp;
		<button type=button onclick='return removeDate(this);' value=<?php echo $date['id']; ?> class=small>delete</button>
	</div>

<?php 
	endforeach;
	echo '</div>'; 
	endif;
?>





<!-- DATE GUI -->


<?php if($EDIT)
	
	//$data = $this->__get('data');
	//debug($this->data);
	
?>	


<?php if($EDIT): ?>
<br>
<div name=dateGuiPlaceholder class=switch onclick='return showDateGui();' style='width:10em;'>add more dates...</div>
<?php else: ?>
<h2>Date</h2>
<?php endif; ?>	

<div name=newDate class=simpleSection>


<div editOnly style='display:none; font: bold 14px helvetica;'>Additional Date</div>

<input type=radio name=dateType value=byDate tool=tool_byDate ><label for="radio1">calendar date</label>
<input type=radio name=dateType value=byDayOfWeek tool=tool_byDayOfWeek ><label for="radio1">day of week</label>

	<br><br>
	
	<div name=dateGui>
		<!-- TODO -->
		<?php echo $this->Form->hidden('Date.0.date_type_id', array('value'=>'1')); ?>
		
		<div name=tool_byDate id=tool_byDate style='display:none;'>
		
			<table>		
				<tr><td style='vertical-align:middle;'>
				<?php echo $this->Form->input('Date.0.start_date', 
					array('type'=>'text', 'size'=>'14', 'tool'=>'tool_singleDateOptions', 
						'trigger'=>'change')); ?>
				</td>
				<td style='vertical-align:middle;'><?php echo $this->Form->input('Date.0.repeating', 
					array('type'=>'checkbox', 'label'=>array('text'=>'Every Year'))); ?>
				</td></tr>
			</table>
		
			<div id=tool_singleDateOptions style='margin-left:20px;'>
				<input name=singleDateOptions type=radio checked tool='' defaultChecked><label>all day</label>
				<input name=singleDateOptions type=radio tool=tool_timesOptions><label>set start/end time & date...</label>
			
				<div id=tool_timesOptions style='display:none; margin-left:20px;'>
				
					<?php echo $this->Form->input('Date.0.start_time', 
						array('type'=>'text')); ?>
					
					
					
					<?php echo $this->Form->input('Date.0.end_time', 
						array('type'=>'text')); ?>
					
					
					<input type=checkbox tool=tool_endDate style='margin-left:10px;'><label>ends on separate day</label>
						<div id=tool_endDate style='display:none; margin-left:20px;'>
							<?php echo $this->Form->input('Date.0.end_date',
								array('type'=>'text')); ?>
						</div>
					
				</div>	
			</div>	
		
		</div>
		
		
		
		<div id=tool_byDayOfWeek style='display:none; padding:0px 0px 0px 20px;'>	
			<?php
				echo $this->Form->radio('Date.0.days_of_week', 
					array('1' => 'Sun', '2' => 'Mon', '3' => 'Tue', '4' => 'Wed',
						'5' => 'Thu', '6' => 'Fri', '7' => 'Sat'),
					array('tool'=>'tool_weekDayOptions',
						'legend'=>false
						));
				
			?>
			<div id=tool_weekDayOptions style='display:none; padding:0px 0px 0px 40px;'>
					
								
				<!-- nth weekday of month -->
				
				<?php
					echo $this->Form->radio('Date.0.weeks_pattern', 
						array('every_week' => 'Every Week'),
						array('value' => 'every_week', 'tool'=>'', 'defaultChecked' => true)
						);
						
				?>
				<br>
				
				
				
				<?php
					echo $this->Form->radio('Date.0.weeks_pattern', 
							array('nth_week' => 'Every Other Week'),
							array('value' => false, 'tool'=>'tool_nth_week')
						);
				?>
				<br>
				<div id="tool_nth_week" style='display:none;'>
					<!-- TODO -->
					&nbsp;&nbsp;&nbsp;starting on:
					<input type=text class=dateBox name='jjjjjjjjjjj' 
						<?php if(isset($year)) echo "value='$year-$month-$day'"; ?> clearOnCancel />
					<br>
				</div>
				
				
				
				<?php
					echo $this->Form->radio('Date.0.weeks_pattern', 
							array('nth_weekdays_of_month' => 'Nth day(s) of month (eg 1st and 3rd sundays):'),
							array('value' => false, 'tool'=>'tool_nth_weekdays_of_month')
						);
				?>
				<br>
				<div id='tool_nth_weekdays_of_month' style='display:none;'>
				
					<?php
					
					    echo $this->Form->input('Date.0.weeks_of_month', array(
						    'multiple' => 'checkbox',
						    'options' => array('1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th',
								'5' => '5th')	
						    )
						    );
					
					?>
					
				</div>
				
			</div>
		</div>

	</div>
	<br>
	
	
	<?php if($EDIT): ?>

	<button name='save' class='small'  type=button onclick='return saveDate(this);'>save</button>
	<button name='cancel' class='small'  type=button onclick='return hideDateGui();'>cancel</button>	
	
	<?php endif; ?>	

</div>

<br><br> 



<table style='width:13em;'>

<?php if($EDIT): ?>

	<tr><td style='vertical-align:middle;'>
	<?php echo $this->Form->button('return to calendar', array('type'=>'button', 'onclick'=>'location=\'/entries\';')); ?>
	</td></tr>
	
<?php else: ?>

	<tr><td style='vertical-align:middle;'>
	<?php echo $this->Form->button('save all'); ?>
	</td><td style='vertical-align:middle;'>
	<?php echo $this->Form->button('cancel', array('type'=>'button', 'onclick'=>'location=\'/entries\';')); ?>
	</td></tr>

<?php endif; ?>	

</table>





</form>

<!-- FORM END -->


	
<!-- TEMP HIDDEN DIV -->

<div id=hider style='display:none;'></div>


<!-- TIMEPICKER -->

<ul class=TimePicker id=TimePicker style='display:none;'>

<?php
	for($i=1; $i<12; $i++){
		echo "<li>
				<ul >
					<li>$i:00 AM </li>
					<li class=arrow>&raquo;

						<ul>
							<li>$i:15 AM</li>
							<li>$i:30 AM</li>
							<li>$i:45 AM</li>
						</ul>
					</li> 


					<li>$i:00 PM </li>
					<li class=arrow>&raquo;

						<ul>
							<li>$i:15 PM</li>
							<li>$i:30 PM</li>
							<li>$i:45 PM</li>
						</ul>
					</li>
				</ul>
			</li>";
	
	
	}


?>
</ul>

