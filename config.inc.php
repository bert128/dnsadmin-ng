<?php
/* establish database connection */

    include_once('DB.php');

    $DB = DB::connect("mysql://pdns:tmZ2rkjL7tQ3@localhost/pdns");

    if (DB::isError($DB)) {
        print $DB->getMessage();
        exit;
    }


$CONFIG = array(
	# Application Title
	"AppTitle" => 'DNSAdmin-NG'
);

$SESSION_NAME = "DNSAdmin";

session_name($SESSION_NAME);

?>
