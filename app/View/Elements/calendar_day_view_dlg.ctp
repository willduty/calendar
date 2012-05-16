<?php
	$monthName = $today->format('F'); 
	$dayHoursArray = $this->Calendar->getDayViewArray($entries, $year, $month, $day);	
	?>

	
	<table class=dlg_day_view_table>
	
	<tr>
		<td colspan=5 >
			<table class=dlg_day_view_hdr>
				<tr>
					<td > 
						<?php echo $monthName . " " . $day . ", " . $year . ' ('.$today->format('l').')'; ?> 
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<?php if(@count($dayHoursArray['allday'])): ?>
		<tr>
			<td colspan=5 class=dlg_all_day_cell>
				<?php if(count($dayHoursArray['allday']) == 1) $display = 'display:inline;' ?>
				<div style='<?php echo $display; ?>'>All Day Entries:</div>
				<?php 
					foreach($dayHoursArray['allday'] as $dayEntry){
						echo $this->Calendar->makeCalendarDateEntryLink($dayEntry);
						echo "<br>";
					}
				?>
			</td>
		</tr>
	<?php endif; ?>
	
<?php
	// echo count($dayHoursArray);
	for($i=0; $i<12; $i++){ 
		$hour = ($i == 0) ? 12 : $i;
		
		echo "<tr>";
		echo "<td class=dlg_hour_cell_hdr>$hour:00 am</td>";
		$id = $today->format('Y/n/j/') . $hour . "/00/am";
		echo "<td class=dlg_hour_cell name=hourCell id=$id style='color:purple;'>";
		if(isset($dayHoursArray[$hour])){
			foreach($dayHoursArray[$hour] as $entry){
				echo $this->Calendar->makeCalendarDateEntryLink($entry);
			}
		}
		echo "</td>";
		
		if($i==0)
			echo "<td rowspan=12 ></td>";
		
		echo "<td class=dlg_hour_cell_hdr>$hour:00 pm</td>";
		$id = $today->format('Y/n/j/') . $hour . "/00/pm";
		echo "<td class=dlg_hour_cell name=hourCell id=$id>";
		
		if(isset($dayHoursArray[$hour + 12])){
			foreach($dayHoursArray[$hour + 12] as $entry){
				echo $this->Calendar->makeCalendarDateEntryLink($entry);
				echo "<br>";	
			}
		}
		
		echo "</td>";
		echo "</tr>";	
	}
?>
</table>