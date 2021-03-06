<?php

/* user related functions */

function makesalt($algo, $len) {
    static $seed = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $salt = '';
    for ($i = 0; $i < $len; $i++) {
        $salt .= substr($seed, mt_rand(0, 63), 1);
    }
    return $algo . $salt;
}

function changepass($userid, $newpass) {
	global $DB, $CONFIG;

	if ($CONFIG["crypto"] > 0) {		/* using new salted crypt */
		$salt = makesalt('$6$', 16);
		$password = crypt($newpass, $salt);
	} else {
		$password = md5($newpass);
	}

	$query = $DB->prepare("UPDATE users SET password=? WHERE id=?");
	$dbreturn = $DB->execute($query, array($password, (int) $userid));

	if (PEAR::isError($dbreturn)) {

                redirect("error.php");
                exit();
        }
	return 0;
}

function modify_user ($userid, $username, $fullname, $email, $descr, $flags) {
	global $DB;

	$query = $DB->prepare("UPDATE users SET username=?, fullname=?, email=?, description=? WHERE id=?");
	$dbreturn = $DB->execute($query, array($username, $fullname, $email, $descr, $userid));
	if (PEAR::isError($dbreturn)) {
		redirect("error.php");
		exit();
	}
	setflag($userid, "admin", $flags['admin']);
	setflag($userid, "add", $flags['add']);
	return 0;
}

function add_user ($username, $fullname, $email, $descr, $password, $flags) {
	global $DB;

	$query = $DB->prepare("INSERT INTO users (username, fullname, email, description, active) VALUES (?, ?, ?, ?, 1)");
	$dbreturn = $DB->execute($query, array($username, $fullname, $email, $descr));

	if (PEAR::isError($dbreturn)) {
		error($dbreturn->getMessage());
		exit();
	}

	$uid = user_name2id($username);
	changepass($uid, $password);
	setflag($uid, "admin", $flags['admin']);
	setflag($uid, "add", $flags['add']);
	$_SESSION['infonotice']="Successfully added user";
	redirect("useradmin.php");
}

/* form for adding or editing a user */
function userform ($userid) {
	global $DB;

	$query = $DB->prepare("SELECT * FROM users WHERE id=?");

	$flag_admin = 'f';
	$flag_add = 'f';

	if ($userid != 0) {
		$dbreturn = $DB->execute($query, array((int) $userid));
		if ((PEAR::isError($dbreturn)) || ($dbreturn->numRows() != 1)) {
			redirect("error.php");
			exit();
		}

		if ($dbreturn->numRows() != 1) {	/* database fault */
			$userid = 0;
		} else {
			$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);
		}
		if (checkflag($userid, 'admin')) { $flag_admin=1; }
		if (checkflag($userid, 'add')) { $flag_add=1; }
	}
?>
<div class="section">
<?php	if ($userid == 0) {
		print "<h1>Create new user</h1>\n";
	} else {
		$username = id2user($userid);
		print "<h1>Edit user $username</h1>\n";
	}
?>
<br>
<form action="edituser.php" method="post">
	<table class="form">
	<input type="hidden" name="userid" value="<?php print $userid; ?>">
<?php		/* form elements here */
	if ($userid != 0) { inputline("User Name", "username", $row->username); } else { inputline("User Name", "username", ""); }
	if ($userid != 0) { inputline("Full Name", "fullname", $row->fullname); } else { inputline("Full Name", "fullname", ""); }
	if ($userid != 0) { inputline("Email", "email", $row->email); } else { inputline("Email", "email", ""); }
	if ($userid != 0) { inputline("Change Password", "password", ""); } else { inputline("Password", "password", ""); }
	if ($userid != 0) { inputbox("Description", "descr", $row->description); } else { inputbox("Description", "descr", ""); }

	tickbox("admin", $flag_admin, "Admin");
	tickbox("add", $flag_add, "Can add domains");

#checkflag ($userid, $flag)
#function tickbox ($name, $srcvar, $title) {
#function inputbox ($descr, $name, $default)
#function inputline ($descr, $name, $default)
?>
        <tr><td class="controls" colspan="2">
		<input type="submit" name="save" value="<?php if ($userid != 0) { print "Save"; } else { print "Add"; } ?>" title="Save changes">
		<input type="submit" name="delete_user" value="Delete" onClick="return confirmAction('Delete this user?')" <?php if ($userid==0) echo " disabled" ?> title="Delete current entry and return to previous screen">
		<input type="submit" name="cancel" value="Cancel" title="Abandon changes">
	</tr>
</table>
</form>
</div>
<?php
}

/* convert userid to user name */
function id2user ($userid) {
        global $DB;

        $query = $DB->prepare("SELECT username FROM users WHERE id = ?;");
        $dbreturn = $DB->execute($query, array((int) $userid));

        if ((PEAR::isError($dbreturn)) || ($dbreturn->numRows() != 1)) {
                return 0;
        }

        $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

        return $row->username;
}


/* count how many domains a given user owns */
function countdoms ($userid) {
	global $DB;

	$query = $DB->prepare("SELECT id FROM zones WHERE owner=?");
	$dbreturn = $DB->execute($query, array((int) $userid));

        if ((PEAR::isError($dbreturn)) || ($dbreturn->numRows() < 1)) {
                return 0;
        }

	$total = $dbreturn->numRows();
	return $total;
}

/* display users flags */
function show_flags ($userid) {
	if (checkflag($userid, 'admin')) { print "A"; } else { print "."; };
	if (checkflag($userid, 'add')) { print "C"; } else { print "."; };
}


function showusers ($count, $page, $adminlist, $search, $public) {
	global $DB, $row_classes, $content_footer;

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }

	$user = $_SESSION["userid"];

	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

	set_items($count);

/* do the database queries */
	if (strlen($search) > 0) {
		$users = $DB->prepare("SELECT * FROM users WHERE users.username LIKE ? OR users.fullname LIKE ? OR users.email LIKE ? ORDER BY users.username LIMIT ? OFFSET ?");
		$dbreturn = $DB->execute($users, array($searchstr, $searchstr, $searchstr, (int) $count, (int) $offset));

		$tpall = $DB->prepare("SELECT * FROM users WHERE users.username LIKE ? OR users.fullname LIKE ? OR users.email LIKE ? ORDER BY users.username");
		$dbreturnall = $DB->execute($tpall, array($searchstr, $searchstr, $searchstr));
	} else {
		$users = $DB->prepare("SELECT * FROM users ORDER BY users.username LIMIT ? OFFSET ?");
		$dbreturn = $DB->execute($users, array((int) $count, (int) $offset));

		$tpall = $DB->prepare("SELECT * FROM users ORDER BY users.username");
		$dbreturnall = $DB->execute($tpall, NULL);
	}

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		header('Location: error.php?error=dberror');
	}

	if (PEAR::isError($dbreturnall)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturnall->getMessage());
		header('Location: error.php?error=dberror');
	}

	$total = $dbreturnall->numRows();
	$num_pages = ceil($dbreturnall->numRows() / $count);

	if ($page > $num_pages) { $page = $num_pages; }

?>
<div class="section">
<?php
	if (strlen($search) > 0) {
		print "<h1>User search results</h1>\n";
	} else {
                print "<h1>User</h1>\n";
	}

  	if (!$dbreturn->numRows() >= 1) {
		$rows = $dbreturn->numRows();
		if (strlen($search) > 0) {
			echo "No users matched your search query.<br>\n";
			echo "</div>\n";
			return;
		} else {
			echo "No users available (this should never occur!).<br>\n";
			echo "</div>\n";
			return;
		}
	} else {

		$thisfile = $_SERVER['PHP_SELF'];
		if ($page > 1) {
			$content_footer["left"] = "<a href=\"". $thisfile ."?page=". ($page-1) ."&search=". htmlentities($search) ."\">&#171 Previous Page</a>";
		}

                $content_footer["middle"] = "Page $page of $num_pages";

		if ($page < $num_pages) {
			$content_footer["right"] = "<a href=\"". $thisfile ."?page=". ($page+1) ."&search=". htmlentities($search) ."\">Next Page &#187</a>";
		}
	}
?>

	<div class="controls">
<?php show_numberset($thisfile, $page, $search, 0); ?>
        </div>

        <table class="list users">
        <tr class="header">
                <td class="username">Username</td>
                <td class="fullname">Full Name</td>
		<td class="email">Email</td>
		<td class="domains">Domains</td>
		<td class="flags">Flags</td>
        </tr>
<?php
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {

	if (DB::isError($row)) {
		header('Location: error.php?error=dberror');
		exit();
	}


?>
        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td class="username"><a href="edituser.php?id=<?php print $row->id; ?>"><?php print htmlentities($row->username); ?></a></td>
                <td class="fullname"><?php print htmlentities($row->fullname); ?></td>
		<td class="email"><?php print htmlentities($row->email); ?></td>
		<td class="domains"><?php print countdoms($row->id); ?></td>
		<td class="flags"><?php show_flags($row->id); ?></td>
        </tr>
<?php	}	?>
        </table>
</div>
<?php
}

function setflag($userid, $name, $value) {
        global $DB;

        $query = $DB->prepare("SELECT * FROM userflags WHERE uid=? AND keyword=?");
        $response = $DB->execute($query, array((int) $userid, $name));

        if (PEAR::isError($response)) {
                $error = $response->getMessage();
		redirect("error.php");
                exit();
        }

        if ($response->numRows() != 1) {                                        /* not one record present */
                wipe_pref($userid, $name);
                $query = $DB->prepare("INSERT INTO userflags (uid, keyword, value) VALUES (?, ?, ?)");
                $return = $DB->execute($query, array((int) $userid, $name, (int) $value));
        } else {
                $row = $response->fetchRow(DB_FETCHMODE_OBJECT);
                $query = $DB->prepare("UPDATE userflags SET value=? WHERE id=?");
                $return = $DB->execute($query, array((int) $value, (int) $row->id));
        }
}

function wipe_flag($userid, $name) {
        global $DB;

        $wipeold = $DB->prepare("DELETE FROM userflags WHERE user=? AND keyword=?");
        $return = $DB->execute($wipeold, array((int) $userid, $name));
        return 0;
}

function user_name2id($username) {
        global $DB;

        $query = $DB->prepare("SELECT id FROM users WHERE username=?");
        $dbreturn = $DB->execute($query, array($username));


        if ($dbreturn->numRows() != 1) {
                return FALSE;
        }
        $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

        return $row->id;
}

/* create a selection list for usernames */
function select_userid() {
        global $DB;
	$user = $_SESSION['userid'];

        $query = $DB->prepare("SELECT id, username FROM users");

        $dbreturn = $DB->execute($query, NULL);

	print "<tr class=\"owner\">\n";
	print "<td class=\"owner\">Owner</td>\n";
	print "<td class=\"owner\">\n";
	print "<select name=\"owner\" size=\"1\">\n";

        if ($dbreturn->numRows() < 1) {
                print "<option value=\"10\">System</option>";
		print "</select>\n";
		print "</td>\n";
                return;
        }

        while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
                print "<option value=\"". $row->id ."\"";
		if ($row->id==$user) {
			print " SELECTED";
		}
		print ">". htmlentities(id2user($row->id)) ."</option>\n";
        }

	print "</select>\n";
	print "</td>\n";

}

?>
