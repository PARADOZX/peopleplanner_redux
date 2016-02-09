<div id="users_display">
	<div id="users_header">ATTENDEES</div>
	<div id="users_attendees_list">
<?php

foreach ($data as $key) {
	echo "<div class='users_attendees'>" . "<span title='" . $key['firstName'] . "' style='background-color:" . $key['color'] . "' class='users_attendees_mark'></span>" . $key['firstName'] . "</div>";
}

?>
	</div>
</div>