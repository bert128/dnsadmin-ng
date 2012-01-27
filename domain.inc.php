<?php

/* functions for dealing with domains */


/* count records belonging to a domain */
function domain_type($domainid) {
        global $DB;

        $query = $DB->prepare("SELECT type FROM domains WHERE id=?");
        $dbreturn = $DB->execute($query, array((int) $domainid));

        if (PEAR::isError($dbreturn)) {
                writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		return "ERROR";
        }

        if ($dbreturn->numRows() == 1) {
                $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);
		return $row->type;
        }

	return "ERROR";
}

/* check if domain exists by name */
function domain_exists($name) {
	global $DB;

	$query = $DB->prepare("SELECT id FROM domains WHERE name=?");
	$dbreturn = $DB->execute($query, array($name));

	if (PEAR::isError($dbreturn)) {
		error("Database error");
	}

	if ($dbreturn->numRows() == 0) {
		return FALSE;
	}
	return TRUE;
}

function get_domain_id($name) {
        global $DB;

        $query = $DB->prepare("SELECT id FROM domains WHERE name=?");
        $dbreturn = $DB->execute($query, array($name));

        if (PEAR::isError($dbreturn)) {
                error("Database error");
        }

        if ($dbreturn->numRows() == 1) {
		$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);
		return $row->id;
        }
	error("Cannot find domain");
}

function add_domain_slave($name, $masterip, $owner) {
	global $DB;

	$query = $DB->prepare("INSERT INTO domains (name, master, type) VALUES (?, ?, 'SLAVE')");
	$dbreturn = $DB->execute($query, array($name, $masterip));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Failed to add domain");
	}

	$domainid = get_domain_id($name);

	$query = $DB->prepare("INSERT INTO zones (domain_id, owner) VALUES (?, ?)");
	$dbreturn = $DB->execute($query, array((int) $domainid, (int) $owner));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Cannot update domain owner");
	}

	$_SESSION['infonotice']="Successfully created slave domain: $name";
}

function add_domain_native($name, $template, $owner) {

error("add_domain_native function not implemented");

}
?>
