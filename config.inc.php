<?php
/* establish database connection */

    include_once('DB.php');

    $DB = DB::connect("mysql://pdns:tmZ2rkjL7tQ3@localhost/pdns");

    if (DB::isError($DB)) {
        print $DB->getMessage();
        exit;
    }

    $DBAUDIT = DB::connect("mysql://pdnsaudit:tmZ2rkjL7tQ3@localhost/pdnsaudit");

    if (DB::isError($DBAUDIT)) {
        print $DBAUDIT->getMessage();
        exit;
    }


$CONFIG = array(
	# Application Title
	"AppTitle" => 'DNSAdmin-NG'
);

$rectypes = array('A', 'AAAA', 'CNAME', 'HINFO', 'MX', 'NAPTR', 'NS', 'PTR', 'SOA', 'TXT');

$SESSION_NAME = "DNSAdmin";

session_name($SESSION_NAME);

?>
