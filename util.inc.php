<?php

       $content_footer = array();
       $page_footer = array();

       $row_classes = array("even", "odd");

       $page_footer["left"] = "Currently logged in as ${_SESSION['username']}";

       if (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') == true) {
                $page_footer["right"] = "Stop using Internet Explorer you idiot!";
        } else {
                if (count($_SESSION['backlink']) > 0)
                        $page_footer["right"] = "[ <a href=\"back.php\">Back</a> ]";
        }

	function page_header ($title, $sub_title = '&nbsp;') {
                global $onload, $page_navigation, $CONFIG;

/*	disabled
                if ($_SERVER['REQUEST_URI'] !=
                    $_SESSION['backlink'][count($_SESSION['backlink'])-1]) {
                        # FIXME: Ensure referrer points to us!
                        array_push($_SESSION['backlink'],
                            $_SERVER['REQUEST_URI']);
                        if (count($_SESSION['backlink']) > 20)
                                array_shift($_SESSION['backlink']);
                }
*/
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


# does a domain already exist in the system
	function domainexists ($domain) {
		return FALSE;
	}

function domain_id2name($domainid) {
        global $DB;

        $query = $DB->prepare("SELECT name FROM domains WHERE id=?");
        $dbreturn = $DB->execute($query, array((int) $domainid));


        if ($dbreturn->numRows() != 1) {
                return FALSE;
        }
	$row = $dbreturn->fetchRow(DB_FETCHMODE_OBJECT);

	return $row->name;
}

function show_numberset($thisfile, $page, $search, $id)
{
?>
<form action="<?php print htmlentities($thisfile); ?>" method="get">
Show per page:
<select name="items" size="1">
<option value="10">10</option>
<option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
<?php
if (!is_numeric($page)) { $page = 0; }
if (!is_numeric($id)) { $id = 0; }
if (isset($page)) { print "<input type=\"hidden\" name=\"page\" value=\"".$page."\">\n"; }
if (isset($id)) { print "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"; }
if (isset($search)) { $searchx = htmlentities($search); print "<input type=\"hidden\" name=\"search\" value=\"$searchx\">\n"; }
?>
<input type="text" name="itemsx">

<input type="submit" name="set" value="set" title="Set per page display">

</form>
<?php
}
?>
