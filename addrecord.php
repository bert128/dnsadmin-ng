<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('users.inc.php');
	include_once('forms.inc.php');
	include_once('pages.inc.php');
	include_once('search.inc.php');
	include_once('error.inc.php');
	include_once('validate.inc.php');
	include_once('templates.inc.php');
	include_once('addrecord.inc.php');

	$user = $_SESSION["userid"];

if (isset($_POST['add'])) {
	if (isset($_POST['domainid'])) { $domainid = $_POST['domainid']; } else { error("Invalid domain id"); }
	if (isset($_POST['proc'])) { $proc = $_POST['proc']; } else { error("Invalid domain type"); }

	if (isset($_POST['name'])) { $name = $_POST['name']; } else { $name=""; }
	if (isset($_POST['type'])) { $type = $_POST['type']; } else { error("Invalid record type"); }
	if (isset($_POST['priority'])) { $priority = $_POST['priority']; } else { $priority=0; }
	if (isset($_POST['content'])) { $content = $_POST['content']; } else { $content=""; }
	if (isset($_POST['ttl'])) { $ttl = $_POST['ttl']; } else { $ttl=3600; }

/* validate  */
	if (!validate_record($domainid, $proc, $name, $type, $priority, $content, $ttl)) {
		error("Record validation failed");
	}

	if ($proc==0) {			/* domain record */
		if (!(is_owner($domainid, $user)) && !(isadmin())) {
			error("Non admin user attempting to add record");
		}
		add_record($domainid, $proc, $name, $type, $priority, $content, $ttl);
		redirect("editdomain.php?id=$domainid");
	} else if ($proc==1) {		/* template record */
		if (!(is_owner_tp($domainid, $user)) && !(isadmin())) {
			error("Non admin user attempting to add record");
		}
		if (!validate_record($domainid, $proc, $name, $type, $priority, $content, $ttl)) {
			error("Record validation failed");
		}
		add_template_record($domainid, $proc, $name, $type, $priority, $content, $ttl);
		redirect("tpedit.php?id=$domainid");
	} else {
		error("Invalid record type");
	}
}

if (isset($_GET['id'])) {
	$userid = $_GET['id'];
	$username = id2user($userid);
	page_header("Edit User $username");
} else {
	$userid = 0;
	page_header("Add new user");
}

userform($userid);

if (isset($_GET['id'])) {
	showdomains($perpage, $page, 0, $search, $userid);
}

page_footer();

?>
