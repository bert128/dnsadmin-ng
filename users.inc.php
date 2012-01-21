<?php

/* user related functions */
function id2user ($userid) {
        global $DB;

        $query = $DB->prepare("SELECT username FROM users WHERE id = ?;");
        $dbreturn = $DB->execute($query, array((int) $userid));

        if ((PEAR::isError($dbreturn)) || ($dbreturn->numRows() != 1)) {
                return 0;
        }

        $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

        return $row->username;
}

?>
