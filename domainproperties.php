<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('addrecord.inc.php');
	include_once('records.inc.php');
	include_once('domain.inc.php');

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
<table class="domaininfo">
<tr>
	<td>Domain type:</td>
	<td><?php print domain_type($domainid); ?></td>
</tr>
<tr>
	<td>Total records:</td>
	<td><?php print domain_records($domainid, ""); ?></td>
</tr>
<tr>
	<td>Total IPv4 hosts:</td>
	<td><?php print domain_records($domainid, "A"); ?></td>
</tr>
	<td>Total IPv6 hosts:</td>
	<td><?php print domain_records($domainid, "AAAA"); ?></td>
</tr>
</tr>
	<td>Total PTR records:</td>
	<td><?php print domain_records($domainid, "PTR"); ?></td>
</tr>
</table>

</div>
<?php
addform($domainid, 0);

/* put ajax controls here */

page_footer();

?>
