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

	redirect("tpedit.php?id=$domainid");
}


?>
