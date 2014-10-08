

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
	<link href='css/style.css' rel="stylesheet" type="text/css">
 	<link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
</head>

<body>
<div id="container">
	<header>
		<img src="img/logo.png" title="who's coming?" />
		<div id="user">
			<h3 id="sign_title">
				<?php if (!isset($_SESSION['user'])) echo "Sign In"; ?>
			</h3>
			<div id="sign_log">
				<?php if (!isset($_SESSION['user'])) echo '<label>Email : <br /><input id="log_in_email" type="text" /></label><br />
				<label>Password : <br /><input id="log_in_pass" type="password" /><label><br />
				<button onclick="logIn()">log in</button><br /><br/><p>Not registered? <a href="#" onclick="register.show()">Register</a></p>'; ?>
			</div>
			<div id="register">
				<h3>Register</h3>
				<p><label>First Name: <input id="register_first_name" type="text" required/></label></p><br/>
				<p>Email: <input id="register_email" type="email" /></p><br/>
				<p>Password: <input id="register_pass" type="password" /></p><br/>
				<p>Confirm Password: <input id="register_pass2" type="password" /></p><br/>
				<button onclick="register.send()">Send</button>
			</div>
		</div>
	</header>
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
	}).done(function(data){
		// if data does not contain the text 'firstName' then no user match or server error
		if (new RegExp("firstName").test(data)){
			var data = JSON.parse(data);
			userObj.ID = data.userID;
			loggedIn.calendar(); 
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
		$('#user').empty();
		$('#inner').empty().html(data);
	});
}

var register = {
	show : function (){
		$('#sign_log, #sign_title').hide();
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
				$('#register').hide();
				$('#sign_log, #sign_title').show();
				$('#start_page').append(data);
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
	}, 
	confirm_invite : function(){
		if (confirm('Are you sure you want to send invite?')) {
			var invite_email = document.getElementById('invite_email').value;
			//validate email
			if (this.email(invite_email)) {
				loggedIn.send_invite(invite_email);
			} else {
				alert('invalid email');
			}
	    } else {
		   	alert('didn\'t send');
		}
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

			$('#sign_title').empty();
			$('#sign_log').empty().html('<button onclick="logOut()">log out</button>');

			if (data != false) {
				//resets inner divs
				$('#start_page, #calendar, #user_list').empty();

				$('#calendar').append(data).hide().slideDown(750).show();

				userObj.table = $('#tableName').attr('data-tableName');
				$('#' + userObj.table).attr('selected', 'selected');

				loggedIn.userlist();
				init();	
			} else {
				pages.renderpage('#start_page', '#start_page', pages.startpage());
			}

		});
	},
	userlist : function(){
		$.ajax({
			type: "GET",
			url : "class/calender_ajax.php",
			data : {action : "userlist", table: userObj.table}
		}).success(function(data){
			if(document.getElementById('is_admin').getAttribute('data-admin') != 0) {
				$('#user_list').append('<div id="invite"><button id="invite_button">Invite</button></div>');
				$('#invite').on('click', function(){
				   	$('#calendar, #user_list').empty();
				   	pages.renderpage('#start_page', '#start_page', pages.invite());
				});
			}
			$('#user_list').append(data);
		});
	},
	send_invite : function(email){
		$.ajax({
			type: "POST",
			url : "class/calender_ajax.php",
			data : {action : "send_invite", table: userObj.table, email: email}
		}).success(function(data){
			alert(data);
  		});    
	}
}

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

		//create a new trip
		$('#new_trip_button').on('click', function(){
			var newTripName = $('#new_trip').val();
			$.post('class/calender_ajax.php', {action : 'new', newTripName : newTripName})
			.then(function(data){
				alert(data);
				$('#start_page').empty();
				loggedIn.calendar();
			});
		});
	},
	//this may actually be less efficient
	renderpage : function(id, id2, page){
		$(id).empty();
		$(id2).append(page);
		this.events();
	},
	startpage : function(){
		//would HEREDOC work here?
		var startpage = '<br /><h2 class="header">Get Started</h2><br />';
		startpage += '<div onclick="pages.renderpage(' + "'#start_page' , '#start_page', pages.createtrip()" + ')">Create A New Trip!</div><br />';
		startpage += '<div onclick="pages.renderpage(' + "'#start_page', '#start_page', pages.jointrip()" + ')">Join An Existing Trip!</div>';
		return startpage;
	},
	jointrip : function(){
		var existingtrip = '<br /><h2>Join An Existing Trip</h2><br/>';
		existingtrip += '<div>Enter Trip Key: ' + '<input type="text" id="trip_id" /><button id="trip_id_button">Go!</button></div><br />';
		// existingtrip += '<button onclick="pages.renderpage(' + "'#start_page', '#start_page', pages.startpage())" + '">Go back</button>';
		existingtrip += '<button onclick="loggedIn.calendar()">Go back</button>';
		return existingtrip;
	}, 
	createtrip: function(){
		var createtrip = '<br /><h2>Create A New Trip</h2><br />';
		createtrip += '<div>Destination: ' + '<input type="text" id="new_trip" /><button id="new_trip_button">Go!</button></div><br />';
		// createtrip += '<button onclick="pages.renderpage(' + "'#start_page', '#start_page', pages.startpage())" + '">Go back</button>';
		createtrip += '<button onclick="loggedIn.calendar()">Go back</button>';
		return createtrip;
	},
	invite: function(){
		var invite = '<br /><h2>Invite</h2><br />';
		invite += '<div>Send invite by email: <input id="invite_email" type="text" /></div>';
		invite += '<button onclick="validate.confirm_invite()">Invite</button>';
		invite += '<button onclick="loggedIn.calendar()">Go back</button>';
		return invite;
	}
};

//sets event handlers
function init(){
	document.getElementById('previousMonth').addEventListener('click', function(){
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

	document.getElementById('nextMonth').addEventListener('click', function(){
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

		$('<div />').css({
			position: "absolute",
			width: "100%",
			height: "100%",
			left: 15,
			top: 20,
			zIndex: 1000
		})
		.text('Updating...')
		.appendTo($(this).css({
			position: "relative",
			backgroundColor: "white",
			opacity: 0.6
		}));

		//get the day
		var day = parseInt(this.innerText);
		//cache some of these vars
		var month = (document.getElementById('previousMonth').getAttribute('data-preMonth') != 12) ? parseInt(document.getElementById('previousMonth').getAttribute('data-preMonth')) + 1: 1;
		var year = (document.getElementById('previousMonth').getAttribute('data-preMonth') != 12) ? parseInt(document.getElementById('previousMonth').getAttribute('data-preYear')) : parseInt(document.getElementById('previousMonth').getAttribute('data-preYear')) + 1;
		
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
					document.getElementById('calendar').innerHTML = data;
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
	});

   	//bind tooltip-plugin feature to td's
	$('td').tooltip({
	        rounded: true
	});

};

// init();

//calls loggedIn() -- generates calendar if user visits from new window and still in Session.
if('<?php if (isset($_SESSION['user'])) echo true; ?>' != ''){
  $('#select_div').show();
  loggedIn.calendar();
}

</script>

</body>
</html>