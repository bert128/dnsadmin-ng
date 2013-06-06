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
	include_once('pwqcheck.inc.php');

$user = $_SESSION["userid"];
$username = $_SESSION["username"];

if (isset($_POST['change'])) {
	if (isset($_POST['oldpass'])) { $oldpass=$_POST['oldpass']; } else { error("No old password submitted"); }
	if (isset($_POST['newpass1'])) { $newpass1=$_POST['newpass1']; } else { error("No new password submitted"); }
	if (isset($_POST['newpass2'])) { $newpass2=$_POST['newpass2']; } else { error("No new password submitted"); }

	if (!($newpass1==$newpass2)) { error("Passwords do not match"); }

	$seluser = $DB->prepare("SELECT * FROM users WHERE id=?");
	$return = $DB->execute($seluser, array($user));

	if (DB::isError($return)) {
		error("Database error");
	}

	if ($return->numRows() != 1) {
		error("Database error");
	}

	while ($row = $return->fetchRow(DB_FETCHMODE_OBJECT)) {
		$firstchar = substr($row->password, 0, 1);
		if ($firstchar=='$') {
			$cryptpass = crypt($oldpass, $row->password);
		} else {
			$cryptpass = md5($oldpass);
		}

		if (!($cryptpass == $row->password)) { error("Password incorrect"); }
	}

//	$pwcheck = pwqcheck($newpass1, $oldpass, $username, "", "");

//	if ($pwcheck!="OK") {
//		error("Password change failed: $pwcheck");
//	}

	changepass ($user, $newpass1);
	$_SESSION['infonotice']="Successfully changed password";
	redirect("index.php");
}

page_header("Change Password");

passwdform($user);

/* put ajax controls here */

page_footer();

?>
