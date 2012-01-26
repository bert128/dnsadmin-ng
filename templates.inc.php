<?php

/* Template functions */

function template_id2name($tpid) {
        global $DB;

        $query = $DB->prepare("SELECT name FROM templates WHERE id=?");
        $dbreturn = $DB->execute($query, array((int) $tpid));


        if ($dbreturn->numRows() != 1) {
                return FALSE;
        }
        $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

        return $row->name;
}

function add_template_record($domainid, $proc, $name, $type, $priority, $content, $ttl) {
	global $DB;

        $query = $DB->prepare("INSERT INTO tprecords (domain_id, name, type, content, ttl, prio) VALUES (?, ?, ?, ?, ?, ?)");
        $dbreturn = $DB->execute($query, array($domainid, $name, $type, $content, $ttl, $priority));

	if (PEAR::isError($dbreturn)) {
		error("Database error when inserting template record");
	}

	$_SESSION['infonotice']="Successfully created template: $name";
	redirect("tpedit.php?id=$domainid");
}

function delete_template($tpid) {
	global $DB;

	$user = $_SESSION['userid'];
	$name = template_id2name($tpid);

	if (!(is_owner_tp($tpid, $user)) && !(isadmin())) {
		error("Permission error");
	} else {
		$query = $DB->prepare("DELETE FROM templates WHERE id=?");
		$dbreturn = $DB->execute($query, array((int) $tpid));

		$query = $DB->prepare("DELETE FROM tprecords WHERE domain_id=?");
		$dbreturn = $DB->execute($query, array((int) $tpid));
		$_SESSION['infonotice']="Successfully deleted template: $name";
		redirect("templates.php");
	}
}

function add_template($name, $owner, $public) {
	global $DB;

	$query = $DB->prepare("INSERT INTO templates (name, owner, public) VALUES (?, ?, ?)");
	$dbreturn = $DB->execute($query, array($name, (int)$owner, (int)$public));

	if (PEAR::isError($dbreturn)) {
		error("Database error when inserting template record");
	}

	redirect("templates.php");
}

?>
