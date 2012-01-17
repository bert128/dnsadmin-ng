<!-- END CONTENT -->

	</td>
</tr>

<tr>	<td id="contentfooter">
<?php	if (count($content_footer) > 0) { ?>
	<table id="content-footer">
	<tr>
		<td class="left"><?php if (isset($content_footer["left"])) { print $content_footer["left"]; } ?></td>
		<td class="middle"><?php if (isset($content_footer["middle"])) { print $content_footer["middle"]; } ?></td>
		<td class="right"><?php if (isset($content_footer["right"])) { print $content_footer["right"]; } ?></td>
	</tr>
	</table>
<?php	}	?>
	</td>
</tr>

<tr>	
	<td id="pagefooter" colspan="2">
		<table id="page-footer">
		<tr>
			<td class="left"><?php print htmlentities($page_footer["left"]); ?></td>
			<td class="right"><?php print $page_footer["right"]; ?></td>
		</tr>
	</table>
	</td>
</tr>
	

</table>

<?php
#	foreach($_SESSION['backlink'] as $backlink) { echo "<p>",htmlentities($backlink),"</p>\n"; }
?>
</body>
</html>
