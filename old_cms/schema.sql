CREATE TABLE IF NOT EXISTS `files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` longtext NOT NULL,
  `Size` int(11) NOT NULL,
  `Mime` varchar(255) NOT NULL,
  `Is_img` int(11) NOT NULL,
  `Timestamp` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;


CREATE TABLE IF NOT EXISTS `gallery` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Set` int(11) NOT NULL,
  `File` longtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;


CREATE TABLE IF NOT EXISTS `gallery_set` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` longtext NOT NULL,
  `Clean_title` longtext NOT NULL,
  `Category` int(11) NOT NULL,
  `Owner` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;


CREATE TABLE IF NOT EXISTS `members` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` longtext NOT NULL,
  `Clean_title` longtext NOT NULL,
  `Content` longtext NOT NULL,
  `Image` longtext NOT NULL,
  `Category` int(11) NOT NULL,
  `Gallery` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;


CREATE TABLE IF NOT EXISTS `member_category` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` longtext NOT NULL,
  `Clean_name` longtext NOT NULL,
  `Image` longtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;


CREATE TABLE IF NOT EXISTS `pages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` longtext NOT NULL,
  `Clean_title` longtext NOT NULL,
  `Content` longtext NOT NULL,
  `Internal` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;


CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_name` varchar(255) NOT NULL,
  `Ppal_email` longtext NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Salt` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL DEFAULT '0',
  `Affil_id` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

