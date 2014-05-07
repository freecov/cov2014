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

require("externalcalendars.php");

echo "<PRE>";

// item test.
$item = new externalCalendarItem(1,2,"sum","desc");
print_result("ExternalCalendarItem: Self check", true, $item->isIdentical( $item ) );

$item2 = new externalCalendarItem();
$item2->setStart( 1 );
$item2->setEnd( 2 );
$item2->setSummary( "sum" );
$item2->setDescription( "desc" );
print_result("ExternalCalendarItem: Compare check 1", true, $item->isIdentical( $item2 ) );

$item2->setStart( 2 );
print_result("ExternalCalendarItem: Compare check 2", false, $item->isIdentical( $item2 ) );


// lib test.
$data = <<< VCALEND
BEGIN:VCALENDAR
VERSION:1.0
BEGIN:VEVENT
DTSTART:20080107T080000
DTEND:20080107T170000
DESCRIPTION:test event1
END:VEVENT
BEGIN:VEVENT
DTSTART:20041016
DTEND:20080108T170000
DESCRIPTION:KovoKs 
 followed by
  tom 
 albers
END:VEVENT
END:VCALENDAR
VCALEND;

$vcal = new vCalendarParser( $data );
$vcal->parse();

print_result("vacllib: Amount test", 2, $vcal->count() );

$item = $vcal->getNext();
print_result("vcallib: Date check type1", mktime(8,0,0,1,7,2008), $item["START"] );

$item = $vcal->getNext();
print_result("vcallib: Date check type2", mktime(0,0,0,10,16,2004), $item["START"] );
print_result("vcallib: folding check", "KovoKs followed by tom albers", $item["DESCRIPTION"] );


function print_result( $desc, $shouldbe, $is ) {
	echo "$desc: ";
	if ( $shouldbe == $is )
		echo "-----> PASS\n";
	else { 
		echo "-----> FAIL*****\n";
		echo "       Expected: $shouldbe\n";
		echo "       Received: $is\n";
	}
}

?>
