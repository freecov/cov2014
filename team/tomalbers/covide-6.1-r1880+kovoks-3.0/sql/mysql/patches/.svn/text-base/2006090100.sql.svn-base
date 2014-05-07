
-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_abbreviations'
-- 

CREATE TABLE cms_abbreviations (
  id int(11) NOT NULL auto_increment,
  abbreviation varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_banners'
-- 

CREATE TABLE cms_banners (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  image text,
  rating int(11) default NULL,
  url text,
  internal_stat int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_banners_log'
-- 

CREATE TABLE cms_banners_log (
  id int(11) NOT NULL auto_increment,
  bannerid int(11) default NULL,
  `datetime` int(11) default NULL,
  visitor varchar(255) default NULL,
  clicked tinyint(3) default NULL,
  PRIMARY KEY  (id),
  KEY bannerid (bannerid),
  KEY datum (`datetime`),
  KEY bezoeker (visitor)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_banners_summary'
-- 

CREATE TABLE cms_banners_summary (
  id int(11) NOT NULL auto_increment,
  bannerid int(11) NOT NULL default '0',
  datum int(11) NOT NULL default '0',
  bezoekers int(11) NOT NULL default '0',
  uniek int(11) NOT NULL default '0',
  kliks int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY `unique` (id),
  KEY bannerid (bannerid),
  KEY datum (datum)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_counters'
-- 

CREATE TABLE cms_counters (
  id int(11) NOT NULL auto_increment,
  counter1 int(11) default NULL,
  PRIMARY KEY  (id),
  KEY `unique` (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_data'
-- 

CREATE TABLE cms_data (
  id int(11) NOT NULL auto_increment,
  parentPage int(11) NOT NULL default '0',
  pageTitle varchar(255) default NULL,
  pageLabel varchar(255) default NULL,
  datePublication int(11) default '0',
  pageData mediumtext,
  pageRedirect varchar(255) default NULL,
  isPublic tinyint(3) default '1',
  isActive tinyint(3) default '1',
  isMenuItem tinyint(3) default '1',
  keywords varchar(255) default NULL,
  apEnabled tinyint(3) default '0',
  isTemplate tinyint(3) default '0',
  isList tinyint(3) default '0',
  useMetaData tinyint(3) default '0',
  isSticky tinyint(3) default '0',
  search_fields varchar(255) default NULL,
  search_descr varchar(255) default NULL,
  isForm tinyint(3) default '0',
  date_start int(11) default '0',
  date_end int(11) default '0',
  date_changed int(11) default '0',
  notifyManager varchar(50) default '0',
  isGallery tinyint(3) default '0',
  pageRedirectPopup tinyint(3) default NULL,
  popup_data varchar(255) default NULL,
  new_code mediumtext,
  new_state char(2) default NULL,
  search_title varchar(255) default NULL,
  search_language varchar(255) default NULL,
  search_override tinyint(3) default NULL,
  pageAlias varchar(255) default NULL,
  isSpecial varchar(1) default NULL,
  date_last_action int(11) default NULL,
  google_changefreq varchar(255) default 'monthly',
  google_priority varchar(255) default '0.5',
  autosave_info varchar(255) NOT NULL,
  autosave_data mediumtext NOT NULL,
  PRIMARY KEY  (id),
  FULLTEXT KEY ftdata (pageData,pageTitle,pageLabel,pageAlias,search_title,search_fields,search_descr,keywords)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_date'
-- 

CREATE TABLE cms_date (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  date_begin int(11) NOT NULL default '0',
  description text NOT NULL,
  date_end int(11) default NULL,
  repeating varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY `unique` (pageid,date_begin)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_date_index'
-- 

CREATE TABLE cms_date_index (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  dateid int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY `unique` (pageid,`datetime`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_files'
-- 

CREATE TABLE cms_files (
  id int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `type` varchar(255) default 'application/octet-stream',
  size varchar(255) default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_formulieren'
-- 

CREATE TABLE cms_formulieren (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  field_name varchar(255) NOT NULL,
  field_type varchar(255) NOT NULL,
  field_value text NOT NULL,
  is_required tinyint(3) NOT NULL default '0',
  is_mailto tinyint(3) NOT NULL default '0',
  is_mailfrom tinyint(3) NOT NULL default '0',
  is_mailsubject tinyint(3) NOT NULL default '0',
  is_redirect tinyint(3) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_gallery'
-- 

CREATE TABLE cms_gallery (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  gallerytype smallint(3) NOT NULL default '0',
  cols int(11) NOT NULL default '0',
  `rows` int(11) NOT NULL default '0',
  thumbsize int(11) NOT NULL default '0',
  bigsize int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_gallery_photos'
-- 

CREATE TABLE cms_gallery_photos (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  `file` text NOT NULL,
  description text NOT NULL,
  `order` int(11) NOT NULL default '0',
  cachefile varchar(255) NOT NULL,
  count int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_images'
-- 

CREATE TABLE cms_images (
  id int(11) NOT NULL auto_increment,
  page_id int(11) NOT NULL default '0',
  path varchar(255) default NULL,
  description varchar(255) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_languages'
-- 

CREATE TABLE cms_languages (
  id int(11) NOT NULL auto_increment,
  filename varchar(255) default NULL,
  text_nl varchar(255) default NULL,
  text_uk varchar(255) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_license'
-- 

CREATE TABLE cms_license (
  cms_name varchar(255) NOT NULL,
  cms_meta tinyint(3) NOT NULL default '0',
  cms_license varchar(255) NOT NULL,
  cms_date int(11) NOT NULL default '0',
  cms_external text,
  search_fields text,
  search_descr varchar(255) default NULL,
  cms_forms tinyint(3) NOT NULL default '0',
  cms_list tinyint(3) NOT NULL default '0',
  cms_linkchecker_url varchar(255) default NULL,
  search_author varchar(255) default NULL,
  search_copyright varchar(255) default NULL,
  search_email varchar(255) default NULL,
  cms_changelist tinyint(3) NOT NULL default '0',
  cms_banners tinyint(3) NOT NULL default '0',
  cms_searchengine tinyint(3) NOT NULL default '0',
  db_version int(11) NOT NULL default '0',
  cms_gallery tinyint(3) NOT NULL default '0',
  website_url text,
  site_stylesheet text,
  cms_versioncontrol tinyint(3) default NULL,
  search_use_pagetitle tinyint(3) default NULL,
  search_language varchar(255) default NULL,
  cms_page_elements tinyint(3) NOT NULL default '1',
  cms_permissions tinyint(3) NOT NULL default '1',
  multiple_sitemaps tinyint(3) default NULL,
  google_verify varchar(255) default NULL,
  cms_linkchecker tinyint(3) NOT NULL,
  cms_hostnames text NOT NULL,
  cms_defaultpage int(11) NOT NULL,
  PRIMARY KEY  (cms_license)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_list'
-- 

CREATE TABLE cms_list (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  `query` text NOT NULL,
  `fields` text NOT NULL,
  `order` varchar(255) NOT NULL,
  count int(11) NOT NULL default '0',
  listposition varchar(50) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_logins_log'
-- 

CREATE TABLE cms_logins_log (
  id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  `datetime` int(11) NOT NULL default '0',
  user_agent varchar(255) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_metadata'
-- 

CREATE TABLE cms_metadata (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL default '0',
  fieldid int(11) NOT NULL default '0',
  `value` text NOT NULL,
  isDefault tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_metadef'
-- 

CREATE TABLE cms_metadef (
  id int(11) NOT NULL auto_increment,
  field_name varchar(255) NOT NULL,
  field_type varchar(20) NOT NULL,
  field_value text NOT NULL,
  `order` int(11) NOT NULL default '0',
  `group` varchar(255) default NULL,
  fphide tinyint(3) default NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_permissions'
-- 

CREATE TABLE cms_permissions (
  id int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  uid varchar(50) NOT NULL default '0',
  editRight int(11) NOT NULL default '0',
  viewRight int(11) NOT NULL default '0',
  deleteRight int(11) NOT NULL default '0',
  manageRight int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY pid (pid),
  KEY uid (uid)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_siteviews'
-- 

CREATE TABLE cms_siteviews (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL,
  `view` mediumtext NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_temp'
-- 

CREATE TABLE cms_temp (
  id int(11) NOT NULL auto_increment,
  pageid int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `fields` text NOT NULL,
  order1 varchar(255) NOT NULL,
  order2 varchar(255) NOT NULL,
  order3 varchar(255) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_templates'
-- 

CREATE TABLE cms_templates (
  id int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  `data` longtext,
  category varchar(10) NOT NULL,
  PRIMARY KEY  (id)
);

-- --------------------------------------------------------

-- 
-- Table structure for table 'cms_users'
-- 

CREATE TABLE cms_users (
  id int(11) NOT NULL auto_increment,
  username varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  is_enabled tinyint(3) NOT NULL,
  PRIMARY KEY  (id)
);

ALTER TABLE license ADD has_cms INT(11);
ALTER TABLE users ADD xs_cms_level INT(11);

