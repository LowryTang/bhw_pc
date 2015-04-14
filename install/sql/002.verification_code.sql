DROP TABLE IF EXISTS `kz_verification_code`;
CREATE TABLE `kz_verification_code` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`tel` varchar(20) NOT NULL COMMENT '手机号',
	`class` varchar(50) NOT NULL default 'registration' COMMENT '验证码分类',
	`code` int(11) unsigned NOT NULL COMMENT '验证码',
	`create_time` int(20) unsigned NOT NULL COMMENT '注册码生成时间',
	PRIMARY KEY  (`id`),
	index (`tel`,`create_time`)   
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='短信验证码记录表'
