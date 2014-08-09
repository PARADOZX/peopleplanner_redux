<?php

session_start();

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 300)) {
    // last request was more than 5 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp


?>


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
	#inner {
		min-width : 1100px;
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
	#calendar {

	}
	#calendarTitle {
		font-size : 36pt;
	}
	.calendarDayBox:hover {
		background-color: gray;
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
		display : inline-block;
		height: 35px;
		width : 7px;
		margin-right: 0px;
	}
	#register {
		display : none;
	}
	#user_list {
		font-family: 'Just Another Hand', cursive;
		font-size: 20pt;
		float : left;
		width : 100px;
	}
	</style>
</head>

<body>
<div>
	<h3 id="sign_title">
		<?php if (!isset($_SESSION['user'])) echo "Sign In"; ?>
	</h3>
	<div id="sign_log">
		<?php if (!isset($_SESSION['user'])) echo 'Email : <input id="log_in_email" type="text" /><br />
		Password : <input id="log_in_pass" type="password" /><br />
		<a href="#" onclick="register()">Register</a>
		<button onclick="logIn()">log in</button>'; ?>
	</div>
	<div id="register">
		<h3>Register</h3>
		<input id="register_name" type="text" /><br/>
		<input id="register_email" type="email" /><br/>
		<input id="register_pass" type="password" /><br/>
		<input id="register_pass2" type="password" /><br/>
	</div>
</div>
<div id="inner">
	<div id="user_list"></div>
	<div id="calendar"></div>
</div>

<script>

//user object holds user ID for events
var userObj = {};

function logIn(){
	var email = document.getElementById('log_in_email').value;
	var password = document.getElementById('log_in_pass').value;
	$.ajax({
		type: "POST",
		url : "class/calender_ajax.php",
		data : {email : email, password : password, action : 'login'}
	})
	.done(function(data){
		//if data does not contain the text 'firstName' then no user match or server error
		if (new RegExp("firstName").test(data)){
			var data = JSON.parse(data);
			userObj.ID = data.userID;
			loggedIn.calendar();
			loggedIn.userlist();
		} else {
			alert(data);
		}
	});
}

function logOut(){
	$.ajax({
		type : "GET",
		url : "class/calender_ajax.php",
		data : {
			action : 'logout'
		}
	}).done(function(data){
		$('#inner').empty().html(data);
	});
}

function register(){
	$('#sign_log, #sign_title').empty();
	$('#register').show();
}

// function loggedIn(){
// 	var ajax = {
// 		type: "GET",
// 		url : "class/calender_ajax.php",
// 		data: "html"
// 	};
// 	$.ajax(ajax).done(function(data){
// 		$('#sign_title').empty().text('Sign Out');
// 		$('#sign_log').empty().html('<button onclick="logOut()">log out</button>');
// 		$('#calendar').append(data).hide().slideDown(750).show();

// 		init();	
// 	});
// }

var loggedIn = {
	calendar : function(){
		var ajax = {
			type: "GET",
			url : "class/calender_ajax.php",
			data: "html"
		};
		$.ajax(ajax).done(function(data){
			$('#sign_title').empty().text('Sign Out');
			$('#sign_log').empty().html('<button onclick="logOut()">log out</button>');
			$('#calendar').append(data).hide().slideDown(750).show();

			init();	
		});
	},
	userlist : function(){
		$.ajax({
			type: "GET",
			url : "class/calender_ajax.php",
			data : {action : "userlist"}
		}).success(function(data){
			$('#user_list').append(data);
		});
	}
};

//sets event handlers
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
				//redefines AJAX asynchronous functions since every event within init() is binded to a dynamically generated element.
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
			dataType: "html", 	
		};

		$.ajax(ajax1).done(function(data){
			$('#calendar').fadeOut(200, function(){
				$(this).fadeIn(200).html(data);
				init();
			});
		});
	});

	//calendar day box click event
	$('table').on('click', '.calendarDayBox', function(){
		//get the day
		var day = parseInt($(this).text());
		//cache some of these vars
		var month = ($('#previousMonth').attr('data-preMonth') != 12) ? parseInt($('#previousMonth').attr('data-preMonth')) + 1: 1;
		var year = ($('#previousMonth').attr('data-preMonth') != 12) ? parseInt($('#previousMonth').attr('data-preYear')) : parseInt($('#previousMonth').attr('data-preYear')) + 1;
		
		//if SESSION is still set then use SESSION['user'] to define userID (need this b/c userObj.ID is undefined 
		//if page is refreshed; however the SESSION['user'] will still be defined)
		var userID = '<?php if (!empty($_SESSION['user'])) echo $_SESSION['user']; ?>';
		//otherwise use userObj.ID to define userID
		if (userObj.ID) {
			var userID = userObj.ID;
		}

		var ajax1 = {
			type: "POST",
			url : "class/calender_ajax.php",
			data : {
				day : day,
				month : month,
				year : year,
				userID : userID
			},
			dataType: "html", 	
		};

		$.ajax(ajax1).done(function(data){
			//if toggle (delete or add) is successful than refresh AJAX.  CREATE A FUNCTION FOR 'GET' AJAX CALL TO
			//REDUCE REDUNDANT CODE.
			if (data == true) {
				var ajax1 = {
					type: "GET",
					url : "class/calender_ajax.php",
					data : {
						month : month,
						year : year
					},
					dataType: "html", 	
				};
				$.ajax(ajax1).done(function(data){
					// $('#calendar').fadeOut(150, function(){
					// 	$(this).fadeIn(150).html(data);
					// });
					$('#calendar').html(data);
					init();
				});

			}

		});
	});

	//dot click event
	$('table').on('click', '.dot', function(e){
		e.stopPropagation();
		alert('dot');
	});

};

init();



//calls loggedIn() -- generates calendar if user visits from new window and still in Session.
if('<?php if (isset($_SESSION['user'])) echo true; ?>' != ''){
  loggedIn.calendar();
  loggedIn.userlist();
}
</script>

</body>
</html>