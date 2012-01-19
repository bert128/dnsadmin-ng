<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
        include_once('util.inc.php');
        include_once('userprefs.inc.php');

	save_userprefs($_SESSION['userid']);
	
	$_SESSION['errornotice'] = "Preferences saved";
	redirect("index.php");

?>
