CREATE TABLE `adminGroupAccess` (
  `adminGroupAccessID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminGroupID` INTEGER UNSIGNED NOT NULL,
  `access` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`adminGroupAccessID`),
  INDEX `adminGroupID_access`(`adminGroupID`, `access`),
  INDEX `access`(`access`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `adminGroup` (
  `adminGroupID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`adminGroupID`),
  UNIQUE INDEX `name`(`name`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `adminUserAccess` (
  `adminUserAccessID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminUserID` INTEGER UNSIGNED NOT NULL,
  `access` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`adminUserAccessID`),
  INDEX `adminUserID_access`(`adminUserID`, `access`),
  INDEX `access`(`access`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `adminUserGroupMap` (
  `adminUserID` INTEGER UNSIGNED NOT NULL,
  `adminGroupID` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`adminUserID`, `adminGroupID`),
  INDEX `adminGroupID`(`adminGroupID`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `adminUser` (
  `adminUserID` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(100) NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` text NOT NULL,
  `name` varchar(45) NOT NULL,
  `status` enum('active','inactive') NOT NULL default 'active',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`adminUserID`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `adminUser` VALUES (1, '', 'admin', '541b05d599f0b6a15f3c7d4bb52a6e480ffbc7eef8ede0f67085efcaed03915c0807b868c', 'admin', 'active', NOW());
INSERT INTO `adminUserAccess` (`adminUserID`, `access`) VALUES (1, 'SUPERADMIN');

CREATE TABLE `contentModule` (
  `contentModuleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageID` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  PRIMARY KEY (`contentModuleID`),
  UNIQUE KEY `languageID_name` (`languageID`, `name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contentModuleHistory` (
  `contentModuleHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recordEditor` varchar(10) NOT NULL,
  `recordEditorID` int(10) unsigned NOT NULL,
  `action` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `contentModuleID` int(10) unsigned NOT NULL,
  `languageID` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  `effectiveThrough` datetime NOT NULL,
  PRIMARY KEY (`contentModuleHistoryID`),
  KEY `contentModuleID_lastModified_effectiveThrough` (`contentModuleID`,`lastModified`,`effectiveThrough`),
  KEY `name_lastModified_effectiveThrough` (`name`,`lastModified`,`effectiveThrough`),
  KEY `lastModified_effectiveThrough` (`lastModified`,`effectiveThrough`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `email` (
  `emailID` int(10) unsigned NOT NULL auto_increment,
  `languageID` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `html` text NOT NULL,
  `text` text NOT NULL,
  `fromEmail` varchar(255) NOT NULL,
  `headerID` INTEGER UNSIGNED NOT NULL,
  `footerID` INTEGER UNSIGNED NOT NULL,
  `recipients` TEXT DEFAULT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  PRIMARY KEY  (`emailID`),
  UNIQUE KEY `languageID_name` USING BTREE (`languageID`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `emailHistory` (
  `emailHistoryID` int(10) unsigned NOT NULL auto_increment,
  `recordEditor` varchar(10) NOT NULL,
  `recordEditorID` int(10) unsigned NOT NULL,
  `action` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `emailID` int(11) unsigned NOT NULL,
  `languageID` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `html` text NOT NULL,
  `text` text NOT NULL,
  `fromEmail` varchar(255) NOT NULL,
  `headerID` INTEGER UNSIGNED NOT NULL,
  `footerID` INTEGER UNSIGNED NOT NULL,
  `recipients` TEXT DEFAULT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  `effectiveThrough` datetime NOT NULL,
  PRIMARY KEY  (`emailHistoryID`),
  KEY `emailID` (`emailID`),
  KEY `lastModified_effectiveThrough` (`lastModified`,`effectiveThrough`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `email` (`languageID`, `name`, `subject`, `html`, `text`, `fromEmail`, `headerID`, `footerID`, `dateAdded`, `lastModified`) VALUES (1, 'testEmail', 'This is a test {$testObject.variable}', '{$anotherVariable}', '{$anotherVariable}', 'noreply@example.com', 0, 0, NOW(), NOW());

INSERT INTO `emailHistory` (`recordEditor`, `recordEditorID`, `action`, `comments`, `emailID`, `languageID`, `name`, `subject`, `html`, `text`, `fromEmail`, `headerID`, `footerID`, `dateAdded`, `lastModified`, `effectiveThrough`) SELECT 'SYSTEM', 0, 'SAVE', 'New record', `emailID`, `languageID`, `name`, `subject`, `html`, `text`, `fromEmail`, `headerID`, `footerID`, `dateAdded`, `lastModified`, '9999-12-31 23:59:59' FROM `email`;

CREATE TABLE `emailSection` (
  `emailSectionID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `languageID` int(10) unsigned NOT NULL,
  `type` ENUM('header', 'footer') NOT NULL DEFAULT 'header',
  `name` VARCHAR(100) NOT NULL,
  `html` TEXT NOT NULL,
  `text` TEXT NOT NULL,
  `dateAdded` DATETIME NOT NULL,
  `lastModified` DATETIME NOT NULL,
  PRIMARY KEY (`emailSectionID`),
  UNIQUE INDEX `languageID_type_name`(`languageID`, `type`, `name`),
  INDEX `name`(`name`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `emailSectionHistory` (
  `emailSectionHistoryID` int(11) unsigned NOT NULL auto_increment,
  `recordEditor` varchar(10) NOT NULL,
  `recordEditorID` int(10) unsigned NOT NULL,
  `action` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `emailSectionID` INTEGER UNSIGNED NOT NULL,
  `languageID` int(10) unsigned NOT NULL,
  `type` ENUM('header', 'footer') NOT NULL DEFAULT 'header',
  `name` VARCHAR(100) NOT NULL,
  `html` TEXT NOT NULL,
  `text` TEXT NOT NULL,
  `dateAdded` DATETIME NOT NULL,
  `lastModified` DATETIME NOT NULL,
  `effectiveThrough` datetime NOT NULL,
  PRIMARY KEY (`emailSectionHistoryID`),
  INDEX `emailSectionID`(`emailSectionID`),
  KEY `lastModified_effectiveThrough` (`lastModified`,`effectiveThrough`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `formData` (
  `formDataID` INTEGER UNSIGNED NOT NULL auto_increment,
  `languageID` int(10) unsigned NOT NULL,
  `first` VARCHAR(45) DEFAULT NULL,
  `last` VARCHAR(45) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `type` VARCHAR(255) NOT NULL,
  `date` DATETIME NOT NULL,
  `data` TEXT DEFAULT NULL,
  PRIMARY KEY (`formDataID`),
  INDEX `email`(`email`),
  INDEX `languageID_type_email`(`languageID`, `type`, `email`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `language` (
  `languageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `dateAdded` DATETIME NOT NULL,
  PRIMARY KEY (`languageID`),
  UNIQUE KEY `name` USING BTREE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `language` VALUES (1, 'English', 'http://www.example.com', '/images/flags/english.png', 1, NOW());

CREATE TABLE  `navigation` (
  `navigationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageID` int(10) unsigned NOT NULL,
  `label` varchar(45) NOT NULL,
  `url` varchar(100) NOT NULL,
  `parent` int(10) unsigned NOT NULL,
  `order` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`navigationID`),
  KEY `languageID_parent_order` USING BTREE (`languageID`, `parent`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `page` (
  `pageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageID` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `metaDescription` text NOT NULL,
  `metaKeywords` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `articleDate` datetime DEFAULT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  PRIMARY KEY (`pageID`),
  UNIQUE KEY `languageID_name` (`languageID`, `name`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pageHistory` (
  `pageHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recordEditor` varchar(10) NOT NULL,
  `recordEditorID` int(10) unsigned NOT NULL,
  `action` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `pageID` int(10) unsigned NOT NULL,
  `languageID` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `metaDescription` text NOT NULL,
  `metaKeywords` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `articleDate` datetime DEFAULT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  `effectiveThrough` datetime NOT NULL,
  PRIMARY KEY (`pageHistoryID`),
  KEY `pageID_lastModified_effectiveThrough` (`pageID`,`lastModified`,`effectiveThrough`),
  KEY `name_lastModified_effectiveThrough` (`name`,`lastModified`,`effectiveThrough`),
  KEY `lastModified_effectiveThrough` (`lastModified`,`effectiveThrough`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `page` (`pageID`, `languageID`, `name`, `type`, `title`, `content`, `metaDescription`, `metaKeywords`, `status`, `dateAdded`, `lastModified`) VALUES (1, 1, 'homepage', 'content', 'Homepage', 'Default homepage', '', '', 'active', NOW(), NOW()), (2, 1, 'error_404', 'content', '404 Not Found', 'Page Not Found', '', '', 'active', NOW(), NOW()), (3, 1, 'error_500', 'content', '500 Internal Server Error', 'Internal Server Error', '', '', 'active', NOW(), NOW());

INSERT INTO `pageHistory` (`pageHistoryID`, `recordEditor`, `recordEditorID`, `action`, `comments`, `pageID`, `languageID`, `name`, `type`, `title`, `content`, `summary`, `metaDescription`, `metakeywords`, `status`, `articleDate`, `dateAdded`, `lastModified`, `effectiveThrough`) SELECT '', 'admin', 1, 'SAVE', 'New record', `pageID`, `languageID`, `name`, `type`, `title`, `content`, `summary`, `metaDescription`, `metakeywords`, `status`, `articleDate`, `dateAdded`, `lastModified`, '9999-12-31 23:59:59' FROM `page`;

CREATE TABLE  `session` (
  `session_id` varchar(32) NOT NULL,
  `session_data` longtext NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `siteTag` (
  `siteTagID` int(10) unsigned NOT NULL auto_increment,
  `languageID` int(10) unsigned NOT NULL,
  `referrer` varchar(100) NOT NULL default '',
  `description` varchar(100) NOT NULL default '',
  `matchType` enum('exact match','regular expression', 'explicit call') NOT NULL default 'exact match',
  `matchValue` varchar(255) NOT NULL,
  `placement` enum('header','footer') NOT NULL default 'header',
  `weight` tinyint(3) unsigned NOT NULL,
  `HTTP` text NOT NULL,
  `HTTPS` text NOT NULL,
  `status` enum('active','inactive') NOT NULL default 'active',
  `dateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastModified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`siteTagID`),
  UNIQUE KEY `languageID_referrer_description` (`languageID`, `referrer`,`description`),
  KEY `description` (`description`),
  KEY `dateAdded` (`dateAdded`),
  KEY `matchType` (`matchType`),
  KEY `status_placement` USING BTREE (`status`,`placement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `siteTagHistory` (
  `siteTagsHistoryID` int(10) unsigned NOT NULL auto_increment,
  `recordEditor` varchar(10) NOT NULL,
  `recordEditorID` int(10) unsigned NOT NULL,
  `action` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `siteTagID` int(10) unsigned NOT NULL,
  `languageID` int(10) unsigned NOT NULL,
  `referrer` varchar(100) NOT NULL default '',
  `description` varchar(100) NOT NULL default '',
  `matchType` enum('exact match','regular expression', 'explicit call') NOT NULL default 'exact match',
  `matchValue` varchar(255) NOT NULL,
  `placement` enum('header','footer') NOT NULL default 'header',
  `weight` tinyint(3) unsigned NOT NULL,
  `HTTP` text NOT NULL,
  `HTTPS` text NOT NULL,
  `status` enum('active','inactive') NOT NULL default 'active',
  `dateAdded` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `effectiveThrough` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`siteTagsHistoryID`),
  KEY `siteTagID` (`siteTagID`),
  KEY `lastModified_effectiveThrough` (`lastModified`,`effectiveThrough`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `siteTemplate` (
  `siteTemplateID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageID` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  PRIMARY KEY (`siteTemplateID`),
  KEY `languageID` (`languageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `siteTemplateHistory` (
  `siteTemplateHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recordEditor` varchar(10) NOT NULL,
  `recordEditorID` int(10) unsigned NOT NULL,
  `action` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  `siteTemplateID` int(10) unsigned NOT NULL,
  `languageID` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `dateAdded` datetime NOT NULL,
  `lastModified` datetime NOT NULL,
  `effectiveThrough` datetime NOT NULL,
  PRIMARY KEY (`siteTemplateHistoryID`),
  KEY `siteTemplateID_lastModified_effectiveThrough` (`siteTemplateID`,`lastModified`,`effectiveThrough`),
  KEY `lastModified_effectiveThrough` (`lastModified`,`effectiveThrough`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
