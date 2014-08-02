<!doctype html>
<html lang='en'>
<head>
	<meta charset="utf-8" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<style>
	.calendarDayBox:hover {
		background-color: yellow;
		cursor: pointer;
	}
	table {
		margin : 0 auto;
		 width : 75%;
		 height : auto
	}
	table td {
		width : 14.2%;
		height : 25px;
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
			document.getElementById('calendar').innerHTML = data;
			init();
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
			document.getElementById('calendar').innerHTML = data;
			init();
		});
	});
};
init();

</script>
</body>
</html>