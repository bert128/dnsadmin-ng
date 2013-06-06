<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('iputil.inc.php');
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
	include_once('domain.inc.php');

	$user = $_SESSION["userid"];

if (isset($_POST['modify'])) {
	if (isset($_POST['edit'])) { $edit = $_POST['edit']; } else { error("Invalid record to modify"); }
	if (isset($_POST['proc'])) { $proc = $_POST['proc']; } else { error("Invalid domain type"); }

	if (isset($_POST['name'])) { $name = $_POST['name']; } else { $name=""; }
	if (isset($_POST['type'])) { $type = $_POST['type']; } else { error("Invalid record type"); }
	if (isset($_POST['priority'])) { $priority = $_POST['priority']; } else { $priority=0; }
	if (isset($_POST['content'])) { $content = $_POST['content']; } else { $content=""; }
	if (isset($_POST['ttl'])) { $ttl = $_POST['ttl']; } else { $ttl=3600; }

/* calculate domainid based on doing a db lookup of $edit */
	$domainid = record2domain($proc, $edit);
	if (!validate_record($domainid, $proc, $name, $type, $priority, $content, $ttl)) {
		error("Record validation failed");
	}

	if ($proc==0) {			/* domain record */
		if (!(is_owner($domainid, $user)) && !(isadmin())) {
			error("Non admin user attempting to modify record");
		}
		modify_record($edit, $proc, $name, $type, $priority, $content, $ttl);
		redirect("editdomain.php?id=$domainid");
	} else if ($proc==1) {		/* template record */
		if (!(is_owner_tp($domainid, $user)) && !(isadmin())) {
			error("Non admin user attempting to modify template record");
		}
		modify_template_record($edit, $domainid, $proc, $name, $type, $priority, $content, $ttl);
		redirect("tpedit.php?id=$domainid");
	} else {
		error("Invalid record type");
	}

}


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

if (isset($_POST['addhost'])) {
	if (isset($_POST['domainid'])) { $domainid = $_POST['domainid']; } else { error("Invalid domain id"); }
	if (isset($_POST['proc'])) { $proc = $_POST['proc']; } else { error("Invalid domain type"); }

	if (isset($_POST['ipv4'])) { $ipv4 = $_POST['ipv4']; } else { error("Invalid IPv4 address"); }
	if (isset($_POST['ipv6'])) { $ipv6 = $_POST['ipv6']; } else { error("Invalid IPv6 address"); }

	if (isset($_POST['name'])) { $name = $_POST['name']; } else { $name=""; }

	if (isset($_POST['ttl'])) { $ttl = $_POST['ttl']; } else { $ttl=3600; }
	if (isset($_POST['reverse'])) { $reverse = $_POST['reverse']; } else { $reverse="f"; }

/* validate  */
	if (!validate_record($domainid, 0, $name, "A", 0, $ipv4, $ttl)) {
		error("Record validation failed");
	}
	if (!validate_record($domainid, 0, $name, "AAAA", 0, $ipv6, $ttl)) {
		error("Record validation failed");
	}

	if (!(is_owner($domainid, $user)) && !(isadmin())) {
		error("Non admin user attempting to add record");
	}
	add_record($domainid, 0, $name, "A", 0, $ipv4, $ttl);
	add_record($domainid, 0, $name, "AAAA", 0, $ipv6, $ttl);
/* add corresponding reverse dns? */

	redirect("editdomain.php?id=$domainid");
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
