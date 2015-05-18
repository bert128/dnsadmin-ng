<?php
/* establish database connection */

    include_once('DB.php');

    $DB = DB::connect("mysql://pdns:dnspassword@localhost/pdns");

    if (DB::isError($DB)) {
        print $DB->getMessage();
        exit;
    }

    $DBAUDIT = DB::connect("mysql://pdnsaudit:pdnsaudit@localhost/pdnsaudit");

    if (DB::isError($DBAUDIT)) {
        print $DBAUDIT->getMessage();
        exit;
    }


$CONFIG = array(
	# main logo displayed on login page
        "LoginLogo" => 'images/ev6logo.png',
	# Application Title
	"AppTitle" => 'DNSAdmin-NG',

	# new crypto
	"crypto" => 1
);

$rectypes = array('A', 'AAAA', 'CNAME', 'HINFO', 'MBOXFW', 'MX', 'NAPTR', 'NS', 'PTR', 'SSHFP', 'SOA', 'SRV', 'TXT', 'URL');

$SESSION_NAME = "DNSAdmin";

session_name($SESSION_NAME);

?>
