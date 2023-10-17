<?php
	session_start();

	//remove session
	session_destroy();
	session_unset();

	//remove cookies
	if (isset($_COOKIE['userid']) and isset($_COOKIE['password'])) {
		setcookie('userid', '', time() - 3600);
		setcookie('password', '', time() - 3600);
	}

	//redirect to index page with a JavaScript alert
	echo '<script>alert("You have signed out."); window.location.href="../../clients/index.php";</script>';
?>
