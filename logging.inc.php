<?php

        function writelog ($user, $userlevel, $type, $data) {
                global $DBAUDIT;
                $squer = $DBAUDIT->prepare("INSERT INTO audit (user, userlevel, time, type, data) VALUES (?, ?, NULL, ?, ?);");

                $return = $DBAUDIT->execute($squer, array((int) $user, (int) $userlevel, (int) $type, $data));

                if (DB::isError($return)) {
                        print $DBAUDIT->getMessage();
                        header('Location: login.php?errpr=dberror');
                }
        }

?>
