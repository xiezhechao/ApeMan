# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.27)
# Database: metronic
# Generation Time: 2015-11-09 07:43:29 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table sys_admin_auth_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_admin_auth_group`;

CREATE TABLE `sys_admin_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '角色名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '角色状态 1：开启 2：关闭',
  `rules` char(80) NOT NULL DEFAULT '' COMMENT '角色拥有的权限规则id 多权限规则id用 , 连接',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_admin_auth_group` WRITE;
/*!40000 ALTER TABLE `sys_admin_auth_group` DISABLE KEYS */;

INSERT INTO `sys_admin_auth_group` (`id`, `title`, `status`, `rules`)
VALUES
	(1,'游客',1,'1'),
	(2,'运营',1,'1,3');

/*!40000 ALTER TABLE `sys_admin_auth_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_admin_auth_group_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_admin_auth_group_access`;

CREATE TABLE `sys_admin_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT '对应用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '对应角色id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_admin_auth_group_access` WRITE;
/*!40000 ALTER TABLE `sys_admin_auth_group_access` DISABLE KEYS */;

INSERT INTO `sys_admin_auth_group_access` (`uid`, `group_id`)
VALUES
	(3,2);

/*!40000 ALTER TABLE `sys_admin_auth_group_access` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_admin_auth_rule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_admin_auth_rule`;

CREATE TABLE `sys_admin_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id 自增',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '权限规则标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '权限规则名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '权限规则类型 1：url 2：菜单',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '权限规则状态 1：开启 2：关闭',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '权限规则条件',
  `pid` int(11) NOT NULL DEFAULT '1' COMMENT '上级模块',
  `menu_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单类型 0：不为菜单 1：左侧菜单 2：顶部菜单 3：底部菜单',
  `icon` varchar(20) NOT NULL DEFAULT 'icon-home' COMMENT 'icon class属性',
  `sort` int(11) NOT NULL DEFAULT '0',
  `is_header` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为菜单头部 0 否 1是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_admin_auth_rule` WRITE;
/*!40000 ALTER TABLE `sys_admin_auth_rule` DISABLE KEYS */;

INSERT INTO `sys_admin_auth_rule` (`id`, `name`, `title`, `type`, `status`, `condition`, `pid`, `menu_type`, `icon`, `sort`, `is_header`)
VALUES
	(1,'Admin/Index/index','首页',1,1,'',0,1,'icon-home',0,0),
	(2,'','系统设置',1,1,'',0,1,'icon-home',0,0),
	(3,'Admin/System/core','核心设置',1,1,'',2,1,'icon-home',0,0);

/*!40000 ALTER TABLE `sys_admin_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_admin_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_admin_user`;

CREATE TABLE `sys_admin_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(5) NOT NULL DEFAULT '' COMMENT '附加码',
  `nick_name` varchar(20) DEFAULT NULL COMMENT '昵称',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱地址',
  `phone` char(11) DEFAULT NULL COMMENT '手机号码',
  `country` varchar(20) DEFAULT NULL COMMENT '国家',
  `city` varchar(20) DEFAULT NULL COMMENT '城市',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `street` varchar(100) DEFAULT NULL COMMENT '街道',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登陆时间',
  `last_login_ip` int(11) DEFAULT NULL COMMENT '最后登陆IP',
  `login_count` int(11) DEFAULT NULL COMMENT '登陆次数',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `head_portrait` varchar(200) DEFAULT NULL COMMENT '头像',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `sys_admin_user` WRITE;
/*!40000 ALTER TABLE `sys_admin_user` DISABLE KEYS */;

INSERT INTO `sys_admin_user` (`id`, `account`, `password`, `salt`, `nick_name`, `email`, `phone`, `country`, `city`, `district`, `street`, `last_login_time`, `last_login_ip`, `login_count`, `create_time`, `head_portrait`)
VALUES
	(1,'admin','e6c8bbb83fc3e5f5da4c5a1616e70166','j8968','超级管理员','1413207722@qq.com',NULL,'','',NULL,'',1447051840,2130706433,10,1446828520,NULL),
	(3,'test','f276e27d5ab1144d5a6919f84f71b93f','cTXrr','运营','',NULL,'','',NULL,'',1447031692,2130706433,5,1446868652,NULL),
	(4,'','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `sys_admin_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
