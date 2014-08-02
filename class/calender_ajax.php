 <?php 
error_reporting('ALL');
date_default_timezone_set('America/New_York');


class Calendar {
	//sets the first day of the month to 1 
    protected $days_in_month = 0;
    protected $day;
    protected $month;
    protected $year;
    protected $title;
    protected $nextYear;
    protected $previousYear;
    protected $nextMonth;
    protected $previousMonth;

	function __construct(){
		$this->month = (isset($_GET['month']) && filter_var($_GET['month'], FILTER_VALIDATE_INT, array("options"=>
		array("min_range"=>1, "max_range"=>12)))) ? $_GET['month'] : '';

		$this->year = (isset($_GET['year']) && filter_var($_GET['month'], FILTER_VALIDATE_INT)) ? $_GET['year'] : '';
	}

	public function create(){
		//get's today's date
		$date =time();
		//This puts the day, month, and year in seperate variables
		// $this->day = date('d', $date);  //not required
		if($this->month == ''){
			$this->month = date('m', $date);
			$this->year = date('Y', $date);
		}
		//Here we generate the first day of the month
		$first_day = mktime(0,0,0,$this->month, 1, $this->year) ;

		//This gets us the month name
		$this->title = date('F', $first_day);
		
		//Here we find out what day of the week the first day of the month falls on 
		$day_of_week = date('D', $first_day) ; 

		 //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero

		 switch($day_of_week){ 
			 case "Sun": $this->blank = 0; break; 
			 case "Mon": $this->blank = 1; break; 
			 case "Tue": $this->blank = 2; break; 
			 case "Wed": $this->blank = 3; break; 
			 case "Thu": $this->blank = 4; break; 
			 case "Fri": $this->blank = 5; break; 
			 case "Sat": $this->blank = 6; break; 
 		}

		 //We then determine how many days are in the current month
		 $this->days_in_month = cal_days_in_month(0, $this->month, $this->year) ; 

	}

	public function render(){
		
		$this->monthYearCounter();

		//This counts the days in the week, up to 7
		$day_count = 1;
		$day_num = 1;

		 //Here we start building the table heads 
		echo "<table border=1 width=294>";

		echo "<tr><th colspan=7><span id='previousMonth' data-preMonth='" . $this->previousMonth . "' data-preYear='" . (($this->previousYear) ? $this->year - 1 : $this->year) . "' style='font-size:6pt; float: left' class='scrollNextMonth'>Previous Month</span> $this->title $this->year <span id='nextMonth' data-nextMonth='" . $this->nextMonth . "' data-nextYear='" . (($this->nextYear) ? $this->year + 1 : $this->year) . "' style='font-size:6pt; float: right' class='scrollNextMonth'>Next Month</span></th></tr>";

		echo "<tr><td width=42>S</td><td width=42>M</td><td 
			width=42>T</td><td width=42>W</td><td width=42>T</td><td 
			width=42>F</td><td width=42>S</td></tr>";

        echo "<tr>";

		//first we take care of those blank days

		while ( $this->blank > 0 ) { 
			echo "<td></td>"; 
			$this->blank = $this->blank-1; 
			$day_count++;
		} 

        //count up the days, untill we've done all of them in the month
		while ( $day_num <= $this->days_in_month ){ 
			echo "<td> $day_num </td>"; 
			$day_num++; 	
			$day_count++;

		 //Make sure we start a new row every week
	    	if ($day_count > 7){
				echo "</tr><tr>";
				$day_count = 1;
			}

		 } 

		 //Finaly we finish out the table with some blank details if needed
		while ( $day_count >1 && $day_count <=7 ){		 
			 echo "<td> </td>"; 
		    $day_count++; 
		} 

		echo "</tr></table>"; 
	}

	private function monthYearCounter(){
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
	}
}

$calender = new Calendar();

$calender->create();
$calender->render();

?>