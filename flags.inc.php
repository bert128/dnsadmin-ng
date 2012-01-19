<?php
	include_once("logging.inc.php");

        function getflag ($userid, $priv) {
                global $DB;
                $squery = $DB->prepare("SELECT value FROM userflags WHERE uid=? AND keyword=?");

                $return = $DB->execute($squery, array((int) $userid, $priv));

                if ($return->numRows() < 1) {
                        return 0;
                } else if ($return->numRows() < 1) {
                        writelog($_SESSION['username'], $_SESSION['userid'], 4, "WARNING: duplicate permission entries");
                        return 0;
                }

                $row = $return->fetchRow(DB_FETCHMODE_OBJECT);

                return $row->value;
        }

?>
