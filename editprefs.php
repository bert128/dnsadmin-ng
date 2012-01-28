<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('addrecord.inc.php');
	include_once('forms.inc.php');
	include_once('error.inc.php');
	include_once('templates.inc.php');
	include_once('users.inc.php');

$user = $_SESSION["userid"];

page_header("User Preferences");

prefsform($user);

/* put ajax controls here */

page_footer();

?>
