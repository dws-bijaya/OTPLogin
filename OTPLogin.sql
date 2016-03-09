SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `otplogin_activations`
--

CREATE TABLE IF NOT EXISTS `otplogin_activations` (
  `userid` int(11) unsigned NOT NULL,
  `activation_code` varchar(32) NOT NULL DEFAULT '',
  `created_on` int(10) unsigned NOT NULL DEFAULT '0',
  `macid` int(11) NOT NULL,
  `expire` int(11) unsigned NOT NULL,
  UNIQUE KEY `userid` (`userid`,`activation_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `otplogin_macs`
--

CREATE TABLE IF NOT EXISTS `otplogin_macs` (
  `macid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `macadd` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `parent` varchar(50) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`macid`),
  UNIQUE KEY `macadd` (`macadd`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `jailkeeper_otp_session`
--

CREATE TABLE IF NOT EXISTS `otplogin_otp_session` (
  `userid` int(11) unsigned NOT NULL,
  `macid` int(11) unsigned NOT NULL DEFAULT '0',
  `otp` int(11) unsigned NOT NULL,
  `expire` int(11) unsigned NOT NULL,
  `ip` varchar(20) NOT NULL,
  `active` enum('1','2','3') NOT NULL DEFAULT '1',
  UNIQUE KEY `userid` (`userid`,`otp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;