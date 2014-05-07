cov2014 is a fork of covide crm, an opensource crm with some nice functions like asterisk integration, google aps integration and more. They have gone closed source and provide the software as saas now.

This new projects goal is to create a stable version of the latest opensource version and develop it to a kick ass crm product. This includes the rewrite of google apps integration, replacing asterisk with Freeswitch, creating an API so we can integrate easily. Integration is key

The name may change later on.

We are looking for programmers to add to our team on a freelance basis. 
$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

U wont get rich as long as we are poor )) But we will pay a bounty for specific functions.


Covide

	Version     : 9.2.2
	Copyright   : Copyright 1999-2009 by Covide BV.
	Homepage    : http://www.covide.net
	Projectpage : http://sourceforge.net/projects/covide
	License     : General Public License

	Covide offers you OpenSource and webbased CRM-Groupware with possibilities 
	for integrated usage as mail-, web-, fileserver and VoIP PBX.

Table of contents

	1. About Covide
		1.1 3rd party software used
	2. Installation / Upgrade
		2.1. Requirements
		2.2. Installation / Upgrade
		2.2.1. Manual installation / Upgrade
		2.3. Configuring
	3. Feedback / Contributing.
		3.1. Feedback (bugs/questions/feature requests)
		3.2. Contributing
			3.2.1. Writing patches/contributions.
		3.3. Credits
	4. Roadmap
	5. Legal stuff

1. About Covide

	Covide offers you OpenSource and webbased CRM-Groupware with possibilities 
	for integrated usage as mail-, web-, and fileserver AND VoIP PBX!

	It provides features for communicating and maintaining projects, appointments,
	email, support calls, incidents, etc etc
	
1.1 3rd party software used

	Covide uses some 3rd party software.
	- Synchronisation with PDA's and Outlook is implemented using Funambol 6.0
	  Homepage: http://www.funambol.com/opensource
	  Download: http://www.funambol.com/opensource/download.php?file_id=funambol-6.0.exe
	- VOIP parts are implemented using Asterisk PBX
	  Homepage: http://www.asterisk.org
	  Download: http://www.asterisk.org/downloads
	- HTML editor used is tinyMCE
	  Homepage: http://tinymce.moxiecode.com/
	  Download: We ship tinyMCE with the sources of Covide
	- HTML to PDF conversion is implemented using HTML_ToPDF by rustyparts
	  Homepage: http://www.rustyparts.com/pdf.php
	  Download: We ship HTML_ToPDF with the sources of Covide
	- Multiple Fileupload is implemented using jupload
	  Homepage:
	  Download: We ship jupload with the sources of Covide
	- Code highlighting in the CMS editor is implemented using EditArea
	  Homepage:
	  Download: We ship editarea with the sources of Covide
	- HTML editor used in some places is Wyzz
	  Homepage: http://www.wyzz.info/
	  Download: We ship wyzz with the sources of Covide

2. Installation
	Although we put great effort to make the installation as simple as possible,
	it's still a process that should be completed by people with extensive
	UNIX knowledge.

2.1. Requirements

	see REQUIREMENTS file for more information
	
2.2. Installation / Upgrade

	2.2.1. Manual installation / Upgrade

	To install a completely new version, follow these steps:

	1. Create a new MySQL database.
	2. Create a new user for the database.
	3. Import the file sql/$DATABASE_TYPE/covide.sql into the database.
	4. Change the defines in the src/conf/offices.php file to match with the 
	   database and user names. Also change the other settings to your liking.
	5. Copy all .php files in the src/ directory to your webserver directory.
	6. Create a dir /var/covide_files and give the webserver user read/write permissions.
	7. log in with username/password covide/covide .

	For the rss feeds on the portal, you need some kind of trigger.
	We recommend using cron for this with something like:
	wget -q -r http://your.covide.url/index.php?mod=rss
	
	
	To upgrade a current version:

	See UPGRADING

	It is A Good Thing To Do (tm) to backup everything before doing this.
	
2.3. Configuring
	
	Once logged in click on the first icon right of the C on top of the screen.


3. Feedback / Contributing.

	If you wish to contribute to Covide, please read this section. It contains
	rules and guidelines for contribution. 

3.1. Feedback (bugs/questions/feature requests)

	If you've got feedback about Covide, and wish to share it, you can do so
	in a couple of ways.

	1. Send email to <support (AT) covide (DOT) nl>
	2. Use the tools in sourceforge
	
	The preferred way is using the sourceforge tools.

	When giving feedback, please keep these following rules in mind:

	* Be specific. If you've found a bug or have a feature request, please
	  provide the URL where you've found the bug or want the feature. Try to
	  be short but clear.
	* If you're going to report a bug, please follow the standard bug-reporting
	  guidelines. You can find a good guide to reporting bugs here:
	  http://www.chiark.greenend.org.uk/~sgtatham/bugs.html

3.2. Contributing

	If you wish to contribute to this project, you'll have to come up with a
	good contribution. 
	What kind of contributions is the core dev team interested in? 

	* Bug-fixing patches.
	* Feature enhancements. (Please note the 'enhancement' part.. it means the core dev team would
	  like enhancements to currently available features).
	
	Completely new features might get accepted, but don't yet bet money on it. The best
	way to go here is to first tell the core dev team about your idea. We will then let you know
	if it's likely to be included in Covide. 

	If you contribute a completely new module, we might include it in the Covide
	distribution or provide an additional download on the website for the 
	module. That way, people can still use the module but we don't have to 
	provide support for it.

	3.2.1. Writing patches/contributions.

	There are, at the moment, official coding guidelines. 
	If you need more information about those guidelines, send us an email at <support@terrazur.nl>
	and we can send them to you.
	Also look at the other sources to get an impression about code-style, layout, used functions,
	etc. 
	
	Some rules you should follow are:
	* Create patches using: diff -ruN [latest-release] [your-changes]
	  Run it in the directory which CONTAINS the covide/ directory.
	* If you can, use the following editor settings:
	  - Vim: 
		:set tabstop=2
		:set softtabstop=2
		:set shiftwidth=2
		:set noexpandtab
		:set guioptions-=T
	  - Other editors:
		Tabs should be tabs, not just a couple of spaces.
		Only use tabs at the start of a line. Use spaces eveywhere else 
		(aligning).
		Try to keep lines in sourcecode under 120 characters. If you really have to make a longer line
		that is ok, but please try to keep under it. Almost all editors have some option to place a marker
		at 120 characters so it is easier to see.

3.3. Credits

	See the CREDITS FILE

4. Roadmap

	So, where's this project going?

	Just keep an eye on the website

5. Legal stuff

	Covide is Copyright by Covide BV, licensed under 
	the General Public License (GPL)

	Copyright (C), 1999-2009 by Covide BV

	This program is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published by the Free
	Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
	or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	675 Mass Ave, Cambridge, MA 02139, USA.

	For more information, see the COPYING file supplied with this program.
