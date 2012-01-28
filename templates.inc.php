<?php

/* Template functions */

function template_id2name($tpid) {
        global $DB;

        $query = $DB->prepare("SELECT name FROM templates WHERE id=?");
        $dbreturn = $DB->execute($query, array((int) $tpid));


        if ($dbreturn->numRows() != 1) {
                return FALSE;
        }
        $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

        return $row->name;
}

function modify_template_record($record, $domainid, $proc, $name, $type, $priority, $content, $ttl) {
        global $DB;

        $domainid = record2domain(1, $record);
        $domain = template_id2name($domainid);

        $query = $DB->prepare("UPDATE tprecords SET name=?, type=?, content=?, ttl=?, prio=? WHERE id=?");

        $dbreturn = $DB->execute($query, array($name, $type, $content, (int) $ttl, (int) $priority, (int) $record));

        if (PEAR::isError($dbreturn)) {
		error($dbreturn->getMessage());
                error("Database error when modifying template record");
        }
        $_SESSION['infonotice']="Successfully modified record";
        redirect("tpedit.php?id=$domainid");

}

function add_template_record($domainid, $proc, $name, $type, $priority, $content, $ttl) {
	global $DB;

        $query = $DB->prepare("INSERT INTO tprecords (domain_id, name, type, content, ttl, prio) VALUES (?, ?, ?, ?, ?, ?)");
        $dbreturn = $DB->execute($query, array($domainid, $name, $type, $content, $ttl, $priority));

	if (PEAR::isError($dbreturn)) {
		error("Database error when inserting template record");
	}

	$_SESSION['infonotice']="Successfully created template record: $name";
	redirect("tpedit.php?id=$domainid&type=1");
}

function delete_template($tpid) {
	global $DB;

	$user = $_SESSION['userid'];
	$name = template_id2name($tpid);

	if (!(is_owner_tp($tpid, $user)) && !(isadmin())) {
		error("Permission error");
	} else {
		$query = $DB->prepare("DELETE FROM templates WHERE id=?");
		$dbreturn = $DB->execute($query, array((int) $tpid));

		$query = $DB->prepare("DELETE FROM tprecords WHERE domain_id=?");
		$dbreturn = $DB->execute($query, array((int) $tpid));
		$_SESSION['infonotice']="Successfully deleted template: $name";
		redirect("templates.php");
	}
}

function add_template($name, $owner, $public) {
	global $DB;

	$query = $DB->prepare("INSERT INTO templates (name, owner, public) VALUES (?, ?, ?)");
	$dbreturn = $DB->execute($query, array($name, (int)$owner, (int)$public));

	if (PEAR::isError($dbreturn)) {
		error("Database error when inserting template record");
	}

	redirect("templates.php");
}


function select_templates ($def) {
        global $DB;

	select_templates_public($def);
	select_templates_own($def);

}
function select_templates_public ($def) {
        global $DB;

        $pubquery = $DB->prepare("SELECT id, name FROM templates WHERE public=1");
        $dbreturn = $DB->execute($pubquery, NULL);

print "<option value=\"0\"> --- System Templates ---</option>";

        if ($dbreturn->numRows() < 1) {
                return;
        }

        while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
			error("Database error");
                    }
		if ($row->id==$def) {
	                $add = " SELECTED";
		} else {
			$add="";
		}
                print "<option value=\"". $row->id ."\"". $add .">". htmlentities($row->name) ."</option>";
        }
return;
}

function select_templates_own ($def) {
        global $DB;
	$user = $_SESSION['userid'];

        $pubquery = $DB->prepare("SELECT id, name FROM templates WHERE owner=? AND public=0");
        $dbreturn = $DB->execute($pubquery, array($user));

print "<option value=\"0\"> --- User Defined Private Templates ---</option>";

        if ($dbreturn->numRows() < 1) {
                return;
        }

        while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
			error("Database error");
                    }
		if ($row->name==$def) {
	                $add = " SELECTED";
		} else {
			$add="";
		}
                print "<option value=\"". $row->id ."\"". $add .">". htmlentities($row->name) ."</option>";
        }


}

?>
