CREATE TABLE IF NOT EXISTS `calendar` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT 'unique id for an appointment.',
  `timestamp_start` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp of start date and time for the appointment',
  `timestamp_end` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp of end date and time for the appointment',
  `alldayevent` tinyint(2) NOT NULL COMMENT '1 if this is an all-day event',
  `subject` varchar(255) NOT NULL COMMENT 'Short description for the appointment',
  `body` text NOT NULL COMMENT 'Extended description for the appointment. Be sure to remove HTML tags when sending this to funambol.',
  `location` varchar(255) NOT NULL COMMENT 'The location for the appointment',
  `kilometers` int(11) unsigned COMMENT 'Distance to the location of the appointment',
  `reminderset` tinyint(2) NOT NULL COMMENT '1 if a reminder should be sent to the user',
  `reminderminutesbeforestart` int(11) unsigned NOT NULL COMMENT 'Number of minutes before the start of the appointment a reminder should be sent',
  `busystatus` tinyint(4) NOT NULL COMMENT '0 for free, 1 for tentative, 2 for busy, 3 for outofoffice',
  `importance` tinyint(4) NOT NULL COMMENT '0 for low, 1 for normal, 2 for high',
  `address_id` int(11) unsigned NOT NULL COMMENT 'main contact id from address table for this appointment',
  `multirel` varchar(255) NOT NULL COMMENT 'pipe seperated list of additional contacts for this appointment',
  `project_id` int(11) unsigned COMMENT 'main project id from project table for this appointment',
  `is_private` tinyint(2) NOT NULL COMMENT '1 if this is a private appointment that other users are not allowed to view/alter',
  `isrecurring` tinyint(2) NOT NULL default '0' COMMENT 'true if the appointment is a recurring appointment',
  `modified_by` int(11) unsigned NOT NULL COMMENT 'user id of the user that last modified this appointment',
  `modified` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp when this appointment was last modified',
  PRIMARY KEY  (`id`),
  KEY `address_id` (`address_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `calendar_user` (
  `calendar_id` int(11) unsigned NOT NULL COMMENT 'appointment id',
  `user_id` int(11) unsigned NOT NULL COMMENT 'user id as found in the users table',
  `status` int(11) NOT NULL COMMENT 'status of evenvt for this user: 1 for accepted, 2 for rejected, 3 for waiting',
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='link an appointment to one or more users';

ALTER TABLE `calendar` ADD `is_ill` TINYINT( 2 ) NOT NULL ,
ADD `is_specialleave` TINYINT( 2 ) NOT NULL ,
ADD `is_holiday` TINYINT( 2 ) NOT NULL ;
ALTER TABLE `calendar` ADD `is_dnd` TINYINT( 2 ) NOT NULL COMMENT 'With the voip module this will mean the phone wont ring';

CREATE TABLE `calendar_repeats` (
`calendar_id` INT( 11 ) UNSIGNED NOT NULL ,
`repeat_type` INT( 3 ) UNSIGNED NULL ,
`timestamp_end` INT( 11 ) NULL ,
`repeat_frequency` INT( 11 ) UNSIGNED NULL ,
`repeat_days` CHAR( 7 ) NULL
) ENGINE = MYISAM ;

CREATE TABLE `calendar_exceptions` (
`calendar_id` INT( 11 ) UNSIGNED NOT NULL COMMENT 'id from calendar table',
`user_id` INT( 11 ) UNSIGNED NOT NULL COMMENT 'id from userstable',
`timestamp_exception` INT( 11 ) UNSIGNED NOT NULL COMMENT 'UNIX TIMESTAMP of exception date'
) ENGINE = MYISAM ;

