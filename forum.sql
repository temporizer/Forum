--
-- Database: `forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `parent` enum('yes','no') NOT NULL DEFAULT 'no',
  `placement` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `send_to` int(11) NOT NULL,
  `sent_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(500) NOT NULL,
  `access` int(1) NOT NULL DEFAULT '1',
  `banned_until` int(15) NOT NULL DEFAULT '0',
  `auto_logout` enum('yes','no') NOT NULL DEFAULT 'no',
  `post_count` int(11) NOT NULL DEFAULT '0',
  `solved_count` int(11) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `settings` varchar(20) NOT NULL DEFAULT '1:1:1',
  `description` varchar(1000) DEFAULT NULL,
  `create_date` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;