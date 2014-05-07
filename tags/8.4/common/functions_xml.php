<?php
/**
 * Covide Groupware-CRM XML handling class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
/**
 * This work is based on parser_php5.phps on www.criticaldevelopment.net
 * This file has the following lines as copyrightnotice:
 * @author Adam A. Flynn <adamaflynn@criticaldevelopment.net>
 * @copyright Copyright (c) 2006, Adam A. Flynn
 * @version 1.2.0
 * @link http://www.criticaldevelopment.net/xml/
 */
Class XMLParser {
	/* constants */
	/* variables {{{ */
	/**
	 * The xml parser
	 * @var resource
	 */
	private $parser;
	/**
	 * Thi xml document to parse/process
	 * @var string
	 */
	private $xml;
	/**
	 * Document tag
	 * @var object
	 */
	public $document;
	/**
	* Current object depth
	* @var array
	*/
	private $stack;
	/* }}} */
	/* methods */
	/* ___construct {{{ */
	/**
	 * Loads the XML document
	 *
	 * @param string $xml The contents of an xml file
	 * @return XMLParser
	 */
	public function __construct($xml = "") {
		$this->xml = $xml;
		//set stack to be an empty array
		$this->stack = array();
	}
	/* }}} */
	/* parseXML {{{ */
	/**
	 * Use the php xml parser on the xml data
	 */
	public function parseXML() {
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser, "characterDataHandler");
		xml_parse($this->parser, $this->xml);
		xml_parser_free($this->parser);
	}
	/* }}} */
	/* getStackLocation {{{ */
	/**
	 * Get the reference to the current parent
	 *
	 * @return object
	 */
	private function getStackLocation() {
		$return = &end($this->stack);
		return $return;
	}
	/* }}} */
	/* startElement {{{ */
	/**
	 * Handler function for the start of a tag
	 *
	 * @param resource $parser
	 * @param string $name
	 * @param array $attrs
	 */
	private function startElement($parser, $name, $attrs = array()) {
		//Make the name of the tag lower case
		$name = strtolower($name);

		//Check to see if tag is root-level
		if (count($this->stack) == 0)  {
			//If so, set the document as the current tag
			$this->document = new XMLTag($name, $attrs);
			//And start out the stack with the document tag
			$this->stack = array(&$this->document);
		} else {
			//If it isn't root level, use the stack to find the parent
			//Get the reference to the current direct parent
			$parent = $this->getStackLocation();

			$parent->AddChild($name, $attrs, count($this->stack));
			//Update the stack
			$this->stack[] = &end($parent->$name);
		}
	}
	/* }}} */
	/* endElement {{{ */
	/**
	 * Handler function for the end of a tag
	 *
	 * @param resource $parser
	 * @param string $name
	 */
	private function endElement($parser, $name) {
		//Update stack by removing the end value from it as the parent
		array_pop($this->stack);
	}
	/* }}} */
	/* characterDataHandler {{{ */
	/**
	 * Handler function for the character data within a tag
	 *
	 * @param resource $parser
	 * @param string $data
	 */
	private function characterDataHandler($parser, $data) {
		//Get the reference to the current parent object
		$tag = $this->getStackLocation();
		//Assign data to it
		$tag->tagData .= trim($data);
	}
	/* }}} */
}


/**
 * XML Tag Object (php5)
 * 
 * This object stores all of the direct children of itself in the $children array. They are also stored by
 * type as arrays. So, if, for example, this tag had 2 <font> tags as children, there would be a class member
 * called $font created as an array. $font[0] would be the first font tag, and $font[1] would be the second.
 * 
 * To loop through all of the direct children of this object, the $children member should be used.
 *
 * To loop through all of the direct children of a specific tag for this object, it is probably easier 
 * to use the arrays of the specific tag names, as explained above.
 * 
 * @author Adam A. Flynn <adamaflynn@thousandmonkeys.net>
 * @copyright Copyright (c) 2005, Adam A. Flynn
 *
 * @version 1.2.0
 */
Class XMLTag {
	/* constants */
	/* variables {{{ */
	/**
	 * Array with the attributes of this XML tag
	 *
	 * @var array
	 */
	public $tagAttrs;
	/**
	 * The name of the tag
	 *
	 * @var string
	 */
	public $tagName;
	/**
	 * The data the tag contains 
	 * 
	 * So, if the tag doesn't contain child tags, and just contains a string, it would go here
	 *
	 * @var stat
	 */
	public $tagData;
	/**
	 * Array of references to the objects of all direct children of this XML object
	 *
	 * @var array
	 */
	public $tagChildren;
	/**
	 * The number of parents this XML object has (number of levels from this tag to the root tag)
	 *
	 * Used presently only to set the number of tabs when outputting XML
	 *
	 * @var int
	 */
	public $tagParents;
	/* }}} */
	/* methods */
	/* __construct {{{ */
	/**
	 * Constructor, sets up all the default values
	 *
	 * @param string $name
	 * @param array $attrs
	 * @param int $parents
	 * @return XMLTag
	 */
	function __construct($name, $attrs = array(), $parents = 0) {
		//Make the keys of the attr array lower case, and store the value
		$this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);

		//Make the name lower case and store the value
		$this->tagName = strtolower($name);

		//Set the number of parents
		$this->tagParents = $parents;

		//Set the types for children and data
		$this->tagChildren = array();
		$this->tagData = "";
	}
	/* }}} */
	/* addChild {{{ */
	/**
	 * Adds a direct child to this object
	 *
	 * @param string $name
	 * @param array $attrs
	 * @param int $parents
	 */
	public function AddChild($name, $attrs, $parents) {    
		//If there is no array already set for the tag name being added, 
		//create an empty array for it
		if(!isset($this->$name))
			$this->$name = array();

		//Create the child object itself
		$child = new XMLTag($name, $attrs, $parents);

		//Add the reference of it to the end of an array member named for the tag's name
		$this->{$name}[] = &$child;
        
		//Add the reference to the children array member
		$this->tagChildren[] = &$child;
	}
	/* }}} */
	/* getXML {{{ */
	/**
	 * Returns the string of the XML document which would be generated from this object
	 * 
	 * This function works recursively, so it gets the XML of itself and all of its children, which
	 * in turn gets the XML of all their children, which in turn gets the XML of all thier children,
	 * and so on. So, if you call getXML from the document root object, it will return a string for 
	 * the XML of the entire document.
	 * 
	 * This function does not, however, return a DTD or an XML version/encoding tag. That should be
	 * handled by XMLParser::getXML()
	 *
	 * @return string
	 */
	public function getXML() {
		//Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
		$out = "\n".str_repeat("\t", $this->tagParents)."<".$this->tagName;

		//For each attribute, add attr="value"
		foreach($this->tagAttrs as $attr => $value)
			$out .= " ".$attr."=\"".$value."\"";

		//If there are no children and it contains no data, end it off with a />
		if(empty($this->tagChildren) && empty($this->tagData)) {
			$out .= " />";
		} else {
			//If there are children
			if(!empty($this->tagChildren)) {
				$out .= ">";

				//For each child, call the getXML function (this will ensure that all children are added recursively)
				foreach($this->tagChildren as $child)
					$out .= $child->getXML();
				//Add the newline and indentation to go along with the close tag
				$out .= "\n".str_repeat("\t", $this->tagParents);
			} elseif(!empty($this->tagData)) {
				//If there is data, close off the start tag and add the data
				$out .= ">".$this->tagData;
			}

			//Add the end tag    
			$out .= "</".$this->tagName.">";
		}
		//Return the final output
		return $out;
	}
	/* }}} */
}
?>
