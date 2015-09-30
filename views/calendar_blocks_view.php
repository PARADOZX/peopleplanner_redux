<?php

$exists = false;
$users = array();

if (in_array($dayNum, $data['DBdata']['uniqueDates'])) {
// var_dump($data['DBdata']['info'][0]['date']);
	foreach ($data['DBdata']['info'] as $key => $userdate){

		if($userdate['date'] == $dayNum){
			// $users[] = $userdate['firstName'];
			// $users[$userdate['firstName']]['color'] = $userdate['color'];
			$users[] = array('name'=>$userdate['firstName'], 'color'=>$userdate['color']);
		}
	}

	$exists = true;	
}

if ($exists) {
	
	//build date for tooltip query
	$date = $data['year'] . '-' . $data['month'] . '-' . $dayNum;

	$tooltipInfo = $data['dbquery']->tooltip($date);

	foreach ($tooltipInfo['attending'] as $key) {
		$can_make .= $key . "\n";
	}
	foreach ($tooltipInfo['notattending'] as $key) {
		$cannot_make .= $key . "\n";
	}

	echo "<td class='calendarDayBox' title='" . "{$tooltipInfo['count_coming']} " . "can make it: " . "\n" . "$can_make". "\n" . "{$tooltipInfo['count_not_coming']} cannot make it: " . "\n" . "$cannot_make". "'>";
	echo "<div class='calendarDayNumContainer'><div class='calendarDayNum'>$dayNum";
	echo "<div class='calendarDayInfo'>";

	foreach ($users as $key) {
		echo "<div title='" . $key['name'] . "' style='background-color:" . $key['color'] . "' class='dot'></div>";
	}

	echo "</div>";
	echo "</div></div>";
	echo "</td>";
} else {
	echo "<td class='calendarDayBox'><div class='calendarDayNumContainer'><div class='calendarDayNum'>$dayNum</div></div></td>";
}

