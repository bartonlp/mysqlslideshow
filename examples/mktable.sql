--
-- Main slide show table
--

drop table if exists mysqlslideshow;

CREATE TABLE `mysqlslideshow` (
 `id` int(11) NOT NULL auto_increment,
 `imageinfo` varchar(255) NOT NULL, /* return from getimagesize(img, extrinfo) serialized */
 `subject` text default NULL,
 `description` text,
 `data` text,
 `created` datetime,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
