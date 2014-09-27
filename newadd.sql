alter table `fstk_pro` add column `classification` tinyint(4) null default '1' after `channel`;
CREATE TABLE `fstk_classification`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `ename` varchar(15) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '',
  `intro` varchar(200) DEFAULT NULL,
  `last_update` date NOT NULL COMMENT '',
  `url` varchar(200) DEFAULT NULL,
  `isshow` tinyint(4) NOT NULL DEFAULT '1',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `flag` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
INSERT INTO `fstk_classification` (`id`, `name`, `type`, `last_update`,`rank`) VALUES ('1', '“ª√Î∑Ë«¿', '1', '2014-09-04','1')
INSERT INTO `fstk_classification` (`id`, `name`, `type`, `last_update`,`rank`) VALUES ('2', '“ª√Î∑Ë«¿∂¿º“', '2', '2014-09-04','2');
update fstk_pro set classification=1 where classification is null;