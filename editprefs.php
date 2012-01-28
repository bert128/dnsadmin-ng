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

if ((isset($_POST['apply'])) || (isset($_POST['save']))) {
/* apply preferences */
	if (isset($_POST['perpage'])) { $_SESSION['items'] = $_POST['perpage']; }
	if (isset($_POST['template'])) { $_SESSION['deftp'] = $_POST['template']; }
	if (isset($_POST['savelogout'])) { $_SESSION['savelogout'] = $_POST['savelogout']; }
	if (isset($_POST['defttl'])) { $_SESSION['defttl'] = $_POST['defttl']; }
	if (isset($_POST['masterip'])) { $_SESSION['masterip'] = $_POST['masterip']; }

	$_SESSION['infonotice']="Preferences updated";
}

if (isset($_POST['save'])) {
	save_userprefs($user);
	$_SESSION['infonotice']="Preferences saved";
}

page_header("User Preferences");

prefsform($user);

/* put ajax controls here */

page_footer();

?>
