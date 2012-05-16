
<ul class=TimePicker id=TimePicker style='display:none;'>

<?php
	for($i=0; $i<12; $i++){
		$num = $i == 0 ? 12 : $i;
			
		echo "<li>
				<ul >
					<li>$num:00 am </li>
					<li class=arrow>&raquo;

						<ul>
							<li>$num:15 am</li>
							<li>$num:30 am</li>
							<li>$num:45 am</li>
						</ul>
					</li> 


					<li>$num:00 pm </li>
					<li class=arrow>&raquo;

						<ul>
							<li>$num:15 pm</li>
							<li>$num:30 pm</li>
							<li>$num:45 pm</li>
						</ul>
					</li>
				</ul>
			</li>";
	}

?>
</ul>

