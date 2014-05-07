<?php
/**
 * Covide Paging module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

class Layout_paging {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	private $_output = "";

	private $_urlmapping;
	private $_start;
	private $_count;
	private $_fuzzy;

	private $_pages_before_after = 2;
	private $_pagesize;
	private $_skip_selectbox = 0;
	private $_use_text;
	private $_hideWhenEmpty;

	public $_pagesizearray;

	/* methods   */
  public function __construct() {
  	$this->_output = "";
  	$this->_pagesize = $_SESSION["pagesize"];

	require('conf/offices.php');
	if ($html["custom_pagesize"]) {
		$this->_pagesizearray = $html["custom_pagesize"];
	} else {
		$this->_pagesizearray = array(
			5 => 5,
			10 => 10,
			20 => 20,
			50 => 50,
			100 => 100,
			200 => 200,
			500 => 500,
			1000 => 1000
		);
	}
  }
  public function hideWhenEmpty($state) {
  	$this->_hideWhenEmpty = $state;
  }

	public function setOptions($start, $count, $urlmapping, $custom_pagesize=0, $use_text=0, $fuzzy=0) {
		$this->_use_text   = $use_text;
		$this->_urlmapping = $urlmapping;
		$this->_start      = $start;
		$this->_count      = $count;
		$this->_fuzzy      = $fuzzy;

		if ($custom_pagesize) {
			$this->_pagesize = $custom_pagesize;
			$this->_skip_selectbox = 1;
		}
	}

	private function _generate_link($position, $table) {
		$name = ceil(($position / $this->_pagesize) + 1);
		$url  = str_replace("%%", $position, $this->_urlmapping);
		$settings = array(
			"href"=> str_replace("&amp;", "&", $url)
		);
		if ($this->_start == $position)
			$settings["class"] = "currentpage";

		if ($this->_use_text && $settings["class"] == "currentpage") {
			$table->addTag("b");
			$table->addTag("u");
				$table->insertLink($name, $settings);
			$table->endTag("u");
			$table->endTag("b");
		} else {
			$table->insertLink($name, $settings);
		}
		$table->addSpace();
	}

	public function generate_output() {

		if ($this->_hideWhenEmpty && $count == 0)
			return "";

		$pagesize   = $this->_pagesize;
		$start      = $this->_start;

		$range_min  = $this->_start - ($this->_pages_before_after * $pagesize);
		$range_max  = $this->_start + ($this->_pages_before_after * $pagesize) + $pagesize;

		$current_js_url = str_replace("%%", $this->_start, $this->_urlmapping);
		if (!preg_match("/^javascript:/si", $current_js_url)) {
			$current_js_url = "javascript: location.href='".addslashes($current_js_url)."';";
		}

		$next = $this->_start + $pagesize;
		$next_url = str_replace("%%", $next, $this->_urlmapping);
		if ($next >= $this->_count) {
			$next = -1;
		}
		$previous = $this->_start - $pagesize;
		$previous_url = str_replace("%%", $previous, $this->_urlmapping);
		if ($previous < 0) {
			$previous = -1;
		}

		$table = new Layout_table( array("width"=>"100%") );
		$table->addTableRow();
			$table->addTableData();
				if ($previous > -1) {
					if ($this->_use_text) {
						$table->insertTag("a", gettext("previous"), array(
						 	"href" => $previous_url
						));
					} else {
						$table->insertAction("back", gettext("previous"), $previous_url);
					}
				}
				$table->addSpace(2);
				/* always show the first page */
				if ($range_min > 0) {
					$this->_generate_link(0, &$table);
				}
				/* show ... between the first page and the range start */
				if ($range_min > $pagesize) {
					$table->addCode(".. ");
				}
				for ($i=0;$i<($this->_count-$pagesize+$pagesize);$i+=$pagesize) {
					if ($i >= $range_min && $i < $range_max) {
						$this->_generate_link($i, &$table);
					}
				}
				/* show ... between the last page and the range end */
				if ($range_max < ($this->_count-($pagesize*2))) {
					$table->addCode(".. ");
				}
				/* always show the last page */
				if ($range_max < $this->_count) {
					$this->_generate_link($this->_count-$pagesize, &$table);
				}
				$table->addSpace();
				if (!$this->_skip_selectbox) {
					$table->addSelectField("pagesize", $this->_pagesizearray, $this->_pagesize, 0, array(
						"onchange" => "javascript: updatePagesize(this.value, '".addslashes($current_js_url)."');"
					));
				}

				if ($next > -1) {
					if ($this->_use_text) {
						$table->insertTag("a", gettext("next"), array(
						 	"href" => $next_url
						 ));
					} else {
						$table->insertAction("forward", gettext("next"), $next_url);
					}
				}
			$table->endTableData();

			$curcount = $start+$pagesize;
			if ($curcount > $this->_count) {
				$curcount = $this->_count;
			}
			$table->addTabledata( array("align"=>"right") );
				$table->addSpace(3);
				if ($this->_count == 0)
					$table->addCode( "(0 ");
				else
					$table->addCode( "(".($start+1)." ");

				$table->addCode("-");
				$table->addCode( " ".($curcount)." " );
				if (!$this->_fuzzy) {
					$table->addCode( gettext("from total of") );
					$table->addCode( " ".$this->_count.")" );
				} else {
					$table->addCode(")");
				}

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		return $table->generate_output();
	}


}
?>
