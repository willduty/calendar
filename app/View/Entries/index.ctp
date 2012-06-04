
<?php 
	echo $this->Html->script('jquery-1.7.1.min.js'); 
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery.contextmenu.r2.js'); 
	echo $this->Html->script('jquery.qtip-1.0.0-rc3');
	
?>

<script type='text/javascript'>
	var gBase = "<?php echo $this->base ?>";
	var gPath = "<?php echo $this->base . "/" . $this->params['controller']; ?>";
	var gView = "<?php echo $view; ?>";
	
	var entryDisplayData = {
	<?php
	foreach($entries as $index => $entry){
			$entryDisplayStr = $this->Calendar->getEntryDisplayString($entry);
			$name = str_replace("\"", "\\\"", $entry['Entry']['name']);
			echo $entry['Entry']['id'] . ":{name:\"$name\", displayStr:\"$entryDisplayStr\"},";
		}
	?>
	}
</script>


<script type='text/javascript'>

// utility obj for page element positioning
function objPos(elem){
	this.left = getXCoord(elem);
	this.top = getYCoord(elem);
	this.right = this.left + elem.offsetWidth;
	this.bottom = this.top + elem.offsetHeight;
	this.width = elem.offsetWidth;
	this.height = elem.offsetHeight;
}


// get page element coordinates
function getXCoord(elem){
	var x = 0;
	while(elem){
		x += elem.offsetLeft;
		elem = elem.offsetParent;
	}
	return x;
}

function getYCoord(elem){
	var y = 0;
	while(elem){
		y += elem.offsetTop;
		elem = elem.offsetParent;
	}
	return y; // todo get height of elem
}




	function searchCallback(ajaxReturnVal){
		ajaxReturnVal = $.trim(ajaxReturnVal); // todo: this should not be necessary
		var matches = ajaxReturnVal.split(',');
		$('a[name="entry"]').each(function(){
			for(var i in matches)
			if($(this).attr('entryId') == matches[i]){
				$(this).addClass("hilite");
			}
		});
	}
	
	$(document).ready(
		function(){
		
			// highlight today cell (if present)
			var d = new Date()
			var idstr = d.getFullYear() + '\\\/' + (parseInt(d.getMonth()) + 1) + '\\\/' + d.getDate();
			$('#'+idstr).addClass('todayCell');
			
		
			// context menu for calendar day
			$('td.calendarCell').contextMenu('dateCtxMenu', {
				  bindings: {
					'addSingleDateEntry': function(t) {
					  location = gPath + "/add/" + t.getAttribute("id");
					},
					'highlightDay': function(t) {
					  location = gPath + "/highlightDay/"+gView+"/" + t.getAttribute("id");
					},
					'viewDay': function(t) {		
						location = gPath + "/index/day/" + t.getAttribute("id");
					}
				  }
			  });
			
			
			// context menu for calendar entry
			$('a[name=entry]').contextMenu('entryCtxMenu', {
				  bindings: {
					'Edit': function(t) {
					  location = gPath + "/edit/" + t.getAttribute("entryId");
					},
					'Reminder': function(t) {
						
						$('#remindersDlg').find('form').attr('action', gBase + '/reminders/add/' + t.getAttribute('entryId'));
					
						var tip = $(t).qtip({
							content: {
								title: 'Add Reminder',
								text: $('#remindersDlg')
							},
							show: {ready:true},
							hide: 'unfocus',
							api:{
								onHide: function(){
                						$(t).qtip('destroy');
                						}
							},
							style: {
								width : {min : 200},
								name: 'light',
								padding: '7px 13px',
								border:{width:1, color:'#333'},
								tip: true 
							}
						})
						.click(function(){return false;})
						
						
						return false;
						
						$.get(gBase + "/reminders/add/" + t.getAttribute("entryId"),
							function(resp){
								alert(resp)
						})
						//location = gBase + "/reminders/add/" + t.getAttribute("entryId");
					},
					'Delete': function(t) {
						var name = entryDisplayData[t.getAttribute("entryId")].name;
						if(confirm("are you sure you want to delete calendar entry \"" + name + "\"?"))
							location = gPath + "/delete/" + t.getAttribute("entryId");
					}
				  }
			  });
			
			// context menu for calendar cell in day and week views
			$('[class=hourCellWeekView],[name=hourCellDayView]').contextMenu('hourCtxMenu', {
				  bindings: {
					'AddEntry': function(t) {	 
					  location = gPath + "/add/" + t.getAttribute("id");
					}
				  }
			  });
			  
			
			
			
			
			$('a[name=entry]').each(function(){
				var text = $(this).html();
				var entryId = $(this).attr('entryId');
				$(this).qtip({
					content: {
						title: text,
						text: entryDisplayData[entryId].displayStr 
					},
					show: 'click',
					hide: 'unfocus',
					style: {
						width : {min : 200},
						name: 'light',
						padding: '7px 13px',
						border:{width:1, color:'#333'},
						tip: true, 
						title:{color:'black'}
						
					}
				})
			 });
			
			 
			$('a[name=entry]').click(function(e){
				return false;
			 });
						
						
			$('#newCategory').qtip({
				content: {
					title: 'New Category',
					text: $('#newCatDlg')
				},
				show: 'click',
				hide: 'unfocus',
				style: {
					width : {min : 200},
					name: 'light',
					padding: '7px 13px',
					border:{width:1, color:'#333'},
					tip: true
				}
		
			})
			.click(function(){return false;})
			
			
			
			
			// new category add form and callback
			$('#newCatForm').submit(function(){
				$.post(gBase + "/categories/add",
					$(this).serialize(),
					function newCatCallback(ajaxResp){
						try{
							var obj = $.parseJSON(ajaxResp);
							if(obj.success){
								// alert('Category added')
								location = location;
							}
							else
								alert('add category failed.')
						}catch(e){alert('response error;')}
						
					}
				)
				return false;
			})
			
			
			// context menu for category
			$('a[name=categoryLink]').contextMenu('categoryCtxMenu', {
				  bindings: {
					'Delete': function(t) {
					
						// check for entries before deleting category
						$.get(gBase + '/entries/entriesByCategory/' + t.getAttribute("categoryId"),
							function(resp){
								var json = $.parseJSON(resp);
								
								// if no entries just delete category, else prompt first
								if(json.entries.length == 0 || 
												confirm("This category contains one or more entries. "+
												"Are you sure you want to delete this category?\n"+
												"(Entries will not be deleted but will not longer belong to this category.)"))
										location = gBase + "/categories/delete/" + t.getAttribute("categoryId");
									
							})
						
							
						return false;
						
					},
					'SelectColor' : function(t){
						$('#selectColor').show().css({'left':$(t).offset().left + $(t).width(),
															'top':$(t).offset().top});
						
						$(document).bind('click', function(e){
								if(e.target.className == 'colorCell'){
									var form = $('<form action="'+gBase + '/categories/update/' + t.getAttribute('categoryId') + '" method=POST>'+
										'<input type=hidden name=data[Category][color] value="'+e.target.getAttribute('value')+'">'+
										'</form> ')
									$(document.body).append(form)
									form.submit();
								}
								$('#selectColor').hide();
								$(document).unbind('click', arguments.callee);
							})
					}
					
				  }
			  });
			
			
						
			$('#newCalendar').qtip({
				content: {
					title: 'New Calendar',
					text: $('#newCalendarDlg')
				},
				show: 'click',
				hide: 'unfocus',
				style: {
					width : {min : 200},
					name: 'light',
					padding: '7px 13px',
					border:{width:1, color:'#333'},
					tip: true
				}
		
			})
			.click(function(){return false;})
			
			
			
			
			
			
			
			
			
			
			
			
			// ajax, search for terms
			$("#searchForm").submit(function(){
				$.get(gBase+"/entries/search",
						$(this).serialize(),
						searchCallback);
				return false;
			});
			
			
			// remove flash after few seconds
			if($('#flashElem').children().length){
				$('#flashElem:first-child')
					.delay(1800)
					.fadeOut(300)
					.queue(function(){$(this).remove();})
			}
			
			
			$('[class=arrow]').each(function(){	
				var t = $(this).parents('[name=calendarCell]').get(0);
				$(this).bind('click', showDayDlg);
				
				function showDayDlg(){
					$.get(
						gBase + '/entries/getCalendar/day/' + t.getAttribute("id"),
						function(resp){
						
							pos = new objPos(t)
							var left = pos.right;
							var top = pos.top;
							
							$('#detailsDlg').show()
								.css({'top': top, 'left': left, 'background':'black', 'border':'1px solid black'})
								.html(resp)
								.find('[name=hourCellDayView]').contextMenu('hourCtxMenu', {
									  bindings: {
										'AddEntry': function(t) {	 
										  location = gPath + "/add/" + t.getAttribute("id");
										}
									  }
								  });
			
								
							$(document).bind('mousedown', function(e){
								if($(e.target).attr('name') == 'hourCellDayView'){
									return false;
								}
								
								$('#detailsDlg').empty().hide();
								$(document).unbind('mousedown', arguments.callee);
							})
						
					})
				}
			})
			
			// END DOCUMENT.READY
		}
	);

</script>




<!-- content table -->


<table border=0>
<tr><td style='width:900px;'>

<?php


// some utility vars used in views
$monthName = $today->format('F'); // month name for display in month and day nav bars
$currentDate = new DateTime();


// calendar table
echo "<table style='width:100%;' border=0>";


	echo "<tr style=''><td style='padding: 0px 0px 3px 0px;'>";


	echo $this->Html->link("this month", 
							array('controller'=>'entries', 'month', 
								$currentDate->format('Y'), 
								$currentDate->format('n')), 
							array('class'=>'buttonLink'));
	echo "&nbsp;"; 
	echo $this->Html->link("this week", 
							array('controller'=>'entries', 'week', 
								$currentDate->format('Y'), 
								$currentDate->format('n'), 
								$currentDate->format('d')), 
							array('class'=>'buttonLink'));
	echo "&nbsp;"; 
	echo $this->Html->link("today", 
							array('controller'=>'entries', 'day', 
								$currentDate->format('Y'), 
								$currentDate->format('n'), 
								$currentDate->format('d')), 
							array('class'=>'buttonLink'));
	

	echo "&nbsp; | "; 
	echo "&nbsp;"; 
	echo $this->Html->link('New Entry...', 
							array('controller' => 'entries', 'action' => 'add'), 
							array('class'=>'buttonLink'));
										
	if(isset($category)){
		echo '<div style="float:right; margin:0px 30px 0px 0px; color:#900;">showing only entries in: <b>'.$category['Category']['name'].'</b></div>';
	}
		
		
						
	?>
		</td></tr>
		<tr><td >
	<?php

	// draw the calendar depending on view (year, month or day)
	switch($view){

		case 'list':
			echo $this->element("calendar_list_view");
			break;

		case "month":
			echo $this->element("calendar_month_view");
			break;

		case 'day':
			echo $this->element('calendar_day_view');
			break;
			
		case "year":
			echo "todo: year view";
			break;
			
		case "week":
			echo $this->element('calendar_week_view');	
			break;
		
			
	}

	echo "\r\n";

	echo "</td></tr></table>";

	// end draw calendar




	?>

	<table style='width:100%;'><tr><td id=flashElem>

		<?php
			echo $this->Session->flash(); 
		?>
		</td></tr>
	</table>


	</td>



	<!-- right hand column -->
	<td style="width:120px; padding:0px 0px 0px 20px;" class='sidebar'>


		
		<br>




		<h2>Calendars</h2>
		<?php
			foreach($calendars as $calendar){
				$calendar = $calendar['Calendar'];
				echo $this->Html->link($calendar['name'], array('action'=>'index', "month", $year, $month, 
											'calendarId'=>$calendar['id']), 
										array('class'=>
										$calendar['id'] == $calendarId ? 
										'sidebarOptionsSelected' : 'sidebarOptions'));
			
			}
		
		echo $this->Html->link('add new calendar&raquo;', 
					array(), 
					array('id'=>'newCalendar', 'escape'=>false));	
		?>
			
		<br>	
		<br>	



		
		<h2>Categories</h2>
		
		<?php 
			
			foreach($categories as $c){
				$c = $c['Category'];
				@$style = 'color:'.$c['color'].';';
				
				$blt = $c['id'] == @$category['Category']['id'] ? "&#8226" : '';
				
				$class = $c['id'] == @$category['Category']['id'] ? 'sidebarOptionsSelected' : 'sidebarOptions';
				
				echo $this->Html->link($c['name'],
							array('controller'=>'entries', 'action'=>'index', 
								$view, $year, $month, $day, 'categoryId'=>$c['id']),
							array('name'=>'categoryLink', 'categoryId'=>$c['id'], 
								'style'=>$style, 'class'=>$class ));

			}
			
			echo $this->Html->link('show all categories', 
							array('controller'=>'entries', 'action'=>'index', 
								$view, $year, $month, 'categoryId'=>0));
			echo ' | ';
			echo $this->Html->link('add new&raquo;', 
							array('controller'=>'categories', 'action'=>'add', $year, $month), 
							array('id'=>'newCategory', 'escape'=>false));
			
		?>
		
		<div id=newCatDlg style='display:none;'>
		<?php
			echo $this->Form->create('Category', array('id'=>'newCatForm'));
			echo $this->Form->input('name');
			echo '<br>';
			echo $this->Form->end('submit', array('style'=>'background:red; align:right;'));
		?>	
		
		</div>
		
		
		<div id=newCalendarDlg style='display:none;'>
		<?php
			echo $this->Form->create('Calendar', array('controller'=>'calendars', 'action'=>'add', 'id'=>'newCalendarForm'));
			echo $this->Form->input('name');
			echo '<br>';
			echo $this->Form->end('submit', array('style'=>'background:red; align:right;'));
		?>	
		
		</div>
		
		
		
		<br>
		<br>
		
		<h2>View</h2>
		<?php
			//echo $this->Html->link('year', array('action'=>'index', "year", $year, $month));
			echo $this->Html->link('month', array('action'=>'index', "month", $year, $month), 
										array('class'=>($view == 'month' ? 
										'sidebarOptionsSelected' : 'sidebarOptions') ));
			echo $this->Html->link('week', array('action'=>'index', "week", $year, $month, $day), 
										array('class'=>($view == 'week' ? 
										'sidebarOptionsSelected' : 'sidebarOptions') ));
			echo $this->Html->link('day', array('action'=>'index', "day", $year, $month, 1), 
										array('class'=>($view == 'day' ? 
										'sidebarOptionsSelected' : 'sidebarOptions') ));
			echo $this->Html->link('list', array('action'=>'index', "list"), 
										array('class'=>($view == 'list' ? 
										'sidebarOptionsSelected' : 'sidebarOptions') ));
		?>
		
		<br>	


		<h2>Search</h2>
		
		<form id="searchForm" action="">
			<input type="text" name="searchTerms"></input>
		<?php echo $this->Form->end(); ?>
		
		<br><br>
		<!--
		<h2>Email</h2>
		<?php
			echo $this->Html->link('day', array('action'=>'index', "day", $year, $month, 1)) . "<br>";
			echo $this->Html->link('list', array('action'=>'index', "list")) . '<br>';
			echo $this->Html->link('new mailing list', array('action'=>'index', "year", $year, $month)) . "<br>";
		?>
		-->
		
		<br><br>
		
		
	</td>
	</tr>
</table>


<span style='display:none;'>
<!-- Context Menu -->
<div class="contextMenu" id="dateCtxMenu">
	<ul>
		<li id="addSingleDateEntry"> Add Entry For this Day</li>
		<li id="viewDay"> Go to Day view</li>
		<li id="highlightDay"> Highlight This Day</li>
		
	</ul>
</div>
<!-- Context Menu -->
<div class="contextMenu" id="hourCtxMenu">
	<ul>
		<li id="AddEntry"> Add Entry For this Date/Time</li>
	</ul>
</div>

<!-- Context Menu -->
<div class="contextMenu" id="entryCtxMenu">
	<ul>
		<li id="Edit"> Edit This Calendar Entry</li>
		<li id="Reminder"> Add Reminder</li>
		<li id="Delete"> Delete This Entry</li>
		
	</ul>
</div>


<!-- Context Menu -->
<div class="contextMenu" id="categoryCtxMenu">
	<ul>
		<li id="SelectColor"> Select Color</li>
		<li id="Delete"> Delete this Category</li>
	</ul>
</div>


</span>
  
  
  

<div style='position:absolute; top:100px; left:100px; display:none;' id='selectColor'>
	<table style='border:1px solid gray;'>
		<?php
			$colors = array();
			for($i=0; $i<15; $i+=3){
				for($j=0; $j<15; $j+=3){
					for($k=0; $k<15; $k+=4){
						array_push($colors, "#".dechex($i).dechex($j).dechex($k));
					}
				}
			}
			//	echo count($colors);
		//	shuffle($colors);
			for($j = 0; $j < 10; $j++){
				echo '<tr>';
				for($i = 0; $i < 10; $i++){
					$idx = $j*10 + $i;
					echo '<td class=colorCell style="background: '.$colors[$idx].';" value='.$colors[$idx].'><td>';
				}
				echo '</tr>';
			}
		?>
	</table>
</div>
  
<!-- Individual Entry Details Dialog -->
<div id=detailsDlg style='position:absolute;'></div>
<div id=remindersDlg style='display:none;'>
	<?php
		echo $this->Form->create('Reminder', array('id'=>'newReminder'));
		
		
		echo 'Enter days and/or hours before to receive reminder (or just click submit for 1 day in advance)<br>';
		
		echo '<table><tr><td>';
		echo $this->Form->input('days', array('type'=>'select', 'options'=>array(0, 1, 2, 3, 4, 5, 6, 7), 'label'=>'days'));
		echo '</td><td>';
		echo $this->Form->input('hours', array('type'=>'select', 
						'options'=>array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), 
						'label'=>'hours'));
		echo '</td></tr></table>';
		
		echo '<br>';
		
		
		echo $this->Form->submit('submit', array('label'=>'submit', 'name'=>'submit', 'div'=>false));
		echo $this->Form->submit('cancel', array('label'=>'submit', 'name'=>'submit', 'div'=>false));
		
	?>	
		
	
</div>



