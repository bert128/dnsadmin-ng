<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('addrecord.inc.php');
	include_once('pages.inc.php');
	include_once('error.inc.php');

if (!isset($_GET['domain'])) {
	redirect("index.php");
}

$domainid = $_GET['domain'];
$domain = domain_id2name($domainid);
$user = $_SESSION["userid"];

checkperm($user, $domainid);

page_header("Add host to domain $domain");

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

save_userprefs($user);

showdomain($domainid, $perpage, $page, 0, $search);
addhostform($domainid, 0, 0, NULL);

/* put ajax controls here */

page_footer();

?>
