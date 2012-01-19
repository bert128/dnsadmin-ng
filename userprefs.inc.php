<?php
	include_once("logging.inc.php");

function int_read_pref($userid, $name) {
	global $DB;

	$query = $DB->prepare("SELECT value FROM userprefs WHERE user=? AND keyword=?");

	$return = $DB->execute($query, array((int) $userid, $name));

        if (PEAR::isError($return)) {
                writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
                header('Location: error.php?error=dberror');
		exit();
        }

        if ($return->numRows() != 1) {
		return 0;
	}

	$row = $return->fetchRow(DB_FETCHMODE_OBJECT);

	return $row->value;
}

/* clear out old prefs */
function wipe_pref($userid, $name) {
        global $DB;

	$wipeold = $DB->prepare("DELETE FROM userprefs WHERE user=? AND keyword=?");
	$return = $DB->execute($wipeold, array((int) $userid, $name));
	return 0;
}

function int_write_pref($userid, $name, $value) {
	global $DB;

	wipe_pref($userid, $name);

	$query = $DB->prepare("INSERT INTO userprefs (user, keyword, value) VALUES (?, ?, ?)");
	$return = $DB->execute($query, array((int) $userid, $name, (int) $value));
}

function load_userprefs ($userid) {

	$_SESSION['items'] = int_read_pref($userid, "perpage");
	$_SESSION['savelogout'] = int_read_pref($userid, "savelogout");

}

function save_userprefs ($userid) {
	int_write_pref($userid, "perpage", $_SESSION['items']);
	int_write_pref($userid, "savelogout", $_SESSION['savelogout']);
}

/* set how many items per page */
function set_items($items) {

}

?>
