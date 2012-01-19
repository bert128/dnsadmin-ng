<?php
/*
 * functions for adding records
 */

function list_types($domain) {
global $rectypes;

print "<select name=\"items\" size=\"1\">\n";

foreach ($rectypes as $c) {
	$add = "";
        if (eregi('.arpa', $domain) && strtoupper($c) == 'PTR') {
                $add = " SELECTED";
        } elseif (strtoupper($c) == 'A') {
                $add = " SELECTED";
        }
print "<option name=\"$c\"$add>$c</option>\n";
}

print "</select>\n";
}

/* Display a form allowing user to insert a record */
function addform($domainid) {
$domain = domain_id2name($domainid);
?>

<div class="section">
<h1>Add Record</h1>
<table class="addrecord">
<form action="addrecord.php" method="post">
        <tr class="header">
                <td class="name">Record name</td>
                <td class="type">Type</td>
                <td class="priority">Priority</td>
                <td class="content">Content</td>
		<td class="ttl">TTL</td>
        </tr>
	<tr class="input">
                <td class="name"><input type="text" class="name" name="name"></td>
                <td class="type">
<?php list_types($domain); ?>
		</td>
                <td class="priority"><input type="text" class="priority" name="priority"></td>
                <td class="content"><input type="text" class="content" name="content"></td>
		<td class="ttl"><input type="text" class="ttl" name="ttl" value="<?php print htmlentities($_SESSION['defttl']); ?>"></td>
	</tr>
	<tr>
		<td class="controls"><input type="submit" name="submit" value="submit" title="Submit"></td>
                <td class="type"></td>
                <td class="priority"></td>
                <td class="content"></td>
		<td class="ttl"></td>
	</tr>
</form>
</table>
</div>

<?php
}


?>
