<?php require('emoe.inc.php'); ?>
<?php html_header(); ?>
<?php pageNav(); ?>
<?php
	switch ($pagina){
		case "": tekstenOverzicht(); break;
		case "bewerk": tekstenInvoeren(); break;
		case "bewerk_db": tekstenInvoerenDb(); break;
	}

	//------------------------------------------------------------------------------------------
	function tekstenOverzicht(){
		global $menu; ?>

			<?php venster_header("teksten", "", $menu, 0, -1); ?>
				<?php
					$sqlQuery = "SELECT * FROM finance_teksten where ltrim(rtrim(description)) IN (
						'betaling', 'betaling binnen', 'email', 'laatste factuur nr'
					) ORDER BY description ;";
					$result = sql_query ($sqlQuery);
					while ($row = sql_fetch_array($result)){ ?>
				<tr>
					<td width="90%"><span class="d">&nbsp;<?php echo $row["description"]; ?>&nbsp;</span></td>
					<td width="26" align="center"><span class="d"><a href="Javascript:setWaarde('pagina','bewerk');setWaarde('id','<?php echo $row["id"]; ?>');verzend();"><img src="../img/knop_bewerk.gif" border="0"></a></span></td>
				</tr>
				<?php } ?>
			<?php venster_footer(); ?>

<?php }

	//------------------------------------------------------------------------------------------
	function tekstenInvoeren(){
			global $pagina, $id, $menu;

			if ($pagina == "bewerk"){
				$titel = "bewerk tekst";
				$sqlQuery = sprintf("SELECT * FROM finance_teksten WHERE id = %d", $id);
				$result = sql_query ($sqlQuery);
				$row = sql_fetch_array($result);
					$html = $row["html"];
					$type = $row["type"];
			} ?>

			<form name="eenform" method="post" action="?">

			<?php venster_header("teksten", "bewerk", $menu, 0, -1); ?>

						<?php if ($type == 0){ ?>
								<tr><td background="img_td_bg.gif">
								<?php
									$output = new Layout_output();
									$output->addHiddenField("html", "");
									$output->addHiddenField("inhoud", "");
									$output->start_javascript();
										$output->addCode("
											function copyHtml() {
												sync_editor_contents();
												document.getElementById('html').value = document.getElementById('contents').value;
												document.getElementById('inhoud').value = document.getElementById('contents').value;
											}
										");
									$output->end_javascript();
									$editor = new Layout_editor();

									if (!preg_match("/^<p[^>]*?>/si", $html))
										$html = sprintf("<p>%s&nbsp;</p>", $html);

									$output->addTextArea("contents", $html, array(
										"style" => "width: 700px; height: 400px;"
									));
									$output->addCode( $editor->generate_editor(2, $html) );
									echo $output->generate_output();
								?>

								</td></tr>
						<?php } ?>
						<?php if ($type == 1){ ?>
								<tr><td background="img_td_bg.gif"><input type="input" class="inputtext" value="<?php echo $html ?>" name="html" size="20"></td></tr>
						<?php } ?>
						<tr><td align="right" colspan="2"><a href="Javascript:<?php if ($type==0){ ?>copyHtml();<?php } ?>setWaarde('pagina', '<?php echo $pagina; ?>_db');setWaarde('pagina', '<?php echo $pagina; ?>_db');verzend();"><img src="../img/knop_ok.gif" border="0"></a></td></tr>

			<?php venster_footer(); ?>

			</form>
<?php }

	//------------------------------------------------------------------------------------------
	function tekstenInvoerenDb(){
		global $pagina, $id;
		global $html;

		$sqlQuery = sprintf("UPDATE finance_teksten SET html = '%s' WHERE id = %d", $html, $id);
		sql_query ($sqlQuery);
	?><script language="Javascript">setWaarde('pagina', '');verzend();</script><?php
	}
?>

<?php html_footer(); ?>
