<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
class cmsKeywordDensity {
	/** 
	 * run the keyword suggestion module
	 * 
	 * @param id int the page id to check
	 * @param cms one cms_output instance
	 */
	static public function run($id, $cms) {

		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("Keyword density")
		));
		$cms->addMenuItems(&$venster);
		$venster->generateMenuItems();

		$venster->addVensterData();
		
		$cms_data = new Cms_data;
		$uri = sprintf('%s/page/%d', 
			str_replace('/mode/linkchecker#param#', '', $cms_data->linkchecker["url"]), $id);

		$data = self::getdensity($uri);
		// dont know how to implement fast at the moment, sorry
		$output->addTag('style', array('type' => 'text/css'));
			$output->addCode('
				td.keyword_density_error { color: red; }
				td.keyword_density_warning { color: #B04C00; }
				td.keyword_density_low { color: black; }
				td.keyword_density_ok { color: green; }
				td.keyword_density_not_important { color: #666; }
			');
		$output->endTag('style');

		$table[1] = new Layout_table(array(
			'cellpadding' => 1,
			'cellspacing' => 1
		));
		$table[2] = new Layout_table(array(
			'cellpadding' => 1,
			'cellspacing' => 1
		));
		foreach ($data as $k=>$dens) {
			$t =& $table[($k==1) ? 1 : 2];
			$t->addTableRow();
			$t->insertTableHeader(sprintf('# %2$s %1$s', $k, gettext('words')));
			$t->insertTableHeader(gettext('number'), 'header');
			$t->insertTableHeader(gettext('density'), 'header');
			$t->endTableRow();
			foreach ($dens as $keyword=>$value) {
				$t->addTableRow();
				$t->insertTableData($keyword);
				$t->insertTableData($value['count']);
				if ($value['density'] > 6) {
					$c = 'error';
				} elseif ($value['density'] < 1/$k) {
					$c = 'not_important';
				} elseif ($value['density'] > 5) {
					$c = 'warning';
				} elseif ($value['density'] < 2/$k) {
					$c = 'low';
				} else {
					$c = 'ok';
				}
				$t->insertTableData($value['density']."%", array('class' => sprintf('keyword_density_%s', $c) ));
				$t->endTableRow();	
			}
		}
		$table[1]->endTable();
		$table[2]->endTable();
		
		$tl = new Layout_table();
		$tl->addTableRow();
		$tl->insertTableData($table[1]->generate_output(), '', 'top');
		$tl->insertTableData($table[2]->generate_output(), '', 'top');
		$tl->endTableRow();
		$tl->endTable();

		$venster->addCode($tl->generate_output());		
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* author: stephan van de haar <svdhaar@users.sourceforge.net> */
	static private function getdensity($url) {

		$html = file_get_contents($url);
		if ($html) {
			// decode html
			$html = html_entity_decode($html);
			
			// get certain html tags
			$inline = array();
			preg_match_all('/<((meta)|(img)|(a)) [^>]*?>/si', $html, $tags);
			foreach (current($tags) as $p=>$tag) {
				$attr = array();
				// get some attribs
				if (strtolower($tags[1][$p]) == 'meta') {
					if (preg_match('/^<meta name="((description)|(keywords))"/si', $tag)) {
						$inline[] = preg_replace(
							'/^<meta name="((description)|(keywords))" content="([^"]*?)">$/si', '$4', $tag);
					}
				} else {
					preg_match_all('/ ((title)|(alt))="([^"]*?)"/s', $tag, $attr);
					foreach (end($attr) as $a) {
						if (is_array($a)) {
							foreach ($a as $v) {
								$inline[] = $v;
							}
						} else {
							$inline[] = $a;
						}
					}
				}
			}
			// get the meta tags
			#$meta = self::get_meta_data($html);
			#$inline[] = $meta['description'];
			#$inline[] = $meta['keywords'];
			
			// filter some script and css crap
			$htmlc = preg_split('/<\/{0,1}((br)|(p))[^>]*?>/s', $html);
			$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
				'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
				'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
				'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
			);
			
			foreach ($htmlc as $h) {
				$h = preg_replace($search, '', $h);
				$inline[] = strip_tags($h);
			}
			// filter some chars
			foreach ($inline as $k=>$v) {
				$v = str_replace(array('-', '.', ',', '(', ')', '_', '*', '"', '!', '?', "\n", '/', '&#8217;'), ' ', $v);
				$v = preg_replace('/\W/s', ' ', $v);
				$v = preg_replace('/ {1,}/s', ' ', $v);
				$v = trim($v);
				$inline[$k] = mb_strtolower($v);
			}
			// create run toghether words
			$words = array();
			$total = 0;
			foreach ($inline as $inl) {
				$inl = explode(' ', $inl);
				$total += count($inl);
				foreach ($inl as $k=>$i) {
					if (trim($i)) {
						$words[1][] = $i;
						if (array_key_exists($k+1, $inl) && trim($inl[$k+1])) {
							$words[2][] = sprintf('%s %s', $i, $inl[$k+1]);
							if (array_key_exists($k+2, $inl) && trim($inl[$k+2])) {
								$words[3][] = sprintf('%s %s %s', $i, $inl[$k+1], $inl[$k+2]);
							}
						}
					} else {
						$total--;
					}
				}
			}
			// calculate densities
			foreach ($words as $k=>$v) {
				$words[$k] = array_count_values($v);
				foreach ($words[$k] as $word => $value) {
					$words[$k][$word] = array(
						'count' => $value,
						'density' => number_format($value/($total)*100, 2) 
					);
				}
				arsort($words[$k]);
			}
			// stopwords
			$stopwords = array_merge(
				file(dirname(__FILE__).'/stopwords/nl.txt'),
				file(dirname(__FILE__).'/stopwords/en.txt')
			);
			foreach($stopwords as $k=>$v) {
				$stopwords[$k] = trim($v);
			}
			
			foreach ($words as $k=>$v) {
				foreach ($v as $word => $info) {
					$flag = 0;
					$wordlist = explode(' ', $word);
					foreach ($wordlist as $w) {
						if (in_array($w, $stopwords) || mb_strlen($w) < 3) {
							$flag++;
						}
					}
					if ($flag == count($wordlist)) {
						unset($words[$k][$word]);
					}
				}
			}
			// limit results
			$words[1] = array_slice($words[1], 0, 26, true);
			$words[2] = array_slice($words[2], 0, 15, true);
			$words[3] = array_slice($words[3], 0, 10, true);
			return $words;
				
		}
	}
}

?>
