<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('users.inc.php');
	include_once('domaintable.inc.php');
	include_once('pages.inc.php');
	include_once('error.inc.php');

	needadmin();	# this page requires admin privileges

page_header("All Domains");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

save_userprefs($_SESSION['userid']);


showdomains($perpage, $page, 1, $search, 0);


page_footer();

?>
