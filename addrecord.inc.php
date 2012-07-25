<?php
/*
 * functions for adding records
 */

function modify_record($record, $proc, $name, $type, $priority, $content, $ttl) {
	global $DB;

	$domainid = record2domain(0, $record);
	$domain = domain_id2name($domainid);

	$query = $DB->prepare("UPDATE records SET name=?, type=?, content=?, ttl=?, prio=?, ordername=? WHERE id=?");

	if ($name!="") {
		$insname = $name .".". $domain;
	} else {
		$insname = $domain;
	}
	$ordername = generate_ordername($domain, $insname);
	$dbreturn = $DB->execute($query, array($insname, $type, $content, (int) $ttl, (int) $priority, $ordername, (int) $record));

        if (PEAR::isError($dbreturn)) {
		error($dbreturn->getMessage());
                error("Database error when modifying record");
        }
	$_SESSION['infonotice']="Successfully modified record";
        redirect("editdomain.php?id=$domainid");

}

function add_record($domainid, $proc, $name, $type, $priority, $content, $ttl) {
        global $DB;
	$domain = domain_id2name($domainid);

        $query = $DB->prepare("INSERT INTO records (domain_id, name, type, content, ttl, prio, ordername, auth) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");

	if (strlen($name) > 0) {
		$insname = $name .".". $domain;
	} else {
		$insname = $domain;
	}

	$ordername = generate_ordername($domain, $insname);
        $dbreturn = $DB->execute($query, array((int) $domainid, $insname, $type, $content, (int) $ttl, (int) $priority, $ordername));

        if (PEAR::isError($dbreturn)) {
                error("Database error when inserting template record");
        }
	$_SESSION['infonotice']="Successfully created record";
        redirect("editdomain.php?id=$domainid");
}

function list_types($domain, $default) {
global $rectypes;

print "<select id=ddtype name=\"type\" size=\"1\">\n";

foreach ($rectypes as $c) {
	$add = "";
	if (isset($default)) {
		if ($default==$c) {
			$add = " SELECTED";
		}
	} else {
	        if (eregi('.arpa', $domain) && strtoupper($c) == 'PTR') {
	                $add = " SELECTED";
	        } elseif (strtoupper($c) == 'A') {
	                $add = " SELECTED";
	        }
	}
print "<option name=\"$c\"$add>$c</option>\n";
}

print "</select>\n";
}

/* Display a form allowing user to insert a record */
function addform($domainid, $type, $edit, $data) {
if ($type==0) {			/* domain */
	$domain = domain_id2name($domainid);
} else if ($type==1) {
	$domain = template_id2name($domainid);
} else {
	error("Invalid Type Specified");
}
?>

<script type="text/javascript" src="js/addrecord.js"></script>

<div class="section">
<h1>Add Record</h1>
<table class="addrecord">
<form action="addrecord.php" method="post">
<input type="hidden" name="domainid" value="<?php print $domainid; ?>">
<input type="hidden" name="edit" value="<?php print $edit; ?>">
<input type="hidden" name="proc" value="<?php print $type; ?>">

        <tr class="header">
                <td class="name">Record name</td>
                <td class="type">Type</td>
                <td class="priority">Priority</td>
                <td class="content">Content</td>
		<td class="ttl">TTL</td>
        </tr>
	<tr class="input">
                <td class="name"><input type="text" class="name" name="name" value="<?php if (isset($data->name)) { print htmlentities(strip_domain($domainid, $data->name)); } ?>">.<?php print htmlentities($domain) ?></td>
                <td class="type">
<?php if (isset($data->type)) { list_types($domain, $data->type); } else { list_types($domain, NULL); } ?>
		</td>
                <td class="priority"><input type="text" class="priority" name="priority" value="<?php if (isset($data->prio)) { print htmlentities($data->prio); } ?>"></td>
                <td class="content"><input type="text" class="content" name="content" value="<?php if (isset($data->content)) { print htmlentities($data->content); } ?>"></td>
		<td class="ttl"><input type="text" class="ttl" name="ttl" value="<?php if (isset($data->ttl)) { print htmlentities($data->ttl); } else { print htmlentities($_SESSION['defttl']); } ?>"></td>
	</tr>
	<tr>
<?php	if ($edit > 0) { 	?>
		<td class="controls"><input type="submit" name="modify" value="modify" title="Submit"></td>
<?php	} else {		?>
		<td class="controls"><input type="submit" name="add" value="add" title="Submit"></td>
<?php	}			?>
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
