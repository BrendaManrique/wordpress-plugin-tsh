CREATE TABLE IF NOT EXISTS `calendarevents` (
  `eventId` int(5) NOT NULL AUTO_INCREMENT,
  `empId` int(5) NOT NULL DEFAULT '0',
  `isAdmin` int(1) NOT NULL DEFAULT '0',
  `isShared` int(1) NOT NULL DEFAULT '0',
  `isPublic` int(1) NOT NULL DEFAULT '0',
  `startDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventTitle` varchar(50) CHARACTER SET utf8 NOT NULL,
  `eventDesc` text COLLATE utf8_bin,
  PRIMARY KEY (`eventId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `compiled` (
  `compileId` int(5) NOT NULL AUTO_INCREMENT,
  `compliedBy` int(5) NOT NULL,
  `weekNo` int(2) unsigned zerofill NOT NULL,
  `clockYear` int(4) NOT NULL,
  `dateComplied` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`compileId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `documents` (
  `docId` int(5) NOT NULL AUTO_INCREMENT,
  `empId` int(5) NOT NULL,
  `docName` varchar(255) COLLATE utf8_bin NOT NULL,
  `docDesc` longtext COLLATE utf8_bin NOT NULL,
  `docUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `docDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`docId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `employees` (
  `empId` int(5) NOT NULL AUTO_INCREMENT,
  `isAdmin` int(1) NOT NULL DEFAULT '0',
  `isMgr` int(1) NOT NULL DEFAULT '0',
  `empEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `empFirst` varchar(255) CHARACTER SET utf8 NOT NULL,
  `empMiddleInt` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `empLast` varchar(255) CHARACTER SET utf8 NOT NULL,
  `empDob` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `empSsn` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `empAvatar` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT 'empAvatar.png',
  `empPhone1` varchar(255) COLLATE utf8_bin NOT NULL,
  `empPhone2` varchar(255) COLLATE utf8_bin NOT NULL,
  `empPhone3` varchar(255) COLLATE utf8_bin NOT NULL,
  `empAddress1` text COLLATE utf8_bin,
  `empAddress2` text COLLATE utf8_bin,
  `empPosition` varchar(255) CHARACTER SET utf8 NOT NULL,
  `empPayGrade` varchar(255) COLLATE utf8_bin NOT NULL,
  `empStartSalery` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `empStartHourly` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `empCurrSalery` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `empCurrHourly` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `empSalaryTerm` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT 'Year',
  `leaveHours` int(3) NOT NULL DEFAULT '0',
  `empHireDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isActive` int(1) NOT NULL DEFAULT '0',
  `empLastVisited` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `empTerminationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `terminationReason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`empId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `emptasks` (
  `empTaskId` int(5) NOT NULL AUTO_INCREMENT,
  `assignedTo` int(5) NOT NULL DEFAULT '0',
  `createdBy` int(5) NOT NULL DEFAULT '0',
  `taskTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `taskDesc` longtext COLLATE utf8_bin NOT NULL,
  `taskNotes` longtext COLLATE utf8_bin,
  `taskPriority` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `taskStatus` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `taskStart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `taskDue` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isClosed` int(1) NOT NULL DEFAULT '0',
  `dateClosed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`empTaskId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `leaveearned` (
  `earnedId` int(5) NOT NULL AUTO_INCREMENT,
  `empId` int(5) NOT NULL DEFAULT '0',
  `weekNo` int(2) unsigned zerofill NOT NULL,
  `clockYear` int(4) NOT NULL,
  `leaveHours` decimal(3,1) NOT NULL DEFAULT '0.0',
  `dateEntered` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`earnedId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `leavetaken` (
  `takenId` int(5) NOT NULL AUTO_INCREMENT,
  `empId` int(5) NOT NULL DEFAULT '0',
  `clockYear` int(4) NOT NULL,
  `hoursTaken` decimal(3,1) NOT NULL DEFAULT '0.0',
  `dateEntered` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`takenId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notices` (
  `noticeId` int(5) NOT NULL AUTO_INCREMENT,
  `createdBy` int(5) NOT NULL DEFAULT '0',
  `isActive` int(1) NOT NULL DEFAULT '1',
  `noticeTitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `noticeText` longtext COLLATE utf8_bin NOT NULL,
  `noticeDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `noticeStart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `noticeExpires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`noticeId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `privatemessages` (
  `messageId` int(5) NOT NULL AUTO_INCREMENT,
  `fromId` int(5) NOT NULL DEFAULT '0',
  `toId` int(5) NOT NULL,
  `origId` int(5) NOT NULL DEFAULT '0',
  `messageTitle` varchar(50) CHARACTER SET utf8 NOT NULL,
  `messageText` text COLLATE utf8_bin,
  `messageDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `toRead` int(1) NOT NULL DEFAULT '0',
  `toArchived` int(1) NOT NULL DEFAULT '0',
  `toDeleted` int(1) NOT NULL DEFAULT '0',
  `fromDeleted` int(1) NOT NULL DEFAULT '0',
  `lastUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`messageId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sitesettings` (
  `installUrl` varchar(100) COLLATE utf8_bin NOT NULL,
  `localization` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'en',
  `siteName` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessName` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessAddress` longtext COLLATE utf8_bin NOT NULL,
  `businessEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessPhone1` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessPhone2` varchar(255) COLLATE utf8_bin NOT NULL,
  `uploadPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'uploads/',
  `businessDocs` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'documents/',
  `fileTypesAllowed` varchar(255) COLLATE utf8_bin NOT NULL,
  `avatarFolder` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'avatars/',
  `avatarTypes` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'jpg,png,svg',
  `allowRegistrations` int(1) NOT NULL DEFAULT '0',
  `enableTimeEdits` int(1) NOT NULL DEFAULT '0',
  `enablePii` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`installUrl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `timeclock` (
  `clockId` int(5) NOT NULL AUTO_INCREMENT,
  `empId` int(5) NOT NULL DEFAULT '0',
  `weekNo` int(2) unsigned zerofill NOT NULL,
  `clockYear` int(4) NOT NULL,
  `running` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`clockId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timeedits` (
  `editId` int(5) NOT NULL AUTO_INCREMENT,
  `entryId` int(5) NOT NULL,
  `editedBy` int(5) NOT NULL,
  `editedDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origStartTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origEndTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editedStartTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editedEndTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editReason` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`editId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timeentry` (
  `entryId` int(5) NOT NULL AUTO_INCREMENT,
  `clockId` int(5) NOT NULL,
  `empId` int(5) NOT NULL DEFAULT '0',
  `entryDate` date NOT NULL DEFAULT '0000-00-00',
  `startTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entryType` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`entryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;