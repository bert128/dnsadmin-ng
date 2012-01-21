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

	needadmin();    # this page requires admin privileges

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
