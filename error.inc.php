<?php

function error($message) {
	$_SESSION['errornotice'] = $message;
	redirect("index.php");
	exit();
}

if (isset($_GET["error"])) {
        $error = $_GET["error"];

        switch ($error) {
                case "dberror":
                        $error_msg = "Database Error";
                        break;
                case "notfound":
                        $error_msg = "User not found";
                        break;
                case "failed":
                        $error_msg = "Login failed";
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
