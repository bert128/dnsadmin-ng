<?php
	include_once('flags.inc.php');

        session_start();
        if (!isset($_SESSION['username'])) {
                header('Location: login.php');
                exit;
        }

/* check validity of the current session */
                $seluser = $DB->prepare("SELECT * FROM users WHERE username = ? AND active=1");

                $return = $DB->execute($seluser, array($_SESSION['username']));

                if (DB::isError($return)) {
                        print $DB->getMessage();
                        header('Location: login.php?error=dberror');
                }

                if ($return->numRows() != 1) {
                        $logmsg = "Attempted login for invalid user: ". $username ." from ". $_SERVER['REMOTE_ADDR'] ." at ". gmdate("D, d M Y H:i:s") ." ";
                        writelog(0, 0, 2, $logmsg);
                        header('Location: login.php?error=notfound');
                }

                while ($row = $return->fetchRow(DB_FETCHMODE_OBJECT)) {

                    if (DB::isError($row)) {
                        header('Location: login.php?error=dberror');
                    }

                                $_SESSION['canadd'] = getflag($row->id, "add");
                                $_SESSION['admin'] = getflag($row->id, "admin");
                                $_SESSION['subadmin'] = getflag($row->id, "subadmin");

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

function is_owner_recmap($recmap, $userid) {
	global $DB;

	$query = $DB->prepare("SELECT * FROM recmap WHERE user=? AND id=?");
	$dbreturn = $DB->execute($query, array((int)$userid, (int) $recmap));

	if ($dbreturn->numRows() >= 1) {
		return TRUE;
	}

	return FALSE;
}

function is_public_tp($tpid) {
	global $DB;

	$query = $DB->prepare("SELECT id FROM templates WHERE id=? AND public=1");
	$dbreturn = $DB->execute($query, array((int) $tpid));

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
	if (!(is_owner_tp($tpid, $userid)) && !(isadmin()) && !(is_public_tp($tpid))) {
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
