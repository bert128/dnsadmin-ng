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

	needadmin();    # this page requires admin privileges

if (isset($_POST['delete_user'])) {
	if (isset($_POST['userid'])) { $userid = $_POST['userid']; } else { $_SESSION['errornotice']="Delete called without userid"; redirect("index.php"); }

	$username = id2user($userid);
	$query = $DB->prepare("DELETE FROM users WHERE id=?");
	$dbreturn = $DB->execute($query, array($userid));

	$query = $DB->prepare("DELETE FROM userprefs WHERE user=?");
	$dbreturn = $DB->execute($query, array($userid));

	$query = $DB->prepare("DELETE FROM userflags WHERE uid=?");
	$dbreturn = $DB->execute($query, array($userid));

	$query = $DB->prepare("DELETE FROM zones WHERE owner=?");
	$dbreturn = $DB->execute($query, array($userid));

	$_SESSION['infonotice']="Successfully deleted user $username";
	redirect("useradmin.php");
	exit();
}

if (isset($_POST['save'])) {
	if (isset($_POST['userid'])) { $userid = $_POST['userid']; } else { $userid=0; }
	if (isset($_POST['username'])) { $username = $_POST['username']; } else { redirect("error.php?error=2"); }
	if (isset($_POST['fullname'])) { $fullname = $_POST['fullname']; } else { redirect("error.php?error=3"); }
	if (isset($_POST['email'])) { $email = $_POST['email']; } else { error("Invalid Email"); }
	if (isset($_POST['descr'])) { $descr = $_POST['descr']; } else { error("Invalid User Description"); }
	if (isset($_POST['password'])) { $password = $_POST['password']; } else { $password = ""; }

	if (isset($_POST['admin'])) { $flags['admin'] = $_POST['admin']; } else { $flags['admin'] = 0; }
	if (isset($_POST['add'])) { $flags['add'] = $_POST['add']; } else { $flags['add'] = 0; }

/* more validation */
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { error("Invalid Email"); }

	if ($userid==0) {
		if (userexists($username)) {
			redirect("error.php?error=userexists");
			exit();
		}
		add_user ($username, $fullname, $email, $descr, $password, $flags);
	} else {
		if (userexists($username)) {	/* special logic to allow username changes */
			$existing = user_name2id($username);
			if ($existing==$userid) {
				modify_user ($userid, $username, $fullname, $email, $descr, $flags);
				$_SESSION['infonotice']="Successfully updated user $username";
				if (strlen($password) > 0) {
					changepass ($userid, $password);
					$_SESSION['infonotice']="Successfully updated user $username and changed password";
				}
			} else {
				$_SESSION['errornotice']="Username already exists";
			}
		}
	}
	redirect("useradmin.php");
}

if (isset($_GET['id'])) {
	$userid = $_GET['id'];
	$username = id2user($userid);
	page_header("Edit User $username");
} else {
	$userid = 0;
	page_header("Add new user");
}

userform($userid);

if (isset($_GET['id'])) {
	showdomains($perpage, $page, 0, $search, $userid);
}

page_footer();

?>
