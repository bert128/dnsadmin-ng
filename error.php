<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');

page_header("ERROR");


if (isset($_GET["error"])) {
	$error = $_GET["error"];

	switch ($error) {
		case "dberror":
			$error_msg = "Database Error";
			break;
		case "notfound":
			$error_msg = "User not found";
			break;
		case "unpriv":
			$error_msg = "Privilege Violation";
			break;
		case "domainexists":
			$error_msg = "Domain already exists";
			break;
		default:
			$error_msg = "Unknown Error";
	}
}


?>

        <h1><?php print htmlentities($error_msg); ?></h1>

<?php

page_footer();

?>
