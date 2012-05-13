
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


	function dayOfWeekName(num){
		switch(num){
			case 1: return 'Sunday'; 
			case 2: return 'Monday'; 
			case 3: return 'Tuesday'; 
			case 4: return 'Wednesday'; 
			case 5: return 'Thursday'; 
			case 6: return 'Friday'; 
			case 7: return 'Saturday'; 
		}
	}
	
	function ordinal(number) {
		var suffix = '';
		if (number % 100 > 10 && number %100 < 14)
			suffix = "th";
		else
			switch(number % 10) {
				case 0: suffix = "th"; break;
				case 1: suffix = "st"; break;
				case 2: suffix = "nd"; break;
				case 3: suffix = "rd"; break;
				default: suffix = "th"; break;
			}
		return number + suffix;	
	}


	var entriesArr = {
	
		<?php
		$temp = array();
		foreach($entries as $index => $entry){
			echo $entry['Entry']['id'] .":" .json_encode($entry) . ",\r\n\r\n";
		}
		?>
	
		
		getEntryNameById : function(id){
			var entry = this[id].Entry;
			return entry.name;
		},
	
		getEntryDisplayById : function(id){
			var entry = this[id].Entry;
			var str = '';
			
			if(this[id].Category.id){
				str += "<b>Category:</b> <span style='color:#303'>" +this[id].Category.name + "</span><br><br>";
			}
			
			if(entry.address)
				str += entry.address + "<br>";
				
			if(entry.city)
				str += entry.city;
			if(entry.state)
				str += ", " + entry.state;
			if(entry.country)
				str += " " + entry.country;
			
			if(entry.city || entry.state || entry.country){
				str += "<br>";
			}
			
			if(entry.address && entry.city && entry.state){
				str += "<a href='http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q="+
						escape(entry.address)+",+"+escape(entry.city)+",+"+escape(entry.state)+
						"&aq=0&oq=&sll=37.0625,-95.677068&sspn=48.287373,107.138672&vpsrc=0&ie=UTF8&hq="+
						escape(entry.address)+",+"+escape(entry.city)+",+"+escape(entry.state)+"&t=m&z=15' "+
						"target='_blank'>map</a><br>";
			}
			
			if(str.length)
				str += "<br>";
			
			str += "<b>Dates:</b><ul style='list-style:none;'>";
			try{
			for(var i in this[id].Date){
				str += "<li>" + formatDateObj(this[id].Date[i]) + '</li>';
			}
			}catch(a){alert(a)}
			str+= "</ul>";
			
			function formatDateObj(date){
				var str = '';
				if(date.days_of_week){
					if(date.weeks_pattern == 'every_week')
						str += 'Every ';
					if(date.weeks_pattern == 'nth_week')
						str += 'Every Other ';
						
					else if(date.weeks_pattern == 'nth_weekdays_of_month'){
						var arr = date.weeks_of_month.split(',');
						for(var i in arr)
							arr[i] = ordinal(arr[i]);
						if(arr.length == 1)
							str += arr[0] + " ";
						else 
							str += arr.join(' & ') + " ";
					}
					
					var arr = date.days_of_week.split(',');
					for(var i in arr){
						arr[i] = dayOfWeekName(parseInt(arr[i]))
					}
					str += arr.join(", ");
				}
				if(date.start_date)
					str += date.start_date;
				if(date.end_date)
					str += " thru " + date.end_date;
				
				var start = getClockTime(date.start_time)
				var end = getClockTime(date.end_time)
				
				if(start && end) 
					str +=  ", " + start + "-" + end;
				else if(start && !end)
					str += ", " + start;
				else if(!start && end)
					str += ', [start time unspecified]' + "-" + end + "<br>";
				
				function getClockTime(time){
					var now  = new Date();
					try{var t = time.split(":")}catch(e){return null};
					now.setHours(t[0], t[1], t[2], 0);
					var hour   = now.getHours();
					var minute = now.getMinutes();
					var second = now.getSeconds();
					var ap = "AM";
					if (hour   > 11) { ap = "PM";             }
					if (hour   > 12) { hour = hour - 12;      }
					if (hour   == 0) { hour = 12;             }
					if (hour   < 10) { hour   = hour;   }
					if (minute < 10) { minute = "0" + minute; }
					if (second < 10) { second = "0" + second; }
					var timeString = hour + ':' + minute + (parseInt(second) ? (':' + second) : "") + ap;
					return timeString;
				} 
				
				return str;
			}
		
			str += "<br><a href='"+path+"/edit/"+entry.id+"'> edit</a>" + " | " + 
					"<a href='"+path+"/delete/"+entry.id+"' onclick='return confirm(\"Entry will be deleted. Are you sure?\")'> delete</a>";
				
			return str;
		}
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
					'openDayView': function(t) {
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
						$.get(basePath + "/reminders/add/" + t.getAttribute("entryId"),
							function(resp){
								alert(resp)
						})
						//location = basePath + "/reminders/add/" + t.getAttribute("entryId");
					},
					'Delete': function(t) {
						
						var name = entriesArr.getEntryNameById(t.getAttribute("entryId"));
						if(confirm("are you sure you want to delete calendar entry \"" + name + "\"?"))
							location = path + "/delete/" + t.getAttribute("entryId");
					}
				  }
			  });
			
			// context menu for calendar entry
			$('[class=hourCellWeekView],[class=hourCell]').contextMenu('hourCtxMenu', {
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
						text: entriesArr.getEntryDisplayById(entryId)
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
					.delay(3000)
					.fadeOut(300)
					.queue(function(){$(this).remove();})
			}
			
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


echo "</td></tr>";






// draw the calendar depending on view (year, month or day)
switch($view){

	case 'list':
		// all entries as a list, not calendar layout
		echo $this->element("list_view");
		
		break;

		
	case "month":

		echo $this->element("month_view");

	
		break;
		
		

	case 'day':
		echo $this->element('day_view');
		break;
		
	case "year":
		echo "year view";
		break;
		
	case "week":
		echo $this->element('week_view');
		
		break;
		
		
		
}



echo "\r\n";



echo "</table>";

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
		foreach($categories as $catId => $cat){
			if(isset($category) && $catId == $category)
				echo "<span class='selectedInactiveLink'>". $cat . "</span>";
			else
				echo $this->Html->link($cat,
									array('controller'=>'entries', 'action'=>'index', 
										$view, $year, $month, $day, 'categoryId'=>$catId),
									array('name'=>'categoryLink', 'categoryId'=>$catId));
			
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
		echo $this->Form->end('submit');
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
		<li id="openDayView"> View Day</li>
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
		<li id="Delete"> Delete this Category</li>
	</ul>
</div>




</span>
  
<!-- Individual Entry Details Dialog -->
<div id=detailsDlg></div>
