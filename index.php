<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('users.inc.php');
	include_once('domaintable.inc.php');
	include_once('pages.inc.php');
	include_once('recmap.inc.php');

page_header("Your Domains");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

showdomains($perpage, $page, 0, $search, $_SESSION['userid']);

showmappedrecords($perpage, $page, 0, $search, $_SESSION['userid']);

page_footer();

?>
