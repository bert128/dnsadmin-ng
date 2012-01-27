<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('addrecord.inc.php');
	include_once('pages.inc.php');
	include_once('templates.inc.php');
	include_once('templatetable.inc.php');

if (!isset($_GET['id'])) {
	redirect("index.php");
}

$tpid = $_GET['id'];
$template = template_id2name($tpid);
$user = $_SESSION["userid"];

checkpermtp($user, $template);

page_header("Editing template $template");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

save_userprefs($user);

showtemplate($tpid, $perpage, $page, 0, $search);
addform($tpid, 1, 0, NULL);

/* put ajax controls here */

page_footer();

?>
