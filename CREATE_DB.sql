-- phpMyAdmin SQL Dump
-- version 4.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 04, 2013 at 12:55 PM
-- Server version: 5.1.70-log
-- PHP Version: 5.5.4-pl0-gentoo

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `type` enum('Recap','Coach','Scout','Comment','Rules') NOT NULL DEFAULT 'Comment',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2325 ;

-- --------------------------------------------------------

--
-- Table structure for table `download`
--

CREATE TABLE IF NOT EXISTS `download` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `filename` varchar(50) NOT NULL DEFAULT '',
  `filetype` varchar(20) NOT NULL DEFAULT '',
  `compression` varchar(10) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `showfrom` int(10) unsigned NOT NULL DEFAULT '0',
  `showthru` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=167 ;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE IF NOT EXISTS `game` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `away` smallint(5) unsigned NOT NULL DEFAULT '0',
  `away_hc` smallint(5) unsigned NOT NULL DEFAULT '0',
  `away_sub` smallint(5) unsigned NOT NULL DEFAULT '0',
  `away_rate` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home` smallint(5) unsigned NOT NULL DEFAULT '0',
  `home_hc` smallint(5) unsigned NOT NULL DEFAULT '0',
  `home_sub` smallint(5) unsigned NOT NULL DEFAULT '0',
  `home_rate` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `site` smallint(5) unsigned NOT NULL DEFAULT '0',
  `scheduled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `away_q1` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `away_q2` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `away_q3` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `away_q4` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `away_ot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `away_score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_q1` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_q2` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_q3` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_q4` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_ot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_score` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forecast` varchar(20) NOT NULL DEFAULT '',
  `wind` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `temp` tinyint(4) NOT NULL DEFAULT '0',
  `inserted` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2456 ;

-- --------------------------------------------------------

--
-- Table structure for table `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `href` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `game` smallint(5) unsigned NOT NULL DEFAULT '0',
  `log` blob NOT NULL,
  PRIMARY KEY (`game`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `news` text NOT NULL,
  `top` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=148 ;

-- --------------------------------------------------------

--
-- Table structure for table `nfl`
--

CREATE TABLE IF NOT EXISTS `nfl` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `team` varchar(30) NOT NULL DEFAULT '',
  `nick` varchar(30) NOT NULL DEFAULT '',
  `acro` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `acro` (`acro`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `nfl`
--

INSERT INTO `nfl` (`id`, `team`, `nick`, `acro`) VALUES
(1, 'Buffalo', 'Bills', 'BUF'),
(2, 'Miami', 'Dolphins', 'MIA'),
(3, 'New England', 'Patriots', 'NE'),
(4, 'New York', 'Jets', 'NYJ'),
(5, 'Baltimore', 'Ravens', 'BAL'),
(6, 'Cincinnati', 'Bengals', 'CIN'),
(7, 'Cleveland', 'Browns', 'CLE'),
(8, 'Pittsburgh', 'Steelers', 'PIT'),
(9, 'Houston', 'Texans', 'HOU'),
(10, 'Indianapolis', 'Colts', 'IND'),
(11, 'Jacksonville', 'Jaguars', 'JAX'),
(12, 'Tennessee', 'Titans', 'TEN'),
(13, 'Denver', 'Broncos', 'DEN'),
(14, 'Kansas City', 'Chiefs', 'KC'),
(15, 'Oakland', 'Raiders', 'OAK'),
(16, 'San Diego', 'Chargers', 'SD'),
(17, 'Dallas', 'Cowboys', 'DAL'),
(18, 'New York', 'Giants', 'NYG'),
(19, 'Philadelphia', 'Eagles', 'PHI'),
(20, 'Washington', 'Redskins', 'WAS'),
(21, 'Chicago', 'Bears', 'CHI'),
(22, 'Detroit', 'Lions', 'DET'),
(23, 'Green Bay', 'Packers', 'GB'),
(24, 'Minnesota', 'Vikings', 'MIN'),
(25, 'Atlanta', 'Falcons', 'ATL'),
(26, 'Carolina', 'Panthers', 'CAR'),
(27, 'New Orleans', 'Saints', 'NO'),
(28, 'Tampa Bay', 'Buccaneers', 'TB'),
(29, 'Arizona', 'Cardinals', 'ARI'),
(30, 'St. Louis', 'Rams', 'STL'),
(31, 'San Francisco', '49ers', 'SF'),
(32, 'Seattle', 'Seahawks', 'SEA');

-- --------------------------------------------------------

--
-- Table structure for table `pending`
--

CREATE TABLE IF NOT EXISTS `pending` (
  `game` smallint(5) unsigned NOT NULL DEFAULT '0',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rate` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `log` blob NOT NULL,
  PRIMARY KEY (`game`,`team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pickem`
--

CREATE TABLE IF NOT EXISTS `pickem` (
  `game` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `points` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `winner` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`game`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roster`
--

CREATE TABLE IF NOT EXISTS `roster` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `firstname` varchar(30) NOT NULL DEFAULT '',
  `lastname` varchar(30) NOT NULL DEFAULT '',
  `pos` enum('QB','HB','FB','WR','TE','LT','LG','C','RG','RT','RE','DT','LE','ROLB','MLB','LOLB','CB','FS','SS','K','P') NOT NULL DEFAULT 'QB',
  `ovr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yrl` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tot` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `bon` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sal` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `age` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spd` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `str` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `awr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `agi` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `acc` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `car` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `jmp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `btk` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tak` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tha` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pbk` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rbk` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kpw` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kac` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `imp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sta` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `inj` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tgh` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33244 ;

-- --------------------------------------------------------

--
-- Table structure for table `season`
--

CREATE TABLE IF NOT EXISTS `season` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `pre_weeks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reg_weeks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `post_weeks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `post_names` text NOT NULL,
  `post_teams` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `start` int(10) unsigned NOT NULL DEFAULT '0',
  `week` int(10) unsigned NOT NULL DEFAULT '0',
  `log_begin_offset` int(11) DEFAULT '0',
  `log_end_offset` int(11) DEFAULT '0',
  `individual` text NOT NULL,
  `tiebreaker` text NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `spawn` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_blocking`
--

CREATE TABLE IF NOT EXISTS `stats_blocking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `pancakes` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sacks_allowed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42181 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_defense`
--

CREATE TABLE IF NOT EXISTS `stats_defense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `tot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `loss` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sack` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ff` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `frec` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `int` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ret` smallint(6) NOT NULL DEFAULT '0',
  `deflections` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `safeties` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cth_allow` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `big_hits` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76074 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_kicking`
--

CREATE TABLE IF NOT EXISTS `stats_kicking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `fgm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fga` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fgsblocked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpa` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpsblocked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kickoffs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `touchbacks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6969 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_kick_returns`
--

CREATE TABLE IF NOT EXISTS `stats_kick_returns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `long` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5956 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_passing`
--

CREATE TABLE IF NOT EXISTS `stats_passing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `cmp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `sack` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `int` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `long` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5864 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_punting`
--

CREATE TABLE IF NOT EXISTS `stats_punting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `long` tinyint(4) NOT NULL DEFAULT '0',
  `blocks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `in20` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `touchbacks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4276 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_punt_returns`
--

CREATE TABLE IF NOT EXISTS `stats_punt_returns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `long` tinyint(4) NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5956 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_receiving`
--

CREATE TABLE IF NOT EXISTS `stats_receiving` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `rec` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `long` tinyint(4) NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `drop` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yac` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27369 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_rushing`
--

CREATE TABLE IF NOT EXISTS `stats_rushing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `yds` smallint(6) NOT NULL DEFAULT '0',
  `long` tinyint(4) NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fum` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15686 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_scoring_defense`
--

CREATE TABLE IF NOT EXISTS `stats_scoring_defense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `q1` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `q2` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `q3` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `q4` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpa` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `2pm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `2pa` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fgm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fga` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `safeties` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4205 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_scoring_offense`
--

CREATE TABLE IF NOT EXISTS `stats_scoring_offense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `q1` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `q2` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `q3` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `q4` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ot` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `xpa` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `2pm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `2pa` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fgm` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fga` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `safeties` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4205 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_team_defense`
--

CREATE TABLE IF NOT EXISTS `stats_team_defense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `first_downs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `third_down_conv` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `third_downs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fourth_down_conv` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fourth_downs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `two_pt_conv_made` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `two_pt_conv_att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redzone_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redzone_td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redzone_fg` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rushing_att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rushing_yds` smallint(6) NOT NULL DEFAULT '0',
  `rushing_td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `passing_yds` smallint(6) NOT NULL DEFAULT '0',
  `passing_cmp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `passing_att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `passing_td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `interceptions` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sacks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sack_yds` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fumbles_forced` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fumbles_recovered` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `penalties` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `penalty_yds` smallint(5) unsigned NOT NULL DEFAULT '0',
  `top` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4205 ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_team_offense`
--

CREATE TABLE IF NOT EXISTS `stats_team_offense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `game` int(10) unsigned NOT NULL DEFAULT '0',
  `matchup` varchar(10) NOT NULL DEFAULT '',
  `team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `first_downs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `third_down_conv` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `third_downs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fourth_down_conv` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fourth_downs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `two_pt_conv_made` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `two_pt_conv_att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redzone_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redzone_td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `redzone_fg` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rushing_att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rushing_yds` smallint(6) NOT NULL DEFAULT '0',
  `rushing_td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `passing_yds` smallint(6) NOT NULL DEFAULT '0',
  `passing_cmp` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `passing_att` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `passing_td` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `interceptions` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sacks` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sack_yds` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fumbles` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fumbles_lost` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `penalties` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `penalty_yds` smallint(5) unsigned NOT NULL DEFAULT '0',
  `top` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4205 ;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `season` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `conference` varchar(3) NOT NULL DEFAULT '',
  `division` varchar(10) NOT NULL DEFAULT '',
  `team` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=325 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `nick` varchar(20) NOT NULL DEFAULT '',
  `pwd` varchar(40) NOT NULL DEFAULT '',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '',
  `show_email` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `icq` varchar(10) NOT NULL DEFAULT '',
  `xfire` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `show_ip` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `style` varchar(20) NOT NULL DEFAULT '',
  `logos` varchar(20) NOT NULL DEFAULT '',
  `last_hit` int(10) unsigned NOT NULL DEFAULT '0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('Active','Disabled','Pending') NOT NULL DEFAULT 'Pending',
  `usertext` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick` (`nick`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113 ;

INSERT INTO `user` (`nick`, `pwd`, `admin`, `status`) VALUES
('admin', SHA1('admin'), 1, 'Active');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
