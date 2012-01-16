<?php
	include('config.inc.php');
	session_start();
	$_SESSION = array();
	if (isset($_COOKIE[$SESSION_NAME])) {
		setcookie($SESSION_NAME, '', time()-42000, '/');
	}
	session_destroy();
	header('Location: login.php');
	exit;
?>
