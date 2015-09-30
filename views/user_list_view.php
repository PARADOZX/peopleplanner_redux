<div id="users_display"><b>Attendees</b><hr/>

<?php

foreach ($data as $key) {
	echo '<br />' . "<div class='attendees'>" . $key['firstName'] . "<div title='" . $key['firstName'] . "' style='background-color:" . $key['color'] . "' class='attendees_dot'></div></div>";
}

?>

</div>