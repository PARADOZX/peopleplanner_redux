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
<div id="main-container">
	<header>
		<!-- <img src="img/logo.png" title="who's coming?" /> -->
		<div id="header-title">Who's Coming?</div>
		<div id="header-auth">
			<div id="sign-in">
				<?php if (!isset($_SESSION['user'])) echo '<h3>Sign In</h3><label>Email : <br /><input id="log-in-email" type="text" /></label><br />
				<label>Password : <br /><input id="log-in-pass" type="password" /><label><br />
				<button onclick="Authentication.logIn()">log in</button><br /><br/><p>Not registered? <a href="#" onclick="Authentication.register.show()">Register</a></p>'; ?>
			</div>
			<div id="register">
				<h3>Register</h3>
				<p><label>First Name: <input id="register-first-name" type="text" required/></label></p><br/>
				<p>Email: <input id="register-email" type="email" /></p><br/>
				<p>Password: <input id="register-pass" type="password" /></p><br/>
				<p>Confirm Password: <input id="register-pass2" type="password" /></p><br/>
				<button onclick="Authentication.register.send()">Send</button>
			</div>
		</div>
	</header>
	<div id="main">
		<div id="user_list"></div><div id="calendar"></div>
	</div>
	
</div>
<script src="javascript/script.js"></script>
<script>

function checkSessionUser(){
	var userID = '<?php if (!empty($_SESSION['user'])) echo $_SESSION['user']; ?>';
	return userID;
}

//calls loggedIn() -- generates calendar if user visits from new window and still in Session.
if('<?php if (isset($_SESSION['user'])) echo true; ?>' != ''){
  $('#select_div').show();
  loggedIn.calendar();
}

</script>

</body>
</html>