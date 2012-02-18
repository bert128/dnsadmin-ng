<?php

/* code to handle paging
 */

if (isset($_GET['items'])) {
	if (!is_numeric($_GET['items'])) { error("Invalid items per page value"); }
	if ($_GET['items'] <= 0) { error("Invalid items per page value"); }
	$perpage = $_GET['items'];
} else {
	$perpage = $_SESSION['items'];
};

if (isset($_GET['page'])) { $page = $_GET['page']; } else { $page = 0; };


if (isset($_GET['itemsx'])) {
        if (is_numeric($_GET['itemsx'])) {
                $perpage = $_GET['itemsx'];
        }
}

?>
