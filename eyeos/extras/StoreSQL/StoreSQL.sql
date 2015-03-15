SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `eyeos`
--

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE IF NOT EXISTS `token` (
  `cloudspaceName` varchar(50) NOT NULL,
  `userID` varchar(128) NOT NULL,
  `tkey` varchar(50) NOT NULL,
  `tsecret` varchar(50) NOT NULL,
  PRIMARY KEY (`cloudspaceName`,`userID`),
  UNIQUE KEY `tkey` (`tkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

