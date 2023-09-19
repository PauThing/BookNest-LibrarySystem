<?php
	session_start();
	
	session_destroy();
	
	//remove cookies
	if(isset($_COOKIE['userid']) AND isset($_COOKIE['password']))
	{
		setcookie('userid', '', time() - 3600);
		setcookie('password', '', time() - 3600);
	}

	$_SESSION['message'] = "You have signed out.";
	
	// Redirect to index page with a JavaScript alert
    echo '<script>alert("You have signed out."); window.location.href="../index.php";</script>';
?>
