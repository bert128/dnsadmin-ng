<?php

        session_start();
        if (!isset($_SESSION['username'])) {
                header('Location: login.php');
                exit;
        }

function needadmin() {
	if ($_SESSION['admin'] != 1) {
		redirect("error.php?error=unpriv");
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

?>
