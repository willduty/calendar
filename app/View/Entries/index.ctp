

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
	
		getEntryDisplayById : function(id){
			var entry = this[id].Entry;
			var str = '';
			
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
			
			for(var i in this[id].Date){
				str += "<li>" + formatDateObj(this[id].Date[i]) + '</li>';
			}
			
			str+= "</ul>";
			
			function formatDateObj(date){
				var str = '';
				if(date.days_of_week){
					var arr = date.weeks_of_month.split(',');
					if(date.weeks_of_month == 'every_week')
						str += 'Every ';
					else{
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
		
			str += "<a href='"+path+"/edit/"+entry.id+"'> edit</a>" + " | " + 
					"<a href='"+path+"/delete/"+entry.id+"' onclick='return confirm(\"entry will be deleted are you sure?\")'> delete</a>";
				
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
					'Delete': function(t) {
						if(confirm("are you sure you want to delete this calendar entry?"))
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
						tip: true
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
			function newCatCallback(ajaxResp){
			//	alert(ajaxResp)
				var obj = ajaxResp;
				location = location;
			}
			
			$('#newCatForm').submit(function(){
				$.post(basePath + "/categories/add",
					$(this).serialize(),
					newCatCallback
				)
				return false;
			})
			
			
			// context menu for category
			$('a[name=categoryLink]').contextMenu('categoryCtxMenu', {
				  bindings: {
					'Delete': function(t) {
						if(confirm("are you sure you want to delete this category?"))
							location = basePath + "/categories/delete/" + t.getAttribute("categoryId");
					}
				  }
			  });
			
			
			// ajax, search for terms
			$("#searchForm").submit(function(){
				$.get("/calendar/entries/search",
						$(this).serialize(),
						searchCallback);
				return false;
			});
			
		}
	);
</script>

<table>
<tr><td style='width:900px;'>

<?php


// some utility vars used in views
$monthName = $today->format('F'); // month name for display in month and day nav bars
$dayList = array(1 => 'Sun', 2 => 'Mon', 3 => 'Tue', 4 => 'Wed', 5 => 'Thu', 6 => 'Fri', 7 => 'Sat'); 
$currentDate = new DateTime();


// begin calendar table
echo "<table style='width:100%; '>";


echo "<tr style=''><td colspan=7 style='background:white; padding: 2px 0px 2px 2px;'>";


echo $this->Html->link("today", array('controller'=>'entries', 'day', $currentDate->format('Y'), $currentDate->format('n'), $currentDate->format('d')), array('class'=>'buttonLink'));
echo "&nbsp;"; 
echo $this->Html->link("this month", array('controller'=>'entries', 'month', $currentDate->format('Y'), $currentDate->format('n')), array('class'=>'buttonLink'));
echo "&nbsp;"; 
echo $this->Html->link("this week", array('controller'=>'entries', 'week', $currentDate->format('Y'), $currentDate->format('n'), $currentDate->format('d')), array('class'=>'buttonLink'));


echo "&nbsp; | "; 
echo "&nbsp;"; 
echo $this->Html->link('New Entry...', array('controller' => 'entries', 'action' => 'add'), array('class'=>'buttonLink'));


echo "</td></tr>";






// draw the calendar depending on view (year, month or day)
switch($view){

	case 'list':
		// all entries as a list, not calendar layout
		?>
		<tr>
			<td colspan=7 style='background-color:black; color:white; align:center;'>
				<table class='calendarHdr'><tr>	
					<td>All Calendar Entries</td>
				</tr></table>
			</td>
		</tr>
		<tr><td colspan=7  style='height:500px;'>
			<table style='width:100%;'>
			
			<tr>
				<td class=inactiveLink style="color:white; background:gray; width:30%">Name</td>
				<td class=inactiveLink style="color:white; background:gray; width:55%">Date</td>
				<td class=inactiveLink style="color:white; background:gray; width:15%">Category</td>
			</tr>
		
		<?php
		foreach($entries as $entry){
			echo '<tr>';
			
			echo '<td style="width:30%">';
			echo $this->Calendar->makeCalendarDateEntryLink($entry);
			echo '</td>';
			
			echo '<td style="width:55%" class=inactiveLink>';
			$dateStrings = array();
			foreach($entry['Date'] as $date){
				array_push($dateStrings, $this->Calendar->dateAsString($date));
			}
			echo implode(', ', $dateStrings);
			echo '</td>';
			
			echo '<td style="width:15%">';
			if(isset($entry['Entry']['category_id']))
				echo '<div class=inactiveLink>'.$categories[$entry['Entry']['category_id']].'</div>';
			echo '</td>';
			
			echo '</tr>';
		}
		echo '</table></td></tr>';
		break;

		
	case "month":

		// calendar header
		$nextMonth = $month + 1 < 13 ? $month + 1 : 1;
		$prevMonth = $month - 1 > 0 ? $month - 1 : 12;

		$prevLink = $this->Html->link('<< prev', 
										array('controller'=>'entries', 
										'action'=>'index', 
										$view,
										($prevMonth == 12) ? $year - 1 : $year, 
										$prevMonth), 
										array('class'=>'prevnext'));
		$nextLink = $this->Html->link('next >>', 
										array('controller'=>'entries', 
										'action'=>'index', 
										$view,
										($nextMonth == 1) ? $year + 1 : $year, 
										$nextMonth), 
										array('class'=>'prevnext'));

		?>

			<!-- calendar header/nav -->
			<tr>
				<td colspan=7>
					<table class='calendarHdr'>
						<tr>	
							<td class='calendarHdr' style='background:black;'> <?php echo $prevLink; ?> </td>
							<td class='calendarHdr' style="color:white; font-weight:bold; background:black"> <?php echo $monthName . " " . $year; ?> </td>
							<td class='calendarHdr' style='background:black;'> <?php echo $nextLink; ?> </td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- week day names -->
			<tr>
				<?php foreach($dayList as $weekDay){ ?>
				<td style='width:100px' class='calendarSubHdr'><?php echo $weekDay; ?></td>
				<?php } ?>
			</tr>

		<?php 



		// calendar body
		$monthCtr = $month;
		$monthsArray = $this->Calendar->getMonthViewArray($entries, $year, $month);
		reset($monthsArray);
		
		$firstDayOfMonth = $today->format('w');
		$firstDayOfMonth++;
		if($firstDayOfMonth == 8)
			$firstDayOfMonth = 1;
		
		// do we need to show a bit of last month?
		// if $firstDayOfMonth is sunday then no: set $monthArray to current month. else to prev month 	
		if($firstDayOfMonth == 1){
			$monthArray = next($monthsArray); 
			$dayCtr = 1;
		}
		else{
			$monthCtr = $prevMonth;
			$monthNthDayMap = $this->Calendar->getNthWeekdayOfMonthMap(($prevMonth == 12) ? $year - 1 : $year, $prevMonth);
			$dayCtr = end($monthNthDayMap[1]); // last sunday of previous month
			$monthArray = current($monthsArray); 
		}

		$continueFlag = true;
		
		// loop over and over to make row for each week starting with week 
		// containing first day of month to week containing last day of month.
		while(true){
			
			// make row of days of week 
			echo "<tr>";
			
			foreach($dayList as $key => $weekDay){
				
				// if we're out of days for the month, switch to following month
				if(!isset($monthArray[$dayCtr])){
					$dayCtr = 1;
					$monthArray = next($monthsArray);
					$monthCtr = ($monthCtr == 12 ? 1 : $monthCtr + 1);
					
				}
				
				// set up cell style 
				$cellStyle = "";
				
				if(date("jnY") ==  $dayCtr . ($monthCtr) . $year){
					// highlight if day is today
					$cellStyle .= "border-style:solid; border-width:2px; border-color:red; background-color:pink;";
				}

				if(key($monthsArray) != 1) // gray out days not in month
					$cellStyle .= "background-color:lightgray;";
				
				
				// finally, draw the calendar day cell
				echo "<td style='$cellStyle' class='calendarCell' id='$year/$monthCtr/$dayCtr'>";
				
				echo $dayCtr . "<br>"; // date of month numeral
				
				// entries for day
				echo "<div class='noOverflowDiv' style='width:100px; height:70px;'>";
				if(is_array($monthArray[$dayCtr])){
					foreach($monthArray[$dayCtr] as $entry){
						
						// show calendar date entry
						echo $this->Calendar->makeCalendarDateEntryLink($entry);
						echo "<br>";
					}
				}
				echo "</div></td>";
				
				// break weeks loop if day is saturday (7) and it's either the last day of current month or we are in nextmonth
				if($key == 7){
					if(!isset($monthArray[$dayCtr + 1]) || key($monthsArray) == 2)
						$continueFlag = false;
				}
				
				$dayCtr++; // increment day of month counter
			}
			
			// make row of days of week
			echo "</tr>";
			
			if(!$continueFlag)
				break;			
		}
		break;
		
		

	case 'day':

		$dayHoursArray = $this->Calendar->getDayViewArray($entries, $year, $month, $day);
			
		$nextMonth = $month;
		$prevMonth = $month;
		
		$numDays = $this->Calendar->getNumDaysInMonth($year, $month);
		$nextDay = $day+1;
		if($nextDay > $numDays){
			$nextDay = 1;
			$nextMonth = $month + 1 < 13 ? $month + 1 : 1;
		}
		$prevDay = $day - 1;
		if($prevDay < 1){
			$prevMonth = $month - 1 > 0 ? $month - 1 : 12;
			$prevDay = $this->Calendar->getNumDaysInMonth($year, $prevMonth);;
		}

		$prevLink = $this->Html->link('<< prev', 
										array('controller'=>'entries', 
										'action'=>'index', 
										$view,
										($prevMonth == 12) ? $year - 1 : $year, 
										$prevMonth,
										$prevDay), 
										array('class'=>'prevnext'));
		$nextLink = $this->Html->link('next >>', 
										array('controller'=>'entries', 
										'action'=>'index', 
										$view,
										($month == 12 && $day == 31) ? $year + 1 : $year, 
										$nextMonth,
										$nextDay), 
										array('class'=>'prevnext'));
		
		?>
		<tr>
			<td colspan=5>
				<table class='calendarHdr'><tr>	
					<td class='calendarHdr' style='background:black;'> <?php echo $prevLink; ?> </td>
					<td class='calendarHdr' style="color:white; font-weight:bold; background:black"> <?php echo $monthName . " " . $day . ", " . $year . ' ('.$today->format('l').')'; ?> </td>
					<td class='calendarHdr' style='background:black;'> <?php echo $nextLink; ?> </td>
				</tr></table>
			</td>
		</tr>

		<?php
		// all day events...
		if(@count($dayHoursArray['allday'])):
		?>
			<tr>
				<td colspan=4 style='background-color:white; align:left;'>
					<div class=subHdr>All Day Entries:</div>
					<?php 
						
						foreach($dayHoursArray['allday'] as $dayEntry){
							echo $this->Calendar->makeCalendarDateEntryLink($dayEntry);
							echo "<br>";
						}
					?>
				</td>
			</tr>

		
		<?php
			endif;
		?>
		
		<!-- am/pm -->
		
			<?php
				// echo count($dayHoursArray);
				for($i=0; $i<12; $i++){ 
					$hour = ($i == 0) ? 12 : $i;
					
					echo "<tr>";
					echo "<td style='width:10%' class=hourCellHdr>$hour:00 AM</td>";
					$id = $today->format('Y/n/j/') . $hour . "/AM";
					echo "<td style='width:40%' class=hourCell id=$id>";
					if(isset($dayHoursArray[$hour])){
						foreach($dayHoursArray[$hour] as $entry){
							echo $this->Calendar->makeCalendarDateEntryLink($entry);
						}
					}
					echo "</td>";
					
					if($i==0)
						echo "<td rowspan=12 class=spacerCellDayView></td>";
					
					echo "<td style='width:10%' class=hourCellHdr>$hour:00 PM</td>";
					$id = $today->format('Y/n/j/') . $hour . "/PM";
					echo "<td style='width:40%' class=hourCell id=$id>";
					
					if(isset($dayHoursArray[$hour + 12])){
						foreach($dayHoursArray[$hour + 12] as $entry){
							
							// calendar date entry
							echo $this->Calendar->makeCalendarDateEntryLink($entry);
							echo "<br>";
				
						}
					}
					
					echo "</td>";
					echo "</tr>";	
				}
				
				?>
		

		
		<?php

		break;
		
	case "year":
		echo "year view";
		break;
		
	case "week":
	
		echo "<tr><td style='background:#eee;'>";
		
			// begin week view table
			
			echo "<table style='height:500px; width:100%;' class=weektable >";
			
			// get first day of week relative to $today
			$dayOfWeek = $this->Calendar->getFirstDayOfWeek($today);
			
			
			// column headers for days of week
			$prevLink = $this->Calendar->getFirstDayOfPrevWeek($today);
				
			$prevLink = $this->Html->link('<< prev', 
											array('controller'=>'entries', 
											'week',
											$prevLink->format('Y'), 
											$prevLink->format('n'), 
											$prevLink->format('j')), 
											array('class'=>'prevnext'));

			$nextLink = $this->Calendar->getFirstDayOfNextWeek($today);
			$nextLink = $this->Html->link('next >>', 
											array('controller'=>'entries', 
											'week',
											$nextLink->format('Y'), 
											$nextLink->format('n'), 
											$nextLink->format('j')), 
											array('class'=>'prevnext'));
			
			// week view calender nav bar
			echo 
			"<tr>
				<td colspan=20 style='height:20px;'>
					<table class='calendarHdr' border=1>
						<tr>	
							<td class='calendarHdr' style='background:black;'> $prevLink </td>
							<td class='calendarHdr' style='color:white; font-weight:bold; background:black'>
								Week of ".$dayOfWeek->format('M j Y')." </td>
							<td class='calendarHdr' style='background:black;'> $nextLink </td>
						</tr>
					</table>
				</td>
			</tr>";
			
			
			
			// week view calender day column headers
			
			echo "<tr>";
			
			$hdrArr = array();
			$daysArr = array();
			
			for($n=1; $n<8; $n++){
				$weekDay = $dayOfWeek;
			
				array_push($hdrArr, "<td class=calendarSubHdr style='height:20px;' colspan=2>".
					$weekDay->Format('D, M j')."</td>");
				
				// for each day of week get hours array. index for each hour may be empty or contain entries
				$dayHoursArray = $this->Calendar->getDayViewArray($entries, $weekDay->format('Y'), 
					$weekDay->format('n'), $weekDay->format('j'));
				array_push($daysArr, $dayHoursArray);
				
				$weekDay->add(new DateInterval("P1D"));
				
			}
			echo implode("<td class=spacerCellWeekView></td>", $hdrArr); 
			
			echo "</tr>";
			
			
			
			// all-day events row
			$tds = array();
			$boolAllDay = false;
			foreach($daysArr as $d){
					$str = "<td colspan=2 class='hourCellWeekView' >
							<div>All Day Entries:</div>";
								
				if(@count($d['allday'])){
					foreach($d['allday'] as $dayEntry){
						$str .= "<div class=noOverflowDiv style='width:120px'>" . 
							$this->Calendar->makeCalendarDateEntryLink($dayEntry);
						$str .=  "</div>";
					}
				}
				$str .= "</td>";
				array_push($tds, $str);
			}
			echo "<tr>".implode("<td class=spacerCellWeekView></td>", $tds)."</tr>";
				
			
		
			// week view calender hour columns
			echo "<tr>";

			$rows = array();		
			for($hr=0; $hr<13; $hr++){
				echo "<tr>";
				$tds = array();
				$dayOfWeek = $this->Calendar->getFirstDayOfWeek($today);
			
				foreach($daysArr as $d){
				
					$hour = $hr == 0 ? 12 : $hr;
					$id = $dayOfWeek->format('Y/n/j/') . $hour . "/AM";
					$str = "<td class='hourCellWeekView' id=$id><div class=noOverflowDiv  style='width:61px'>$hour:00 AM<br>";
					foreach($d[$hr] as $entry){
						$str .= $this->Calendar->makeCalendarDateEntryLink($entry) . "<br>";
					}
					$id = $dayOfWeek->format('Y/n/j/') . $hour . "/PM";
					$str .= "</div></td><td class=hourCellWeekView id=$id><div class=noOverflowDiv style='width:61px'>$hour:00 PM<br>";
					foreach($d[$hr+12] as $entry){
						$str .= $this->Calendar->makeCalendarDateEntryLink($entry). "<br>";
					}
					$str .= "</div></td>";
					array_push($tds, $str);
					
					$dayOfWeek->add(new DateInterval('P1D'));
				}
				echo implode("<td class=spacerCellWeekView></td>", $tds);
				echo "</tr>";
			}
			
			
			echo "</tr>";
		
			echo "</table>";
			// end week view table 
			
		echo "</td></tr>";
		break;
		
		
		
}



echo "\r\n";



echo "</table>";

// end draw calendar




?>

<br>
<table style='width:100%;'><tr><td>
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
		echo $this->Html->link('[show all entries]', 
								array('controller'=>'entries', 'action'=>'index', 
									$view, $year, $month, 'categoryId'=>0));
		echo "<br>";
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


<?php 
?>

