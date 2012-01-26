<?php
	include_once("logging.inc.php");

function showtemplates ($count, $page, $adminlist, $search, $public) {
	global $DB, $row_classes, $content_footer;

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }

	$user = $_SESSION["userid"];

	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

	set_items($count);

/* do the database queries */
	if ($public==1) {				/* show public templates */
		if (strlen($search) > 0) {
			$templates = $DB->prepare("SELECT * FROM templates WHERE public=1 AND templates.name LIKE ? ORDER BY templates.name LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($templates, array($searchstr, (int) $count, (int) $offset));

			$tpall = $DB->prepare("SELECT id FROM templates WHERE public=1 AND templates.name LIKE ? ORDER BY templates.name");
			$dbreturnall = $DB->execute($tpall, array($searchstr));
		} else {
			$templates = $DB->prepare("SELECT * FROM templates WHERE public=1 ORDER BY templates.name LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($templates, array((int) $count, (int) $offset));

			$tpall = $DB->prepare("SELECT id FROM templates WHERE public=1 ORDER BY templates.name");
			$dbreturnall = $DB->execute($tpall, NULL);
		}
	} else {					/* show private templates */
		if ($adminlist==1) {
			if (strlen($search) > 0) {		# admin doing a search
				$templates = $DB->prepare("SELECT * FROM templates WHERE templates.name LIKE ? ORDER BY templates.name LIMIT ? OFFSET ?");
				$dbreturn = $DB->execute($templates, array($searchstr, (int) $count, (int) $offset));

				$tpall = $DB->prepare("SELECT id FROM templates WHERE templates.name LIKE ? ORDER BY templates.name");
				$dbreturnall = $DB->execute($tpall, array($searchstr));
			} else {		# no search
				$templates = $DB->prepare("SELECT * FROM templates ORDER BY templates.name LIMIT ? OFFSET ?");
				$dbreturn = $DB->execute($templates, array((int) $count, (int) $offset));

				$tpall = $DB->prepare("SELECT id FROM templates ORDER BY templates.name");
				$dbreturnall = $DB->execute($tpall, NULL);
			}
		} else {
			if (strlen($search) > 0) {		# non admin doing a search
				$templates = $DB->prepare("SELECT * FROM templates WHERE templates.owner = ? AND templates.name LIKE ? ORDER BY templates.name LIMIT ? OFFSET ?");
				$dbreturn = $DB->execute($templates, array((int) $user, $searchstr, (int) $count, (int) $offset));

				$tpall = $DB->prepare("SELECT id FROM templates WHERE templates.owner = ? AND templates.name LIKE ? ORDER BY templates.name");
				$dbreturnall = $DB->execute($tpall, array($searchstr, (int) $user));
			} else {		# no search
				$templates = $DB->prepare("SELECT * FROM templates WHERE templates.owner = ? ORDER BY templates.name LIMIT ? OFFSET ?");
				$dbreturn = $DB->execute($templates, array((int) $user, (int) $count, (int) $offset));

				$tpall = $DB->prepare("SELECT id FROM templates WHERE templates.owner = ? ORDER BY templates.name");
				$dbreturnall = $DB->execute($tpall, array((int) $user));
			}
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

?>
<div class="section">
<?php
        if ($public==1) {
		if (strlen($search) > 0) {
			print "<h1>Public template searchresults</h1>\n";
		} else {
	                print "<h1>Public Templates</h1>\n";
		}
	} else {
	        if (strlen($search) > 0) {              ?>
	                        <h1>Your template search results: (<?php print $total; ?>)</h1>
<?php	   	} else {
        	        if ($adminlist==1) {            ?>
        	                <h1>Total templates in system: (<?php print $total; ?>)</h1>
<?php   	        } else {                        ?>
        	                <h1>Templates for user <?php print htmlentities($_SESSION['username']); ?>, Total (<?php print $total; ?>)</h1>
<?php   	        }
        	}
	}

  	if (!$dbreturn->numRows() >= 1) {
		$rows = $dbreturn->numRows();
		if (strlen($search) > 0) {
			echo "No templates matched your search query.<br>\n";
			echo "</div>\n";
			return;
		} else {
			echo "No templates available.<br>\n";
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

        <table class="list templates">
        <tr class="header">
                <td class="tpname">Template name</td>
                <td class="owner">Owner</td>
<?php 	if ($public==0) { 	?>
		<td class="type">Type</td>
<?php	}			?>
                <td class="actions">Actions</td>
        </tr>
<?php
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }


?>
        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td class="tpname"><?php print htmlentities($row->name); ?></td>
                <td class="owner"><?php print htmlentities(id2user($row->owner)); ?></td>
<?php 	if ($public==0) { 	?>
		<td class="type"><?php if ($row->public==0) { print "private"; } else { print "public"; } ?></td>
<?php	}			?>
		<td class="actions"><?php
		if ($public==1) {
			print "[<a href=\"createdomain.php?template=$row->id\">Use</a>]";
		} else {
			print "[<a href=\"createdomain.php?template=$row->id\">Use</a>|<a href=\"tpedit.php?id=$row->id\">Edit</a>|<a href=\"tpdelete.php?id=$row->id\">Delete</a>]";
		}
		?></td>
        </tr>
<?php	}	?>
        </table>
</div>
<?
}


function showtemplate ($tpid, $count, $page, $adminlist, $search) {
	global $DB, $row_classes, $content_footer;

	$user = $_SESSION["userid"];

	if (!(is_owner_tp($tpid, $user)) && !(isadmin())) {
		redirect("error.php?error=perm");
	}

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }


	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

/* do the database queries */
		if (strlen($search) > 0) {		# doing a search
			$domainq = $DB->prepare("SELECT * FROM tprecords WHERE domain_id=? AND content LIKE ? OR name LIKE ? ORDER BY name LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domainq, array((int) $tpid, $searchstr, $searchstr, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT * FROM tprecords WHERE domain_id=? AND content LIKE ? OR name LIKE ? ORDER BY name");
			$dbreturnall = $DB->execute($domsall, array((int) $tpid, $searchstr, $searchstr));
		} else {		# no search
			$domainq = $DB->prepare("SELECT * FROM tprecords WHERE domain_id=? ORDER BY name LIMIT ? OFFSET ?");
			$dbreturn = $DB->execute($domainq, array((int) $tpid, (int) $count, (int) $offset));

			$domsall = $DB->prepare("SELECT * FROM tprecords WHERE domain_id=? ORDER BY name");
			$dbreturnall = $DB->execute($domsall, array((int) $tpid));
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
			<h1>Total templates in system: (<?php print $total; ?>)</h1>
<?php		} else {			?>
			<h1>Records for template: <?php print htmlentities(template_id2name($tpid)); ?>, Total: (<?php print $total; ?>)</h1>
<?php		}
	}					?>


	<div class="controls">
		<table class="controls">
			<tr class="controls">
				<td class="left">
					<form action="tproperties.php" method="get">
						<input type="hidden" name="id" value="<?php print htmlentities($tpid); ?>">
						<input type="submit" name="set" value="Properties" title="properties">
					</form>
				</td>
				<td class="right"><?php show_numberset($thisfile, $page, $search, $tpid); ?></td>
			</tr>
		</table>
        </div>

        <table class="list records">
        <tr class="header">
                <td class="name">Host name</td>
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
                <td class="name"><a href="editrecord.php?type=1&id=<?php print $row->id; ?>"><?php print htmlentities($row->name); ?></a></td>
		<td class="type"><?php print $row->type; ?></td>
		<td class="content"><?php print $row->content; ?></td>
		<td class="ttl"><?php print $row->ttl; ?></td>
		<td class="priority"><?php print $row->prio; ?></td>
		<td class="actions">[<a href="record-delete.php?type=1&id=<?php print $row->id; ?>">Delete</a> | <a href="editrecord.php?id=<?php print $row->id; ?>">Edit</a>]</td>
        </tr>
<?
	}
?>        </table>
</div>
<?

  }
}


?>
