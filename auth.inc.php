<?php

        session_start();
        if (!isset($_SESSION['username'])) {
                header('Location: login.php');
                exit;
        }

function needadmin() {
	if ($_SESSION['admin'] != 1) {
		error("This function requires admin privileges");
	}
}

# is user an admin
function isadmin() {
	if ($_SESSION['admin']==1) {
		return TRUE;
	}
	return FALSE;
}

# has user permission to create domains
function cancreate() {
	if ($_SESSION['canadd'] < 1) {
		return FALSE;
	}
	return TRUE;
}

function is_owner($domainid, $userid) {
	global $DB;

	$query = $DB->prepare("SELECT domain_id, owner FROM zones WHERE domain_id=? AND owner=?");
	$dbreturn = $DB->execute($query, array((int)$domainid, (int) $userid));

	if ($dbreturn->numRows() >= 1) {
		return TRUE;
	}

	return FALSE;
}

function is_owner_tp($tpid, $userid) {
	global $DB;

	$query = $DB->prepare("SELECT id FROM templates WHERE id=? AND owner=?");
	$dbreturn = $DB->execute($query, array((int)$userid, (int) $tpid));

	if ($dbreturn->numRows() >= 1) {
		return TRUE;
	}

	return FALSE;
}

/* Is the currently logged in user allowed to access this domain */
/* TODO: sub admin support */
function checkperm($userid, $domainid) {
	if (!(is_owner($domainid, $userid)) && !(isadmin())) {
	        error("Insufficient privileges for requested function");
	}
}

function checkpermtp($userid, $tpid) {
	if (!(is_owner_tp($tpid, $userid)) && !(isadmin())) {
	        redirect("error.php?error=perm");
	}
}

function checkflag ($userid, $flag) {
        global $DB;

        $query = $DB->prepare("SELECT * FROM userflags WHERE uid=? AND keyword=?");
        $dbreturn = $DB->execute($query, array((int) $userid, $flag));

        if ($dbreturn->numRows() >= 1) {
		$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);
		if ($row->value==1) {
			return TRUE;
		}
        }

        return FALSE;
}

function userexists ($username) {
        global $DB;

        $query = $DB->prepare("SELECT id FROM users WHERE username=?");
        $dbreturn = $DB->execute($query, array($username));

        if ($dbreturn->numRows() >= 1) {
                return TRUE;
        }

        return FALSE;
}

?>
