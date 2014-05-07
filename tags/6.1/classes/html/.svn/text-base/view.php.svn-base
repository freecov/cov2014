<?php
/**
 * Covide View object
 *
 * Window/Venster interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2005 Covide BV
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
	private $_limit              = array();
	private $_sort_mappings      = array();
	private $_sort_field         = array();
	private $_sort_param         = array();

	private $is_ie = 0;
	/* methods   */

  public function __construct() {
  	$this->_output = "";
  	if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"])) {
  		$this->is_ie = 1;
  	}
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
				$str = "?".implode("&", $uri);
				$output->insertImage($img, gettext("sorteer op deze kolom"), $str);

			} else {
				$output->insertImage($img, gettext("sorteer op deze kolom"),
					sprintf("javascript: document.getElementById('%s').value = '%s|%s'; document.getElementById('%s').submit();",
						$this->_sort_field[0], $this->_sort_mappings[$mapping], $order, $this->_sort_field[1]));
			}
			return $output->generate_output();
		}
	}

	public function addMapping($name, $mappings, $align="", $class="", $limit="", $no_mobile=0) {
		/* limit only applies to generate_output_vertical */
		if (!$GLOBALS["covide"]->mobile || !$no_mobile) {
			if (preg_match("/^%%/s",$name)) {
				$name = str_replace("%%","",$name);
				$this->_alternative_header[$name] = "%%".$name;
			}
			$this->_mappings[$name] = $mappings;

			if ($align) {
				$this->_align[$name] = $align;
			}
			if ($class) {
				$this->_class[$name] = $class;
			}
			if ($limit) {
				$this->_limit[$name] = $limit;
			}
		}
	}
	public function addsubMapping($mappings, $style_mapping) {
		$this->_submapping[] = $mappings;
		$this->_submapping_style[] = $style_mapping;
	}

	public function addData($data) {
		$this->_data = $data;
	}

	private function parseMapping($format, $record) {
		$buf="";
		if (!is_array($format)) {
			$format=array($format);
		}
		foreach ($format as $f) {
			if (preg_match("/^\%%/si", $f)) {
				/* complex mapping */
				$buf.= $this->parseComplexMapping( str_replace("%%","",$f) , &$record);
			} elseif (preg_match("/^\%/si", $f)) {
				/* field mapping */
				$col = preg_replace("/[^a-z0-9_]/si","",$f);
				$buf.= str_replace("%", "&#".ord("%").";", $record[$col]);
			} else {
				/* text mapping */
				$buf.= $f;
			}
		}
		$buf = preg_replace("/(\t)|(\r)|(\n)/s","", nl2br($buf) );
		return $buf;
	}

	private function parseComplexImage($mapping, $record) {
		$output  = new Layout_output();

		$src     = $this->parseMapping($mapping["src"], &$record);
		$alt     = $this->parseMapping($mapping["alt"], &$record);
		$link    = $this->parseMapping($mapping["link"], &$record);
		$confirm = $this->parseMapping($mapping["confirm"], &$record);
		$id      = $this->parseMapping($mapping["id"], &$record);
		$target  = $mapping["target"];

		$settings = array(
			"href"    => $link,
			"target"  => $target,
			"confirm" => $confirm
		);

		if ($mapping["type"]=="image") {
			$output->insertImage($src, $alt, $settings, 0, 0, $id);
		} else {
			$output->insertAction($src, $alt, $settings, 0);
		}
		return $output->generate_output();
	}

	private function parseComplexLink($mapping, $record) {
		$output = new Layout_output();

		$link = $this->parseMapping($mapping["link"], &$record);
		$text = $this->parseMapping($mapping["text"], &$record);
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

	private function parseComplexArray($mapping, $record) {
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

	private function parseComplexMapping($mapping_identifier, $record) {
		$buf = "";
		$mapping = $this->_complex[$mapping_identifier];
		if (!is_array($mapping)) return;

		foreach ($mapping as $k=>$m) {
			/* if no check condition or condition is true */
			if (!$m["check"] || ($this->parseMapping($m["check"], &$record))) {
				if (!$GLOBALS["covide"]->mobile || !$m["no_mobile"]) {
					if ($m["type"]=="array") {
						$buf.= $this->parseComplexArray($m, &$record);
					} elseif ($m["type"]=="image" || $m["type"]=="action") {
						$buf .= $this->parseComplexImage($m, &$record);
					} elseif ($m["type"]=="link") {
						$buf .= $this->parseComplexLink($m, &$record);
					} else {
						$buf .= $this->parseMapping($m["text"], &$record);
						if ($this->_sort_mappings[$m["alias"]]) {
							$buf.= $this->generateSortLink($m["alias"]);
						}
					}
				}
			}
		}
		return $buf;
	}
	public function defineComplexMapping($name, $mapping, $class="") {
		$this->_complex[$name] = $mapping;
		if ($class) {
			$this->_class[$name] = $class;
		}
	}

	public function generate_output() {
		$mappings = $this->_mappings;
		$submapping = $this->_submapping;
		$submapping_style = $this->_submapping_style;
		$data = $this->_data;

 		$table = new Layout_table(array("cellspacing"=>1, "class"=>"view_header"), 1);
		$table->addTableRow();
		foreach ($mappings as $name=>$map) {
			if ($this->_alternative_header[$name]) {
				$_name = $name;
				$name = $this->parseMapping($this->_alternative_header[$name], array(""));
				$table->insertTableHeader($name.$this->generateSortLink($_name), array("class"=>"list_header_center", "align"=>$this->_align[$_name]));
			} else {
				$table->insertTableHeader($name.$this->generateSortLink($name), array("class"=>"list_header_center", "align"=>$this->_align[$name]));
			}
		}
		$table->endTableRow();
		if (!is_array($data)) {
			$data = array();
		}

		/* extra hack for MSIE */
		if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"])) {
			$extra_onmouseover = "setBgColor(this,1);";
			$extra_onmouseout  = "setBgColor(this,0);";
		}

		foreach ($data as $r=>$d) {
			$_flag++;
			foreach ($submapping as $sskey=>$ssmap) {
				$table->addTableRow(array("class"=>"list_record_header"));
				$submapping_result = $this->parseMapping($submapping_style[$sskey], &$d);
				$submapping_data   = $this->parseMapping($ssmap, &$d);

				if ($submapping_data) {
					if ($submapping_result==1) {
						$table->addTableData(array(
							"colspan"=>count($mappings),
							"class"=>"list_data_highlighted"
							#"style"=>"border-top: 1px solid #666"
						));
						$table->addCode ( $submapping_data );
						$table->endTableData();
					} else {
						$table->addTableData(array(
							"colspan"=>count($mappings),
							"class"=>"list_data_submapping"
							#"style"=>"border-top: 1px solid #666"
						));
						$table->addCode ( $submapping_data );
						$table->endTableData();
					}
				}
				$table->endTableRow();
			}

			$table->addTableRow( array(
				"class"       => "list_record",
				"onmouseover" => $extra_onmouseover,
				"onmouseout"  => $extra_onmouseout
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

				/* if mapping is of type column or type complex then pre-lookup the style */
				$class = "list_data_clean";
				if (!is_array($m)) {
					if (preg_match("/^\%/s", $m)) {
						$class = " ".$this->_class[ preg_replace("/^\%{1,2}/s", "", $m) ];
					}
				}
				$class .= " valign_top";
				$class = trim($class);

				$table->addTableData(array("class"=>$class, "style"=>$sortstyle, "align"=>$this->_align[$name]));
				$prepare_data = $this->parseMapping($m, &$d);

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
		}
		if (!$_flag) {
			$table->addTableRow();
				$table->addTableData(array(
					"colspan"=>count($mappings),
					"class"=>"list_data",
				));
				$table->addCode( gettext("geen items gevonden") );
				$table->endTableData();
			$table->endTableRow();
		}

		$table->endTable();
		$this->_output = $table->generate_output();

		return $this->_output;
	}

	public function generate_output_vertical($fullwidth="") {
		$mappings = $this->_mappings;
		$data_array = $this->_data;
		if (!is_array($data_array))
			$data_array = array();

		reset($data_array);
		$data = current($data_array);

		if ($fullwidth) {
			$width = "100%";
		} else {
			$width = "";
		}
 		$table = new Layout_table( array("cellspacing"=>1, "width"=>$width) , 1);
		foreach ($mappings as $name=>$map) {
			$col = $name;
			$prepare_data = trim($this->parseMapping($mappings[$col], &$data ));
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
