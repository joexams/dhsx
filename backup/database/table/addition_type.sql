DROP TABLE IF EXISTS `addition_type`;

CREATE TABLE `addition_type` (
  `id` int(11) NOT NULL auto_increment
  ,`name` varchar(45) default NULL
  ,`description` text
  ,PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
