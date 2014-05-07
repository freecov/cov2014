<?php
	function html_header() {
		$output = &$GLOBALS["finance"]->output;
		$output = new Layout_output();
		$output->layout_page("Finance", $_REQUEST["verbergIface"]);
		$output->addTag("table");
		$output->addTag("tr");
		$output->addTag("td");
		echo $output->generate_output();
	}
	function html_footer() {
		$output = &$GLOBALS["finance"]->output;
		$output->endTag("td");
		$output->endTag("tr");
		$output->endTag("table");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	function venster_header($title, $subtitle, $menu, $width="", $padding="", $custom="") {
		$venster = new Layout_venster(array(
			"title" => $title,
			"subtitle" => $subtitle
		));
		if (is_array($menu)) {
			for ($i=0; $i < count($menu); $i+=2) {
				$venster->addMenuItem($menu[$i], $menu[$i+1]);
				$is_menu = 1;
			}
			if ($is_menu)
				$venster->generateMenuItems();
		}
		$venster->addVensterData();
		$table = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		$venster->addCode($table->generate_output());

		echo $venster->generate_output();
	}
	function venster_footer() {
		$output =& $GLOBALS["finance"]->output;
		$output->endTag("table");
		$output->endTag("td");
		$output->addTag("td", array("class"=>"venster_right") );
			$output->insertTag("div", "", array(
				"class" => "venster_right_spacer"
			));
			$output->addSpace();
		$output->endTag("td");
		$output->endTag("tr");
		$output->endTag("table");
		$output->addComment("end venster object");
		echo $output->generate_output();
	}
	function tabel_header($width, $useWindowClass=0) {
		$table = new Layout_table(array(
			"width" => $width,
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		echo $table->generate_output();
	}
	function tabel_footer() {
		echo "</table>";
	}
	function td($state=0) {
		if ($state)
			return " class=\"list_data\" ";
		else
			return " class=\"list_header\" style=\"padding-right: 4px;\" ";
	}
	function toonInfoSpan($text, $short, $disableCursor=0) {
		$output = new Layout_output();
		$output->insertTag("a", $short, array(
			"href" => sprintf("javascript: infoLayer('%s');",
				str_replace("'", "\\'", $text))
		));
		echo $output->generate_output();
	}
	function insertAction($action, $alt, $uri) {
		$output =& $GLOBALS["finance"]->output;
		$output->insertAction($action, $alt, $uri);
		echo $output->generate_output();
	}
?>