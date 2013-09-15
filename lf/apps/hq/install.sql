--
-- Table structure for table `hq_auditlog`
--

CREATE TABLE `hq_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `category` varchar(128) NOT NULL,
  `open` int(11) NOT NULL,
  `closed` int(11) NOT NULL,
  `backburner` int(11) NOT NULL,
  `critical` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

CREATE TABLE `hq_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `note` text NOT NULL,
  `date` datetime NOT NULL,
  `ticket_id` int(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=latin1;

CREATE TABLE `hq_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `wiki` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

CREATE TABLE `hq_reference` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `owner_id` int(5) NOT NULL,
  `project` varchar(128) NOT NULL,
  `category` varchar(128) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

CREATE TABLE `hq_tickets` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `owner_id` int(5) NOT NULL,
  `project` varchar(128) NOT NULL,
  `category` varchar(128) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `flagged` varchar(64) NOT NULL,
  `assigned` int(11) NOT NULL,
  `replies` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=174 DEFAULT CHARSET=latin1;

