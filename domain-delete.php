<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('domain.inc.php');
	include_once('addrecord.inc.php');
	include_once('pages.inc.php');
	include_once('records.inc.php');
	include_once('templates.inc.php');
	include_once('templatetable.inc.php');

if (!isset($_GET['id'])) {
	redirect("index.php");
}

$did = $_GET['id'];

delete_domain($did);

?>
