<?php

function tickbox ($name, $srcvar, $title) {
?>
        <tr> 
                <td class="entrylabel"><?php print htmlentities($title); ?></td>
                <td class="none">
                        <?php  if ($srcvar == 't') { ?>
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

?>
