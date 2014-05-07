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
		<table border="0" cellspacing="0" cellpadding="0" <?php if ($prc) echo "width=\"".$vensterBreedte."\""; ?> >
		<?php if ($vensterTitel!=""){ ?>
				<tr><td colspan="2" align="left">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr><td valign="bottom" align="left">
						<span class="onderdeel">
							<nobr>
							<span <?php initRenderState($unique); ?>>
								<?php if (strlen(trim($vensterTitel))) { echo gettext($vensterTitel); } ?>
							</span>
							</span>
							</nobr>
						</td>
						<td valign="top" align="left"><span class="titel">&nbsp;<?php if (strlen(trim($vensterSubTitel))) { echo gettext($vensterSubTitel); } ?></span></td></tr>
					</table>
				</td></tr>
		<?php } ?>
			<tr>
			<?php if (count($menuItems) > 0){ ?>
				<td valign="top">
					<img src="<?php echo $parent ?>themes/<?php echo $theme; ?>/menu_top.gif"><br>
					<?php
						for($i=0;$i<count($menuItems);$i+=2){
							knop($menuItems[$i], $menuItems[$i+1]);
						}
					?>
					<img src="<?php echo $parent ?>themes/<?php echo $theme; ?>/menu_onder.gif">
					<br><br>
					<?php
						switch ($custom) {
							case "agenda":	venster_header_custom_agenda(); break;
							#case "email":		venster_header_custom_email(); break;
						}
					?>
				</td>
			<?php } ?>
			<td valign="top" align="left">
			<table <?php checkRenderState($unique); ?> class="dlg1" cellpadding="1" cellspacing="0" border="0" <?php if ($prc) echo "width='".$vensterBreedte."'"; ?>>
				<tr><td>
					<table border="0" cellspacing="0" cellpadding="0" <?php if ($prc) echo "width='".$vensterBreedte."'"; ?>>
					<tr>
					<td align="left">
						<table class="dlg2" cellpadding="<?php echo ($vensterPadding>-1)?$vensterPadding:"5"; ?>" cellspacing="0" border="0" <?php echo ($vensterBreedte>0)?"width='$vensterBreedte'":""; ?> >
						<?php

?>