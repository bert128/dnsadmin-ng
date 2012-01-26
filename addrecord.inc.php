<?php
/*
 * functions for adding records
 */

function add_record($domainid, $proc, $name, $type, $priority, $content, $ttl) {
        global $DB;

	error("add_record function not implemented");

        $query = $DB->prepare("INSERT INTO tprecords (domain_id, name, type, content, ttl, prio) VALUES (?, ?, ?, ?, ?, ?)");
        $dbreturn = $DB->execute($query, array($domainid, $name, $type, $content, $ttl, $priority));

        if (PEAR::isError($dbreturn)) {
                error("Database error when inserting template record");
        }

        redirect("tpedit.php?id=$domainid");
}

function list_types($domain) {
global $rectypes;

print "<select id=ddtype name=\"type\" size=\"1\">\n";

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
function addform($domainid, $type) {
if ($type==0) {			/* domain */
	$domain = domain_id2name($domainid);
} else if ($type==1) {
	$domain = template_id2name($domainid);
} else {
	redirect("error.php");
	exit();
}
?>

<script type="text/javascript" src="js/addrecord.js"></script>

<div class="section">
<h1>Add Record</h1>
<table class="addrecord">
<form action="addrecord.php" method="post">
<input type="hidden" name="domainid" value="<?php print $domainid; ?>">
<input type="hidden" name="proc" value="<?php print $type; ?>">

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
		<td class="controls"><input type="submit" name="add" value="add" title="Submit"></td>
                <td class="type"></td>
                <td class="priority"></td>
                <td class="content"></td>
		<td class="ttl"></td>
	</tr>
</form>
</table>
</div>

<div class="section help" id="help-a">
<h1>A Records</h1>
This is where the help text for A records goes.
</div>

<div class="section help" id="help-aaaa">
<h1>AAAA Records</h1>
This is where the help text for AAAA records goes.
</div>

<div class="section help" id="help-cname">
<h1>CNAME Records</h1>
This is where the help text for CNAME records goes.
</div>

<div class="section help" id="help-hinfo">
<h1>HINFO Records</h1>
This is where the help text for HINFO records goes.
</div>

<div class="section help" id="help-mx">
<h1>MX Records</h1>
This is where the help text for MX records goes.
</div>

<div class="section help" id="help-mboxfw">
<h1>MBOXFW Records</h1>
This is where the help text for MBOXFW records goes.
</div>

<div class="section help" id="help-naptr">
<h1>NAPTR Records</h1>
This is where the help text for NAPTR records goes.
</div>

<div class="section help" id="help-ns">
<h1>NS Records</h1>
This is where the help text for NS records goes.
</div>

<div class="section help" id="help-ptr">
<h1>PTR Records</h1>
This is where the help text for PTR records goes.
</div>

<div class="section help" id="help-soa">
<h1>SOA Records</h1>
This is where the help text for SOA records goes.
</div>

<div class="section help" id="help-srv">
<h1>SRV Records</h1>
This is where the help text for SRV records goes.
</div>

<div class="section help" id="help-txt">
<h1>TXT Records</h1>
This is where the help text for TXT records goes.
</div>

<div class="section help" id="help-url">
<h1>URL Records</h1>
This is where the help text for URL records goes.
</div>

<?php
}



?>
