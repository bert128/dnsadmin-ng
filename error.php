<?      # $Id: index.php,v 1.2 2008/12/04 12:50:51 bertcvs Exp $
        include('config.inc.php');
        include('auth.inc.php');
	include('util.inc.php');

page_header("ERROR");


if ($_GET["error"]) {
        if ($_GET["error"] == "dberror") {
                $error_msg = "Database Error";
        } else if ($_GET["error"] == "notfound") {
                $error_msg = "User not found";
        } else if ($_GET["error"] == "unpriv") {
                $error_msg = "Privilege Violation";
        } else if ($_GET["error"] == "domainexists") {
                $error_msg = "Domain already exists";
        } else {
                $error_msg = "Unknown Error";
        }
}


?>

        <h1><?= htmlentities($error_msg); ?></h1>

<?

page_footer();

?>
