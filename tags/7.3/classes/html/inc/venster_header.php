<?php
		$prc = (strstr($vensterBreedte, "%")) ? true : false;
		if (!$prc) {
			$settings = array(
				"width"=>$vensterBreedte
			);
		}
		$table = new Output_table($settings);
		$table->addTableRow();
		$table->addTableData();





		?>
		<table border="0" cellspacing="0" cellpadding="0" <? if ($prc) echo "width=\"".$vensterBreedte."\""; ?> >
		<? if ($vensterTitel!=""){ ?>
				<tr><td colspan="2" align="left">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr><td valign="bottom" align="left">
						<span class="onderdeel">
							<nobr>
							<span <? initRenderState($unique); ?>>
								<? if (strlen(trim($vensterTitel))) { echo gettext($vensterTitel); } ?>
							</span>
							</span>
							</nobr>
						</td>
						<td valign="top" align="left"><span class="titel">&nbsp;<? if (strlen(trim($vensterSubTitel))) { echo gettext($vensterSubTitel); } ?></span></td></tr>
					</table>
				</td></tr>
		<? } ?>
			<tr>
			<? if (count($menuItems) > 0){ ?>
				<td valign="top">
					<img src="<?=$parent?>themes/<?=$theme;?>/menu_top.gif"><br>
					<?
						for($i=0;$i<count($menuItems);$i+=2){
							knop($menuItems[$i], $menuItems[$i+1]);
						}
					?>
					<img src="<?=$parent?>themes/<?=$theme;?>/menu_onder.gif">
					<br><br>
					<?
						switch ($custom) {
							case "agenda":	venster_header_custom_agenda(); break;
							#case "email":		venster_header_custom_email(); break;
						}
					?>
				</td>
			<? } ?>
			<td valign="top" align="left">
			<table <? checkRenderState($unique); ?> class="dlg1" cellpadding="1" cellspacing="0" border="0" <? if ($prc) echo "width='".$vensterBreedte."'"; ?>>
				<tr><td>
					<table border="0" cellspacing="0" cellpadding="0" <? if ($prc) echo "width='".$vensterBreedte."'"; ?>>
					<tr>
					<td align="left">
						<table class="dlg2" cellpadding="<?= ($vensterPadding>-1)?$vensterPadding:"5";?>" cellspacing="0" border="0" <?= ($vensterBreedte>0)?"width='$vensterBreedte'":"";?> >
						<?

?>