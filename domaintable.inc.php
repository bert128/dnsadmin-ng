<?php
	include_once("logging.inc.php");

function showdomains ($count, $page, $adminlist, $search, $user) {
	global $DB, $row_classes, $content_footer;

	$searchstr = "%".$search."%";

/* do the database queries */
	if ($adminlist==1) {
		if (strlen($search) > 0) {		# admin doing a search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE domains.name LIKE ? ".
				"GROUP BY domainname, domain_id ORDER BY domainname");
			$dbreturn = $DB->execute($domains, array($searchstr));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE domains.name LIKE ? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array($searchstr));
		} else {		# no search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id ".
				"GROUP BY domainname, domain_id ORDER BY domainname");
			$dbreturn = $DB->execute($domains, NULL);

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, NULL);
		}
	} else {
		if (strlen($search) > 0) {		# non admin doing a search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? AND domains.name LIKE ? ".
				"GROUP BY domainname, domain_id ORDER BY domainname");
			$dbreturn = $DB->execute($domains, array((int) $user, $searchstr));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? AND domains.name LIKE ? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array((int) $user, $searchstr));
		} else {		# no search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, domains.type AS type, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? ".
				"GROUP BY domainname, domain_id ORDER BY domainname");
			$dbreturn = $DB->execute($domains, array((int) $user));

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
/*
		if ($page > 1) {
			$content_footer["left"] = "<a href=\"". $thisfile ."?page=". ($page-1) ."&search=". htmlentities($search) ."\">&#171 Previous Page</a>";
		}

                $content_footer["middle"] = "Page $page of $num_pages";

		if ($page < $num_pages) {
			$content_footer["right"] = "<a href=\"". $thisfile ."?page=". ($page+1) ."&search=". htmlentities($search) ."\">Next Page &#187</a>";
		}
*/
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
        [ <a href="addhost.php?domain=<?php print htmlentities($domainid); ?>">Add host</a> ]<br>

<?php
/*
	<div class="controls">
<?php show_numberset($thisfile, $page, $search, 0); ?>
        </div>
*/
?>
        <table class="display compact" id="domains-table">
	<thead>
	        <tr class="header">
	                <td>Domain name</td>
	                <td>Type</td>
	                <td>Owner(s)</td>
	                <td>Actions</td>
	        </tr>
	</thead>
	<tbody>
<?php
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
#if (($adminlist == 1) || (isowner($row->id))) { # only if we own the domain or are admin

?>
        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td><a href="editdomain.php?id=<?php print $row->domain_id; ?>"><?php print htmlentities($row->domainname); ?></a></td>
		<td><?php print $row->type; ?></td>
                <td>
			<select name="owners"size="1">
			<?php print domainowners($row->domain_id); ?>
			</select>
		</td>
		<td>[ <a href="domain-delete.php?id=<?php print $row->domain_id; ?>" onClick="return confirmAction('Delete domain: <?php print htmlentities($row->domainname); ?>?')">Delete</a> | <a href="editdomain.php?id=<?php print $row->domain_id; ?>">Edit</a> ]</td>
        </tr>
<?php
#          }
	}
?>		</tbody>
        </table>
</div>
<script>
    $(document).ready(function() {
        $('#domains-table').DataTable({
            "order": [ 0, 'asc' ],
            "columns": [
                {
                    "orderable": true,
                    "searchable": true
                },
                {
                    "orderable": true,
                    "searchable": false
                },
                {
                    "orderable": true,
                    "searchable": false
                },
                {
                    "orderable": false,
                    "searchable": false
                }
            ]
        });
    });
</script>

<?php

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

	$searchstr = "%".$search."%";

/* order nicely */
/* select name, type, content, CASE type WHEN 'SOA' THEN 1 WHEN 'NS' THEN 2 WHEN 'MX' THEN 3 WHEN 'TXT' THEN 4 WHEN 'A' THEN 5 WHEN 'AAAA' THEN 5 ELSE 6 END AS ordering FROM records WHERE domain_id=20 ORDER BY ordering limit 50; */
/* do the database queries */
		if (strlen($search) > 0) {		# doing a search
			$domainq = $DB->prepare("SELECT *, CASE type WHEN 'SOA' THEN 1 WHEN 'NS' THEN 2 WHEN 'MX' THEN 3 WHEN 'TXT' THEN 4 WHEN 'A' THEN 5 WHEN 'AAAA' THEN 5 ELSE 6 END AS ordering  FROM records WHERE domain_id=? AND content LIKE ? OR name LIKE ? ORDER BY ordering");
			$dbreturn = $DB->execute($domainq, array((int) $domainid, $searchstr, $searchstr));

			$domsall = $DB->prepare("SELECT *, CASE type WHEN 'SOA' THEN 1 WHEN 'NS' THEN 2 WHEN 'MX' THEN 3 WHEN 'TXT' THEN 4 WHEN 'A' THEN 5 WHEN 'AAAA' THEN 5 ELSE 6 END AS ordering FROM records WHERE domain_id=? AND content LIKE ? OR name LIKE ? ORDER BY ordering");
			$dbreturnall = $DB->execute($domsall, array((int) $domainid, $searchstr, $searchstr));
		} else {		# no search
			$domainq = $DB->prepare("SELECT *, CASE type WHEN 'SOA' THEN 1 WHEN 'NS' THEN 2 WHEN 'MX' THEN 3 WHEN 'TXT' THEN 4 WHEN 'A' THEN 5 WHEN 'AAAA' THEN 5 ELSE 6 END AS ordering FROM records WHERE domain_id=? ORDER BY ordering");
			$dbreturn = $DB->execute($domainq, array((int) $domainid));

			$domsall = $DB->prepare("SELECT *, CASE type WHEN 'SOA' THEN 1 WHEN 'NS' THEN 2 WHEN 'MX' THEN 3 WHEN 'TXT' THEN 4 WHEN 'A' THEN 5 WHEN 'AAAA' THEN 5 ELSE 6 END AS ordering FROM records WHERE domain_id=? ORDER BY ordering");
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
			$content_footer["left"] = "<a href=\"". $thisfile ."?page=". ($page-1) ."&id=". $domainid ."&search=". htmlentities($search) ."\">&#171 Previous Page</a>";
		}

                $content_footer["middle"] = "Page $page of $num_pages";

		if ($page < $num_pages) {
			$content_footer["right"] = "<a href=\"". $thisfile ."?page=". ($page+1) ."&id=". $domainid ."&search=". htmlentities($search) ."\">Next Page &#187</a>";
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
				<td class="right">[ <a href="addhost.php?domain=<?php print htmlentities($domainid); ?>">Add host</a> ]</td>
			</tr>
		</table>
        </div>

        <table class="display compact" id="domain-records">
	<thead>
	        <tr class="header">
	                <td>Record name</td>
	                <td>Type</td>
	                <td>Content</td>
	                <td>TTL</td>
	                <td>Priority</td>
	                <td>Actions</td>
	        </tr>
	</thead>
	<tbody>
<?php
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
#if (($adminlist == 1) || (isowner($row->id))) { # only if we own the domain or are admin

?>
        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td><a href="editrecord.php?type=0&id=<?php print $row->id; ?>"><?php print htmlentities($row->name); ?></a></td>
		<td><?php print $row->type; ?></td>
		<td><?php print $row->content; ?></td>
		<td><?php print $row->ttl; ?></td>
		<td><?php print $row->prio; ?></td>
		<td>[<a href="record-delete.php?type=0&id=<?php print $row->id; ?>" onClick="return confirmAction('Delete this record?')">Delete</a>]</td>
        </tr>
<?php
	}
?>
	</tbody>
        </table>
<script>
    $(document).ready(function() {
        $('#domain-records').DataTable({
            "order": [[ 1, 'asc' ], [ 0, 'asc' ]],
            "columns": [
                {
                    "orderable": true,
                    "searchable": true
                },
                {
                    "orderable": true,
                    "searchable": true,
                    "render": function ( data, type, row, meta ) {
                        //Do not alter printing etc
                        if (type !== 'sort') {
                            return data;
                        }

                        //Else sorting initiated
                        switch(data) {
                            case "SOA":
                                return 0;

                            case "NS":
                                return 1;

                            case "MX":
                                return 2;

                            default:
                                return 3;
                        }
                    }
                },
                {
                    "orderable": false,
                    "searchable": false
                },
                {
                    "orderable": false,
                    "searchable": false
                },
                {
                    "orderable": false,
                    "searchable": false
                },
                {
                    "orderable": false,
                    "searchable": false
                }
            ]
        });
    });
</script>
</div>
<?php

  }
}


?>
