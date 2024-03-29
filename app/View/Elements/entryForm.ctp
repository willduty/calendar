
<?php 
	echo $this->Html->script('jquery-1.6.2.min.js'); 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 
	echo $this->Html->script('DropDown.js'); 
	echo $this->Html->script('GuiTree.js'); 
	echo $this->Html->css('TimePicker');
	
	$_EDIT = $this->action == 'edit' || FALSE;	
	$mainPage = $this->base . '/entries';
	
?>




<script type=text/javascript>
	var basePath = "<?php echo $this->base ?>";
	
	<?php
	
		if($_EDIT)
			echo 'gEntryId = ' . $this->data['Entry']['id'] . ';';
		
		if(isset($date))
			echo 'gDate = "' . $date->format('m/d/Y') . '";';
		
		if(isset($hour))
			echo 'gHour = "' . $hour . '";';
			
			
	?>

		
	$(document).ready(function(){
		
		$('#formArea').parent().css({'background':'white', 'border':'1px solid lightgray'})

		gui = new GuiTree();
		gui.setup();
		
		// jquery guis
		$("[name$=start_date\\\]]").datepicker();
		$("[name$=end_date\\\]]").datepicker();
		
		try{
			$('[name=data\\\[Date\\\]\\\[0\\\]\\\[start_time\\\]]').each(function(){
				new DropDown(this, 'TimePicker');
			})
			$('[name=data\\\[Date\\\]\\\[0\\\]\\\[end_time\\\]]').each(function(){
				new DropDown(this, 'TimePicker');
			})
			
		}catch(e){alert(e)}
		
		
		if(location.pathname.indexOf('edit') != -1)
			hideDateGui();
		else
			initDateGui();
		
		
		if(typeof gDate != 'undefined'){
			$('#singleDateBox').val(gDate)
		}
		
		if(typeof gHour != 'undefined'){
			gui.openTool('singleDateBox');
			gui.openTool('singleDateTimesOptions');
			$('#singleDateStartTime').val(gHour)
		}
		
		
		$('#additionalDetailsSwitch').click(function(){
			this.innerHTML = (this.innerHTML.indexOf('more') != -1) ? '&laquo; less options ' : 'more options &raquo;';
			$('#additionalDetails').toggle();
		});
		
		// pre submit validation
		$('#EntryForm').bind('submit', validateForm);
		
		$('#EntryName').focus();
		
	})
	
	
	function validateForm(){
		
		var form = $('#EntryForm');
		
		var name = $('#EntryName').val();
		if($.trim(name) == ""){
			alert('Invalid Title')
			return false;
		}
		
		
		if($('#byDateOption:checked').length){
			var dateInput = $("#singleDateBox").val();
			var reg = /[0-9][0-9]\/[0-9][0-9]\/[0-9][0-9][0-9][0-9]/
			if(!reg.test(dateInput)){
				alert('Invalid Date');
				return false;
			}
		}
		
		// ensure week pattern entries have required 
		// additional stuff
		var wp = form.find('[name$=weeks_pattern\\\]]:checked')
		
		if(wp.val() == 'nth_weekdays_of_month'){
			if(0 == form.find('[name$=weeks_of_month\\\]\\\[\\\]]:checked').length){
				alert('for Nth days of month you must select at least one day')
				return false;
			}
		}

		if(wp.val() == 'nth_week'){
			if(form.find('[name$=start_date\\\]]').val() == ''){
				alert('for alternating weeks you must select a start date')
				return false;
			}
		}
		return true;
	}
	
	
	function hideDateGui(){
		$('#dateGuiContainer').append($('div[name=dateGuiPlaceholder]'));
		var gui = $('div[name=newDate]').detach();
		$('#hider').append(gui);
		return false;
	}
	
	function showDateGui(){
		$('#dateGuiContainer').append($('div[name=newDate]'))
		$('#hider').append($('div[name=dateGuiPlaceholder]'));
		initDateGui();
	}
	

	function initDateGui(){
		$('[name=dateType]').first().trigger('click');
	}
	
	
	function updateEntry(elem){
		$.post(basePath + '/entries/updateEntryDetails/' + gEntryId, 
			$('form').serialize(), 
			function(resp){
				var result = $.parseJSON(resp);
				alert(result.success ? 'Update succeeded.' : 'Could not update.')
			})
	}
	
	
	
	// delete a date via ajax
	function removeDate(btn){
	
		
		var count = $('div[name=existingDate]').length;
		if((count == 1 && !confirm('This is the only date for this calendar entry. '+
					'If you delete it you will no longer see the entry in calendar views. '+
					'It will still be visible in a list view. \n\nContinue with delete?'))
			||
			!confirm('Delete date?'))	
			return;
		
		var id = btn.value;
		$.get(basePath + '/dates/delete/' + id + '/' + 0, function(resp){
			var result = $.parseJSON(resp);
			alert(result.success ? 'delete succeeded' : 'could not delete date')
			if(result.success){
			
				$('div[name=existingDate][value='+id+']').remove();
			}
		})
		return false;
	}
	
	// save a date via ajax
	function saveDate(btn){
		
		if(!validateForm())
			return;
			
		$.post(basePath + '/dates/add/' + gEntryId, 
			$('form').serialize(), 
			function(resp){
				try{
				
				var result = $.parseJSON(resp)
				
				if(result.success == false) {
					alert('save failed.')
				}
				else{
					var newDate = $("<div name=existingDate value='"+result.obj.Date.id+"' >"+
						"<b>Date: </b>"+ result.dateAsString +" &nbsp;&nbsp;"+
						"<button type=button onclick='return removeDate(this);' "+
						"value="+result.obj.Date.id+" class=small>delete</button></div>");
					
					var appendAfter = $('#existingDates').find('[name=existingDate]').last();
					appendAfter.length ?
						appendAfter.append(newDate) : $('#existingDates').prepend(newDate)
					
					hideDateGui();
				}
				}catch(e){alert(e)}
			})
	}
	
	function timepicker(elem){
		var list = document.createElement('ul')
		
	}
		
</script>



<!-- BEGIN FORM -->

<div id=formArea style='background:white;'>
<?php if ($_EDIT): ?>	
	<h2 style='color:#a60'>Edit Calendar Entry</h2>
<?php else: ?>
	<h3 style='color:#a60'><b>New Calendar Entry</b></h3>
<?php endif; ?>

<br>
<h2>Entry Details</h2>

<?php

echo $this->Form->create('Entry', array('id'=>'EntryForm'));

echo '<div class="simpleSection">';

echo $this->Form->input('name', array('label'=>array('text'=>'Title '), 'div'=>array('style'=>'font-weight:bold;')));

?>


<div class=switch id=additionalDetailsSwitch style='width:10em;'>more options &raquo;</div>
<div id=additionalDetails style='display:none; padding: 10px 0px 0px 20px;'>
	
	<table class=neatForm70><tr><td>
	<?php
		echo $this->Form->input('category_id', array('label'=>array('class'=>'neatForm70'), 'empty' => '-- choose one -- ', 'style'=>'width:145px;'));
		echo $this->Form->input('url',array('type'=>'text'));
		echo $this->Form->input('email');
		echo $this->Form->input('address', array('type'=>'text'));
		echo $this->Form->input('city');
		echo $this->Form->input('state');
		echo $this->Form->input('zip_code');
		echo $this->Form->input('comments', array('label'=>array('text'=>'Notes', 'class'=>'neatForm70')));
	
	?>
	</td></tr></table>
</div>
<br>


<?php if($_EDIT): 
	echo $this->Form->input('id'); ?>
	<button name='save' class='small' type=button onclick='return updateEntry(this);'>save entry details</button>
	<br>

<?php endif; ?>
	</div>
	<br><br>


<!-- LIST OF EXISTING DATES -->
<?php 
	if($_EDIT):
	echo '<h2>Dates</h2><div id=existingDates class=simpleSectionLight>'; 
	foreach($this->data['Date'] as $date):
?>

	<div name=existingDate value=<?php echo $date['id']; ?> >
		<b>Date: </b><?php echo $this->Calendar->dateAsString($date); ?> 
		&nbsp;&nbsp;
		<button type=button onclick='return removeDate(this);' value=<?php echo $date['id']; ?> class=small>delete</button>
	</div>

<?php 
	endforeach;
	//echo '</div>'; 
	endif;
?>


<!-- DATE GUI -->
<?php if($_EDIT): ?>
	<br>
	<div id=dateGuiContainer>
		<div name=dateGuiPlaceholder class=switch onclick='return showDateGui();' style='width:10em;'>add more dates &raquo;</div>
	</div>

	</div>

<?php else: ?>
	<h2>Date</h2>
<?php endif; ?>	

<div name=newDate class=simpleSection>


<div editOnly style='display:none; font: bold 14px helvetica;'>Additional Date</div>

<input type=radio name=dateType value=byDate tool=tool_byDate id=byDateOption><label for="radio1">Calendar Date</label>
<input type=radio name=dateType value=byDayOfWeek tool=tool_byDayOfWeek ><label for="radio1">Day of Week</label>

	<br><br>
	
	
	<div name=dateGui>
		<!-- TODO -->
		
		<div name=tool_byDate id=tool_byDate style='display:none;'>
		
			<table>		
				<tr><td style='vertical-align:middle;'>
				<?php echo $this->Form->input('Date.0.start_date', 
					array('type'=>'text', 'size'=>'14', 'tool'=>'tool_singleDateOptions', 'id'=>'singleDateBox',
						'trigger'=>'change')); ?>
				</td>
				<td style='vertical-align:middle;'><?php echo $this->Form->input('Date.0.repeating', 
					array('type'=>'checkbox', 'label'=>array('text'=>'Every Year'))); ?>
				</td></tr>
			</table>
		
			<div id=tool_singleDateOptions style='margin-left:20px; display:none;'>
				<input name=singleDateOptions type=radio checked tool='' defaultChecked><label>all day</label>
				<input name=singleDateOptions type=radio tool=tool_timesOptions id=singleDateTimesOptions><label>set start/end time & date &raquo;</label>
			
				<div id=tool_timesOptions style='display:none; margin-left:20px;'>
				
					<br>
					<?php echo $this->Form->input('Date.0.start_time', 
						array('type'=>'text', 'id'=>'singleDateStartTime', 'label'=>array('class'=>'neatForm80'))); ?>
					
					<?php echo $this->Form->input('Date.0.end_time', 
						array('type'=>'text', 'label'=>array('class'=>'neatForm80'))); ?>
					
					<br>
					<input type=checkbox tool=tool_endDate style='margin-left:10px;'><label>ends on separate day</label>
						<br>
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
						array('value' => 'every_week', 'tool'=>'tool_every_week')
						);
				?>
				<br>
				
				
				<div id=tool_every_week class=neatFormNormal>
							
					<?php echo $this->Form->input('Date.0.start_time', 
						array('type'=>'text')); ?>
						
					<?php echo $this->Form->input('Date.0.end_time', 
						array('type'=>'text')); ?>	
					<br>
				</div>
	
	
				<?php
					echo $this->Form->radio('Date.0.weeks_pattern', 
							array('nth_week' => 'Every Other Week'),
							array('value' => false, 'tool'=>'tool_nth_week')
						);
				?>
				<br>
				
				<div id=tool_nth_week class=neatFormNormal>
					
					<?php 	
					// todo
					$valOption = isset($year) ? "$year-$month-$day'" : ''; 
					
					echo $this->Form->input('Date.0.start_date', 
						array('type'=>'text', 'label'=>array('text'=>'Start date', 'value'=>$valOption))); ?>
					
					<?php echo $this->Form->input('Date.0.start_time', 
						array('type'=>'text')); ?>
					
					<?php echo $this->Form->input('Date.0.end_time', 
						array('type'=>'text')); ?>
						
					<br>
				</div>
				
				
				
				<?php
					echo $this->Form->radio('Date.0.weeks_pattern', 
							array('nth_weekdays_of_month' => 'Nth day(s) of month (eg 1st and 3rd sundays):'),
							array('value' => false, 'tool'=>'tool_nth_weekdays_of_month')
						);
				?>
				
				<div id=tool_nth_weekdays_of_month>
				
					<div style='margin: 5px 0px 5px 15px; '>
					<?php
					
					    echo $this->Form->input('Date.0.weeks_of_month', 
							array('multiple' => 'checkbox',
								'options' => array('1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th'),
								'label'=>'', 'style'=>'float:left;vertical-align:middle;')
						    );
					
					?>
					</div>
					
					<div class=neatFormNormal style='clear:both;'>
				
						<?php echo $this->Form->input('Date.0.start_time', 
							array('type'=>'text')); ?>
						
						<?php echo $this->Form->input('Date.0.end_time', 
							array('type'=>'text')); ?>
						
					</div>
		
				</div>
				
				<br>
				
				<?php
					echo $this->Form->radio('Date.0.weeks_pattern', 
							array('months_of_year' => 'Specific month only:'),
							array('value' => false, 'tool'=>'tool_months_of_year')
						);
				?>
				<br>
				
				<div id='tool_months_of_year'>
					
					<div style='clear:both;'>
					<?php
						// TODO make radio
					    echo $this->Form->radio('Date.0.months_of_year',
						    array('1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun', 
								'7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'),
							array('label'=>'')	
						);
					
					?>
					</div>
					<div style='clear:both;'>
					<?php
					
					    echo $this->Form->input('Date.0.weeks_of_month', 
							array('multiple' => 'checkbox',
								'options' => array('1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th'),
								'label'=>'', 'style'=>'float:left;')
						    );
					
					?>
					</div>
					
				</div>
				
			</div>
		</div>

	</div>
	<br>
	
	
	<?php if($_EDIT): ?>

	<button name='save' class='small'  type=button onclick='return saveDate(this);'>save</button>
	<button name='cancel' class='small'  type=button onclick='return hideDateGui();'>cancel</button>	
	
	<?php endif; ?>	

</div>

<br><br> 



<table style='width:10em;'>

<?php if($_EDIT): ?>

	<tr><td style='vertical-align:middle;'>
	<?php echo $this->Form->button('return to calendar', array('type'=>'button', 'onclick'=>'location=\''.$mainPage.'\';')); ?>
	</td></tr>
	
<?php else: ?>

	<tr><td style='vertical-align:middle;'>
	<?php echo $this->Form->button('save'); ?>
	</td><td style='vertical-align:middle;'>
	<?php echo $this->Form->button('cancel', array('type'=>'button', 'onclick'=>'location=\''.$mainPage.'\';')); ?>
	</td></tr>

<?php endif; ?>	

</table>





</form>
</div>
<!-- FORM END -->


	
<!-- TEMP HIDDEN DIV -->
<div id=hider style='display:none;'></div>


<!-- TIMEPICKER -->
<?php echo $this->element('timepicker'); ?>

