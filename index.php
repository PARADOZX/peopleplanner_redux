<!doctype html>
<html lang='en'>
<head>
	<meta charset="utf-8" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 	<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>

	<style>
	* {
		margin : 0;
		padding : 0;
	}
	body {
		/*background: url(images/bg.jpg) top center no-repeat #545454;*/
	}
	table {
		font-family: 'Just Another Hand', cursive;
		font-size: 16pt;
		font-weight: 300;
		border-collapse: collapse;
		margin : 0 auto;
		width : 910px;
		height : auto;
		/*border : 3px solid black;*/
	}
	th, td {
		border: 1px solid black;
	}
	table tr:first-child th {
  		border-top: 0;
  		border-bottom: 0;
	}
	table tr:nth-child(2) td {
		border-top: 0;
	}
	table tr:last-child td {
		border-bottom: 0;
	}
	table tr td:first-child, table tr th:first-child {
	  	border-left: 0;
	}
	table tr td:last-child,	table tr th:last-child {
	  	border-right: 0;
	}
	#calendarTitle {
		font-size : 36pt;
	}
	.calendarDayBox:hover {
		background-color: yellow;
		cursor: pointer;
	}
	.calendarDayInfo {
		width : 100px;
		color: red;
	}
	
	
	.tableDaysHeader {
		text-align: center;
	}
	td {
		width : 130px;
		height : 70px;
	}
	.calendarDayNumContainer {
		width : 130px;
		height : 70px;
	}
	.calendarDayNum {
		margin: 0px auto auto 2px;
	}
	.dot {
		height: 20px;
		width : 5px;
		
	}
	</style>
</head>

<body>
<h2>Who's Coming?</h2>
<div id="calendar"><?php include 'class/calender_ajax.php'; ?></div>

<script>
function init(){
	$('#previousMonth').on('click', function(){
		var preMonth = $(this).attr('data-preMonth');
		var preYear = $(this).attr('data-preYear');
		var ajax1 = {
			type: "GET",
			url : "class/calender_ajax.php",
			data : {
				month : preMonth,
				year : preYear
			},
			dataType: "html", 	//return datatype
		};

		$.ajax(ajax1).done(function(data){
			$('#calendar').fadeOut(200, function(){
				$(this).fadeIn(200).html(data);
				init();
			});
		});
	});

	$('#nextMonth').on('click', function(){
		var nextMonth = $(this).attr('data-nextMonth');
		var nextYear = $(this).attr('data-nextYear');
		var ajax1 = {
			type: "GET",
			url : "class/calender_ajax.php",
			data : {
				month : nextMonth,
				year : nextYear
			},
			dataType: "html", 	//return datatype
		};

		$.ajax(ajax1).done(function(data){
			$('#calendar').fadeOut(200, function(){
				$(this).fadeIn(200).html(data);
				init();
			});
		});
	});
};
init();

</script>
</body>
</html>