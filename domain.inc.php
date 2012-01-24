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

?>
