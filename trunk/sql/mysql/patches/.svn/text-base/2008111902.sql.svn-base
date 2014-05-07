CREATE TABLE `dimdim` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `room` varchar(255) NOT NULL,
  `attendees` varchar(255) NOT NULL,
  `external_attendees` varchar(255) NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;


ALTER TABLE users ADD dimdim_username VARCHAR( 255 ) NULL ,
ADD dimdim_password VARCHAR( 255 ) NULL;


ALTER TABLE calendar ADD dimdim_meeting INT( 11 ) NULL;
