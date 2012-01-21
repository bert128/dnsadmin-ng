<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('templatetable.inc.php');
	include_once('users.inc.php');
	include_once('pages.inc.php');

page_header("Templates");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

save_userprefs($_SESSION['userid']);


showtemplates($perpage, $page, 0, $search, 1); /* public templates */

showtemplates($perpage, $page, 0, $search, 0); /* user's templates */

page_footer();

?>
