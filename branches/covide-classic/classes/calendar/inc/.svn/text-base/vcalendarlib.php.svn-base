<?
/*
   This file contains a class to convert a vcal file to an array.
 
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

Class vCalendarParser {
	private $text = "";
	private $data;
	private $itpos;
	
	public function __construct( $text="" ) {
		$this->text = ereg_replace("\r\n","\n",$text);
	}

        private function ISO8601toEpoc( $date ) {
		if (strlen($date) == 8) {
			// 20041016
			$d = substr($date,6,2);
			$m = substr($date,4,2);
			$y = substr($date,0,4);
		 	return mktime( 0,0,0,$m,$d,$y);
		} else {
			// 20050416T122635Z
	        	return strtotime($date);
		}
	}

	public function parse() {
		$lines = split("\n", $this->text);
		$i = -1;
		unset($data);
		foreach( $lines as $line ) {
			// unfold.
			if (substr($line,0,1) == " ") {
				$this->data[$i][$lastkey] .= substr($line,1);
				continue;
			}

			$pos = stripos( $line, ":" );
			if ( !$pos )
				continue;

			$key = rtrim(strtoupper( substr( $line, 0, $pos ) ) );
			$val = substr( $line, $pos+1 );

			$pos = stripos( $key, ";" );
			if ($pos > 0 )
				$key = substr( $key, 0, $pos );

			switch ($key) {
				case "BEGIN" :
					if ( $val == "VEVENT" )
						$i++;
					break;

				case "DESCRIPTION" :
				case "SUMMARY" :
					$this->data[$i][$key] = $val;
					break;

				case "DTSTART" :
					$this->data[$i]["START"] = $this->ISO8601toEpoc( $val );
					break;
				case "DTEND" :
					$this->data[$i]["END"] = $this->ISO8601toEpoc( $val );
					break;


				case "VERSION" :
					break;

				default :
					// echo "\nUnsupported value: |$key|$val|<br>";
				}

			$lastkey = $key;
		}
		$this->reset();
		return $this->count();
	}

	public function reset() {
		$this->itpos = -1;
	}

	public function count() {
		return count( $this->data );
	}

	public function getNext() {
		$this->itpos++;
		if ($this->itpos > $this->count() )
			return false;
		return( $this->data[$this->itpos] );
	}
}
?>
