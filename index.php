

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
	<script src="javascript/tooltip-plugin"></script>
 	<link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>

	<style>
	* {
		margin : 0;
		padding : 0;
	}
	body {
		/*background: url(images/bg.jpg) top center no-repeat #545454;*/
	}
	#container {
		max-width : 960px;
		margin: 0px auto;
	}
	#user {
		border-bottom: 1px solid black;
		padding : 8px;
	}
	/*#inner {
		min-width : 1100px;
	}*/
	table {
		font-family: 'Roboto', sans-serif;
		font-size: 12pt;
		font-weight: 100;
		border-collapse: collapse;
		margin : 0 auto;
		width : 700px;
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
	#tooltip {
		white-space:pre-wrap;
	}
	.calendarDayInfo {
		width : 100px;
		color: red;
	}
	
	
	.tableDaysHeader {
		text-align: center;
	}
	td {
		width : 100px;
		height : 70px;
	}
	.calendarDayNumContainer {
		width : 100px;
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
		font-family: 'Roboto', sans-serif;
		font-size: 12pt;
		float : left;
		width : 100px;
		padding: 10px;

	}

	#tooltip {
	    /*background: #fff url(../images/search.png) no-repeat 5px 50%;*/
	    background-color: white;
	    border: 1px solid #BFBFBF;
	    float: left;
	    font-size: 12px;
	    max-width: 120px;
	    padding: 1em 1em 1em 3em;
	    position: absolute;
	}
 
	.rounded {
	    -moz-border-radius: 3px;
	    -webkit-border-radius: 3px;
	    -webkit-box-shadow: 2px 2px 4px #888;
		-moz-box-shadow: 2px 2px 4px #888;
		box-shadow: 2px 2px 4px #888;
	}
	</style>
</head>

<body>
<div id="container">
	<div id="user">
		<h3 id="sign_title">
			<?php if (!isset($_SESSION['user'])) echo "Sign In"; ?>
		</h3>
		<div id="sign_log">
			<?php if (!isset($_SESSION['user'])) echo 'Email : <input id="log_in_email" type="text" /><br />
			Password : <input id="log_in_pass" type="password" /><br />
			<a href="#" onclick="register.show()">Register</a>
			<button onclick="logIn()">log in</button>'; ?>
		</div>
		<div id="register">
			<h3>Register</h3>
			<p>First Name: <input id="register_first_name" type="text" required/></p><br/>
			<p>Email: <input id="register_email" type="email" /></p><br/>
			<p>Password: <input id="register_pass" type="password" /></p><br/>
			<p>Confirm Password: <input id="register_pass2" type="password" /></p><br/>
			<button onclick="register.send()">Send</button>
		</div>
	</div>
	<div id="inner">
		<div id="user_list"></div>
		<div id="calendar"></div>
		<div id="start_page"></div>
	</div>
	
</div>

<script>

//user object holds user ID for events
var userObj = {
	ID : '',			
	table : ''
};



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
			// loggedIn.userlist();	  // 1 of 3 commented out
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

var register = {
	show : function (){
		$('#sign_log, #sign_title').empty();
		$('#register').show();
	}, 
	send : function(){
		var first_name = document.getElementById('register_first_name').value;
		var password1 = document.getElementById('register_pass').value;
		var password2 = document.getElementById('register_pass2').value;
		var email = document.getElementById('register_email').value;

		var msg = '';
		if (first_name === '' || password1 === '' || password2 === '' || email === '') msg += 'Make sure all fields are completed.\n';
		if (validate.password(password1, password2) === false) msg += 'Please make sure passwords are the same. \n';
		if (validate.email(email) === false) msg += 'Please make sure email is in proper format. \n';

		if (msg != '') {
			alert(msg);
		} else {
			//jQUERY promise....
			var test = $.post("class/calender_ajax.php", {action : 'register', firstName : first_name, email : email, password : password1})
			.then(function(data){
				alert(data);
			});
		}
	}
};

var validate = {
	password : function(pass1, pass2){
		if (pass1 === pass2) return true;
		return false;
	},
	email : function(email){
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
   		return re.test(email);
	}
};

var loggedIn = {
	calendar : function(table){
		
		var ajax = {
			type: "GET",
			url : "class/calender_ajax.php",
			data: {
				table : userObj.table                     
			}
		};
		$.ajax(ajax).done(function(data){

			$('#sign_title').empty().text('Sign Out');
			$('#sign_log').empty().html('<button onclick="logOut()">log out</button>');

			if (data != false) {
				$('#calendar').append(data).hide().slideDown(750).show();

				userObj.table = $('#tableName').attr('data-tableName');
				$('#' + userObj.table).attr('selected', 'selected');

				loggedIn.userlist();
				init();	
			} else {
				pages.renderpage('#start_page', pages.startpage());
			}

		});
	},
	userlist : function(){
		$.ajax({
			type: "GET",
			url : "class/calender_ajax.php",
			data : {action : "userlist", table: userObj.table}
		}).success(function(data){
			if($('#is_admin').attr('data-admin') != 0)  $('#user_list').append('<span id="invite"><b>INVITE (ADMIN ONLY)</b></span>');
			$('#user_list').append(data);

		});
	}
};

var pages = {
	events : function(){
		$('#trip_id_button').on('click', function(){
			var tableKey = $('#trip_id').val();
			$.get('class/calender_ajax.php', {action : 'join' , tableKey : tableKey})
			.then(function(data){
				if (data == false){
					//'refresh' page when user joins table
					$('#start_page').empty();
					loggedIn.calendar();
				} else {
					alert(data);
				}

			});
		});
	},
	//this may actually be less efficient
	renderpage : function(id, page){
		$(id).empty();
		$(id).append(page);
		this.events();
	},
	startpage : function(){
		//would HEREDOC work here?
		var startpage = '<br/ ><h2 class="header">Get Started</h2><br />';
		startpage += '<div onclick="pages.renderpage(' + "'#start_page' , pages.createtrip()" + ')">Create A New Trip!</div><br />';
		startpage += '<div onclick="pages.renderpage(' + "'#start_page', pages.jointrip()" + ')">Join An Existing Trip!</div>';
		return startpage;
	},
	jointrip : function(){
		var existingtrip = '<br /><h2>Join An Existing Trip</h2><br/>';
		existingtrip += '<div>Enter Trip Key: ' + '<input type="text" id="trip_id" /><button id="trip_id_button">Go!</button></div><br />';
		existingtrip += '<button onclick="pages.renderpage(' + "'#start_page', pages.startpage())" + '">Go back</button>';
		return existingtrip;
	}, 
	createtrip: function(){
		var createtrip = '<br /><h2>Create A New Trip</h2><br />';
		createtrip += '';
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
				year : preYear, 
				table : userObj.table 						
			},
			dataType: "html", 	//return datatype
		};

		$.ajax(ajax1).done(function(data){
			$('#calendar').fadeOut(200, function(){
				$(this).fadeIn(200).html(data);

				$('#' + userObj.table).attr('selected', 'selected');
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
				year : nextYear,
				table : userObj.table						
			},
			dataType: "html", 	
		};

		$.ajax(ajax1).done(function(data){
			$('#calendar').fadeOut(200, function(){
				$(this).fadeIn(200).html(data);

				$('#' + userObj.table).attr('selected', 'selected');
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
				userID : userID, 
				table : userObj.table
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
						year : year,
						table : userObj.table           
					},
					dataType: "html", 	
				};
				$.ajax(ajax1).done(function(data){
					// $('#calendar').fadeOut(150, function(){
					// 	$(this).fadeIn(150).html(data);
					// });
					$('#calendar').html(data);
					if ($('#tooltip')) $('#tooltip').remove();
					$('#' + userObj.table).attr('selected', 'selected');
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

	$('#trip_select').on('change', function(){		
		userObj.table = $(this).val();	
		$('#calendar, #user_list').empty();
		loggedIn.calendar();
		// loggedIn.userlist();
	});

    $('#invite').on('click', function(){
    	$('#calendar, #user_list').hide();
    	$('#inner').append('Invite Menu');
    });

	//bind tooltip-plugin feature to td's
	$('td').tooltip({
	        rounded: true
	});

};

init();




//calls loggedIn() -- generates calendar if user visits from new window and still in Session.
if('<?php if (isset($_SESSION['user'])) echo true; ?>' != ''){
  $('#select_div').show();
  loggedIn.calendar();
  // loggedIn.userlist();
}
</script>

</body>
</html>