<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('domain.inc.php');
	include_once('users.inc.php');
	include_once('forms.inc.php');
	include_once('pages.inc.php');
	include_once('search.inc.php');
	include_once('error.inc.php');
	include_once('validate.inc.php');
	include_once('templates.inc.php');
	include_once('addrecord.inc.php');
	include_once('recmap.inc.php');

	$user = $_SESSION["userid"];

if (isset($_POST['update'])) {
	if (isset($_POST['id'])) { $id = $_POST['id']; } else { error("Invalid record id"); }
	if (isset($_POST['value'])) { $value = $_POST['value']; } else { rror("Invalid value"); }

	if (!(is_owner_recmap($id, $user)) && !(isadmin())) {
		error("Invalid record id");
	}

/* pull the recmap and get the record id */

	$recid = get_recid_recmap($id);

	$query = $DB->prepare("UPDATE records SET content=? WHERE id=?");
	$dbreturn = $DB->execute($query, array($value, (int) $recid));

	$_SESSION['infonotice']="Updated record with content $value";

	redirect("index.php");
}

redirect("index.php");

?>
