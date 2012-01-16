<?php

       $content_footer = array();
       $page_footer = array();

       $row_classes = array("even", "odd");

       $page_footer["left"] = "Currently logged in as ${_SESSION['username']} with privilege ${_SESSION['level']}";

       if (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') == true) {
                $page_footer["right"] = "Stop using Internet Explorer you idiot!";
        } else {
                if (count($_SESSION['backlink']) > 0)
                        $page_footer["right"] = "[ <a href=\"back.php\">Back</a> ]";
        }

	function page_header ($title, $sub_title = '&nbsp;') {
                global $onload, $page_navigation, $CONFIG;

                if ($_SERVER['REQUEST_URI'] !=
                    $_SESSION['backlink'][count($_SESSION['backlink'])-1]) {
                        # FIXME: Ensure referrer points to us!
                        array_push($_SESSION['backlink'],
                            $_SERVER['REQUEST_URI']);
                        if (count($_SESSION['backlink']) > 20)
                                array_shift($_SESSION['backlink']);
                }
                include("page-header.inc.php");
        }

       function page_footer () {
                global $content_footer, $page_footer;
                include("page-footer.inc.php");
        }

        function back($count = 2) {
                for ($i = 0; $i < ($count-1); $i++)
                        array_pop($_SESSION['backlink']);
                if (count($_SESSION['backlink']) < 1)
                        redirect("index.php");
                else
                        redirect(array_pop($_SESSION['backlink']));
        }

        function back_not($page) {
                while ($url = array_pop($_SESSION['backlink'])) {
                        if (strpos($url, $page) == false)
                                redirect($url);
                }
                redirect("index.php");
        }

       function redirect ($location) {
                header("Location: $location");
                exit;
        }


	function writelog ($user, $userlevel, $type, $data) {
		global $DBAUDIT;
                $squer = $DBAUDIT->prepare("INSERT INTO audit (user, userlevel, time, type, data) VALUES (?, ?, NULL, ?, ?);");

                $return = $DBAUDIT->execute($squer, array((int) $user, (int) $userlevel, (int) $type, $data));

                if (DB::isError($return)) {
                        print $DBAUDIT->getMessage();
                        header('Location: login.php?errpr=dberror');
                }


	}

# does a domain already exist in the system
	function domainexists ($domain) {
		return FALSE;
	}

?>
