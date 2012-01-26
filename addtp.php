<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('users.inc.php');
	include_once('forms.inc.php');
	include_once('pages.inc.php');
	include_once('search.inc.php');
	include_once('error.inc.php');
	include_once('validate.inc.php');
	include_once('templates.inc.php');
	include_once('addrecord.inc.php');

	$user = $_SESSION["userid"];

if (isset($_POST['add'])) {
	if (isset($_POST['name'])) { $name = $_POST['name']; } else { $name=""; }

	if (isadmin()) {		/* only admin can set the owner field */
		if (isset($_POST['owner'])) { $owner = $_POST['owner']; } else { $owner=$_SESSION['userid']; }
		if (isset($_POST['public'])) { $public = $_POST['public']; } else { $public=0; }
	} else {
		$owner = $_SESSION['userid'];
		$public = 0;
	}

/* validate  */


	add_template($name, $owner, $public);
}

redirect("templates.php");

?>
