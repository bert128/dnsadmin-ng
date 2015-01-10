<?php
	include_once("logging.inc.php");

function showmappedrecords ($count, $page, $adminlist, $search, $user) {
	global $DB, $row_classes, $content_footer;

	if ($count==0) { $count = 100; }
	if ($page==0) { $page = 1; }

	$searchstr = "%".$search."%";
	$offset = $count * ($page - 1);

	set_items($count);

/* do the database queries */
	$mapped = $DB->prepare("select user, record_id, recmap.id AS recmap_id, records.name, records.id AS recid, records.type, records.ttl, records.prio, records.content from recmap LEFT JOIN records ON records.id=record_id WHERE user=?");
	$dbreturn = $DB->execute($mapped, array((int) $user));

	if (PEAR::isError($dbreturn)) {
		writelog($_SESSION['username'], $_SESSION['userid'], 5, $dbreturn->getMessage());
		header('Location: error.php?error=dberror');
	}

	$total = $dbreturn->numRows();
	$num_pages = ceil($dbreturn->numRows() / $count);

	if ($page > $num_pages) { $page = $num_pages; }

  	if (!$dbreturn->numRows() >= 1) {
		if (strlen($search) > 0) {
			echo "No mapped records matching your search query.";
		} else {
			echo "No mapped records available.";
			return;
		}
	}

?>
<div class="section">
<?php
	if (strlen($search) > 0) {		?>
			<h1>Mapped records - Search results: (<?php print $total; ?>)</h1>
<?php	} else {
		if ($adminlist==1) {		?>
			<h1>Total record maps: (<?php print $total; ?>)</h1>
<?php		} else {			?>
			<h1>Mapped records for user: <?php print htmlentities($_SESSION['username']); ?>, Total: (<?php print $total; ?>)</h1>
<?php		}
	}					?>


        <table class="display compact" id="table-recmap">
	<thead>
        <tr class="header">
                <td class="hostname">Hostname</td>
                <td class="type">Type</td>
                <td class="ttl">TTL</td>
                <td class="priority">Priority</td>
                <td class="content">Content</td>
                <td class="controls">Controls</td>
        </tr>
	</thead>
	<tbody>
<?php  #select user, record_id, recmap.id AS recmap_id, records.name, records.id AS recid, records.type, records.ttl, records.prio, records.content from recmap LEFT JOIN records ON records.id=record_id WHERE user=?
	while ($row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT)) {
                    if (DB::isError($row)) {
                        header('Location: error.php?error=dberror');
                    }
#if (($adminlist == 1) || (isowner($row->id))) { # only if we own the domain or are admin

?>
<form action="updmap.php" method="post">
<input type="hidden" name="id" value="<?php print htmlentities($row->recmap_id); ?>">

        <tr class="<?php print $row_classes[$count++ % 2]; ?>">
                <td class="hostname"><?php print htmlentities($row->name); ?></td>
		<td class="type"><?php print htmlentities($row->type); ?></td>
		<td class="ttl"><?php print htmlentities($row->ttl); ?></td>
		<td class="priority"><?php print htmlentities($row->prio); ?></td>
		<td class="content"><input type="text" name="value" class="contents" value="<?php print htmlentities($row->content); ?>"></td>
		<td class="controls"><input type="submit" name="update" value="Update"></td>
        </tr>
</form>
<?php
	}
?>
	</tbody>
        </table>
<script>
    $(document).ready(function() {
        $('#table-recmap').DataTable({
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
                    "orderable": true,
                    "searchable": false
                },
                {
                    "orderable": true,
                    "searchable": true
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


function get_recid_recmap($id)
{
	global $DB;

	$query = $DB->prepare("SELECT * FROM recmap WHERE id=?");
	$dbreturn = $DB->execute($query, array((int) $id));

        if ($dbreturn->numRows() == 1) {
                $row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);
		return $row->record_id;
        }
	error("Data", "Invalid record map id");
}

?>
