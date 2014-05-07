ALTER TABLE `address` ADD INDEX address_classification ( `classification` ) ;
ALTER TABLE `address_businesscards` ADD INDEX addressbcards_classification ( `classification` ) ;
ALTER TABLE `address_businesscards` ADD INDEX addressbcards_addressid ( `address_id` ) ;
ALTER TABLE `address_info` ADD INDEX addressinfo_classification ( `classification` ) ;
ALTER TABLE `address_info` ADD INDEX addressinfo_addressid ( `address_id` ) ;