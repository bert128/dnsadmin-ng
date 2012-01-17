<?php

        session_start();
        if (!isset($_SESSION['username'])) {
                header('Location: login.php');
                exit;
        }

function needadmin() {
	if ($_SESSION['admin'] != 1) {
		redirect("error.php?error=unpriv");
	}
}

# is user an admin
function isadmin() {
	if ($_SESSION['admin']==1) {
		return TRUE;
	}
	return FALSE;
}

# has user permission to create domains
function cancreate() {
	if ($_SESSION['canadd'] < 1) {
		return FALSE;
	}
	return TRUE;
}


?>
