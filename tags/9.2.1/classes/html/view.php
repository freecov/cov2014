<?php
/**
 * Covide View module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

class Layout_view {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	private $_output             = "";
	private $_data               = array();
	private $_settings           = array();
	private $_mappings           = array();
	private $_complex            = array();
	private $_submapping         = array();
	private $_submapping_style   = array();
	private $_alternative_header = array();
	private $_align              = array();
	private $_class              = array();
	private $_allow_html         = array();
	private $_allow_html_fields  = array();
	private $_limit              = array();
	private $_sort_mappings      = array();
	private $_sort_field         = array();
	private $_sort_param         = array();
	private $_use_borders        = 0;
	private $_current_mapping    = "";
	private $_highlight          = array();
	private $_hideWhenEmpty      = 0;

	private $is_ie = 0;
	/* methods   */

  public function __construct($use_borders=0) {
  	$this->_use_borders = $use_borders;
  	$this->_output = "";
  	if (preg_match("/MSIE (5|6|7)/s", $_SERVER["HTTP_USER_AGENT"])) {
  		$this->is_ie = 1;
  	}
  }
	public function hideWhenEmpty($state) {
		$this->_hideWhenEmpty = $state;

	}
	public function addSettings($settings) {
		$this->_settings = $settings;
	}

	public function defineSortForm($fieldid, $formid) {
		$this->_sort_field = array($fieldid, $formid);
	}
	public function defineSortParam($param) {
		$this->_sort_param = $param;
	}
	public function defineHighLight($mapping, $class) {
		$this->_highlight = array(
			"mapping" => $mapping,
			"class"   => $class
		);
	}

	public function defineSort($mapping, $db_field) {
		$this->_sort_mappings[$mapping] = $db_field;
	}

	public function generateSortLink($mapping) {
		if ($this->_sort_mappings[$mapping]) {
			if ($this->_sort_param) {
				$current_sort = explode("|", $_REQUEST[$this->_sort_param]);
			} else {
				$current_sort = explode("|", $_REQUEST[$this->_sort_field[0]]);
			}

			if ($this->_sort_mappings[$mapping] == $current_sort[0]) {
				if ($current_sort[1] == "desc") {
					$img = "extraopthide.gif";
					$order = "asc";
				} else {
					$img = "extraopt.gif";
					$order = "desc";
				}
			} else {
				$img = "extraoptactive.gif";
				$order = "asc";
			}
			$output = new Layout_output();

			if ($this->_sort_param) {
				$uri = explode("&", $_SERVER["QUERY_STRING"]);
				foreach ($uri as $k=>$v) {
					$v = explode("=", $v);
					if ($v[0] == $this->_sort_param) {
						unset($uri[$k]);
					}
				}
				$uri[] = $this->_sort_param."=".sprintf("%s|%s", $this->_sort_mappings[$mapping], $order);
				$str = "?".implode("&amp;", $uri);
				$output->insertImage($img, gettext("sort this column"), $str);

			} else {
				$output->insertImage($img, gettext("sort this column"),
					sprintf("javascript: document.getElementById('%s').value = '%s|%s'; document.getElementById('%s').submit();",
						$this->_sort_field[0], $this->_sort_mappings[$mapping], $order, $this->_sort_field[1]));
			}
			return $output->generate_output();
		}
	}

	public function setHtmlField($name) {
		$this->_allow_html_fields[] = $name;
	}

	public function addMapping($name, $mappings, $settings="", $class="", $limit="", $no_mobile=0, $allow_html=0) {
		$align =& $settings;
		if (is_array($settings)) {
			$align =& $settings["align"];
			$class =& $settings["class"];
			$limit =& $settings["limit"];
			$allow_html =& $settings["allow_html"];
		}
		/* limit only applies to generate_output_vertical */
		if (!$GLOBALS["covide"]->mobile || !$no_mobile) {
			if (preg_match("/^%%/s",$name)) {
				$name = str_replace("%%","",$name);
				$this->_alternative_header[$name] = "%%".$name;
			}
			$this->_mappings[$name] = $mappings;

			if ($allow_html) $this->_allow_html[$name] = 1;
			if ($align)      $this->_align[$name] = $align;
			if ($class)      $this->_class[$name] = $class;
			if ($limit)      $this->_limit[$name] = $limit;
		}
	}
	public function addsubMapping($mappings, $style_mapping) {
		$this->_submapping[] = $mappings;
		$this->_submapping_style[] = $style_mapping;
	}

	public function addData($data) {
		$this->_data = $data;
	}

	private function parseMapping($format, &$record) {
		$buf="";
		if (!is_array($format))
			$format = array($format);

		foreach ($format as $f) {
			if (preg_match("/^\%%/si", $f)) {
				/* complex mapping */
				$buf.= $this->parseComplexMapping( str_replace("%%","",$f) , $record);
			} elseif (preg_match("/^\%/si", $f)) {
				/* database field mapping */
				$col = preg_replace("/[^a-z0-9_]/si","",$f);
				$t   = str_replace("%", "&#".ord("%").";", $record[$col]);
				if (!$this->_allow_html[$this->_current_mapping] && !in_array($col, $this->_allow_html_fields)) {
					$t = str_replace("<", "&lt;", $t);
					$t = str_replace(">", "&gt;", $t);
				}

				$buf.= 	$t;
			} else {
				/* text mapping */
				$buf.= $f;
			}
		}
		$buf = preg_replace("/(\t)|(\r)|(\n)/s","", nl2br($buf) );
		return $buf;
	}

	private function parseComplexImage($mapping, &$record) {
		$output  = new Layout_output();

		$src     = $this->parseMapping($mapping["src"], $record);
		$alt     = $this->parseMapping($mapping["alt"], $record);
		$link    = $this->parseMapping($mapping["link"], $record);
		$confirm = $this->parseMapping($mapping["confirm"], $record);
		$id      = $this->parseMapping($mapping["id"], $record);
		$target  = $mapping["target"];

		$settings = array(
			"href"    => $link,
			"target"  => $target,
			"confirm" => $confirm
		);
		if ($mapping["type"]=="image") {
			$output->insertImage($src, $alt, $settings, 0, 0, $id);
		} else {
			if ($mapping["fader"])
				$output->insertAction($src, $alt, $settings, "", 0, 1);
			else
				$output->insertAction($src, $alt, $settings, "");
		}
		return $output->generate_output();
	}

	private function parseComplexLink($mapping, &$record) {
		$output = new Layout_output();
		$link = $this->parseMapping($mapping["link"], $record);
		$text = $this->parseMapping($mapping["text"], $record);
		$target = $mapping["target"];

		$settings = array(
			"href"=>$link
		);
		if ($target) {
			$settings["target"] = $target;
		}

		$output->insertLink($text, $settings);
		return $output->generate_output();
	}


	private function parseComplexMultiLink($mapping, &$record) {

		$output = new Layout_output();
		$multiLinks = explode(",", $record[preg_replace("/^%/si", "", $mapping["link"][1])]);
		$multiNames = explode(",", $record[preg_replace("/^%/si", "", $mapping["text"])]);
		$tmprecord = $record;
		foreach($multiLinks as $k => $v) {
			$tmprecord[preg_replace("/^%/si", "", $mapping["link"][1])] = $v;
			$tmprecord[preg_replace("/^%/si", "", $mapping["text"])] = $multiNames[$k];
			$link = $this->parseMapping($mapping["link"], $tmprecord);
			$text = $this->parseMapping($mapping["text"], $tmprecord);
			$target = $mapping["target"];

			$settings = array(
				"href"=>$link
			);
			if ($target) {
				$settings["target"] = $target;
			}

			$output->insertLink($text, $settings);
		}
		return $output->generate_output();
	}


	private function parseComplexArray($mapping, &$record) {
		$output = new Layout_output();
		$datamap = $record[$mapping["array"]];
		if (!is_array($datamap)) return;
		foreach ($datamap as $k=>$v) {
			if (is_array($v)) {
				$output->addCode( $this->parseMapping( $mapping["mapping"], $v) );
				$output->addTag("br");
			}
		}
		return $output->generate_output();
	}

	private function parseComplexMapping($mapping_identifier, &$record) {
		$buf = "";
		$mapping = $this->_complex[$mapping_identifier];
		if (!is_array($mapping)) return;

		foreach ($mapping as $k=>$m) {
			/* if no check condition or condition is true */
			if (!$m["check"] || ($this->parseMapping($m["check"], $record))) {
				if (!$GLOBALS["covide"]->mobile || !$m["no_mobile"]) {
					if ($m["type"]=="array") {
						$buf.= $this->parseComplexArray($m, $record);
					} elseif ($m["type"]=="image" || $m["type"]=="action") {
						$buf .= $this->parseComplexImage($m, $record);
					} elseif ($m["type"]=="link") {
						$buf .= $this->parseComplexLink($m, $record);
					} elseif ($m["type"]=="multilink") {
						$buf .= $this->parseComplexMultiLink($m, $record);
					} else {
						$buf .= $this->parseMapping($m["text"], $record);
						if ($this->_sort_mappings[$m["alias"]]) {
							$buf.= $this->generateSortLink($m["alias"]);
						}
					}
				}
			}
		}
		return $buf;
	}
	public function defineComplexMapping($name, $mapping, $settings="") {
		$this->_complex[$name] = $mapping;
		if (is_array($settings)) {
			$class      =& $settings["class"];
			$allow_html =& $settings["allow_html"];
		}
		if ($class)
			$this->_class[$name] = $class;

		if ($allow_html)
			$this->_allow_html[$name] = 1;
	}

	public function generate_output($no_onmouseover=0) {
		$mappings = $this->_mappings;
		$submapping = $this->_submapping;
		$submapping_style = $this->_submapping_style;
		$data = $this->_data;

		if ($this->_hideWhenEmpty && count($data) == 0)
			return "";

 		if ($this->_use_borders) {
			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 3,
				"class"       => "view_header",
				"border"      => 0,
			), 1);
 		} else {
			$table = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 3,
				"class"       => "view_header",
			), 1);
 		}
		$table->addTag("thead");
		$table->addTableRow();
		foreach ($mappings as $name=>$map) {
			$th_style = array("class" => "list_header_center");
			if ($this->_align[$_name]) {
				$th_style["style"] = "text-align: ".$this->_align[$_name];
			}
			if ($this->_alternative_header[$name]) {
				$_name = $name;
				$th_style = array("text-align" => "left", "class" => "list_header_center");
				$name = $this->parseMapping($this->_alternative_header[$name], $th_style);
				$table->insertTableHeader($name.$this->generateSortLink($_name), $th_style);
			} else {
				$table->insertTableHeader($name.$this->generateSortLink($name), $th_style);
			}
		}
		$table->endTableRow();
		$table->endTag("thead");
		if (!is_array($data)) {
			$data = array();
		}

		foreach ($data as $r=>$d) {
			$_flag++;
			$_use_tbody = 0;

			$table->addTableBody(array(
				//"id"          => "vh_$_flag",
				"onmouseover" => "setBgColor(this, 1);",
				"onmouseout"  => "setBgColor(this, 0);"
			));
			foreach ($submapping as $sskey=>$ssmap) {

				$submapping_result = $this->parseMapping($submapping_style[$sskey], $d);
				$submapping_data   = $this->parseMapping($ssmap, $d);

				if ($submapping_data) {
					$table->addTableRow(array(
						"class" => "list_record_header",
					));
					if ($submapping_result==1) {
						$table->addTableData(array(
							"colspan" => count($mappings),
							"class"   => "list_data_highlighted"
						));
						$table->addCode ( $submapping_data );
						$table->endTableData();
					} else {
						$table->addTableData(array(
							"colspan"     => count($mappings),
							"class"       => "list_data_submapping"
						));
						$table->addCode ( $submapping_data );
						$table->endTableData();
					}
					$table->endTableRow();
				}
			}

			if ($this->_highlight["mapping"])
				$hl = $this->parseMapping($this->_highlight["mapping"], $d);
			else
				$hl = "";

			$table->addTableRow( array(
				"style" => "height: 18px;",
				"class" => sprintf("list_record%s", ($hl) ? " ".$this->_highlight["class"]:"")
			));

			if ($this->_sort_param) {
				$sort_fld = $_REQUEST[$this->_sort_param];
			} else {
				$sort_fld = explode("|", $this->_sort_field);
				$sort_fld = $_REQUEST[$sort_fld[0]];
			}
			$sort_fld = explode("|", $sort_fld);
			$sort_fld = $sort_fld[0];

			foreach ($mappings as $name=>$m) {

				/* set current mapping */
				$this->_current_mapping = $name;

				/* if mapping is of type column or type complex then pre-lookup the style */
				$class = "list_data_clean";
				if (!is_array($m)) {
					if (preg_match("/^\%/s", $m)) {
						/* TODO: Find out why this was: $class = " ".$this->_class[ preg_replace("/^\%{1,2}/s", "", $m) ]; */
						$class .= " ".$this->_class[$name];
					}
				}
				$class .= " valign_top";
				$class = trim($class);

				$table->addTableData(array("class"=>$class, "style"=>$sortstyle, "align"=>$this->_align[$name]));
				$prepare_data = $this->parseMapping($m, $d);

				/* limit view */
				if ($this->_limit[$name]) {
					$count = substr_count(preg_replace("/<br[^>]*?>/si", "<br>", $prepare_data), "<br>");
					if ($count >= 6) {
						$limit_height = "height: 140px; overflow:auto;";
					} else {
						$limit_height = "";
					}

					$table->addTag("div", array(
						"class"  => "limit_height",
						"style" => $limit_height
					));
						$table->addCode($prepare_data);
					$table->endTag("div");

				} else {
					$table->addCode($prepare_data);
				}
				$table->endTableData();
			}
			if ($GLOBALS["covide"]->mobile) {
				$table->addTableData();
					$table->addTag("hr");
				$table->endTableData();
			}
			$table->endTableRow();
			$table->endTableBody();
		}
		if (!$_flag) {
			$table->addTableRow();
				$table->addTableData(array(
					"colspan"=>count($mappings),
					"class"=>"list_data",
				));
				$table->addCode( gettext("no items found") );
				$table->endTableData();
			$table->endTableRow();
		}

		$table->endTable();
		$this->_output = $table->generate_output();

		return $this->_output;
	}

	public function generate_output_vertical($fullwidth="") {
		//all views in fullwidth ?
		$fullwidth = 1;
		$mappings = $this->_mappings;
		$data_array = $this->_data;
		if (!is_array($data_array))
			$data_array = array();

		reset($data_array);
		$data = current($data_array);

		if ($this->_hideWhenEmpty && count($data) == 0)
			return "";

		if ($fullwidth) {
			$table = new Layout_table( array("cellspacing"=>1, "width"=>"100%") , 1);
		} else {
			$table = new Layout_table( array("cellspacing"=>1) , 1);
		}
		foreach ($mappings as $name=>$map) {

			/* set current mapping */
			$this->_current_mapping = $name;

			$col = $name;
			$prepare_data = trim($this->parseMapping($mappings[$col], $data));
			if ($prepare_data) {
				$table->addTableRow( array("class"=>$this->_class[$col]) );

				if ($this->_alternative_header[$name]) {
					$name = $this->parseMapping($this->_alternative_header[$name], array(""));
					$table->insertTableData($name, array("class"=>"list_header", "align"=>$this->_align[$col]));
				} else {
					$table->insertTableData($name, array("class"=>"list_header", "align"=>$this->_align[$name]));
				}
				$table->addTableData( array("class"=>"list_data") );
				if ($this->_limit[$name]) {

					$count = substr_count(preg_replace("/<br[^>]*?>/si", "<br>", $prepare_data), "<br>");
					if ($count > 6) {
						$limit_height = "height: 140px; overflow:auto;";
					} else {
						$limit_height = "";
					}

					$table->addTag("div", array(
						"class"  => "limit_height",
						"style" => $limit_height
					));
						$table->addCode($prepare_data);
					$table->endTag("div");

				} else {
					$table->addCode($prepare_data);
				}
				$table->endTableData();
				$table->endTableRow();
			}
		}
		$table->endTable();
		$this->_output = $table->generate_output();

		return $this->_output;
	}

}
?>
