<?php

function tickbox ($name, $srcvar, $title) {
?>
        <tr>
                <td class="entrylabel"><?php print htmlentities($title); ?></td>
                <td class="none">
                        <?php  if ($srcvar == 1) { ?>
                <input type="checkbox" name="<?php print htmlentities($name); ?>" value="1" CHECKED>
                        <?php  } else { ?>
                <input type="checkbox" name="<?php print htmlentities($name); ?>" value="1">
                        <?php  } ?>
                </td>
        </tr>
<?php
}

// This is a resizeable input box for lots of text
// Arguments:
//  $descr - description (shown in the line before the input box as per the default stylesheet
//  $name - the name of the input box, used for javascript
//  $default - the default contents of the input box
//  $edit (global) - wether the user should be able to edit the contents of this box
function inputbox ($descr, $name, $default)
{
?>
        <tr>
                <td class="entrylabel"></td>
                <td class="input" colspan="1">
                </td>
        </tr>
        <tr>
                <td class="entrylabel">
                        <?php print htmlentities($descr); ?><br>
                </td>
                <td class="input" colspan="1">
                        <textarea name="<?php print htmlentities($name); ?>" id="<?php print htmlentities($name); ?>" rows="6"><?php print htmlentities($default); ?></textarea>
                </td>

        </tr>
<?php
}

// This is a single line input box
// Arguments:
//  $descr - description (shown in the line before the input box as per the default stylesheet
//  $name - the name of the input box, used for javascript
//  $default - the default contents of the input box
//  $edit (global) - wether the user should be able to edit the contents of this box

function inputline ($descr, $name, $default)
{
global $edit;
?>
        <tr>
                <td class="entrylabel"><?php print htmlentities($descr); ?></td>
                <td class="input" colspan="1">
                        <input type="text" name="<?php print htmlentities($name); ?>" value="<?php print htmlentities($default); ?>">
                </td>
        </tr>
<?php
}

/* form allowing creation of a template */
function tp_addform() {
?>

<div class="section">
<h1>Create Template</h1>
<table class="addrecord">
<form action="addtp.php" method="post">
<?php
	inputline("Template Name", "name", "");
	tickbox("public", "", "Public");

	if (isadmin()) {	/* show list of users here */
		select_userid();
	}
?>
        <tr>
                <td class="controls"><input type="submit" name="add" value="add" title="Submit"></td>
        </tr>
</form>
</table>
</div>
<?php
}

/* form allowing creation of a domain */
function domain_addform($deftemplate) {
?>
<script type="text/javascript" src="js/adddomain.js"></script>

<div class="section">
<h1>Create Domain</h1>
<table class="addrecord">
<form action="createdom.php" method="post">
<?php
	inputline("Domain Name", "name", "");

?>
	<tr class="type">
		<td class="type">Domain Type</td>
		<td class="type">
			<select name="type" size="1" id="domaintype">
				<option value="0" SELECTED>Native</option>
				<option value="1">Slave</option>
			</select>
		</td>
	</tr>
	<tr class="hidden" id="masterip">
		<td class="master">Master Server</td>
		<td class="master"><input type="text" name="master" value=""></td>
	</tr>
	<tr class="template" id="template">
		<td class="template">Domain Template</td>
		<td class="template">
			<select name="template" size="1" id="domaintemplate">
<?php	select_templates($deftemplate);	?>
			</select>
		</td>
	</tr>
<?php
        if (isadmin()) {        /* show list of users here */
                select_userid();
        }
?>

        <tr>
                <td class="controls"><input type="submit" name="add" value="add" title="Submit"></td>
        </tr>
</form>
</table>
</div>
<?php
}

function record_editform($type, $record) {
	global $DB;

}

function passwdform($user) {
        global $DB;

?>
<div class="section">
<h1>Change Password</h1>
<form action="changepass.php" method="post">
        <table class="form">

<?php           /* form elements here */
	inputline("Old Password", "oldpass", "");
	inputline("New Password", "newpass1", "");
	inputline("Confirm New Password", "newpass2", "");

?>
        <tr><td class="controls" colspan="2">
                <input type="submit" name="change" value="Change" title="Change Password">
                <input type="submit" name="cancel" value="Cancel" title="Abandon changes">
        </tr>
</table>
</form>
</div>
<?php
}

?>
