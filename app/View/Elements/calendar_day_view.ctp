

<?php

	$monthName = $today->format('F'); 

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
	
	
	<table class=hoursTableClass>

	
	<?php if (!isset($showHdr) || $showHdr == true): ?>
	<tr>
		<td colspan=5>
			<table class='calendarHdr'>
				<tr>	
					<td class='calendarHdr' style='background:black;'> <?php echo $prevLink; ?> </td>
					<td class='calendarHdr' style="color:white; font-weight:bold; background:black"> 
						<?php echo $monthName . " " . $day . ", " . $year . ' ('.$today->format('l').')'; ?> 
					</td>
					<td class='calendarHdr' style='background:black;'> <?php echo $nextLink; ?> </td>
				</tr>
			</table>
		</td>
	</tr>
	<?php endif; ?>
	
	
	<?php
	// all day events...
	if(@count($dayHoursArray['allday'])):
	?>
		<tr>
			<td colspan=5 style=' align:left;'>
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
		
		echo "<tr class=hourCellRow>";
		echo "<td style='width:10%' class=hourCellHdr>$hour:00 am</td>";
		$id = $today->format('Y/n/j/') . $hour . "/00/am";
		echo "<td style='width:40%' class=hourCell name=hourCell id=$id>";
		if(isset($dayHoursArray[$hour])){
			foreach($dayHoursArray[$hour] as $entry){
				echo $this->Calendar->makeCalendarDateEntryLink($entry);
			}
		}
		echo "</td>";
		
		if($i==0)
			echo "<td rowspan=12 class=spacerCellDayView></td>";
		
		echo "<td style='width:10%' class=hourCellHdr>$hour:00 pm</td>";
		$id = $today->format('Y/n/j/') . $hour . "/00/pm";
		echo "<td style='width:40%' class=hourCell name=hourCell id=$id>";
		
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
</table>