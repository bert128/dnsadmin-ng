<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('templatetable.inc.php');
	include_once('users.inc.php');
	include_once('pages.inc.php');
	include_once('forms.inc.php');

page_header("Templates");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

showtemplates($perpage, $page, 0, $search, 1); 		/* public templates */

showtemplates($perpage, $page, 0, $search, 0); 		/* user's templates */

tp_addform();						/* form for creation of template */

page_footer();

?>
