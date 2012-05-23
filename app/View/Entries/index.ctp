
<?php 
	echo $this->Html->script('jquery-1.7.1.min.js'); 
	echo $this->Html->script('jquery-ui-1.8.16.custom.min.js'); 
	echo $this->Html->css('smoothness/jquery-ui-1.8.16.custom.css');
	echo $this->Html->script('jquery.contextmenu.r2.js'); 
	echo $this->Html->script('jquery.qtip-1.0.0-rc3');
	
?>

<script type='text/javascript'>
	var basePath = "<?php echo $this->base ?>";
	var path = "<?php echo $this->base . "/" . $this->params['controller']; ?>";

	var entryDisplayData = {
	<?php
	foreach($entries as $index => $entry){
			$entryDisplayStr = $this->Calendar->getEntryDisplayString($entry);
			$name = str_replace("\"", "\\\"", $entry['Entry']['name']);
			echo $entry['Entry']['id'] . ":{name:\"$name\", displayStr:\"$entryDisplayStr\"},";
		}
	?>
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
					  location = path + "/add/" + t.getAttribute("id");
					},
					'highlightDay': function(t) {
					  location = path + "/highlightDay/<?php echo $view; ?>/" + t.getAttribute("id");
					},
					'viewDay': function(t) {		
						location = path + "/index/day/" + t.getAttribute("id");
					}
				  }
			  });
			
			
			// context menu for calendar entry
			$('a[name=entry]').contextMenu('entryCtxMenu', {
				  bindings: {
					'Edit': function(t) {
					  location = path + "/edit/" + t.getAttribute("entryId");
					},
					'Reminder': function(t) {
						var _t = t;
						$.get(basePath + "/reminders/add/" + t.getAttribute("entryId"),
							function(resp){
								alert(resp)
						})
						//location = basePath + "/reminders/add/" + t.getAttribute("entryId");
					},
					'Delete': function(t) {
						var name = entryDisplayData[t.getAttribute("entryId")].name;
						if(confirm("are you sure you want to delete calendar entry \"" + name + "\"?"))
							location = path + "/delete/" + t.getAttribute("entryId");
					}
				  }
			  });
			
			// context menu for calendar entry
			$('[class=hourCellWeekView],[name=hourCell]').contextMenu('hourCtxMenu', {
				  bindings: {
					'AddEntry': function(t) {	 
					  location = path + "/add/" + t.getAttribute("id");
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
						font:'normal 12px verdana',
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
				$.post(basePath + "/categories/add",
					$(this).serialize(),
					function newCatCallback(ajaxResp){
						try{
							var obj = $.parseJSON(ajaxResp);
							if(obj.success){
								alert('Category added')
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
						$.get(basePath + '/entries/entriesByCategory/' + t.getAttribute("categoryId"),
							function(resp){
								var json = $.parseJSON(resp);
								
								// if no entries just delete category, else prompt first
								if(json.entries.length == 0 || 
												confirm("This category contains one or more entries. "+
												"Are you sure you want to delete this category?\n"+
												"(Entries will not be deleted but will not longer belong to this category.)"))
										location = basePath + "/categories/delete/" + t.getAttribute("categoryId");
									
							})
						
							
						return false;
						
					},
					'SelectColor' : function(t){
						$('#selectColor').show().css({'left':$(t).offset().left + $(t).width(),
															'top':$(t).offset().top});
						
						$(document).bind('click', function(e){
								if(e.target.className='colorCell'){
									var form = $('<form action="'+basePath + '/categories/update/' + t.getAttribute('categoryId') + '" method=POST>'+
										'<input type=hidden name=data[Category][color] value="'+e.target.getAttribute('value')+'"/>'+
										'</form>')
									form.submit();
								}
								$('#selectColor').hide();
								$(document).unbind('click', arguments.callee);
							})
					}
					
				  }
			  });
			
			
			// ajax, search for terms
			$("#searchForm").submit(function(){
				$.get(basePath+"/entries/search",
						$(this).serialize(),
						searchCallback);
				return false;
			});
			
			
			// remove flash after few seconds
			if($('#flashElem').children().length){
				$('#flashElem:first-child')
					.delay(2000)
					.fadeOut(300)
					.queue(function(){$(this).remove();})
			}
			
			
			$('[class=arrow]').each(function(){	
				var t = $(this).parents('[name=calendarCell]').get(0);
				$(this).bind('click', showDayDlg);
				
				function showDayDlg(){
					$.get(
						basePath + '/entries/getCalendar/day/' + t.getAttribute("id"),
						function(resp){
							var left = t.offsetWidth + t.offsetLeft;
							$(t).parents().each(function(){left+=this.offsetLeft})
							var top = t.offsetTop;
							
							$('#detailsDlg').show()
								.css({'top': top, 'left': left, 'background':'black', 'border':'1px solid black'})
								.html(resp)
								.find('[name=hourCell]').contextMenu('hourCtxMenu', {
									  bindings: {
										'AddEntry': function(t) {	 
										  location = path + "/add/" + t.getAttribute("id");
										}
									  }
								  });
			
								
							$(document).bind('mousedown', function(e){
								if($(e.target).attr('name') == 'hourCell'){
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

<table>
<tr><td style='width:900px;'>

<?php


// some utility vars used in views
$monthName = $today->format('F'); // month name for display in month and day nav bars
$currentDate = new DateTime();


// begin calendar table
echo "<table style='width:100%;'>";


echo "<tr style=''><td colspan=7 style='background:white; padding: 2px 0px 2px 2px;'>";


echo $this->Html->link("today", 
						array('controller'=>'entries', 'day', 
							$currentDate->format('Y'), 
							$currentDate->format('n'), 
							$currentDate->format('d')), 
						array('class'=>'buttonLink'));
echo "&nbsp;"; 
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


echo "&nbsp; | "; 
echo "&nbsp;"; 
echo $this->Html->link('New Entry...', 
						array('controller' => 'entries', 'action' => 'add'), 
						array('class'=>'buttonLink'));

echo "</td></tr><tr><td>";


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


<!-- spacer column -->
<td style="width:50px; background:white;"></td>

<!-- right hand column -->
<td style="width:250px; background:white;">


	<h2>Search</h2>
	
	<form id="searchForm" action="">
		<input type="text" name="searchTerms"></input>
		<button type=submit>go</button>
	<?php echo $this->Form->end(); ?>
	
	<br><br>
	
	
	<h2>Categories</h2>
	
	<?php 
		
		// categories loaded separately in controller
		echo $this->Html->link('[show all entries]', 
								array('controller'=>'entries', 'action'=>'index', 
									$view, $year, $month, 'categoryId'=>0),
								array('style'=>'color:#669;'));
		echo "<br>";
		
		foreach($categories as $cat){
			$cat = $cat['Category'];
			@$style = 'color:'.$cat['color'].';';
			
			if(isset($category) && $cat['id'] == $category)
				echo "<span class='selectedInactiveLink'>". $cat['name'] . "</span>";
			else
				echo $this->Html->link($cat['name'],
									array('controller'=>'entries', 'action'=>'index', 
										$view, $year, $month, $day, 'categoryId'=>$cat['id']),
									array('name'=>'categoryLink', 'categoryId'=>$cat['id'], 'style'=>$style ));
			
			echo "<br>";
		}
		
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
	
	
	<br><br><br>
	
	<h2>View</h2>
	<?php
		//echo $this->Html->link('year', array('action'=>'index', "year", $year, $month)) . "<br>";
		echo $this->Html->link('month', array('action'=>'index', "month", $year, $month)) . "<br>";
		echo $this->Html->link('week', array('action'=>'index', "week", $year, $month, $day)) . "<br>";
		echo $this->Html->link('day', array('action'=>'index', "day", $year, $month, 1)) . "<br>";
		echo $this->Html->link('list', array('action'=>'index', "list"));
	?>
	
	
	
	<br><br><br>
	
	<h2>Email</h2>
	<?php
		echo $this->Html->link('day', array('action'=>'index', "day", $year, $month, 1)) . "<br>";
		echo $this->Html->link('list', array('action'=>'index', "list")) . '<br>';
		echo $this->Html->link('new mailing list', array('action'=>'index', "year", $year, $month)) . "<br>";
	?>
	
	
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
			shuffle($colors);
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
