<?php

/* functions for dealing with records */


/* count records belonging to a domain */
function domain_records($domainid, $type) {
        global $DB;

	if (strlen($type) > 0) {
	        $query = $DB->prepare("SELECT id FROM records WHERE domain_id=? AND type=?");
	        $dbreturn = $DB->execute($query, array((int) $domainid, $type));
	} else {
	        $query = $DB->prepare("SELECT id FROM records WHERE domain_id=?");
	        $dbreturn = $DB->execute($query, array((int) $domainid));
	}

        if (PEAR::isError($dbreturn)) {
                writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		return 0;
        }

	return $dbreturn->numRows();
}

function delete_record($type, $record) {
	global $DB;

	if ($type==0) {		/* dns records */
		$query = $DB->prepare("DELETE FROM records WHERE id=?");
	} else if ($type==1) {
		$query = $DB->prepare("DELETE FROM tprecords WHERE id=?");
	} else {
		error("Invalid delete request");
	}

	$dbreturn = $DB->execute($query, array((int) $record));

        if (PEAR::isError($dbreturn)) {
                writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		error("Failed to delete");
        }

	$_SESSION['infonotice']="Successfully deleted record";
}

?>
