<?php
	include_once("logging.inc.php");

/* read a preferences value from database, if record not found or invalid then use default specified in arg 3 */
function int_read_pref($userid, $name, $default) {
	global $DB;

	$query = $DB->prepare("SELECT value FROM userprefs WHERE user=? AND keyword=?");

	$return = $DB->execute($query, array((int) $userid, $name));

        if (PEAR::isError($return)) {
                writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
                header('Location: error.php?error=dberror');
		exit();
        }

        if ($return->numRows() != 1) {
		return $default;
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

	$query = $DB->prepare("SELECT * FROM userprefs WHERE user=? AND keyword=?");
	$response = $DB->execute($query, array((int) $userid, $name));

	if ($response->numRows() != 1) {					/* not one record present */
		wipe_pref($userid, $name);
		$query = $DB->prepare("INSERT INTO userprefs (user, keyword, value) VALUES (?, ?, ?)");
		$return = $DB->execute($query, array((int) $userid, $name, (int) $value));
	} else {
		$row = $response->fetchRow(DB_FETCHMODE_OBJECT);
		$query = $DB->prepare("UPDATE userprefs SET value=? WHERE id=?");
		$return = $DB->execute($query, array((int) $value, (int) $row->id));
	}
}

function load_userprefs ($userid) {
	$_SESSION['items'] = int_read_pref($userid, "perpage", 20);
	$_SESSION['savelogout'] = int_read_pref($userid, "savelogout", 1);
	$_SESSION['defttl'] = int_read_pref($userid, "defttl", 86400);
	$_SESSION['deftp'] = int_read_pref($userid, "deftp", 1);
}

function save_userprefs ($userid) {
	int_write_pref($userid, "perpage", $_SESSION['items']);
	int_write_pref($userid, "defttl", $_SESSION['defttl']);
	int_write_pref($userid, "savelogout", $_SESSION['savelogout']);
	int_write_pref($userid, "deftp", $_SESSION['deftp']);
}

/* set how many items per page */
function set_items($items) {

}

?>
