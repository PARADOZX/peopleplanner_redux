 <?php 




class Calendar {
	//This counts the days in the week, up to 7
	protected $day_count = 1;
	//sets the first day of the month to 1 
    protected $day_num = 1;
    protected $days_in_month = 0;
    protected $day;
    protected $month;
    protected $year;
    protected $title;

	function __construct(){
		$this->month = (isset($_GET['month'])) ? $_GET['month'] : '';
		$this->year = (isset($_GET['year'])) ? $_GET['year'] : '';
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

		$this->day_count = 1;
		$this->day_num = 1;
 
		$previousYear = false;
		$nextYear = false;
		$previousMonth = $this->month - 1;
		$nextMonth = $this->month + 1;
		if ($previousMonth < 1) {
			$previousMonth = 12;
			$previousYear = true;
		}
		if ($nextMonth > 12) {
			$nextMonth = 1;
			$nextYear = true;
		}

		 //Here we start building the table heads 
		echo "<table border=1 width=294>";

		echo "<tr><th colspan=7><a href='index.php?month=" . $previousMonth . "&year=" . (($previousYear) ? $this->year - 1 : $this->year) . "' style='font-size:6pt; float: left' class='scrollNextMonth'>Previous Month</a> $this->title $this->year <a href='index.php?month=" . $nextMonth . "&year=" . (($nextYear) ? $this->year + 1 : $this->year) . "' style='font-size:6pt; float: right' class='scrollNextMonth'>Next Month</a></th></tr>";

		echo "<tr><td width=42>S</td><td width=42>M</td><td 
			width=42>T</td><td width=42>W</td><td width=42>T</td><td 
			width=42>F</td><td width=42>S</td></tr>";

        echo "<tr>";

		//first we take care of those blank days

		while ( $this->blank > 0 ) { 
			echo "<td></td>"; 
			$this->blank = $this->blank-1; 
			$this->day_count++;
		} 

        //count up the days, untill we've done all of them in the month
		while ( $this->day_num <= $this->days_in_month ){ 
			echo "<td> $this->day_num </td>"; 
			$this->day_num++; 	
			$this->day_count++;

		 //Make sure we start a new row every week
	    	if ($this->day_count > 7){
				echo "</tr><tr>";
				$this->day_count = 1;
			}

		 } 

		 //Finaly we finish out the table with some blank details if needed
		while ( $this->day_count >1 && $this->day_count <=7 ){		 
			 echo "<td> </td>"; 
		    $this->day_count++; 
		} 

		echo "</tr></table>"; 
	}

}



 ?>