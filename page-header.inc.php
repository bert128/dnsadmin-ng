<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
	<title><?php print $CONFIG["AppTitle"]; ?>: <?php print $title; ?></title>
	<link rel="stylesheet" type="text/css" href="css/default.css" media="screen" />
</head>
<body>

<div id="cssnotice">
<h1>!! UNSUPPORTED BROWSER !!</h1><br>
<h2>
This notice only appears when using old/outdated browsers which lack proper support for CSS2.<br>
This application requires a modern browser to function correctly, and is unlikely to function correctly in older browsers.
</h2>
</div>

<table id="main">
<tr>
	<td id="header" colspan="2"><?php print $title; ?></td>
</tr>
<tr>
	<td id="navigation" rowspan="2">
	<div class="box">
		<h1>Main Menu</h1>
		<ul class="MENU">
			<li><a href="index.php" accesskey="H">Home</a></li>
			<li><a href="domains.php" accesskey="H">My Domains</a></li>
<?php
if ($_SESSION['canadd']) { ?>
			<li><a href="createdomain.php" accesskey="A">Add Domain</a></li>
<?php } ?>
		</ul>
	</div>

	<div class="box">
                <h1>User Controls</h1>
                <ul>
			<li><a href="changepassword.php">Change Password</a></li>
        		<li><a href="logout.php">Logout</a></li>
		</ul>
	</div>

<?php	if ($_SESSION['level'] >= 10) { ?>
	<div class="box">
		<h1>Administration</h1>
		<ul>
			<li><a href="useradmin.php">User Admin</a></li>
			<li><a href="domainadmin.php">Domain Admin</a></li>
		</ul>
	</div>
<?php	}	?>

<?php	print $page_navigation;	?>

	</td>

	<td id="content">

<!-- BEGIN CONTENT -->
