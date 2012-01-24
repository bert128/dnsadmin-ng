<?php

function error($message) {
	$_SESSION['errornotice'] = $message;
	redirect("index.php");
	exit();
}

?>
