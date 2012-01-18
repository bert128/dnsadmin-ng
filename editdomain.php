<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');

if (!isset($_GET['id'])) {
print "Error";
exit;
	redirect("index.php");
}	

$domainid = $_GET['id'];
$domain = domain_id2name($domainid);

page_header("Records for domain $domain");

if (isset($_GET['items'])) { $perpage = $_GET['items']; } else { $perpage = $_SESSION['items']; };
if (isset($_GET['page'])) { $page = $_GET['page']; } else { $page = 0; };


if (isset($_GET['itemsx'])) {
        if (is_numeric($_GET['itemsx'])) {
                $perpage = $_GET['itemsx'];
        }
}

if (isset($_GET['search'])) {
	$search = $_GET['search'];
} else {
	$search = "";
}

$_SESSION['items'] = $perpage;

save_userprefs($_SESSION['userid']);


showdomain($domainid, $perpage, $page, 0, $search);

/* put ajax controls here */

page_footer();

?>
