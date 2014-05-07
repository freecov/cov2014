<?
/*
   This file contains classes to import external calendar items and merge
   them to the Covide Calendar storage.
 
   Copyright 2008 KovoKs B.V. kovoks@kovoks.nl

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License as
   published by the Free Software Foundation; either version 2 of 
   the License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once("vcalendarlib.php");

Class externalCalendarItem {
	private $start = -1;
	private $end = -1;
	private $summary = "";
	private $description = "";
	private $covideId = -1;
		
	public function __construct( $start=0, $end=0, $summary="", $description="" ) {
		$this->setStart( $start );
		$this->setEnd ( $end );
		$this->setSummary( $summary );
		$this->setDescription( $description );
	}

	// setters
	public function setStart( $i ) {
		$this->start = $i;
	}
	
	public function setEnd( $i ) {
		$this->end = $i;
	}
	
	public function setSummary( $i ) {
		$this->summary = $i;
	}

	public function setDescription( $i ) {
		$this->description = $i;
	}

	public function setCovideId( $i ) {
		$this->covideId = $i;
	}

	// getters
	public function start() {
		return $this->start;
	}
	
	public function end() {
		return $this->end;
	}
	
	public function summary() {
		return $this->summary;
	}
	
	public function description() {
		return $this->description;
	}
	
	public function covideId() {
		return $this->covideId;
	}
	
	// methods
	public function isIdentical( $other ) {
		return $this->start == $other->start() && 
			$this->end == $other->end() && 
			$this->summary == $other->summary() &&
			$this->description == $other->description();
	}

	// sorting...
	static function comp( $a, $b ) {
		if ($a->start() < $b->start() )
			return -1;
		elseif ($a->start() > $b->start() )
			return 1;

		// *sigh* equal start date!
		elseif ($a->end() < $b->end() )
			return -1;
		elseif ($a->end() > $b->end() )
			return 1;

		// *sig* equal end date!
		else {
			$res = strcmp( $a->summary(), $b->summary() );
			if ($res == 0)
				return strcmp( $a->description(), $b->description() );
			else
				return $res;
		}
	}
}

class externalCalendar {
	private $id = -1;
	private $clock = 0;
	private $user;

	public function __construct( $id ) {
		$this->id = $id;
	}

	private function getCovideItems() {
		 if ($this->id == -1)
		 	return;

		 $sql = "select id, timestamp_start, timestamp_end, subject, body from calendar where external_id = ". $this->id;
		 $res = sql_query($sql);
		 while ($row = sql_fetch_assoc($res)) {
		 	$calendarItem = new externalCalendarItem( $row["timestamp_start"],
				$row["timestamp_end"], $row["subject"], $row["body"] );
			$calendarItem->setCovideId( $row["id"] );
			$calendarItems[] = $calendarItem;
		}
		return $calendarItems;
	}

	private function readFile() {
		// read the filename..
		$sql = "select id, url from calendar_external where id = ". $this->id;
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$filename = $row["url"];

		if ($filename == "") {
			echo "ERROR: calendarid not found";
			exit;
		}

		// read the file
		$handle = fopen($filename, "rb");
		$contents = '';
		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
		}
		fclose($handle);

		// parse the vCalendar data and store the data
		$parse = new vCalendarParser( $contents );
	        $numlines = $parse->parse();

		// make externalCalendarItems from them.
		unset( $calinfo );
		$item = $parse->getNext();
		while ($item) {
			$start = $item["START"];
			$end = $item["END"];
		     	$summary = $item["SUMMARY"];
		     	$description = $item["DESCRIPTION"];
			$calItem = new externalCalendarItem( $start, $end, $summary, $description );
			$calItems[] = $calItem;
			$item = $parse->getNext();
		}
		return( $calItems );
	}

	public function setUser( $user ) {
		$this->user = $user;
	}

	public function sync() {
		$this->startDebugClock();
		$this->debug("Retrieving Covide Items");
		$currentItems = $this->getCovideItems();

		$this->debug("Found ".count($currentItems)." items, now starting to read the vCal-file");
		$newItems = $this->readFile();

		$this->debug("Found ".count($newItems)." items, now starting to sort both arrays");

		// sort both on start date.
		if ( count( $currentItems ) > 0 )
			usort( $currentItems, array("externalCalendarItem","comp" ) );
		if ( count( $newItems ) > 0 )
			usort( $newItems, array("externalCalendarItem","comp" ) );

		$this->debug("Done, now starting to compare both arrays");
		// check if the newItems already exist in the currentItems. 
		if ( count($currentItems) > 0) {
			$start = 0;
			foreach( $currentItems as $currentItem ) {
				$found = false;

		//		echo "\n\n\nCompare:\n";
		//		print_r ($currentItem);

				$took_amount = 0;
				for ($j = $start; $j < count( $newItems) ; $j++) {
					if ( $newItems[$j]->start() > $currentItem->start() ) {
						break;
					}

					if ( $newItems[$j]->covideId() == -1 && $currentItem->isIdentical( $newItems[$j] ) ) {
						$found = true;
						$newItems[$j]->setCovideId( $currentItem->covideId() );
						break;
					}
					$took_amount++;

		//			echo "\n\n\nNo match with:\n";
		//			print_r ($newItems[$j]);
				}
				
				$start = $j+1;
		//		echo "Found out after $took_amount items, position is at: $start\n";

				if ( !$found )
					$currentItemsToDelete[] = $currentItem;


			}
		}

		$this->debug("Done, now starting to delete ".count($currentItemsToDelete)." items from the database");
		// Delete the items which are no longer needed.
		if ( count($currentItemsToDelete) > 0 ) {
			foreach( $currentItemsToDelete as $item ) {
				$sql = "delete from calendar where id = " . $item->covideId();
				$res = sql_query($sql);

				$sql = "delete from calendar_user where calendar_id = " . $item->covideId();
				$res = sql_query($sql);

				$sql = "delete from calendar_repeats where calendar_id = " . $item->covideId();
				$res = sql_query($sql);
			}
		}

		// Add the items which are new.
		$this->debug("Done, now starting to add potentially ".count($newItems)." items to the database");
		$actual = 0;
		if ( count($newItems) > 0 ) {
			foreach ($newItems as $item ) {
				if ($item->covideId() != -1)
					continue;
				++$actual;

				// sanitise a bit more
				if ($item->summary() == "" )
					$item->setSummary( $item->description() );

				if ($this->user == '')
					$this->user = $_SESSION["user_id"];

				if ($this->user == '')
					return;

				$tempend = $item->end();
				$isrecurring = 0;
				$allday = 0;
				if ( ($item->start() + 24*60*60) <= $item->end()) {
					$allday = 1;
					$isrecurring = 1;
				//	$tempend = $item->start();
				}
				
				$sql = "insert into calendar (timestamp_start, timestamp_end, alldayevent, isrecurring, subject, ";
				$sql .= "body, modified, modified_by, external_id) ";
				$sql .= "values ( ". $item->start().",". $tempend .",".$allday.",".$isrecurring;
				$sql .= ",'". addslashes($item->summary())."','";
				$sql .= addslashes($item->description()) . "'," .date("U") .",". $this->user.",".$this->id.")";
				$res = sql_query($sql);
				$new_id = sql_insert_id("calendar");

				$sql = "insert into calendar_user values ( ".$new_id.",".$this->user.",1)";
				$res = sql_query($sql);

				// since this can be a allday event, it can be a repeating item.
				if ( $allday == 1 ) {
					$sql = "insert into calendar_repeats values ( ".$new_id.",1,".$item->end().",1,'yyyyyyy')";
					$res = sql_query($sql);
				}
			}
		}

		$this->debug("Done, added $actual items in the end...");
	}

	public function delete() {
		 if ($this->id == -1)
		 	return;

		$currentItems = $this->getCovideItems();

		if (count($currentItems) == 0)
			return;

		foreach( $currentItems as $item ) {
			$sql = "delete from calendar where id = " . $item->covideId();
			$res = sql_query($sql);

			$sql = "delete from calendar_user where calendar_id = " . $item->covideId();
			$res = sql_query($sql);

			$sql = "delete from calendar_repeats where calendar_id = " . $item->covideId();
			$res = sql_query($sql);
		}
	}

	function startDebugClock() {
		$this->clock = date("U");
	}
		
	function debug( $msg ) {
		$debug = false;

		if (!$debug)
			return;

		$time = date("U");
		echo ($time - $this->clock)."s: ".$msg."<br>\n";
	}
}
?>
