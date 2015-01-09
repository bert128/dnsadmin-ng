<?php

/* functions for dealing with domains */

/* see if a PTR exists */
function ptr_exists($domainid, $ptr) {
	global $DB;

	$query = $DB->prepare("SELECT id FROM records WHERE domain_id=? AND type='PTR' AND name=?");

print "Selecting name: $ptr from domain $domainid<br>\n";
	$dbreturn = $DB->execute($query, array((int) $domainid, $ptr));

	if (PEAR::isError($dbreturn)) {
		return 0;
	}

/* no existing record */
	if ($dbreturn->numRows()==0) {
		return 0;
	}
/* one existing record */
	if ($dbreturn->numRows()==1) {
		$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);
		return $row->id;
	}
/* anything else, eg multiple records */
	return 0;
}

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

function record2domain($type, $edit) {
	global $DB;

	if ($type==0) {		/* domain */
		$query = $DB->prepare("SELECT domain_id FROM records WHERE id=?");
	} else if ($type==1) {
		$query = $DB->prepare("SELECT domain_id FROM tprecords WHERE id=?");
	}

	$dbreturn = $DB->execute($query, array($edit));

	if ($dbreturn->numRows() != 1) {
		error("Invalid record lookup");
	}

	$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

	return $row->domain_id;
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
	return 0;
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
	if ($domainid==0) {
		error("Invalid domain");
	}

	$query = $DB->prepare("INSERT INTO zones (domain_id, owner) VALUES (?, ?)");
	$dbreturn = $DB->execute($query, array((int) $domainid, (int) $owner));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Cannot update domain owner");
	}

	$_SESSION['infonotice']="Successfully created slave domain: $name";
}

function add_domain_native($name, $template, $owner) {
	global $DB;

	checkpermtp($owner, $template);			/* perform perm check */

	$query = $DB->prepare("INSERT INTO domains (name, type) VALUES (?, 'NATIVE')");
	$dbreturn = $DB->execute($query, array($name));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Failed to add domain");
	}

	$domainid = get_domain_id($name);
	if ($domainid==0) {
		error("Invalid domain");
	}

	apply_template($template, $domainid);

	$query = $DB->prepare("INSERT INTO zones (domain_id, owner) VALUES (?, ?)");
	$dbreturn = $DB->execute($query, array((int) $domainid, (int) $owner));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Cannot update domain owner");
	}

	$_SESSION['infonotice']="Successfully created domain: $name";
}

function generate_ordername($domain, $name)
{
        global $DB;

	$sendname="";
        $newname = str_replace($domain, "", $name);

        $tokens = explode('.', $newname);

        $items = count($tokens);
        $items -= 2;    /* strip the blank entries */

        while ($items>=0) {
            $sendname .= "$tokens[$items] ";
            $items--;
        }

        $sendname = trim($sendname);

        return $sendname;
}

function strip_domain($zoneid, $name)
{
        global $DB;
        $domain = domain_id2name($zoneid);

	if ($name==$domain) { /* special case for no subdomain */
		return "";
	}

        $newname = str_replace(".". $domain, "", $name);
        return $newname;
}

function apply_template($template, $domain) {
	global $DB;

	$query = $DB->prepare("SELECT * FROM tprecords WHERE domain_id=?");
	$dbreturn = $DB->execute($query, array($template));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Cannot select template records");
	}

	$dname = domain_id2name($domain);

	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
		$wrquery = $DB->prepare("INSERT INTO records (domain_id, name, type, content, ttl, prio, ordername, auth) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
		if (strlen($row->name) > 0) {
			$name = $row->name .".". $dname;
		} else {
			$name = $dname;
		}
		$ordername = generate_ordername($domain, $name);
		$dbreturn2 = $DB->execute($wrquery, array((int) $domain, $name, $row->type, $row->content, (int) $row->ttl, (int) $row->prio, $ordername));
		if (PEAR::isError($dbreturn2)) {
			writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
			error("Cannot apply domain template");
		}
	}

}

function delete_domain($id) {
        global $DB;

        $user = $_SESSION['userid'];
        $name = domain_id2name($id);

        if (!(is_owner($id, $user)) && !(isadmin())) {
                error("Permission error");
        } else {
                $query = $DB->prepare("DELETE FROM domains WHERE id=?");
                $dbreturn = $DB->execute($query, array((int) $id));

                $query = $DB->prepare("DELETE FROM records WHERE domain_id=?");
                $dbreturn = $DB->execute($query, array((int) $id));

                $query = $DB->prepare("DELETE FROM zones WHERE domain_id=?");
                $dbreturn = $DB->execute($query, array((int) $id));


                $_SESSION['infonotice']="Successfully deleted domain: $name";
                redirect("index.php");
        }
}
?>
