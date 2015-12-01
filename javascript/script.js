//user object holds user ID for events
var userObj = {
	ID : '',			
	table : ''
};

var Authentication = {
	logIn : function(){
		var email = document.getElementById('log_in_email').value,
			password = document.getElementById('log_in_pass').value;

		$.ajax({
			type: "POST",
			url : "app.php",
			data : {ctrl : 'Auth', action : 'login', email : email, password : password}
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
	},
	logOut : function(){
		$.ajax({
			type : "GET",
			url : "app.php",  
			data : {
				ctrl : 'Auth',
				action : 'logout'
			}
		}).done(function(data){
			$('#user').empty();
			$('#main').empty().html(data);
		});
	}, 
	register : {
		show : function (){
			$('#sign_in').hide();
			$('#register').show();
		}, 
		send : function(){
			var first_name = document.getElementById('register_first_name').value,
				password1 = document.getElementById('register_pass').value,
				password2 = document.getElementById('register_pass2').value,
				email = document.getElementById('register_email').value;

			var msg = '';
			if (first_name === '' || password1 === '' || password2 === '' || email === '') msg += 'Make sure all fields are completed.\n';
			if (Authentication.validate.password(password1, password2) === false) msg += 'Please make sure passwords are the same. \n';
			if (Authentication.validate.email(email) === false) msg += 'Please make sure email is in proper format. \n';

			if (msg != '') {
				alert('something is wrong.');
			} else {
				$.ajax({
					url : "app.php",
					type : "POST",
					data : {
						ctrl: 'Auth', 
						action : 'register', 
						firstName : first_name, 
						email : email, 
						password : password1
					}
				})
				.done(function(data){
					$('#register').hide();
					$('#sign_in').show();
					$('#calendar').append(data);
				});
			}
		}
	},
	validate : {
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
	}
};

var loggedIn = {
	calendar : function(table){
		$.ajax({
			type: 'GET',
			url : 'app.php',	
			data: {
				ctrl : 'App',
				action : 'loadTable',
				table : userObj.table   
			}                  
		})
		.done(function(data){
			$('#sign_in').empty().html('<button onclick="Authentication.logOut()">log out</button>');

			$('#calendar, #user_list').empty();
			$('#calendar').append(data).hide().slideDown(750).show();

			if ($('#tableName').attr('data-tableName') !== undefined) {
				userObj.table = $('#tableName').attr('data-tableName');
				$('#' + userObj.table).attr('selected', 'selected');

				loggedIn.userlist();
				init();	
			}
		});
	},
	userlist : function(){
		$.ajax({
			type: "GET",
			url : 'app.php',	
			data : {
				ctrl : 'App',
				action : "getUserList", 
				table: userObj.table
			}
		}).done(function(data){
			if(document.getElementById('is_admin').getAttribute('data-admin') != 0) {
				$('#user_list').append('<div id="invite"><button id="invite_button">Invite</button></div>');
				$('#invite').on('click', function(){
				   	$('#calendar, #user_list').empty();
				   	pages.renderpage('#calendar', '#calendar', pages.invite());
				});
			}
			$('#user_list').append(data);
		});
	},
	send_invite : function(email){
		$.ajax({
			type: "POST",
			url : 'app.php',  
			data : {
				ctrl : 'App', 
				action : "sendInvite", 
				table: userObj.table, 
				email: email
			}
		}).done(function(data){
			alert(data);
  		});    
	}
}

var pages = {
	events : function(){
		$('#trip_id_button').on('click', function(){
			var tableKey = $('#trip_id').val();
			$.get('app.php', {ctrl : 'App', action : 'joinExistingTrip' , tableKey : tableKey})  
			.done(function(data){
				if (data == false){
					//'refresh' page when user joins table
					loggedIn.calendar();
				} else {
					alert(data);
				}
			});
		});

		//create a new trip
		$('#new_trip_button').on('click', function(){
			var newTripName = $('#new_trip').val();
			$.post('app.php', {ctrl : 'App', action : 'createNewTrip', newTripName : newTripName})  
			.done(function(data){
				alert(data);
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
	jointrip : function(){
		var existingtrip = '<br /><h2>Join An Existing Trip</h2><br/>';
		existingtrip += '<div>Enter Trip Key: ' + '<input type="text" id="trip_id" /><button id="trip_id_button">Go!</button></div><br />';
		existingtrip += '<button onclick="loggedIn.calendar()">Go back</button>';
		return existingtrip;
	}, 
	createtrip: function(){
		var createtrip = '<br /><h2>Create A New Trip</h2><br />';
		createtrip += '<div>Destination: ' + '<input type="text" id="new_trip" /><button id="new_trip_button">Go!</button></div><br />';
		createtrip += '<button onclick="loggedIn.calendar()">Go back</button>';
		return createtrip;
	},
	invite: function(){
		var invite = '<br /><h2>Invite</h2><br />';
		invite += '<div>Send invite by email: <input id="invite_email" type="text" /></div>';
		invite += '<button onclick="Authentication.validate.confirm_invite()">Invite</button>';
		invite += '<button onclick="loggedIn.calendar()">Go back</button>';
		return invite;
	}
};

//sets event handlers
function init(){
	document.getElementById('previousMonth').addEventListener('click', function(){
		var preMonth = $(this).attr('data-preMonth'),
			preYear = $(this).attr('data-preYear');
		$.ajax({
			type: "GET",
			url : 'app.php',
			data : {
				month : preMonth,
				year : preYear, 
				table : userObj.table,
				ctrl : 'App', 
				action : 'loadTable'
			},
			dataType: "html", 	//return datatype
		})
		.done(function(data){
			$('#calendar').fadeOut(200, function(){
				$(this).fadeIn(200).html(data);

				$('#' + userObj.table).attr('selected', 'selected');
				//redefines AJAX asynchronous functions since every event within init() is binded to a dynamically generated element.
				init();
			});
		});
	});

	document.getElementById('nextMonth').addEventListener('click', function(){
		var nextMonth = $(this).attr('data-nextMonth'),
			nextYear = $(this).attr('data-nextYear');
		$.ajax({
			type: "GET",
			url : 'app.php',	
			data : {
				month : nextMonth,
				year : nextYear,
				table : userObj.table,
				ctrl : 'App', 
				action : 'loadTable'					
			},
			dataType: "html", 	
		})
		.done(function(data){
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
		var day = parseInt($(this).find('.calendarDayNum').text());
		//cache some of these vars
		var month = (document.getElementById('previousMonth').getAttribute('data-preMonth') != 12) ? parseInt(document.getElementById('previousMonth').getAttribute('data-preMonth')) + 1: 1;
		var year = (document.getElementById('previousMonth').getAttribute('data-preMonth') != 12) ? parseInt(document.getElementById('previousMonth').getAttribute('data-preYear')) : parseInt(document.getElementById('previousMonth').getAttribute('data-preYear')) + 1;
		
		//if SESSION is still set then use SESSION['user'] to define userID (need this b/c userObj.ID is undefined 
		//if page is refreshed; however the SESSION['user'] will still be defined)
		// var userID = '<?php if (!empty($_SESSION['user'])) echo $_SESSION['user']; ?>';
		var userID = checkSessionUser();

		//otherwise use userObj.ID to define userID
		if (userObj.ID) {
			var userID = userObj.ID;
		}

		$.ajax({
			type: "POST",
			url : 'app.php',
			data : {
				day : day,
				month : month,
				year : year,
				userID : userID, 
				table : userObj.table,
				ctrl : 'App',
				action : 'toggleDate'
			},
			dataType: "html", 	
		})
		.done(function(data){
			//if toggle (delete or add) is successful than refresh AJAX.  CREATE A FUNCTION FOR 'GET' AJAX CALL TO
			//REDUCE REDUNDANT CODE.
			if (data == true) {
				$.ajax({
					type: "GET",
					url : 'app.php',
					data : {
						month : month,
						year : year,
						table : userObj.table,
						ctrl : 'App',
						action : 'loadTable'        
					},
					dataType: "html", 	
				})
				.done(function(data){
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

// //calls loggedIn() -- generates calendar if user visits from new window and still in Session.
// if('<?php if (isset($_SESSION['user'])) echo true; ?>' != ''){
//   $('#select_div').show();
//   loggedIn.calendar();
// }