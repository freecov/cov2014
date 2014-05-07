CREATE TABLE products_offers (
  id int(11) NOT NULL auto_increment,
  customerid int(11) NOT NULL,
  supplierid int(11) NOT NULL,
  `condition` blob NOT NULL,
  terms blob NOT NULL,
  validity int(11) NOT NULL,
  samples blob NOT NULL,
  discount blob NOT NULL,
  remarks blob NOT NULL,
  `date` int(11) NOT NULL,
  header varchar(250) NOT NULL,
  locked varchar(1) NOT NULL,
  PRIMARY KEY  (id)
) 

CREATE TABLE products_offers_prods (
  id int(11) NOT NULL auto_increment,
  offerid int(11) NOT NULL,
  productid int(11) NOT NULL,
  offerprice float(10,2) NOT NULL,
  PRIMARY KEY  (id)
) 

ALTER TABLE `users` ADD `xs_offermanage` SMALLINT( 3 ) NULL AFTER `xs_todomanage` ;

ALTER TABLE `products_offers_prods` ADD `minorder` INT( 11 ) NULL DEFAULT '0';
