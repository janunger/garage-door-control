DROP TABLE IF EXISTS `command_queue`;
CREATE TABLE `command_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `command` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
