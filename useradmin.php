<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('templatetable.inc.php');
	include_once('users.inc.php');
	include_once('pages.inc.php');

	needadmin();    # this page requires admin privileges
page_header("User Administration");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

save_userprefs($_SESSION['userid']);


showusers($perpage, $page, 1, $search, 0); /* All users */

page_footer();

?>
