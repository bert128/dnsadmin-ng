<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('addrecord.inc.php');
	include_once('forms.inc.php');
	include_once('error.inc.php');
	include_once('templates.inc.php');

$user = $_SESSION["userid"];

page_header("Create New Domain");
?>
<div class="section">
Placeholders:<br>

Display number of records</br>
Display list of owners</br>
Provide option to turn dnssec on/off</br>
If admin, provide options to add/remove owners</br>

</div>
<?php
domain_addform();

/* put ajax controls here */

page_footer();

?>
