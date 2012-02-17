<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('userprefs.inc.php');
	include_once('domaintable.inc.php');
	include_once('domain.inc.php');
	include_once('users.inc.php');
	include_once('forms.inc.php');
	include_once('pages.inc.php');
	include_once('search.inc.php');
	include_once('error.inc.php');
	include_once('validate.inc.php');
	include_once('templates.inc.php');
	include_once('addrecord.inc.php');

	$user = $_SESSION["userid"];

	if (!cancreate()) {
	        error("Insufficient privilege to create new domains");
	}

if (isset($_POST['add'])) {
	if (isset($_POST['name'])) { $name = $_POST['name']; } else { error("Invalid domain name");; }
	if (isset($_POST['type'])) { $type = $_POST['type']; } else { $type=0; }			/* default to type=native */
	if (isset($_POST['master'])) { $master = $_POST['master']; } else { $master=""; }
	if (isset($_POST['template'])) { $template = $_POST['template']; } else { $template=0; }	/* default template */

	if (isadmin()) {		/* only admin can set the owner field */
		if (isset($_POST['owner'])) { $owner = $_POST['owner']; } else { $owner=$_SESSION['userid']; }
	} else {
		$owner = $_SESSION['userid'];
	}

/* validate  */
	if (domain_exists($name)) {
		error("Domain already exists");
	}

	if ($type==1) {
		if (!validate_ip($master)) {
			error("Invalid Master IP address");
		}
		add_domain_slave($name, $master, $owner);
	} else if ($type==0) {
		add_domain_native($name, $template, $owner);
	}
}

redirect("index.php");

?>
