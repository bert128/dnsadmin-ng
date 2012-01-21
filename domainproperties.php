<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('addrecord.inc.php');

if (!isset($_GET['id'])) {
	redirect("index.php");
}	

$domainid = $_GET['id'];
$domain = domain_id2name($domainid);
$user = $_SESSION["userid"];

checkperm($user, $domainid);

page_header("Properties for domain $domain");
?>
<div class="section">
Placeholders:<br>

Display number of records</br>
Display list of owners</br>
Provide option to turn dnssec on/off</br>
If admin, provide options to add/remove owners</br>

</div>
<?php
addform($domainid);

/* put ajax controls here */

page_footer();

?>
