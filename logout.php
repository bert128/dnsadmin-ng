<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
        include_once('util.inc.php');
        include_once('userprefs.inc.php');

/* does user want to save their prefs on logout */
	if (isset($_SESSION['savelogout'])) {
		if ($_SESSION['savelogout']==1) {
			save_userprefs($_SESSION['userid']);
		}
	}
	session_destroy();
	header('Location: login.php');
	exit;
?>
