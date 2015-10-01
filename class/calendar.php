<?php 


class Calendar {
    protected $daysInMonth = 0;
    protected $day;
    protected $month;
    protected $year;
    protected $title;
    protected $nextYear;
    protected $previousYear;
    protected $nextMonth;
    protected $previousMonth;
    protected $dayNum;
    protected $DB;
    protected $DBdata;
    protected $dbquery;

	function __construct($DBdata='', $table, $DB=''){
		
		$this->month = (isset($_GET['month']) && filter_var($_GET['month'], FILTER_VALIDATE_INT, array("options"=>
		array("min_range"=>1, "max_range"=>12)))) ? $_GET['month'] : '';

		$this->year = (isset($_GET['year']) && filter_var($_GET['month'], FILTER_VALIDATE_INT)) ? $_GET['year'] : '';

		$this->DBdata = $DBdata;

		$this->daysInMonth = $this->getCurrentMonthDetails();

		// $this->monthYearCounter();

		//connect to DB for tooltip plugin
		$this->DB = $DB;
		$this->dbquery = new dbquery($this->DB, $table);

	}

	public function getCurrentMonthDetails(){
		
		//get's today's date
		$date = time();
		//This puts the day, month, and year in seperate variables
		// $this->day = date('d', $date);  //not required
		if(empty($this->month)){
			$this->month = date('m', $date);
			$this->year = date('Y', $date);
		}
		//Here we generate the first day of the month
		$firstDay = mktime(0,0,0,$this->month, 1, $this->year) ;

		//This gets us the month name
		$this->title = date('F', $firstDay);
		
		//Here we find out what day of the week the first day of the month falls on 
		$dayOfWeek = date('D', $firstDay) ; 

		 //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero

		 switch($dayOfWeek){ 
			 case "Sun": $this->blank = 0; break; 
			 case "Mon": $this->blank = 1; break; 
			 case "Tue": $this->blank = 2; break; 
			 case "Wed": $this->blank = 3; break; 
			 case "Thu": $this->blank = 4; break; 
			 case "Fri": $this->blank = 5; break; 
			 case "Sat": $this->blank = 6; break; 
 		}

		 //We then determine how many days are in the current month
 		 // return cal_days_in_month(0, $this->month, $this->year); //v.1

 		//v.2
 		$returnArray = array();
 		$returnArray['blank'] = $this->blank;
 		$returnArray['year'] = $this->year;
 		$returnArray['month'] = $this->month;
 		$returnArray['dbquery'] = $this->dbquery;
 		$returnArray['title'] = $this->title;
 		$returnArray['DBdata'] = $this->DBdata;
 		$returnArray['daysInMonth'] = cal_days_in_month(0, $this->month, $this->year); 
		return $returnArray;

	}

	public function render(){
		
		//This counts the days in the week
		$dayCount = 1;
		$this->dayNum = 1;

		echo '<nav>
		        <ul>
		          <li id="select_div">Select Trip <select id="trip_select">';
		foreach ($this->DBdata['allTrips'] as $key => $value){
			echo "<option id='{$value['tableName']}' value = '{$value['tableName']}'>{$value['tripName']}</option>";
		}

		echo '</select></li>
				  <li id="nav_analytics">Analytics<img src="img/down_arrow.png" />
				    <ul>
				      <li id="nav_analytics_test" onclick="">Test</li>
				    </ul>
				  </li>
				  <li id="nav_create" onclick="pages.renderpage('. "'#user_list, #calendar', '#start_page', pages.createtrip()" .')">Create New Trip</li>
				  <li id="nav_join" onclick="pages.renderpage('. "'#user_list, #calendar', '#start_page', pages.jointrip()" .')">Join An Existing Trip</li>
			    </ul>
			  </nav>';

		echo '<div id="tableName" data-tableName="'. $this->DBdata['tripInfo'][0]['tableName'] . '" hidden></div>';

		//if user is admin then set hidden value to false to show invite option
		echo '<div id="is_admin" data-admin="' . $this->DBdata['tripInfo'][0]['admin'] . '" hidden></div>';

		 //Here we start building the table heads 
		echo "<br/><br/><br/><table id='calendar_view'><caption>" . strtoupper($this->DBdata['tripInfo'][0]['tripName']) . "</caption>";

		echo "<tr><th colspan=7><span id='previousMonth' data-preMonth='" . $this->previousMonth . "' data-preYear='" . (($this->previousYear) ? $this->year - 1 : $this->year) . "' style='font-size:10pt; float: left' class='scrollNextMonth'>Previous Month</span><span id='calendarTitle'>$this->title $this->year</span><span id='nextMonth' data-nextMonth='" . $this->nextMonth . "' data-nextYear='" . (($this->nextYear) ? $this->year + 1 : $this->year) . "' style='font-size:10pt; float: right' class='scrollNextMonth'>Next Month</span></th></tr>";

		echo "<tr class='tableDaysHeader'><td width=42>Sunday</td><td width=42>Monday</td><td 
			width=42>Tuesday</td><td width=42>Wednesday</td><td width=42>Thursday</td><td 
			width=42>Friday</td><td width=42>Saturday</td></tr>";

        echo "<tr>";

		//first we take care of those blank days

		while ( $this->blank > 0 ) { 
			echo "<td class='calendar_blank_days'></td>"; 
			$this->blank = $this->blank-1; 
			$dayCount++;
		} 

        //count up the days, until we've done all of them in the month
		while ( $this->dayNum <= $this->daysInMonth){

			$this->parse($this->dayNum, $this->DBdata);
			// echo "<td> $this->dayNum </td>"; 
			$this->dayNum++; 	
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

		echo "</tr></table>"; 
	}

	public function monthYearCounter(){
		$this->previousYear = false;
		$this->nextYear = false;

		$this->previousMonth = $this->month - 1;
		$this->nextMonth = $this->month + 1;
		if ($this->previousMonth < 1) {
			$this->previousMonth = 12;
			$this->previousYear = true;
		}
		if ($this->nextMonth > 12) {
			$this->nextMonth = 1;
			$this->nextYear = true;
		}

		//v.2
		$returnArrayCounter = array();
		$returnArrayCounter['nextYear'] = $this->nextYear;
		$returnArrayCounter['previousYear'] = $this->previousYear;
		$returnArrayCounter['nextMonth'] = $this->nextMonth;
		$returnArrayCounter['previousMonth'] = $this->previousMonth;
		return $returnArrayCounter;

	}

	//checks if $dayNum exists in uniqueDates of DBdata, if data exists then display all users associated with 
	//the date ($dayNum) by a color bar.
	private function parse($dayNum, $DBdata){

		$exists = false;
		$users = array();
		if (in_array($dayNum, $DBdata['uniqueDates'])) {

			foreach ($DBdata['info'] as $key => $userdate){
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
			$date = $this->year . '-' . $this->month . '-' . $dayNum;

			$tooltipInfo = $this->dbquery->tooltip($date);

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
	}
}


?>