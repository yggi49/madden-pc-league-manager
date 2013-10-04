<?php
/**
 * @(#) standings.php

FFML TIE BREAKERS:

array (
  'div' =>
  array(
    'two' =>
    array (
      array('_head2head',          array('H2H',  'Head to Head')),
      array('_divisionRecord',     array('DIV',  'Division Record')),
      array('_commonGames',        array('CG',   'Common Games')),
      array('_conferenceRecord',   array('CONF', 'Conference Record'), array('equal' => 1)),
      array('_pointsAgainst',      array('PA',   'Points Against')),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
    'more' =>
    array(
      array('_head2head',          array('H2H',  'Head to Head')),
      array('_divisionRecord',     array('DIV',  'Division Record')),
      array('_commonGames',        array('CG',   'Common Games')),
      array('_conferenceRecord',   array('CONF', 'Conference Record'), array('equal' => 1)),
      array('_pointsAgainst',      array('PA',   'Points Against')),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
  ),
  'conf' =>
  array (
    'two' =>
    array (
      array('_head2head',          array('H2H',  'Head to Head')),
      array('_commonGames',        array('CG',   'Common Games'), array('min' => 4)),
      array('_conferenceRecord',   array('CONF', 'Conference Record')),
      array('_pointsAgainst',      array('PA',   'Points Against')),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
    'more' =>
    array (
      array('_head2head',          array('H2H',  'Head to Head'), array('sweep' => 1)),
      array('_commonGames',        array('CG',   'Common Games'), array('min' => 4)),
      array('_conferenceRecord',   array('CONF', 'Conference Record')),
      array('_pointsAgainst',      array('PA',   'Points Against')),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
  ),
)

SERIALIZED: a:2:{s:3:"div";a:2:{s:3:"two";a:8:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:3:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}i:2;a:1:{s:5:"equal";i:1;}}i:4;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:5;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:6;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:7;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}s:4:"more";a:8:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:3:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}i:2;a:1:{s:5:"equal";i:1;}}i:4;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:5;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:6;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:7;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}}s:4:"conf";a:2:{s:3:"two";a:7:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:2;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:3;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:6;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}s:4:"more";a:7:{i:0;a:3:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}i:2;a:1:{s:5:"sweep";i:1;}}i:1;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:2;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:3;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:6;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}}}

================================================================================

NFL TIE BREAKERS:

array(
  'div' =>
  array(
    'two' =>
    array(
      array('_head2head',          array('H2H',  'Head to Head')),
      array('_divisionRecord',     array('DIV',  'Division Record')),
      array('_commonGames',        array('CG',   'Common Games')),
      array('_conferenceRecord',   array('CONF', 'Conference Record')),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_pointsRankingConf',  array('PCNF', 'Best combined ranking among conference teams in points scored and points allowed')),
      array('_pointsRankingAll',   array('PALL', 'Best combined ranking among all teams in points scored and points allowed')),
      array('_pointsCommonGames',  array('PCG',  'Best net points in common games')),
      array('_pointsAllGames',     array('PTS',  'Best net points in all games')),
      array('_touchdowns',         array('TD',   'Best net touchdowns in all games')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
    'more' =>
    array(
      array('_head2head',          array('H2H',  'Head to Head')),
      array('_divisionRecord',     array('DIV',  'Division Record')),
      array('_commonGames',        array('CG',   'Common Games')),
      array('_conferenceRecord',   array('CONF', 'Conference Record')),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_pointsRankingConf',  array('PCNF', 'Best combined ranking among conference teams in points scored and points allowed')),
      array('_pointsRankingAll',   array('PALL', 'Best combined ranking among all teams in points scored and points allowed')),
      array('_pointsCommonGames',  array('PCG',  'Best net points in common games')),
      array('_pointsAllGames',     array('PTS',  'Best net points in all games')),
      array('_touchdowns',         array('TD',   'Best net touchdowns in all games')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
  ),
  'conf' =>
  array(
    'two' =>
    array(
      array('_head2head',          array('H2H',  'Head to Head')),
      array('_conferenceRecord',   array('CONF', 'Conference Record')),
      array('_commonGames',        array('CG',   'Common Games'), array('min' => 4)),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_pointsRankingConf',  array('PCNF', 'Best combined ranking among conference teams in points scored and points allowed')),
      array('_pointsRankingAll',   array('PALL', 'Best combined ranking among all teams in points scored and points allowed')),
      array('_pointsConfGames',    array('PCFG', 'Best net points in conference games')),
      array('_pointsAllGames',     array('PTS',  'Best net points in all games')),
      array('_touchdowns',         array('TD',   'Best net touchdowns in all games')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
    'more' =>
    array(
      array('_head2head',          array('H2H',  'Head to Head'), array('sweep' => 1)),
      array('_conferenceRecord',   array('CONF', 'Conference Record')),
      array('_commonGames',        array('CG',   'Common Games'), array('min' => 4)),
      array('_strengthOfVictory',  array('SOV',  'Strength of Victory')),
      array('_strengthOfSchedule', array('SOS',  'Strength of Schedule')),
      array('_pointsRankingConf',  array('PCNF', 'Best combined ranking among conference teams in points scored and points allowed')),
      array('_pointsRankingAll',   array('PALL', 'Best combined ranking among all teams in points scored and points allowed')),
      array('_pointsConfGames',    array('PCFG', 'Best net points in conference games')),
      array('_pointsAllGames',     array('PTS',  'Best net points in all games')),
      array('_touchdowns',         array('TD',   'Best net touchdowns in all games')),
      array('_coinToss',           array('COIN', 'Coin Toss')),
    ),
  ),
)

SERIALIZED: a:2:{s:3:"div";a:2:{s:3:"two";a:12:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:6;a:2:{i:0;s:18:"_pointsRankingConf";i:1;a:2:{i:0;s:4:"PCNF";i:1;s:80:"Best combined ranking among conference teams in points scored and points allowed";}}i:7;a:2:{i:0;s:17:"_pointsRankingAll";i:1;a:2:{i:0;s:4:"PALL";i:1;s:73:"Best combined ranking among all teams in points scored and points allowed";}}i:8;a:2:{i:0;s:18:"_pointsCommonGames";i:1;a:2:{i:0;s:3:"PCG";i:1;s:31:"Best net points in common games";}}i:9;a:2:{i:0;s:15:"_pointsAllGames";i:1;a:2:{i:0;s:3:"PTS";i:1;s:28:"Best net points in all games";}}i:10;a:2:{i:0;s:11:"_touchdowns";i:1;a:2:{i:0;s:2:"TD";i:1;s:32:"Best net touchdowns in all games";}}i:11;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}s:4:"more";a:12:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:6;a:2:{i:0;s:18:"_pointsRankingConf";i:1;a:2:{i:0;s:4:"PCNF";i:1;s:80:"Best combined ranking among conference teams in points scored and points allowed";}}i:7;a:2:{i:0;s:17:"_pointsRankingAll";i:1;a:2:{i:0;s:4:"PALL";i:1;s:73:"Best combined ranking among all teams in points scored and points allowed";}}i:8;a:2:{i:0;s:18:"_pointsCommonGames";i:1;a:2:{i:0;s:3:"PCG";i:1;s:31:"Best net points in common games";}}i:9;a:2:{i:0;s:15:"_pointsAllGames";i:1;a:2:{i:0;s:3:"PTS";i:1;s:28:"Best net points in all games";}}i:10;a:2:{i:0;s:11:"_touchdowns";i:1;a:2:{i:0;s:2:"TD";i:1;s:32:"Best net touchdowns in all games";}}i:11;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}}s:4:"conf";a:2:{s:3:"two";a:11:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:2;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:3;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:4;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:5;a:2:{i:0;s:18:"_pointsRankingConf";i:1;a:2:{i:0;s:4:"PCNF";i:1;s:80:"Best combined ranking among conference teams in points scored and points allowed";}}i:6;a:2:{i:0;s:17:"_pointsRankingAll";i:1;a:2:{i:0;s:4:"PALL";i:1;s:73:"Best combined ranking among all teams in points scored and points allowed";}}i:7;a:2:{i:0;s:16:"_pointsConfGames";i:1;a:2:{i:0;s:4:"PCFG";i:1;s:35:"Best net points in conference games";}}i:8;a:2:{i:0;s:15:"_pointsAllGames";i:1;a:2:{i:0;s:3:"PTS";i:1;s:28:"Best net points in all games";}}i:9;a:2:{i:0;s:11:"_touchdowns";i:1;a:2:{i:0;s:2:"TD";i:1;s:32:"Best net touchdowns in all games";}}i:10;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}s:4:"more";a:11:{i:0;a:3:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}i:2;a:1:{s:5:"sweep";i:1;}}i:1;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:2;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:3;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:4;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:5;a:2:{i:0;s:18:"_pointsRankingConf";i:1;a:2:{i:0;s:4:"PCNF";i:1;s:80:"Best combined ranking among conference teams in points scored and points allowed";}}i:6;a:2:{i:0;s:17:"_pointsRankingAll";i:1;a:2:{i:0;s:4:"PALL";i:1;s:73:"Best combined ranking among all teams in points scored and points allowed";}}i:7;a:2:{i:0;s:16:"_pointsConfGames";i:1;a:2:{i:0;s:4:"PCFG";i:1;s:35:"Best net points in conference games";}}i:8;a:2:{i:0;s:15:"_pointsAllGames";i:1;a:2:{i:0;s:3:"PTS";i:1;s:28:"Best net points in all games";}}i:9;a:2:{i:0;s:11:"_touchdowns";i:1;a:2:{i:0;s:2:"TD";i:1;s:32:"Best net touchdowns in all games";}}i:10;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}}}

================================================================================

NFL-TIE-BREAKERS:

TO BREAK A TIE WITHIN A DIVISION

If, at the end of the regular season, two or more clubs in the same division
finish with identical won-lost-tied percentages, the following steps will be
taken until a champion is determined.

Two Clubs

1. Head-to-head (best won-lost-tied percentage in games between the clubs).
2. Best won-lost-tied percentage in games played within the division.
3. Best won-lost-tied percentage in common games.
4. Best won-lost-tied percentage in games played within the conference.
5. Strength of victory.
6. Strength of schedule.
7. Best combined ranking among conference teams in points scored and points allowed.
8. Best combined ranking among all teams in points scored and points allowed.
9. Best net points in common games.
10. Best net points in all games.
11. Best net touchdowns in all games.

Three or More Clubs

(Note: If two clubs remain tied after third or other clubs are eliminated
during any step, tie breaker reverts to step 1 of the two-club format).

1. Head-to-head (best won-lost-tied percentage in games among the clubs).
2. Best won-lost-tied percentage in games played within the division.
3. Best won-lost-tied percentage in common games.
4. Best won-lost-tied percentage in games played within the conference.
5. Strength of victory.
6. Strength of schedule.
7. Best combined ranking among conference teams in points scored and points allowed.
8. Best combined ranking among all teams in points scored and points allowed.
9. Best net points in common games.
10. Best net points in all games.
11. Best net touchdowns in all games.

TO BREAK A TIE FOR THE WILD-CARD TEAM

If it is necessary to break ties to determine the two Wild-Card clubs from
each conference, the following steps will be taken.

1. If the tied clubs are from the same division, apply division tie breaker.
2. If the tied clubs are from different divisions, apply the following steps.

Two Clubs

1. Head-to-head, if applicable.
2. Best won-lost-tied percentage in games played within the conference.
3. Best won-lost-tied percentage in common games, minimum of four.
4. Strength of victory.
5. Strength of schedule.
6. Best combined ranking among conference teams in points scored and points allowed.
7. Best combined ranking among all teams in points scored and points allowed.
8. Best net points in conference games.
9. Best net points in all games.
10. Best net touchdowns in all games.
11. Coin toss.

Three or More Clubs

(Note: If two clubs remain tied after third or other clubs are eliminated, tie
breaker reverts to step 1 of applicable two-club format.)

1. Apply division tie breaker to eliminate all but the highest ranked club in
   each division prior to proceeding to step 2. The original seeding within a
   division upon application of the division tie breaker remains the same for
   all subsequent applications of the procedure that are necessary to identify
   the two Wild-Card participants.
2. Head-to-head sweep. (Applicable only if one club has defeated each of the
   others or if one club has lost to each of the others.)
3. Best won-lost-tied percentage in games played within the conference.
4. Best won-lost-tied percentage in common games, minimum of four.
5. Strength of victory.
6. Strength of schedule.
7. Best combined ranking among conference teams in points scored and points allowed.
8. Best combined ranking among all teams in points scored and points allowed.
9. Best net points in conference games.
10. Best net points in all games.
11. Best net touchdowns in all games.
12. Coin toss

When the first Wild-Card team has been identified, the procedure is repeated
to name the second Wild-Card, i.e., eliminate all but the highest-ranked club
in each division prior to proceeding to step 2. In situations where three or
more teams from the same division are involved in the procedure, the original
seeding of the teams remains the same for subsequent applications of the tie
breaker if the top-ranked team in that division qualifies for a Wild-Card
berth.

OTHER TIE-BREAKING PROCEDURES

1. Only one club advances to the playoffs in any tie-breaking step. Remaining
   tied clubs revert to the first step of the applicable division or Wild-Card
   tie breakers. As an example, if two clubs remain tied in any tie-breaker
   step after all other clubs have been eliminated, the procedure reverts to
   step one of the two-club format to determine the winner. When one club wins
   the tie breaker, all other clubs revert to step 1 of the applicable
   two-club or three-club format.

2. In comparing division and conference records or records against common
   opponents among tied teams, the best won-lost-tied percentage is the
   deciding factor since teams may have played an unequal number of games.

3. To determine home-field priority among division titlists, apply Wild-Card
   tie breakers.

4. To determine home-field priority for Wild-Card qualifiers, apply division
   tie breakers (if teams are from the same division) or Wild-Card tie
   breakers (if teams are from different ivisions).

 */

class Page {


  var $period = 'reg';
  var $weeks = '';
  var $standings;

  function Page() {
      global $_SYS;
      mt_srand($_SYS['season'][$_SYS['request']['season']]['start']);
  } // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function array_partition($array) {

      if (!is_array($array)) {
          return false;
      }

      $partition = array();

      foreach ($array as $key => $val) {
          $partition[$val][$key] = $val;
      }

      return array_values($partition);
  }


  function debug($string) {
      global $_SYS;

      if ($_SYS['user']['nick'] == 'igor' || strpos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
          echo $string;
      }
  }


  function _playoffAdvancement($tie, $params=array()) {
      global $_SYS;

      $teams = array_keys($tie);
      $po = array();

      foreach ($teams as $team) {
          $query = 'SELECT MAX(week) AS week
                    FROM   '.$_SYS['table']['game'].'
                    WHERE  season = '.$_SYS['request']['season'].'
                           AND (home = '.$team.' OR away = '.$team.')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();
          $po[$team] = sprintf('%04d', $row['week']);
      }

      asort($po);
      return $po;
  }


  function _head2head($tie, $params=array()) {
      $teams = array_keys($tie);
      $h2h = array();

      if ($params['sweep']) {
          foreach ($teams as $team) {
              $h2h[$team] = '0.500';
              $won = 0;
              $lost = 0;
              $tied = 0;
              $pct = 0;

              foreach ($teams as $opp) {
                  if ($team == $opp) {
                      continue;
                  }

                  if (!$tie[$team]['hth'][$opp] ||
                      $tie[$team]['hth'][$opp]['won'] + $tie[$team]['hth'][$opp]['lost'] + $tie[$team]['hth'][$opp]['tied'] == 0) {
                      continue 2;
                  }

                  $won += $tie[$team]['hth'][$opp]['won'];
                  $lost += $tie[$team]['hth'][$opp]['lost'];
                  $tied += $tie[$team]['hth'][$opp]['tied'];
                  $pct = ($won + $tied / 2) / ($won + $lost + $tied);

                  if ($pct != 1 && $pct != 0) {
                      continue 2;
                  }
              }

              $h2h[$team] = $pct == 1 ? '1.000' : '0.000';
          }
      }
      else {
          foreach ($teams as $team) {
              $h2h[$team] = array('won' => 0, 'lost' => 0, 'tied' => 0);

              foreach ($teams as $opp) {
                  if ($team == $opp) {
                      continue;
                  }

                  if ($tie[$team]['hth'][$opp]) {
                      $h2h[$team]['won'] += $tie[$team]['hth'][$opp]['won'];
                      $h2h[$team]['lost'] += $tie[$team]['hth'][$opp]['lost'];
                      $h2h[$team]['tied'] += $tie[$team]['hth'][$opp]['tied'];
                  }
              }
          }

          foreach (array_keys($h2h) as $team) {
              $h2h[$team] = $h2h[$team]['won'] + $h2h[$team]['tied'] + $h2h[$team]['lost']
                  ? ($h2h[$team]['won'] + $h2h[$team]['tied'] / 2) / ($h2h[$team]['won'] + $h2h[$team]['tied'] + $h2h[$team]['lost'])
                  : 0;
              $h2h[$team] = sprintf('%.3f', $h2h[$team]);
          }
      }

      arsort($h2h);
      return $h2h;
  }


  function _divisionRecord($tie, $params=array()) {
      $teams = array_keys($tie);
      $div = array();

      foreach ($teams as $team) {
          $div[$team] = $tie[$team]['div_pct']
                      ? $tie[$team]['div_pct']
                      : 0;
          $div[$team] = sprintf('%.3f', $div[$team]);
      }

      arsort($div);
      return $div;
  }


  function _commonGames($tie, $params=array()) {
      $teams = array_keys($tie);
      $cg = array();

      $common = $tie[$teams[0]]['opponents'];

      foreach ($teams as $team) {
          $cg[$team] = array('won' => 0, 'lost' => 0, 'tied' => 0);
          $common = array_unique(array_intersect($common, $tie[$team]['opponents']));
      }

      foreach ($teams as $team) {
          foreach ($common as $opp) {
              $cg[$team]['won'] += $tie[$team]['hth'][$opp]['won'];
              $cg[$team]['lost'] += $tie[$team]['hth'][$opp]['lost'];
              $cg[$team]['tied'] += $tie[$team]['hth'][$opp]['tied'];
          }
      }

      $skip = 0;

      if ($params['min']) {
          foreach ($teams as $team) {
              if ($cg[$team]['won'] + $cg[$team]['tied'] + $cg[$team]['lost'] < $params['min']) {
                  $skip = 1;
                  break;
              }
          }
      }

      foreach (array_keys($cg) as $team) {
          if ($skip) {
              $cg[$team] = '0.500';
          }
          else {
              $cg[$team] = $cg[$team]['won'] + $cg[$team]['tied'] + $cg[$team]['lost']
                          ? ($cg[$team]['won'] + $cg[$team]['tied'] / 2) / ($cg[$team]['won'] + $cg[$team]['tied'] + $cg[$team]['lost'])
                          : 0;
              $cg[$team] = sprintf('%.3f', $cg[$team]);
          }
      }

      arsort($cg);
      return $cg;
  }


  function _conferenceRecord($tie, $params=array()) {
      $teams = array_keys($tie);
      $conf = array();
      $games = array();

      foreach ($teams as $team) {
          $games[$team] = $tie[$team]['conf_won'] + $tie[$team]['conf_lost'] + $tie[$team]['conf_tied'];
          $conf[$team] = $tie[$team]['conf_pct']
                      ? $tie[$team]['conf_pct']
                      : 0;
          $conf[$team] = sprintf('%.3f', $conf[$team]);
      }

      if ($params['equal'] && count(array_unique($games)) != 1) {
          foreach ($teams as $team) {
              $conf[$team] = '0.500';
          }
      }

      arsort($conf);
      return $conf;
  }


  function _pointsAgainst($tie, $params=array()) {
      $teams = array_keys($tie);
      $pa = array();

      foreach ($teams as $team) {
          $pa[$team] = sprintf('%04d', $tie[$team]['pts_against']);
      }

      asort($pa);
      return $pa;
  }


  function _strengthOfVictory($tie, $params=array()) {
      $teams = array_keys($tie);
      $sov = array();

      foreach ($teams as $team) {
          $sov[$team] = $tie[$team]['sov_pct']
                      ? $tie[$team]['sov_pct']
                      : 0;
          $sov[$team] = sprintf('%.3f', $sov[$team]);
      }

      arsort($sov);
      return $sov;
  }


  function _strengthOfSchedule($tie, $params=array()) {
      $teams = array_keys($tie);
      $sos = array();

      foreach ($teams as $team) {
          $sos[$team] = $tie[$team]['sos_pct']
                      ? $tie[$team]['sos_pct']
                      : 0;
          $sos[$team] = sprintf('%.3f', $sos[$team]);
      }

      if ($params['draft']) {
          asort($sos);
      }
      else {
          arsort($sos);
      }

      return $sos;
  }


  function _pointsRankingConf($tie, $params=array()) {
      $teams = array_keys($tie);
      $rank = array();
      $ranking_points_scored = array();
      $ranking_points_allowed = array();

      foreach ($teams as $team) {
          if (!$ranking_points_scored[$tie[$team]['conference']]) {
              $ranking_points_scored[$tie[$team]['conference']] = array();

              foreach ($this->standings as $_team) {
                  $ranking_points_scored[$_team['conference']][$_team['id']] = $_team['pts_for'];
              }
          }

          if (!$ranking_points_allowed[$tie[$team]['conference']]) {
              $ranking_points_allowed[$tie[$team]['conference']] = array();

              foreach ($this->standings as $_team) {
                  $ranking_points_allowed[$_team['conference']][$_team['id']] = $_team['pts_against'];
              }
          }

          $rank_scored = 1;

          foreach ($ranking_points_scored[$tie[$team]['conference']] as $points) {
              if ($points > $tie[$team]['pts_for']) {
                  ++$rank_scored;
              }
          }

          $rank_allowed = 1;

          foreach ($ranking_points_allowed[$tie[$team]['conference']] as $points) {
              if ($points > $tie[$team]['pts_against']) {
                  ++$rank_allowed;
              }
          }

          $rank[$team] = sprintf('%04d', $rank_scored + $rank_allowed);
      }

      asort($rank);
      return $rank;
  }


  function _pointsRankingAll($tie, $params=array()) {
      $teams = array_keys($tie);
      $rank = array();
      $ranking_points_scored = array();
      $ranking_points_allowed = array();

      foreach ($teams as $team) {
          if (!count($ranking_points_scored)) {
              foreach ($this->standings as $_team) {
                  $ranking_points_scored[$_team['id']] = $_team['pts_for'];
              }
          }

          if (!count($ranking_points_allowed)) {
              foreach ($this->standings as $_team) {
                  $ranking_points_allowed[$_team['id']] = $_team['pts_against'];
              }
          }

          $rank_scored = 1;

          foreach ($ranking_points_scored as $points) {
              if ($points > $tie[$team]['pts_for']) {
                  ++$rank_scored;
              }
          }

          $rank_allowed = 1;

          foreach ($ranking_points_allowed as $points) {
              if ($points > $tie[$team]['pts_against']) {
                  ++$rank_allowed;
              }
          }

          $rank[$team] = sprintf('%04d', $rank_scored + $rank_allowed);
      }

      asort($rank);
      return $rank;
  }


  function _pointsCommonGames($tie, $params=array()) {
      $teams = array_keys($tie);
      $points = array();

      $common = $tie[$teams[0]]['opponents'];

      foreach ($teams as $team) {
          $common = array_unique(array_intersect($common, $tie[$team]['opponents']));
          $points[$team] = 0;
      }

      foreach ($teams as $team) {
          foreach ($common as $opp) {
              $points[$team] += $tie[$team]['hth'][$opp]['pts_for'];
              $points[$team] -= $tie[$team]['hth'][$opp]['pts_against'];
          }

          $points[$team] = sprintf('%04d', $points[$team]);
      }

      arsort($points);
      return $points;
  }


  function _pointsConfGames($tie, $params=array()) {
      $teams = array_keys($tie);
      $points = array();

      foreach ($teams as $team) {
          $points[$team] = sprintf('%04d', $tie[$team]['conf_pts_for'] - $tie[$team]['conf_pts_against']);
      }

      arsort($points);
      return $points;
  }


  function _pointsAllGames($tie, $params=array()) {
      $teams = array_keys($tie);
      $points = array();

      foreach ($teams as $team) {
          $points[$team] = sprintf('%04d', $tie[$team]['pts_for'] - $tie[$team]['pts_against']);
      }

      arsort($points);
      return $points;
  }


  function _touchdowns($tie, $params=array()) {
      global $_SYS;

      $teams = array_keys($tie);
      $touchdowns = array();

      foreach ($teams as $team) {
          $touchdowns[$team] = '0000';

          if (count($tie[$team]['games_played']) == 0) {
              continue;
          }

          /* offensive touchdowns */

          $query = 'SELECT SUM(rushing_td) + SUM(passing_td) AS `td`
                    FROM   '.$_SYS['table']['stats_scoring_offense'].'
                    WHERE  team = '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] += $row['td'];

          $query = 'SELECT SUM(rushing_td) + SUM(passing_td) AS `td`
                    FROM   '.$_SYS['table']['stats_scoring_defense'].'
                    WHERE  team = '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] -= $row['td'];

          /* punt return touchdowns */

          $query = 'SELECT SUM(td) AS `td`
                    FROM   '.$_SYS['table']['stats_punt_returns'].'
                    WHERE  team = '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] += $row['td'];

          $query = 'SELECT SUM(td) AS `td`
                    FROM   '.$_SYS['table']['stats_punt_returns'].'
                    WHERE  team != '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] -= $row['td'];

          /* kick return touchdowns */

          $query = 'SELECT SUM(td) AS `td`
                    FROM   '.$_SYS['table']['stats_kick_returns'].'
                    WHERE  team = '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] += $row['td'];

          $query = 'SELECT SUM(td) AS `td`
                    FROM   '.$_SYS['table']['stats_kick_returns'].'
                    WHERE  team != '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] -= $row['td'];

          /* defensive touchdowns */

          $query = 'SELECT SUM(td) AS `td`
                    FROM   '.$_SYS['table']['stats_defense'].'
                    WHERE  team = '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] += $row['td'];

          $query = 'SELECT SUM(td) AS `td`
                    FROM   '.$_SYS['table']['stats_defense'].'
                    WHERE  team != '.$team.'
                           AND game IN ('.join(', ', array_keys($tie[$team]['games_played'])).')';
          $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row = $result->fetch_assoc();

          $touchdowns[$team] -= $row['td'];

          $touchdowns[$team] = sprintf('%04d', $touchdowns[$team]);
      }

      arsort($touchdowns);
      return $touchdowns;
  }


  function _coinToss($tie, $params=array()) {
      $teams = array_keys($tie);
      $coin = array();

      foreach ($teams as $team) {
          $coin[$team] = sprintf('%032d', mt_rand());
      }

      asort($coin);
      return $coin;
  }


  function getWinner($tie, $tiebreakers) {
      global $_SYS;

      if (count($tie) == 1) {
          $teams = array_keys($tie);
          return array($teams[0], array('*', '(Tie Breaker lost)'));
      }

      $breakers = count($tie) == 2
                ? $tiebreakers['two']
                : $tiebreakers['more'];

      foreach ($breakers as $breakfunc) {
          $func = $breakfunc[0];
          $short = $breakfunc[1];
          $params = $breakfunc[2] ? $breakfunc[2] : array();

          $breaker = $this->array_partition(call_user_func(array($this, $func), $tie, $params));

          if (count($breaker) > 1) {
              $teams = array_keys($breaker[0]);

              if (count($teams) == 1) {
                  return array($teams[0], $short);
              }
              else {
                  $ties = array();

                  foreach ($teams as $team) {
                      $ties[$team] = $tie[$team];
                  }

                  return $this->getWinner($ties, $tiebreakers);
              }
          }
      }

      /* final -- just take the first team */

      $teams = array_keys($tie);
      return array($teams[0], array('**', 'Unresolved'));
  }


  function resolveTie($tie, $tiebreakers) {

      /* sanity checks */

      if (!is_array($tie)) {
          return false;
      }

      if (count($tie) < 1) {
          return $tie;
      }

      $sorted = array();

      while (count($sorted) < count($tie)) {
          $ties = array();

          foreach (array_keys($tie) as $team) {
              if (!array_key_exists($team, $sorted)) {
                  $ties[$team] = $tie[$team];
              }
          }

          list($team, $breaker) = $this->getWinner($ties, $tiebreakers);
          $sorted[$team] = $tie[$team];
          $sorted[$team]['breaker'] = $breaker;
      }

      return $sorted;
  }


  function sortTeams($a, $b) {
      if ($a['pct'] > $b['pct']) {
          return -1;
      } elseif ($a['pct'] < $b['pct']) {
          return 1;
      } else {
          return 0;
      }
  }


  function getHTML() {
    global $_SYS;

    $output = '';

    /* determine if preseason or postseason shall be shown */

    if ($_GET['period'] == 'ex') {
      $this->period = 'ex';
    } elseif ($_GET['period'] == 'conf') {
      $this->period = 'conf';
    } elseif ($_GET['period'] == 'draft') {
      $this->period = 'draft';
    } elseif ($_GET['period'] == 'pre' || ($_GET['period'] != 'reg' && $_SYS['season'][$_SYS['request']['season']]['pre_weeks'] > 0 && $_SYS['var']['week'] < 0 && $_SYS['var']['season'] == $_SYS['request']['season'])) {
      $this->period = 'pre';
    }

    /* show preseason/postseason switcher */

    $output .= '
<p>
  '.($this->period == 'ex' ? '[ Exhibitions ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?period=ex&amp;season='.$_SYS['request']['season'].'">Exhibitions</a>').' &middot;
  '.($_SYS['season'][$_SYS['request']['season']]['pre_weeks'] > 0 ? ($this->period == 'pre' ? '[ Preseason ] &middot;' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?period=pre&amp;season='.$_SYS['request']['season'].'">Preseason</a> &middot;') : '').'
  '.($this->period == 'reg' ? '[ Regular Season ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?period=reg&amp;season='.$_SYS['request']['season'].'">Regular Season</a>').' &middot;
  '.($this->period == 'conf' ? '[ Conference Standings ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?period=conf&amp;season='.$_SYS['request']['season'].'">Conference Standings</a>').' &middot;
  '.($this->period == 'draft' ? '[ Draft Order ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?period=draft&amp;season='.$_SYS['request']['season'].'">Draft Order</a>').'
</p>';

    /* read games */

    if ($this->period == 'ex') {
        $this->weeks = 'g.week = 0';
    }
    elseif ($this->period == 'pre') {
        $this->weeks = 'g.week < 0';
    }
    else {
        $this->weeks = 'g.week > 0 AND g.week <= '.$_SYS['season'][$_SYS['request']['season']]['reg_weeks'];
    }

    $query = 'SELECT   g.id                                           AS game_id,
                       g.week                                         AS week,
                       g.away                                         AS away,
                       g.home                                         AS home,
                       g.site                                         AS site,
                       tm.id                                          AS my,
                       IF(tm.id = g.away, g.away_score, g.home_score) AS my_score,
                       tm.conference                                  AS my_conference,
                       tm.division                                    AS my_division,
                       CONCAT(tm.conference, " ", tm.division)        AS my_conf_division,
                       CONCAT(n.team, " ", n.nick)                    AS my_team,
                       n.acro                                         AS my_acro,
                       tm.user                                        AS my_user,
                       ta.id                                          AS opp,
                       IF(ta.id = g.away, g.away_score, g.home_score) AS opp_score,
                       ta.conference                                  AS opp_conference,
                       ta.division                                    AS opp_division,
                       CONCAT(ta.conference, " ", ta.division)        AS opp_conf_division
              FROM     '.$_SYS['table']['team'].' AS tm
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON tm.team = n.id
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON (tm.id = g.away OR tm.id = g.home) AND '.$this->weeks.'
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON ta.id = IF(tm.id = g.away, g.home, g.away)
              WHERE    tm.season = '.$_SYS['request']['season'].'
              ORDER BY g.date';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* calculate standings */

    $standings = array();

    while ($row = $result->fetch_assoc()) {
      if (!array_key_exists($row['my'], $standings)) {
        $standings[$row['my']] = array('id'           => intval($row['my']),
                                       'season'       => $_SYS['request']['season'],
                                       'conference'   => $row['my_conference'],
                                       'division'     => $row['my_division'],
                                       'team'         => $row['my_team'],
                                       'acro'         => $row['my_acro'],
                                       'user'         => $row['my_user'],
                                       'games_played' => array(),
                                       'opponents'    => array(),
                                       'hth'          => array(),
                                       'opp_won'      => array(),
                                       'pts_for'      => 0,  'pts_against'  => 0,
                                       'won'          => 0,  'lost'         => 0, 'tied'       => 0, 'pct'       => null, 'record'       => '0-0',
                                       'home_won'     => 0,  'home_lost'    => 0, 'home_tied'  => 0, 'home_pct'  => null, 'home_record'  => '0-0',
                                       'road_won'     => 0,  'road_lost'    => 0, 'road_tied'  => 0, 'road_pct'  => null, 'road_record'  => '0-0',
                                       'conf_won'     => 0,  'conf_lost'    => 0, 'conf_tied'  => 0, 'conf_pct'  => null, 'conf_record'  => '0-0', 'conf_pts_for' => 0, 'conf_pts_against' => 0,
                                       'inter_won'    => 0,  'inter_lost'   => 0, 'inter_tied' => 0, 'inter_pct' => null, 'inter_record' => '0-0',
                                       'div_won'      => 0,  'div_lost'     => 0, 'div_tied'   => 0, 'div_pct'   => null, 'div_record'   => '0-0',
                                       'sos_won'      => 0,  'sos_lost'     => 0, 'sos_tied'   => 0, 'sos_pct'   => null, 'sos_record'   => '0-0',
                                       'sov_won'      => 0,  'sov_lost'     => 0, 'sov_tied'   => 0, 'sov_pct'   => null, 'sov_record'   => '0-0',
                                       'streak_type'  => '', 'streak_count' => 0, 'streak'     => '&nbsp;');
      }

      $row['opp'] = intval($row['opp']);

      $standings[$row['my']]['opponents'][] = $row['opp'];

      if (!array_key_exists($row['opp'], $standings[$row['my']]['hth'])) {
          $standings[$row['my']]['hth'][$row['opp']] = array('won' => 0, 'lost' => 0, 'tied' => 0, 'pts_for' => 0, 'pts_against' => 0);
      }

      /* next game if game hasn't been played or is a non-visible regular season game */

      if (!$row['site']
          || ($this->period == 'reg' && !in_array($row['week'], $_SYS['season'][$_SYS['request']['season']]['visible_weeks'][$this->period]))) {
        continue;
      }

      $standings[$row['my']]['games_played'][$row['game_id']] = $row['opp'];

      if ($row['my_score'] > $row['opp_score']) {
        $_suffix = 'won';
        $standings[$row['my']]['opp_won'][] = $row['opp'];
      } elseif ($row['my_score'] < $row['opp_score']) {
        $_suffix = 'lost';
      } else {
        $_suffix = 'tied';
      }

      $standings[$row['my']]['pts_for']     += $row['my_score'];
      $standings[$row['my']]['pts_against'] += $row['opp_score'];

      $standings[$row['my']]['hth'][$row['opp']]['pts_for']     += $row['my_score'];
      $standings[$row['my']]['hth'][$row['opp']]['pts_against'] += $row['opp_score'];

      $standings[$row['my']][$_suffix] += 1;
      $standings[$row['my']]['hth'][$row['opp']][$_suffix] += 1;

      if ($row['my'] == $row['home']) {
        $standings[$row['my']]['home_'.$_suffix] += 1;
      } else {
        $standings[$row['my']]['road_'.$_suffix] += 1;
      }

      if ($row['my_conference'] == $row['opp_conference']) {
        $standings[$row['my']]['conf_'.$_suffix] += 1;
        $standings[$row['my']]['conf_pts_for']     += $row['my_score'];
        $standings[$row['my']]['conf_pts_against'] += $row['opp_score'];
      } else {
        $standings[$row['my']]['inter_'.$_suffix] += 1;
      }

      if ($row['my_conf_division'] == $row['opp_conf_division']) {
        $standings[$row['my']]['div_'.$_suffix] += 1;
      }

      if ($standings[$row['my']]['streak_type'] == ucfirst($_suffix)) {
        $standings[$row['my']]['streak_count'] += 1;
      } else {
        $standings[$row['my']]['streak_count'] = 1;
        $standings[$row['my']]['streak_type'] = ucfirst($_suffix);
      }
    }

    unset($_suffix);

    /* calculate strength of schedule/victory wins/losses/ties */

    foreach ($standings as $_team) {
      foreach (array('sos_' => 'opponents', 'sov_' => 'opp_won') as $_key => $_val) {
        foreach (array('won', 'lost', 'tied') as $_suffix) {
          foreach ($_team[$_val] as $_opp) {
            $standings[$_team['id']][$_key.$_suffix] += $standings[$_opp][$_suffix];
          }
        }
      }
    }

    /* calculate percentages and record/streak strings */

    foreach ($standings as $_team) {
      foreach (array('', 'home_', 'road_', 'conf_', 'inter_', 'div_', 'sos_', 'sov_') as $_prefix) {
        $standings[$_team['id']][$_prefix.'pct'] =
          $standings[$_team['id']][$_prefix.'won'] + $standings[$_team['id']][$_prefix.'lost'] + $standings[$_team['id']][$_prefix.'tied'] == 0
          ? null
          : ($standings[$_team['id']][$_prefix.'won'] + (1/2) * $standings[$_team['id']][$_prefix.'tied']) / ($standings[$_team['id']][$_prefix.'won'] + $standings[$_team['id']][$_prefix.'lost'] + $standings[$_team['id']][$_prefix.'tied']);

        $standings[$_team['id']][$_prefix.'record'] = $standings[$_team['id']][$_prefix.'won'] . '-' . $standings[$_team['id']][$_prefix.'lost'] . ($standings[$_team['id']][$_prefix.'tied'] == 0 ? '' : '-' . $standings[$_team['id']][$_prefix.'tied']);
      }

      $standings[$_team['id']]['streak'] =
        $standings[$_team['id']]['streak_count'] == 0
        ? '&nbsp;'
        : $standings[$_team['id']]['streak_type'].' '.$standings[$_team['id']]['streak_count'];
    }

    unset($_team, $_prefix, $_suffix, $_key, $_val, $_opp);

    /* sort standings */

    $_col = array();

    foreach ($standings as $_key => $_val) {
      $_col['conference'][$_key] = $_val['conference'];
      $_col['division'][$_key]   = $_val['division'];
      $_col['pct'][$_key]        = $_val['pct'];
    }

    array_multisort($_col['conference'], SORT_ASC,
                    $_col['division'],   SORT_ASC,
                    $_col['pct'],        SORT_DESC,
                    $standings);

    unset($_col, $_key, $_val);

    /* save standings */

    $this->standings = $standings;

    /* resolve ties within each division */

    $divisions = array();

    foreach ($standings as $row) {
        $divisions[$row['conference'].' '.$row['division']][] = $row;
    }

    $standings = array();

    foreach ($divisions as $division) {
        $teams = count($division);
        $current_pos = 1;

        /* search and resolve ties */

        while ($current_pos <= $teams) {
            $record = $division[$current_pos - 1]['pct'];
            $games_played = count($division[$current_pos - 1]['games_played']);
            $tie = array();

            for ($i = $current_pos; $i <= $teams; ++$i) {
                if ($division[$i - 1]['pct'] === $record) {
                    $games_played = max($games_played, count($division[$i - 1]['games_played']));
                    $tie[$division[$i - 1]['id']] = $division[$i - 1];
                }
                else {
                    break;
                }
            }

            /* ties to resolve? */

            if (count($tie) > 1 && $games_played) {
                $tie = $this->resolveTie($tie, $_SYS['season'][$_SYS['request']['season']]['tiebreaker']['div']);
            }

            $standings = array_merge($standings, $tie);
            $current_pos = $i;
        }
    }

    /* conference standings? */

    if ($this->period == 'conf') {
        $divisions = array();

        foreach ($standings as $row) {
            $row['breaker'] = '';
            $divisions[$row['conference']][$row['division']][] = $row;
        }

        $standings = array();

        foreach (array_keys($divisions) as $conference) {
            $compare_team = array();

            /* first the division winners */

            foreach (array_keys($divisions[$conference]) as $division) {
                $compare_team[] = array_shift($divisions[$conference][$division]);
            }

            $_col = array();

            foreach ($compare_team as $_key => $_val) {
                $_col['pct'][$_key] = $_val['pct'];
                $compare_team[$_key]['division'] = '';
            }

            array_multisort($_col['pct'], SORT_DESC, $compare_team);
            unset($_col, $_key, $_val);

            /* search and resolve ties */

            $teams = count($compare_team);
            $current_pos = 1;

            while ($current_pos <= $teams) {
                $record = $compare_team[$current_pos - 1]['pct'];
                $games_played = count($compare_team[$current_pos - 1]['games_played']);
                $tie = array();

                for ($i = $current_pos; $i <= $teams; ++$i) {
                    if ($compare_team[$i - 1]['pct'] === $record) {
                        $games_played = max($games_played, count($compare_team[$i - 1]['games_played']));
                        $tie[$compare_team[$i - 1]['id']] = $compare_team[$i - 1];
                    }
                    else {
                        break;
                    }
                }

                /* ties to resolve? */

                if (count($tie) > 1 && $games_played) {
                    $tie = $this->resolveTie($tie, $_SYS['season'][$_SYS['request']['season']]['tiebreaker']['conf']);
                }

                $standings = array_merge($standings, $tie);
                $current_pos = $i;
            }

            /* now all non-division winners; one-by-one */

            $current_team = array();
            $last_pct = null;

            while (true) {
                $compare_team = array();

                foreach (array_keys($divisions[$conference]) as $division) {
                    if (!$current_team[$division]) {
                        $current_team[$division] = array_shift($divisions[$conference][$division]);
                    }
                }

                /* remove null-entries from $current_team */

                foreach (array_keys($divisions[$conference]) as $division) {
                    if (!$current_team[$division]) {
                        unset($current_team[$division]);
                    }
                }

                if (!$current_team) {
                    break;
                }

                /* sort teams */

                uasort($current_team, array($this, 'sortTeams'));

                /* fetch teams in tie */

                $max = null;
                $single_tie_id = '';
                $games_played = 0;

                if (!is_array($last_tie)) {
                    $last_tie = array();
                }

                foreach ($current_team as $team) {
                    if (is_null($max)) {
                        $max = $team['pct'];
                    }

                    if ($team['pct'] == $max) {
                        $compare_team[$team['id']] = $team;
                        $single_tie_id = $team['id'];
                        $games_played = max($games_played, $team['games_played']);
                    }
                }

                /* get winner */

                if ($games_played && (count($compare_team) > 1 || ($max == $last_pct && in_array($single_tie_id, $last_tie)))) {
                    $compare_team = $this->resolveTie($compare_team, $_SYS['season'][$_SYS['request']['season']]['tiebreaker']['conf']);
                }

                /* store this tie */

                $last_tie = array();

                if (count($compare_team) > 1) {
                    foreach ($compare_team as $team) {
                        $last_tie[] = $team['id'];
                    }
                }

                $last_pct = $max;

                $team = array_shift($compare_team);
                unset($current_team[$team['division']]);
                $team['division'] = '';
                array_push($standings, $team);
            }
        }
    }

    /* draft order */

    elseif ($this->period == 'draft') {
        $draft_tiebreakers = array(
            'two' => array(
                array('_playoffAdvancement', array('PO',   'Playoff Advancement')),
                array('_strengthOfSchedule', array('SOS',  'Strength of Schedule'), array('draft' => 1)),
                array('_coinToss',           array('COIN', 'Coin Toss')),
            ),
        );
        $draft_tiebreakers['more'] = $draft_tiebreakers['two'];

        $draftorder = array();
        $super_bowl_champion = array();
        $super_bowl_finalist = array();

        $super_bowl_champion_id = array();
        $super_bowl_finalist_id = array();

        /* fetch super bowl pairing */

        $query = 'SELECT home, away, site, home_score, away_score
                  FROM   '.$_SYS['table']['game'].'
                  WHERE  season = '.$_SYS['request']['season'].'
                         AND week = '.($_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + $_SYS['season'][$_SYS['request']['season']]['post_weeks']);
        $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        if ($result->rows()) {
            $row = $result->fetch_assoc();

            if ($row['home_score'] > $row['away_score']) {
                $super_bowl_champion_id[] = $row['home'];
                $super_bowl_finalist_id[] = $row['away'];
            }
            elseif ($row['away_score'] > $row['home_score']) {
                $super_bowl_champion_id[] = $row['away'];
                $super_bowl_finalist_id[] = $row['home'];
            }
            else {
                $super_bowl_finalist_id[] = $row['home'];
                $super_bowl_finalist_id[] = $row['away'];
            }
        }

        foreach ($standings as $row) {
            $row['breaker'] = '';
            $row['division'] = '';
            $row['conference'] = 'Draft Order';

            /* do not add super bowl champion or finalist */

            if (in_array($row['id'], $super_bowl_finalist_id)) {
                $super_bowl_finalist[] = $row;
            } elseif (in_array($row['id'], $super_bowl_champion_id)) {
                $super_bowl_champion[] = $row;
            }
            else {
                $draftorder[] = $row;
            }
        }

        $standings = array();

        /* sort remaining teams */

        $_col = array();

        foreach ($draftorder as $_key => $_val) {
            $_col['pct'][$_key] = $_val['pct'];
        }

        array_multisort($_col['pct'], SORT_ASC, $draftorder);
        unset($_col, $_key, $_val);

        /* search and resolve ties */

        $teams = count($draftorder);
        $current_pos = 1;

        while ($current_pos <= $teams) {
            $record = $draftorder[$current_pos - 1]['pct'];
            $games_played = count($draftorder[$current_pos - 1]['games_played']);
            $tie = array();

            for ($i = $current_pos; $i <= $teams; ++$i) {
                if ($draftorder[$i - 1]['pct'] === $record) {
                    $games_played = max($games_played, count($draftorder[$i - 1]['games_played']));
                    $tie[$draftorder[$i - 1]['id']] = $draftorder[$i - 1];
                }
                else {
                    break;
                }
            }

            /* ties to resolve? */

            if (count($tie) > 1 && $games_played) {
                $tie = $this->resolveTie($tie, $draft_tiebreakers);
            }

            $standings = array_merge($standings, $tie);
            $current_pos = $i;
        }

        /* add super bowl finalist */

        if (count($super_bowl_finalist)) {
            if (count($super_bowl_finalist) > 1) {
                $_col = array();

                foreach ($draftorder as $_key => $_val) {
                    $_col['pct'][$_key] = $_val['pct'];
                    $_col['sos'][$_key] = $_val['sos'];
                }

                array_multisort($_col['pct'], SORT_ASC, $draftorder);
                unset($_col, $_key, $_val);
            }

            $standings = array_merge($standings, $super_bowl_finalist);
        }

        /* add super bowl champion */

        if (count($super_bowl_champion)) {
            $standings = array_merge($standings, $super_bowl_champion);
        }
    }

    /* show table */

    $_div = '';

    $output .= '
<table class="standings">';

    foreach ($standings as $row) {
      if (trim($row['conference'].' '.$row['division']) != $_div) {
        if ($_div != '') {
          $output .= '
    <tr class="spacer">
      <td colspan="'.($this->period == 'draft' ? '17' : '16').'"></td>
    </tr>
  </tbody>';
        }

        $_div = trim($row['conference'].' '.$row['division']);
        $output .= '
  <tbody class="head">
  <tr>
    <th colspan="'.($this->period == 'draft' ? '17' : '16').'">'.strtoupper($_div).'</th>
  </tr>
  <tr>
    '.($this->period == 'draft' ? '<th>#</th>' : '').'
    <th>Team</th>
    <th>TB</th>
    <th>W</th>
    <th>L</th>
    <th>T</th>
    <th>PCT</th>
    <th>PF</th>
    <th>PA</th>
    <th>Home</th>
    <th>Road</th>
    <th>Conf</th>
    <th>Inter</th>
    <th>DIV</th>
    <th>SOS</th>
    <th>SOV</th>
    <th>Streak</th>
  </tr>
  </tbody>
  <tbody>';

        $i = 0;
      }

      ++$i;

      $output .= '
  <tr'.($this->period == 'conf' && $i > $_SYS['season'][$_SYS['request']['season']]['post_teams'] ? ' class="noplayoffs"' : '').'>
     '.($this->period == 'draft' ? '<td>'.$i.'</td>' : '').'
     <th scope="row">
       '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['acro']).'.gif" alt="'.$row['acro'].'" class="logo" />' : '').'
       <a href="'.$_SYS['page']['team/home']['url'].'?season='.$_SYS['request']['season'].'&amp;id='.$row['id'].'">'.$row['team'].'</a>
     </th>
     <td>'.($row['breaker'] ? '<abbr title="'.$row['breaker'][1].'">'.$row['breaker'][0].'</abbr>' : '').'</td>
     <td>'.$row['won'].'</td>
     <td>'.$row['lost'].'</td>
     <td>'.$row['tied'].'</td>
     <td>'.(is_null($row['pct']) ? '&nbsp;' : ltrim(sprintf('%.3f', $row['pct']), '0')).'</td>
     <td>'.$row['pts_for'].'</td>
     <td>'.$row['pts_against'].'</td>
     <td>'.$row['home_record'].'</td>
     <td>'.$row['road_record'].'</td>
     <td>'.$row['conf_record'].'</td>
     <td>'.$row['inter_record'].'</td>
     <td>'.$row['div_record'].'</td>
     <td>'.(is_null($row['sos_pct']) ? '&nbsp;' : ltrim(sprintf('%.3f', $row['sos_pct']), '0')).'</td>
     <td>'.(is_null($row['sov_pct']) ? '&nbsp;' : ltrim(sprintf('%.3f', $row['sov_pct']), '0')).'</td>
     <td>'.$row['streak'].'</td>
  </tr>';
    }

    $output .= '
  </tbody>
</table>';

    return $output;
  } // getHTML()
}