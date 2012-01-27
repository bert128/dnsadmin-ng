<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('users.inc.php');
	include_once('domaintable.inc.php');
	include_once('pages.inc.php');
	include_once('forms.inc.php');
	include_once('error.inc.php');
	include_once('addrecord.inc.php');
	include_once('domain.inc.php');

$user = $_SESSION['userid'];

if (isset($_GET['id'])) {
	$record = $_GET['id'];
} else {
	error("No record selected for editing");
}

if (isset($_GET['type'])) {
	$type = $_GET['type'];
} else {
	error("No record type selected for editing");
}

if ($type==0) {
	page_header("Edit domain record");
        $query = $DB->prepare("SELECT * FROM records WHERE id=?");
        $dbreturn = $DB->execute($query, array((int) $record));
} else if ($type==1) {
	page_header("Edit template record");
        $query = $DB->prepare("SELECT * FROM tprecords WHERE id=?");
        $dbreturn = $DB->execute($query, array((int) $record));
}

if ($dbreturn->numRows() != 1) {
	error("Record not found");
}
$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

/* perm check */
if ($type==0) {
	if (!(is_owner($row->domain_id, $user)) && !(isadmin())) {
		error("Permission Violation");
	}
} else if ($type==1) {
	if (!(is_owner_tp($row->domain_id, $user)) && !(isadmin())) {
		error("Permission Violation");
	}
}

addform($row->domain_id, $type, $record, $row);


page_footer();

?>
