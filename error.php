<?php
        include_once('config.inc.php');
        include_once('auth.inc.php');
	include_once('util.inc.php');
	include_once('error.inc.php');

page_header("ERROR");


?>

        <h1><?php print htmlentities($error_msg); ?></h1>

<?php

page_footer();

?>
