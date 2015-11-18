<?php 
@session_start();
#####################################################
require_once('./bin/OTPLogin.php');
#$DB=new mysqli("localhost", "root", "", "test");
#$DB=mysql_connect("localhost", "root", "");mysql_select_db("test", $DB);
$DB= new PDO("mysql:host=localhost;dbname=test", "root", "");
$oOTPLogin=new OTPLogin($DB, 'otplogin', 'OTPLogin_Sess', 2592000, 60 );
#$oOTPLogin->senOTP(1); die;
#####################################################

// Handle page request
dispatch(!isset($_GET['page'])? 'index': $_GET['page'] );
exit(0);
?>




<?php
/*  check login session */
function isLogged() {
	return isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
}

/* Show Page */
function dispatch($page) {
    switch($page) {
    	case "index":
    				indexPage();
    				break;
    	case "login":
    				loginPage();
    				break;
    	case "otp":
    				OTPPage();
    				break;
    	case "download":
    				downloadOTPGenPage();
    				break;
    	case "getotp":
    				getOTP();
    				break;
    	default:
    		show404Page();
    }    
}
/* */
function downloadOTPGenPage () {
	global $oOTPLogin;
	if ( ! isLogged() )
		redirToLoginPage();	
	$userid = isLogged() ;
	$info   = array('Copyright' => '(c) 2015-16, All Right are reserved.');
	$otpurl = strtok('http'  . (isset($_SERVER['HTTPS'])?'s':'') . '://' . ($_SERVER['HTTP_HOST']) . ( ( isset($_SERVER['SERVER_PORT'])  && !in_array($_SERVER['SERVER_PORT'], array(80,443))) ? ":{$_SERVER['SERVER_PORT']}" : "" ) . $_SERVER['REQUEST_URI'], '?');
	$otpurl .="?page=getotp&";
	$oOTPLogin->downloadOTPGenFile($otpurl, $userid, $info);
	exit(0);
}
/* */
function getOTP() {
	global $oOTPLogin;
	$userid = isset($_GET['userid']) ? (int) $_GET['userid'] : 0;
	$macadd = isset($_GET['macadd']) ? $_GET['macadd'] : "";
	$code = $oOTPLogin->getOTP($userid, $macadd);
	if ( is_int($code)) {
		print "OTP code is $code.";
		exit(0);
	}
	print "FAILED:1000";
	exit(0);
}

/* */
function show404Page () {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
	echo "<h1> Page Not Found.";
	exit;
}

/* */
function indexPage() {
	global $oOTPLogin;
	if ( ! isLogged() )
		redirToLoginPage();
	// Validate
	if ( !$oOTPLogin->isValid(isLogged()) )
	{
		redirToLoginPage('otp');
		exit ;
	}
ECHO <<<INDEX
	<html>
	<head>
	<title> Welcome  </title>
	</head>
	<style>
	#login {
	    position:fixed;
	    top: 50%;
	    left: 50%;
	    width:30em;
	    height:18em;
	    margin-top: -9em; /*set to a negative number 1/2 of your height*/
	    margin-left: -15em; /*set to a negative number 1/2 of your width*/
	    border: 1px solid #ccc;
	    background-color: #f3f3f3;
	}
	</style>
	<body>
	<div id="login">
		<center> <h1> Welcome to OTPLogin Page <h1> </center> 
		<br />
		<br />
		<br />
		<center><p style="color:green"> Congrats !!, You are in safe place . <p></center>
	</div>
	</body>
	</html>
INDEX;

}


/* Logged  */
function logged() {
	global $oOTPLogin;
	if (!$PHPLoginJail){askOTPPage();exit;}
}


/* askOTP */
function OTPPage() {
	global $oOTPLogin;
	if ( ! isLogged() )
		redirToLoginPage();
	if ( isset($_POST['form']['users']['otp'])) {
		$otp=(int)$_POST['form']['users']['otp'];
		if ( $oOTPLogin->verifyOTP(isLogged(), $otp ) ) {
			redirToLoginPage('index');
			exit ;
		}
	} 
	$initStatus = $oOTPLogin->initOTP(isLogged());

ECHO <<<askOTP
	<html>
	<head>
	<title> Enter OTP </title>
	</head>
	<script> 
		var m=$initStatus;
		var t=window.setInterval(function(){ document.getElementById('tout').innerHTML=(--m); if (!m) window. clearInterval(t); }, 1000);
	</script>
	<style>
	#login {
	    position:fixed;
	    top: 50%;
	    left: 30%;
	    width:50em;
	    height:18em;
	    margin-top: -9em; /*set to a negative number 1/2 of your height*/
	    margin-left: -15em; /*set to a negative number 1/2 of your width*/
	    border: 1px solid #ccc;
	    background-color: #f3f3f3;
	}
	</style>
	<body>
	<div id="login">
	<table border='1' width="100%">
	<tr>
	<td width=60%>
	<i> <b style='color:green'>Your OTP session has been started, Please download your OTP Generator file and run it to get new OTP.<i>
	</b>
	<br />
	<hr />
	<center><i> Time Left : <span id="tout">$initStatus</span></center>
	<center style='height:20%'></center>
	<fieldset> 
	<legend> OTP </legend>
	<hr />
	<form method="post">
		Enter OTP<input type='text' name='form[users][otp]' value='' />
	<hr />
	<input type='submit' value="Proceed" name='form[users][submit]' />
	</form>
	</fieldset> 
	</td>
	<td align="top" valign="middle">
		 <center> <span>OTPLogin Generator</span> <a href="?page=download"> download </a> </center>
		 <u><b>For window User :</u></b> <br />
		 &nbsp;&nbsp;Run the download file i,e OTPLogin-UID-1.cmd
		 <u><b>For linux User : </u></b><br />
		 &nbsp;&nbsp;1. Open terminal <br />
		 &nbsp;&nbsp;2. chmod +x OTPLogin-UID-1.sh<br />
		 &nbsp;&nbsp;2. ./OTPLogin-UID-1.sh
		 <b>Ouput</b>
		 <pre>
##############################################
OTPLoginGenerator V1.0.0
Generated On: Wed, 18 Nov 2015 07:56:03 GMT
Copyright: (c) 2015-16, All Right are reserved.
UserID:1
#############################################
Please wait .... Requesting  to server ...
Requesting to server . .. ...
Request completed with status 200
Redirecting response to console . .. ...
+++++++++++++++++++++++[OUTPUT]++++++++++++++
OTP code is 572439.
+++++++++++++++++++++++[OUTPUT]++++++++++++++
		 </pre>
	</td>
	</tr>
	</table>
	</div>
	</body>
	</html>
askOTP;
}

/* Login page*/ 
function loginPage() {
	if ( isset($_POST['form'])) {
		$_SESSION['userid'] = 1 ;
		@header('Location: OTPLogin_Example.php');
		return ;
	}

	ECHO <<<loginPage
	<html>
	<head>
	<title> Login </title>
	</head>
	<style>
	#login {
	    position:fixed;
	    top: 50%;
	    left: 50%;
	    width:30em;
	    height:18em;
	    margin-top: -9em; /*set to a negative number 1/2 of your height*/
	    margin-left: -15em; /*set to a negative number 1/2 of your width*/
	    border: 1px solid #ccc;
	    background-color: #f3f3f3;
	}
	</style>
	<body>
	<div id="login">
	<center style='height:20%'></center>
	<fieldset> 
	<legend> Login </legend>
	<form method="post">
	User Name<input type='text' name='form[users][userid]' value='user' />
	Password <input type='password' name='form[users][password]' value='password' />
	<hr />
	<input type='submit' value="Login" name='form[users][submit]' />
	</form>
	</fieldset> 
	</div>
	</body>
	</html>
loginPage;
exit;
}

/* redirToLoginPage */
function redirToLoginPage($page =  null ) {
	$page =  is_null($page) ? 'login' : $page;
	echo <<<redirToLoginPage
<html>
<head>
<title> Please wait .. redirecting to login page </title>
<meta http-equiv='refresh' content='2;url=?page=$page'>
</head>
<body>
<h2> Please wait .. redirecting to $page page
</body>
</html>	
redirToLoginPage;
exit;
}
?>