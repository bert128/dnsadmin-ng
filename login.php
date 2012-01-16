<?php
        include("config.inc.php");
	include("util.inc.php");
	include("userprefs.inc.php");

        $username = $_POST["username"];
        $password = $_POST["password"];

        if ($username && $password) {

		$seluser = $DB->prepare("SELECT * FROM users WHERE username = ? AND active=1");

		$return = $DB->execute($seluser, array($username));

		if (DB::isError($return)) {
			print $DB->getMessage();
			header('Location: login.php?error=dberror');
		}

		if ($return->numRows() != 1) {
			$logmsg = "Attempted login for invalid user: ". $username ." from ". $_SERVER['REMOTE_ADDR'] ." at ". gmdate("D, d M Y H:i:s") ." ";
			writelog(0, 0, 2, $logmsg);
			header('Location: login.php?error=notfound');
		}

		while ($row = $return->fetchRow(DB_FETCHMODE_OBJECT)) {

		    if (DB::isError($row)) {
			header('Location: login.php?error=dberror');
		    }

//		$cryptpass = crypt($password, $row->password);
		$cryptpass = md5($password);

			if ($cryptpass == $row->password) {	# auth successful
        			session_start();
				$_SESSION['username'] = $username;
	                        $_SESSION['lastseen'] = time();
				$_SESSION['level'] = $row->level;
				$_SESSION['userid'] = $row->id;

/* need to pull this from another table
				$_SESSION['override'] = $row->override;
*/

				$_SESSION['backlink'] = array();
				$_SESSION['canadd'] = getflag($row->id, "add");


				load_userprefs($row->id);
				$logmsg = "User ". $username ." logged in from ". $_SERVER['REMOTE_ADDR'] ." at ". gmdate("D, d M Y H:i:s") ." ";
				writelog($row->id, $row->level, 1, $logmsg);
				header('Location: index.php');
			} else { # auth failed
				$logmsg = "Invalid password for user: ". $username ." from ". $_SERVER['REMOTE_ADDR'] ." at ". gmdate("D, d M Y H:i:s") ." ";
				writelog(0, 0, 3, $logmsg);


			}

		}
	}
if ($_GET["error"]) {
	if ($_GET["error"] == "dberror") {
		$error_msg = "Database Error";
	} else if ($_GET["error"] == "notfound") {
		$error_msg = "User not found";
	} else {
		$error_msg = "Unknown Error";
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<html>
<head>
        <title><?php print $CONFIG["AppTitle"]; ?>: Login</title>
        <link rel="stylesheet" type="text/css" href="css/default.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="css/login.css" media="screen"/>
</head>

<div id="cssnotice">
<h1>!! UNSUPPORTED BROWSER !!</h1><br>
<h2>
This notice only appears when using old/outdated browsers which lack proper support for CSS2.<br>
This application requires a modern browser to function correctly, and is unlikely to function correctly in older browsers.
</h2>
</div>

<body>
<script type="text/javascript">   
        function setFocus() {   
                document.getElementById('username').focus();
        }
window.onload = setFocus;
</script>
        

<table class="vcenter"><tr><td>
                
<form action="login.php" method="post">
<table class="login">
<tr>
        <td colspan="2" class="logo"><img src="images/ev6logo.png"></td>
</tr>
<tr>
        <td colspan="2" class="title"><?php print $CONFIG["AppTitle"]; ?> Login</td>
</tr>
<tr>
        <td class="label">Username</td>
        <td class="input">
                <input type="text" name="username" id="username" value="">
        </td>
</tr>
<tr>
        <td class="label">Password</td>
        <td class="input">
                <input type="password" name="password" value="">
        </td>
</tr>
<tr>
        <td colspan="2" class="error"><?php print htmlentities($error_msg); ?></td>
</tr>
<tr>
        <td colspan="2" class="controls">
                <input type="submit" value="Ok">
        </td>
</tr>
</table>
</form>

</td></tr></table>

</body>
</html>
