<?php

function showdomains ($count, $page, $adminlist, $search) {
	global $DB, $row_classes, $content_footer;

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }

	$user = $_SESSION["userid"];

	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

/* do the database queries */
	if ($adminlist==1) {
		if (strlen($search) > 0) {		# admin doing a search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) 
				AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE domains.name LIKE '%?%'
				GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array($searchstr, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE domains.name LIKE ? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array($searchstr));
		} else {		# no search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) 
				AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id 
				GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array((int) $count, (int) $offset));
		}
	} else {
		if (strlen($search) > 0) {		# non admin doing a search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? AND domains.name LIKE ? ".
				"GROUP BY domainname, domain_id ORDER BY domainname LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domains, array((int) $user, $searchstr, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
				"AS owner, domains.name AS domainname FROM domains LEFT JOIN zones ON domains.id=zones.domain_id LEFT JOIN records ON records.domain_id=domains.id WHERE zones.owner=? AND domains.name LIKE ? ".
				"GROUP BY domainname, domain_id");
			$dbreturnall = $DB->execute($domsall, array((int) $user, $searchstr));
		} else {		# no search
			$domains = $DB->prepare("SELECT domains.id AS domain_id, min(zones.owner) ".
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
		print $dbreturn->getMessage();
		header('Location: error.php?error=dberror');
	}

	if (PEAR::isError($dbreturnall)) {
		print $dbreturn->getMessage();
//		header('Location: error.php?error=dberror');
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
			$content_footer["left"] = "<a href=\"". $thisfile ."?page=". ($page-1) ."\">&#171 Previous Page</a>";
		}

                $content_footer["middle"] = "Page $page of $num_pages";

		if ($page < $num_pages) {
			$content_footer["right"] = "<a href=\"". $thisfile ."?page=". ($page+1) ."\">Next Page &#187</a>";
		}

?>
<div class="section">
	<h1>Domains for user <?php print htmlentities($_SESSION['username']); ?> Total (<?php print $total; ?>)</h1>
	<div class="controls">

<form action="<?php print $thisfile; ?>" method="get">
Show per page:
<select name="items" size="1"> 
<option value="10">10</option>
<option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
<input type="hidden" name="page" value="<?php print htmlentities($page); ?>">
<input type="text" name="itemsx">

<input type="submit" name="set" value="set" title="Set per page display">

</form>
    </div>

        <table class="list domains">
        <tr class="header">
                <td class="domain">Domain name</td>
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
                <td class="owner">
			<select name="owners"size="1">
			<? domainowners($row->domain_id) ?>
			</select>
		</td>
		<td class="actions">
		</td>
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

?>
