<?php 
	$dayCount = 1;
	$dayNum = 1;
?>

<nav>
    <ul>
        <li id="select_div">Select Trip 
        	<select id="trip_select">
				<?php 
				foreach ($data['DBdata']['allTrips'] as $key => $value){
					echo "<option id='{$value['tableName']}' value = '{$value['tableName']}'>{$value['tripName']}</option>";
				} 
				?>
				</select>
		</li>
		<li id="nav_analytics">Analytics<img src="img/down_arrow.png" />
		    <ul>
			    <li id="nav_analytics_test" onclick="">Test</li>
		    </ul>
		</li>
		<li id="nav_create" onclick="pages.renderpage('#user_list, #calendar', '#start_page', pages.createtrip())">Create New Trip</li>
		<li id="nav_join" onclick="pages.renderpage('#user_list, #calendar', '#start_page', pages.jointrip())">Join An Existing Trip</li>
	</ul>
</nav>

<div id="tableName" data-tableName="<?php echo $data['DBdata']['tripInfo'][0]['tableName']; ?>" hidden></div>

<!--if user is admin then set hidden value to false to show invite option-->
<div id="is_admin" data-admin="<?php echo $data['DBdata']['tripInfo'][0]['admin']; ?>" hidden></div>

<br/><br/><br/>
<!--Here we start building the table heads -->
<table id="calendar_view">
	<caption> <?php echo strtoupper($data['DBdata']['tripInfo'][0]['tripName']); ?></caption>
	<tr>
		<th colspan=7>
			<span id='previousMonth' data-preMonth='<?php echo $data['previousMonth']; ?>' data-preYear='<?php echo (($data['previousYear']) ? $data['year'] - 1 : $data['year']); ?>' style='font-size:10pt; float: left' class='scrollNextMonth'>Previous Month</span>
			<span id='calendarTitle'><?php echo $data['title'] . ' ' . $data['year'] ?></span>
			<span id='nextMonth' data-nextMonth='<?php echo $data['nextMonth']; ?>' data-nextYear='<?php echo (($data['nextYear']) ? $data['year'] + 1 : $data['year']); ?>' style='font-size:10pt; float: right' class='scrollNextMonth'>Next Month</span>
		</th>
	</tr>
	<tr class='tableDaysHeader'>
		<td width=42>Sunday</td>
		<td width=42>Monday</td>
		<td	width=42>Tuesday</td>
		<td width=42>Wednesday</td>
		<td width=42>Thursday</td>
		<td	width=42>Friday</td>
		<td width=42>Saturday</td>
	</tr>
	<tr>
		<!--first we take care of those blank days-->
		<?php
		while ( $data['blank'] > 0 ) { 
			echo "<td class='calendar_blank_days'></td>"; 
			$data['blank'] = $data['blank']-1; 
			$dayCount++;
		} 

        //count up the days, until we've done all of them in the month
		while ( $dayNum <= $data['daysInMonth']){

			include 'calendar_blocks_view.php';
			// echo "<td> $dayNum </td>"; 
			$dayNum++; 	
			$dayCount++;

		 //Make sure we start a new row every week
	    	if ($dayCount > 7){
				echo "</tr><tr>";
				$dayCount = 1;
			}

		 } 

		 //Finaly we finish out the table with some blank details if needed
		while ( $dayCount >1 && $dayCount <=7 ){		 
			 echo "<td class='calendar_blank_days'> </td>"; 
		    $dayCount++; 
		} 
		?>
	</tr>
</table>