<?php
	include_once("logging.inc.php");

function showdomains ($count, $page, $adminlist, $search, $user) {
	global $DB, $row_classes, $content_footer;

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }

	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

	set_items($count);

/* do the database queries */
	if ($adminlist==1) {
		if (strlen($search) > 0) {		# admin doing a search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE domains.name LIKE ? ".
				"GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array($searchstr, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE domains.name LIKE ? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array($searchstr));
		} else {		# no search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id ".
				"GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array((int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, NULL);
		}
	} else {
		if (strlen($search) > 0) {		# non admin doing a search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? AND domains.name LIKE ? ".
				"GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array((int) $user, $searchstr, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? AND domains.name LIKE ? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array((int) $user, $searchstr));
		} else {		# no search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? ".
				"GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array((int) $user, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array((int) $user));
		}

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

  	if (!$dbreturn->numRows() >= 1) {
		if (strlen($search) > 0) {
			echo "No domains matched your search query.";
		} else {
			echo "No domains available.";
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

?>
<div class="section">
<?php
	if (strlen($search) > 0) {		?>
			<h1>Search results: (<?php print $total; ?>)</h1>
<?php	} else {
		if ($adminlist==1) {		?>
			<h1>Total domains in system: (<?php print $total; ?>)</h1>
<?php		} else {			?>
			<h1>Domains for user: <?php print htmlentities($_SESSION['username']); ?>, Total: (<?php print $total; ?>)</h1>
<?php		}
	}					?>


	<div class="controls">
<?php show_numberset($thisfile, $page, $search, 0); ?>
        </div>

        <table class="list domains">
        <tr class="header">
                <td class="domain">Domain name</td>
                <td class="type">Type</td>
                <td class="owner">Owner(s)</td>
                <td class="actions">Actions</td>
        </tr>
<?php
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
#if (($adminlist == 1) || (isowner($row->id))) { # only if we own the domain or are admin

?>
        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td class="domain"><a href="editdomain.php?id=<?php print $row->domain_id; ?>"><?php print htmlentities($row->domainname); ?></a></td>
		<td class="type"><?php print $row->type; ?></td>
                <td class="owner">
			<select name="owners"size="1">
			<? domainowners($row->domain_id) ?>
			</select>
		</td>
		<td class="actions">[ <a href="domain-delete.php?id=<?php print $row->domain_id; ?>" onClick="return confirmAction('Delete this domain?')">Delete</a> | <a href="editdomain.php?id=<?php print $row->domain_id; ?>">Edit</a> ]</td>
        </tr>
<?	
#          }
	}
?>        </table>
</div>
<?

  }
}

function domainowners ($domainid) {
        global $DB;

        $query = $DB->prepare("SELECT owner FROM zones WHERE domain_id = ?;");

        $dbreturn = $DB->execute($query, array((int) $domainid));

        if ($dbreturn->numRows() < 1) {
                print "<option value=\"10\">System</option>";
                return;
        }

        while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
                print "<option value=\"10\">". htmlentities(id2user($row->owner)) ."</option>";
        }

}

function showdomain ($domainid, $count, $page, $adminlist, $search) {
	global $DB, $row_classes, $content_footer;

	$user = $_SESSION["userid"];

	if (!(is_owner($domainid, $user)) && !(isadmin())) {
		redirect("error.php?error=perm");
	}

	$dname = domain_id2name($domainid);

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }


	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

/* do the database queries */
		if (strlen($search) > 0) {		# doing a search
			$domainq = $DB->prepare("SELECT * FROM records WHERE domain_id=? AND content LIKE ? OR name LIKE ? ORDER BY name LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domainq, array((int) $domainid, $searchstr, $searchstr, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT * FROM records WHERE domain_id=? AND content LIKE ? OR name LIKE ? ORDER BY name");
			$dbreturnall = $DB->execute($domsall, array((int) $domainid, $searchstr, $searchstr));
		} else {		# no search
			$domainq = $DB->prepare("SELECT * FROM records WHERE domain_id=? ORDER BY name LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domainq, array((int) $domainid, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT * FROM records WHERE domain_id=? ORDER BY name");
			$dbreturnall = $DB->execute($domsall, array((int) $domainid));
		}

	if (PEAR::isError($dbreturn)) {
		print $dbreturn->getMessage();
		header('Location: error.php?error=dberror');
	}

	if (PEAR::isError($dbreturnall)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturnall->getMessage());
		print $dbreturnall->getMessage();
//		header('Location: error.php?error=dberror');
	}

	$total = $dbreturnall->numRows();
	$num_pages = ceil($dbreturnall->numRows() / $count);

	if ($page > $num_pages) { $page = $num_pages; }

  	if (!$dbreturn->numRows() >= 1) {
		if (strlen($search) > 0) {
			echo "No records matched your search query.";
		} else {
			echo "No records available.";
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

?>
<div class="section">
<?php
	if (strlen($search) > 0) {		?>
			<h1>Search results: (<?php print $total; ?>)</h1>
<?php	} else {
		if ($adminlist==1) {		?>
			<h1>Total domains in system: (<?php print $total; ?>)</h1>
<?php		} else {			?>
			<h1>Records for domain <?php print htmlentities($dname); ?> Total (<?php print $total; ?>)</h1>
<?php		}
	}					?>


	<div class="controls">
		<table class="controls">
			<tr class="controls">
				<td class="left">
					<form action="domainproperties.php" method="get">
						<input type="hidden" name="id" value="<?php print htmlentities($domainid); ?>">
						<input type="submit" name="set" value="Properties" title="properties">
					</form>
				</td>
				<td class="right"><?php show_numberset($thisfile, $page, $search, $domainid); ?></td>
			</tr>
		</table>
        </div>

        <table class="list records">
        <tr class="header">
                <td class="name">Record name</td>
                <td class="type">Type</td>
                <td class="content">Content</td>
                <td class="ttl">TTL</td>
                <td class="priority">Priority</td>
                <td class="actions">Actions</td>
        </tr>
<?php
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
#if (($adminlist == 1) || (isowner($row->id))) { # only if we own the domain or are admin

?>
        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td class="name"><a href="editrecord.php?type=0&id=<?php print $row->id; ?>"><?php print htmlentities($row->name); ?></a></td>
		<td class="type"><?php print $row->type; ?></td>
		<td class="content"><?php print $row->content; ?></td>
		<td class="ttl"><?php print $row->ttl; ?></td>
		<td class="priority"><?php print $row->prio; ?></td>
		<td class="actions">[<a href="record-delete.php?type=0&id=<?php print $row->id; ?>">Delete</a> | <a href="editrecord.php?type=0&id=<?php print $row->id; ?>">Edit</a>]</td>
        </tr>
<?
	}
?>        </table>
</div>
<?

  }
}


?>
