<?php
/**
 * @(#) parser.php
 */

class GameLog {

  var $stats;
  var $error;

  var $headers = array('slider'     => 'Slider                         Human   CPU',
                       'scorebox'   => 'Team               Q1   Q2   Q3   Q4   OT      FINAL',
                       'individual' => array('passing'      => array('string'   => 'PASSING                        CMP  ATT  YDS  PCT   YPA SACK   TD  INT LONG RATING',
                                                                     'template' => '%-29s %4d %4d %4d %4d %5.1f %4d %4d %4d %4d %6.1f'),
                                             'rushing'      => array('string'   => 'RUSHING                        ATT  YDS   AVG LONG   TD  FUM',
                                                                     'template' => '%-29s %4d %4d %5.1f %4d %4d %4d'),
                                             'receiving'    => array('string'   => 'RECEIVING                      REC  YDS   AVG LONG   TD DROP  YAC',
                                                                     'template' => '%-29s %4d %4d %5.1f %4d %4d %4d %4d'),
                                             'kicking'      => array('string'   => 'KICKING                        FGM  FGA  PCT FGSBLOCKED  XPA  XPM  PCT XPSBLOCKED KICKOFFS TOUCHBACKS',
                                                                     'template' => '%-29s %4d %4d %4d %10d %4d %4d %4d %10d %8d %10d'),
                                             'punting'      => array('string'   => 'PUNTING                        ATT  YDS   AVG LONG BLOCKS IN20 TOUCHBACKS',
                                                                     'template' => '%-29s %4d %4d %5.1f %4d %6d %4d %10d'),
                                             'kick_returns' => array('string'   => 'KICK RETURNS                   ATT  YDS   AVG   TD LONG',
                                                                     'template' => '%-29s %4d %4d %5.1f %4d %4d'),
                                             'punt_returns' => array('string'   => 'PUNT RETURNS                   ATT  YDS   AVG LONG   TD',
                                                                     'template' => '%-29s %4d %4d %5.1f %4d %4d'),
                                             'defense'      => array('string'   => 'DEFENSE                        TOT LOSS SACK   FF FREC  YDS   TD  INT  RET   AVG DEFLECTIONS SAFETIES CTH ALLOW BIG HITS',
                                                                     'template' => '%-29s %4d %4d %4d %4d %4d %4d %4d %4d %4d %5.1f %11d %8d %9d %8d'),
                                             'blocking'     => array('string'   => 'BLOCKING                      PANCAKES SACKS ALLOWED',
                                                                     'template' => '%-29s %8d %13d'),
                                             ),
                       );

  var $slider = array(5 => 'QB Accuracy', 'Pass Blocking', 'Receiver Catching', 'Running Ability',
                     'Offensive line Run Blocking', 'Defensive Awareness', 'Defensive line Knockdowns',
                     'Interceptions', 'Defensive Break Blocks', 'Tackling', 'Fieldgoal Length',
                     'Fieldgoal Accuracy', 'Punt Length', 'Punt Accuracy', 'Kickoff Length');

  var $teamstats = array(28 => 'First Downs', 'Third Down Conversions', 'Third Downs', 'Third Down Percentage',
                         'Fourth Down Conversions', 'Fourth Downs', 'Fourth Down Percentage', 'Two Pt Conversions Made',
                         'Two Pt Conversions Attempted', 'Two Pt Conversion Percentage', 'Offense Redzone Num',
                         'Offense Redzone TDs', 'Offense Redzone FGs', 'Offense Redzone Percentage', 'Rushing Attempts',
                         'Rushing Yards', 'Rushing Average', 'Rushing TDs', 'Passing Yards', 'Passing Completions',
                         'Passing Attempts', 'Completion Percentage', 'Passing Average', 'Passing TDs',
                         'Offensive Pass Interceptions', 'TOTAL OFFENSE', 'Defensive Pass Interceptions',
                         'Punt Return Yards', 'Kick Return Yards', 'Punts', 'Punt Avg', 'Fumbles', 'Fumbles Lost',
                         'Penalties', 'Penalty Yds', 'Turnovers', 'TIME OF POSSESSION');

  /*
   * constructor; creates an empty madden game log
   */
  function GameLog() {} // GameLog()


  /*
   * returns the latest error string
   */
  function error() {

    return $this->error;
  } // error()


  /*
   * converts a time string mm:ss into seconds
   */
  function _min2sec($time) {

    $time = explode(':', trim($time));

    return intval($time[0]) * 60 + intval($time[1]);
  } // _min2sec()


  /*
   * converts an integer into a time string mm:ss
   */
  function _sec2min($time) {

    $time = intval($time);

    $minutes = floor($time/60);
    $seconds = $time - 60 * $minutes;

    return sprintf('%02d:%02d', $minutes, $seconds);
  } // _sec2min()


  /*
   * parses and adds a madden game log file.  returns true on success and
   * false on failure.
   */
  function addFile($filename) {

    return $this->add(file_get_contents($filename));
  } // addFile(filename)


  /*
   * parses and adds a madden game log.  the logfile must be given as a single
   * string.  returns true on success and false on failure.
   */
  function add($logfile) {

    /* parse the log */

    $stats = $this->_parse($logfile);

    if (!$stats) {
      return false;
    }

    /* no need to merge if this is the first log */

    if (is_null($this->stats)) {
      $this->stats = $stats;
      return $this->stats;
    }

    /* compare basic game parameters */

    if ($stats['game']['version'] !== $this->stats['game']['version']) {
      $this->error = 'Game log header line does not match:<br />expected: <code>'.$this->stats['game']['version'].'</code><br />found: <code>'.$stats['game']['version'].'</code>';
      return false;
    }

    if ($stats['game']['away'] !== $this->stats['game']['away']) {
      $this->error = 'Away team does not match:<br />expected: <code>'.$this->stats['game']['away'].'</code><br />found: <code>'.$stats['game']['away'].'</code>';
      return false;
    }

    if ($stats['game']['home'] !== $this->stats['game']['home']) {
      $this->error = 'Home team does not match:<br />expected: <code>'.$this->stats['game']['home'].'</code><br />found: <code>'.$stats['game']['home'].'</code>';
      return false;
    }

    if ($stats['game']['level'] !== $this->stats['game']['level']) {
      $this->error = 'Skill level does not match:<br />expected: <code>'.$this->stats['game']['level'].'</code><br />found: <code>'.$stats['game']['level'].'</code>';
      return false;
    }

    /* calculate game time */

    if (strtotime($this->stats['game']['time']) < strtotime($stats['game']['time'])) {
      $this->stats['game']['time'] = $stats['game']['time'];
    }

    /* calculate score */

    foreach (array_keys($stats['score']) as $_team) {
      foreach ($stats['score'][$_team] as $_quarter => $_score) {
        $this->stats['score'][$_team][$_quarter] += $_score;
      }
    }

    if ($this->stats['score']['away']['final'] > $this->stats['score']['home']['final']) {
      $this->stats['game']['winner'] = 'away';
    } elseif ($this->stats['score']['home']['final'] > $this->stats['score']['away']['final']) {
      $this->stats['game']['winner'] = 'home';
    } else {
      $this->stats['game']['winner'] = 'tie';
    }

    /* take weather stats from game log w/ more time of possession */

    if ($this->_min2sec($stats['team']['time_of_possession']['away']) + $this->_min2sec($stats['team']['time_of_possession']['home']) >
        $this->_min2sec($this->stats['team']['time_of_possession']['away']) + $this->_min2sec($this->stats['team']['time_of_possession']['home'])) {
      $this->stats['game']['forecast'] = $stats['game']['forecast'];
      $this->stats['game']['wind'] = $stats['game']['wind'];
      $this->stats['game']['temp'] = $stats['game']['temp'];
    }

    /* compare slider settings */

    foreach ($this->slider as $_slider) {
      $_slider = strtolower(str_replace(' ', '_', $_slider));

      if ($stats['slider'][$_slider]['human'] !== $this->stats['slider'][$_slider]['human']) {
        $this->error = 'Slider &quot;'.$stats['slider'][$_slider]['title'].'&quot; (Human) does not match:<br />expected: <code>'.$this->stats['slider'][$_slider]['human'].'</code><br />found: <code>'.$stats['slider'][$_slider]['human'].'</code>';
        return false;
      }

      if ($stats['slider'][$_slider]['cpu'] !== $this->stats['slider'][$_slider]['cpu']) {
        $this->error = 'Slider &quot;'.$stats['slider'][$_slider]['title'].'&quot; (CPU) does not match:<br />expected: <code>'.$this->stats['slider'][$_slider]['cpu'].'</code><br />found: <code>'.$stats['slider'][$_slider]['cpu'].'</code>';
        return false;
      }
    }

    unset($_slider);

    /* merge individual stats */

    foreach ($stats['individual'] as $_team => $_categories) {
      foreach ($_categories as $_category => $_players) {
        foreach ($_players as $_player => $_stats) {
          if (!array_key_exists($_player, $this->stats['individual'][$_team][$_category])) {
            $this->stats['individual'][$_team][$_category][$_player] = $_stats;
            continue;
          }

          $_this = &$this->stats['individual'][$_team][$_category][$_player];

          foreach ($_stats as $_stat => $_value) {
            $_this[$_stat] = $this->_merge_individual($_this, $_stat, $_value, $_category);
          }
        }
      }
    }

    unset($_team, $_categories, $_category, $_players, $_player, $_stats, $_this, $_stat, $_value);

    /* merge game statistics */

    foreach ($stats['team'] as $_category => $_stats) {
      foreach (array('away', 'home') as $_site) {
        $_this = &$this->stats['team'][$_category][$_site];

        switch ($_category) {
        case 'third_down_percentage':
          $_this = $this->stats['team']['third_downs'][$_site]
            ? sprintf('%.1f%%', 100 * $this->stats['team']['third_down_conversions'][$_site] / $this->stats['team']['third_downs'][$_site])
            : '0.0%';
          break;
        case 'fourth_down_percentage':
          $_this = $this->stats['team']['fourth_downs'][$_site]
            ? sprintf('%.1f%%', 100 * $this->stats['team']['fourth_down_conversions'][$_site] / $this->stats['team']['fourth_downs'][$_site])
            : '0.0%';
          break;
        case 'two_pt_conversion_percentage':
          $_this = $this->stats['team']['two_pt_conversions_attempted'][$_site]
            ? sprintf('%.1f%%', 100 * $this->stats['team']['two_pt_conversions_made'][$_site] / $this->stats['team']['two_pt_conversions_attempted'][$_site])
            : '0.0%';
          break;
        case 'offense_redzone_percentage':
          $_this = $this->stats['team']['offense_redzone_num'][$_site]
            ? sprintf('%.1f%%', 100 * ($this->stats['team']['offense_redzone_tds'][$_site] + $this->stats['team']['offense_redzone_fgs'][$_site]) / $this->stats['team']['offense_redzone_num'][$_site])
            : '0.0%';
          break;
        case 'rushing_average':
          $_this = $this->stats['team']['rushing_attempts'][$_site]
            ? sprintf('%.1f', $this->stats['team']['rushing_yards'][$_site] / $this->stats['team']['rushing_attempts'][$_site])
            : '0.0';
          break;
        case 'completion_percentage':
          $_this = $this->stats['team']['passing_attempts'][$_site]
            ? sprintf('%.1f%%', 100 * $this->stats['team']['passing_completions'][$_site] / $this->stats['team']['passing_attempts'][$_site])
            : '0.0%';
          break;
        case 'passing_average':
          $_this = $this->stats['team']['passing_attempts'][$_site]
            ? sprintf('%.1f', $this->stats['team']['passing_yards'][$_site] / $this->stats['team']['passing_attempts'][$_site])
            : '0.0';
          break;
        case 'punt_avg':

          if ($this->stats['team']['punts'][$_site]) {

            $_yards = 0;

            foreach ($this->stats['individual'][$_site]['punting'] as $_player) {
              $_yards += $_player['yds'];
            }

            $_this = sprintf('%.1f', $_yards / $this->stats['team']['punts'][$_site]);
          } else {
            $_this = '0.0';
          }

          break;
        case 'time_of_possession':
          $_this = $this->_sec2min($this->_min2sec($_this) + $this->_min2sec($_stats[$_site]));
          break;
        default:
          $_this += $_stats[$_site];
        }
      }
    }

    unset($_category, $_stats, $_yards, $_player);

    /* successfully added game log */

    return true;
  } // add(logfile)


  /*
   * parses a single madden game log.  the logfile must be given as a single
   * string.  returns a hash containing the stats on success and false on failure.
   */
  function _parse($logfile) {

    $stats = array();

    /*
     * split the string into lines
     * discard empty lines
     * trim whitespace around each line
     */

    $logfile = preg_split('/\s*[\r\n]+\s*/', trim($logfile));

    /* delete a "Game was not completed." line */

    if ($logfile[27] === 'Game was not completed.') {
      array_splice($logfile, 27, 1);
    }

    /* check constant strings and parse some game data */

    if (!preg_match('/^(Madden NFL.*?Game Log) - (.*?) at (.*?)$/', $logfile[0], $game['teams'])) {
      $this->error = 'Invalid game log header line: <code>'.$logfile[0].'</code>';
      return false;
    }

    if (!preg_match('/^Game Time: ([A-Z][a-z]{2} [A-Z][a-z]{2} \d{2} \d{2}:\d{2}:\d{2} \d{4})$/', $logfile[1], $game['date'])) {
      $this->error = 'Invalid game time line: <code>'.$logfile[1].'</code>';
      return false;
    }

    if (!preg_match('/^Skill Level: (Rookie|Pro|All Pro|All Madden)$/', $logfile[2], $game['level'])) {
      $this->error = 'Invalid skill level line: <code>'.$logfile[2].'</code>';
      return false;
    }

    if (!preg_match('/^Quarter Length: (\d+) minute\(s\)$/', $logfile[3], $game['quarter'])
        || intval($game['quarter'][1]) < 1
        || intval($game['quarter'][1]) > 15) {
      $this->error = 'Invalid quarter length line: <code>'.$logfile[3].'</code>';
      return false;
    }

    if (!preg_match('/^Slider +Human +CPU$/', $logfile[4])) {
      $this->error = 'Invalid slider header line: <code>'.$logfile[4].'</code>';
      return false;
    }

    if ($logfile[20] !== 'Weather') {
      $this->error = 'Invalid weather header line.  Expected <code>Weather</code>, found: <code>'.$logfile[20].'</code>';
      return false;
    }

    if (!preg_match('/^Forecast: ([A-Z][a-z]+)$/', $logfile[21], $game['forecast'])) {
      $this->error = 'Invalid forecast line: <code>'.$logfile[21].'</code>';
      return false;
    }

    if (!preg_match('/^Wind: (\d+) mph$/', $logfile[22], $game['wind'])) {
      $this->error = 'Invalid wind speed line: <code>'.$logfile[22].'</code>';
      return false;
    }

    if (!preg_match('/^Temp: (\d+) degrees$/', $logfile[23], $game['temp'])) {
      $this->error = 'Invalid temperature line: <code>'.$logfile[23].'</code>';
      return false;
    }

    if (!preg_match('/^Team +Q1 +Q2 +Q3 +Q4 +OT +FINAL$/', $logfile[24])) {
      $this->error = 'Invalid score box header: <code>'.$logfile[24].'</code>';
      return false;
    }

    if (!preg_match('/^('.$game['teams'][2].') +(\d+) +(\d+) +(\d+) +(\d+) +(\d+) +(\d+)$/', $logfile[25], $game['away_score'])) {
      $this->error = 'Invalid away score line: <code>'.$logfile[25].'</code>';
      return false;
    }

    if (!preg_match('/^('.$game['teams'][3].') +(\d+) +(\d+) +(\d+) +(\d+) +(\d+) +(\d+)$/', $logfile[26], $game['home_score'])) {
        $this->error = 'Invalid home score line: <code>'.$logfile[26].'</code>';
      return false;
    }

//     if ($game['away_score'][1] !== $game['teams'][2]) {
//       $this->error = 'Away team in score box does not match away team in log header';
//       return false;
//     }

//     if ($game['home_score'][1] !== $game['teams'][3]) {
//       $this->error = 'Home team in score box does not match home team in log header';
//       return false;
//     }

    if (!preg_match('/^Game Statistics: +'.$game['teams'][2].' +'.$game['teams'][3].'$/', $logfile[27], $game['stats'])) {
      $this->error = 'Invalid game stats header line: <code>'.$logfile[27].'</code>';
      return false;
    }

//     if ($game['stats'][1] !== $game['teams'][2]) {
//       $this->error = 'Away team in game stats header does not match away team in log header';
//       return false;
//     }

//     if ($game['stats'][2] !== $game['teams'][3]) {
//       $this->error = 'Home team in game stats header does not macht home team in log header';
//       return false;
//     }

    $_line = array_pop($logfile);

    if ($_line !== '------------------------------------------------------------') {
      $this->error = 'Invalid second last game log line: <code>'.$_line.'</code>';
      return false;
    }

    $_line = array_pop($logfile);

    if ($_line !== 'Game Log Ends') {
      $this->error = 'Invalid last game log line: <code>'.$_line.'</code>';
      return false;
    }

    unset($_line);

    $stats['game'] = array('version'  => $game['teams'][1],
                           'away'     => $game['teams'][2],
                           'home'     => $game['teams'][3],
                           'time'     => $game['date'][1],
                           'level'    => $game['level'][1],
                           'quarter'  => intval($game['quarter'][1]),
                           'forecast' => $game['forecast'][1],
                           'wind'     => intval($game['wind'][1]),
                           'temp'     => intval($game['temp'][1]));

    $stats['score']['away'] = array('q1'    => intval($game['away_score'][2]),
                                    'q2'    => intval($game['away_score'][3]),
                                    'q3'    => intval($game['away_score'][4]),
                                    'q4'    => intval($game['away_score'][5]),
                                    'ot'    => intval($game['away_score'][6]),
                                    'final' => intval($game['away_score'][7]));

    $stats['score']['home'] = array('q1'    => intval($game['home_score'][2]),
                                    'q2'    => intval($game['home_score'][3]),
                                    'q3'    => intval($game['home_score'][4]),
                                    'q4'    => intval($game['home_score'][5]),
                                    'ot'    => intval($game['home_score'][6]),
                                    'final' => intval($game['home_score'][7]));

    /* parse slider settings */

    $_line = min(array_keys($this->slider));

    for ($i = $_line; $i < $_line + count($this->slider); ++$i) {
      if (!preg_match("/^({$this->slider[$i]}) +(\d+) +(\d+)$/", $logfile[$i], $_matches)) {
        $this->error = 'Invalid slider line: <code>'.$logfile[$i].'</code>';
        return false;
      }

      $_matches[0] = strtolower(str_replace(' ', '_', $this->slider[$i]));
      $stats['slider'][$_matches[0]]['title']  = $_matches[1];
      $stats['slider'][$_matches[0]]['human'] = $_matches[2];
      $stats['slider'][$_matches[0]]['cpu']   = $_matches[3];
    }

    unset($_line, $i, $_matches);

    /* parse game statistics */

    $_line = min(array_keys($this->teamstats));

    for ($i = $_line; $i < $_line + count($this->teamstats); ++$i) {
      if (!preg_match("/^({$this->teamstats[$i]}) +([0-9.:%-]+) +([0-9.:%-]+)$/", $logfile[$i], $_matches)) {
        $this->error = 'Invalid game stats line: <code>'.$logfile[$i].'</code>';
        return false;
      }

      $_matches[0] = strtolower(str_replace(' ', '_', trim($this->teamstats[$i])));
      $stats['team'][$_matches[0]]['title'] = $_matches[1];
      $stats['team'][$_matches[0]]['away'] = $_matches[2];
      $stats['team'][$_matches[0]]['home'] = $_matches[3];
    }

    unset($_line, $i, $_matches);

    /* parse individual stats */

    $_line = 65;

    for ($i = $_line; $i < count($logfile); ++$i) {

      /* check for a new 'individual stats' line */

      if (preg_match('/^Individual Stats: (.*?)$/', $logfile[$i], $_matches)) {
        if (!in_array($_matches[1], array_slice($game['teams'], 2))) {    /* check if this is a log of this team */
          $this->error = 'Invalid individual stats header: <code>'.$logfile[$i].'</code>';
          return false;
        }

        $_team = $_matches[1] == $stats['game']['home'] ? 'home' : 'away';

        /* initiate all categories */

        foreach (array_keys($this->headers['individual']) as $_category) {
          $stats['individual'][$_team][$_category] = array();
        }

        unset($_category);
        continue;
      }

      /* error if we don't have a current team at this point */

      if (!$_team) {
        $this->error = 'You should never see this error here #1!';
        return false;
      }

      /* check for a new stats category (if line consists of capital letters and spaces only) */

      if (preg_match('/^([A-Z]+? ?[A-Z]+)  ([A-Z0-9 ]+)$/', $logfile[$i], $_matches)) {
        if (!array_key_exists(strtolower(str_replace(' ', '_', $_matches[1])), $this->headers['individual'])) {    /* check if this category exists */
          $this->error = 'Invalid category header line: <code>'.$logfile[$i].'</code>';
          return false;
        }

        $_category = strtolower(str_replace(' ', '_', $_matches[1]));

        /* replace column labels if category is KICKING or BLOCKING or DEFENSE */

        switch ($_category) {
        case 'kicking':
          $_matches[2] = 'FGM  FGA  FGPCT FGSBLOCKED  XPA  XPM  XPPCT XPSBLOCKED KICKOFFS TOUCHBACKS';
          break;
        case 'blocking':
          $_matches[2] = 'PANCAKES SACKS_ALLOWED';
          break;
        case 'defense':
          $_matches[2] = 'TOT LOSS SACK   FF FREC  YDS   TD  INT  RET   AVG DEFLECTIONS SAFETIES CTH_ALLOW BIG_HITS';
          break;
        }

        /* split off category fields */

        $_columns = array();
        $_columns = preg_split('/ +/', strtolower(trim($_matches[2])));
        continue;
      }

      /* error if we don't have a current category or columns at this point */

      if (!$_category || count($_columns) == 0) {
        $this->error = 'You should never see this error here #2!';
        return false;
      }

      /* parse current player's stats */

      if ($logfile[$i] === 'No stats.') {
        continue;
      }

      #if (!preg_match('/^([^\d]+)'.str_repeat('\s+(-?[0-9]+\.?[0-9]?)', count($_columns)).'$/', $logfile[$i], $_player)) {
      if (!preg_match('/^\s*(.{27})'.str_repeat('\s+(-?[0-9]+\.?[0-9]?)', count($_columns)).'$/', $logfile[$i], $_player)) {
        $this->error = 'Invalid individual stats line: <code>'.$logfile[$i].'</code>';
        return false;
      }

      $_player[1] = trim($_player[1]);

      $_this = &$stats['individual'][$_team][$_category][$_player[1]];
      $_this['name'] = $_player[1];

      /* iterate over each column */

      for ($j = 0; $j < count($_columns); ++$j) {
        $_this[$_columns[$j]] = $this->_merge_individual($_this, $_columns[$j], $_player[$j + 2], $_category);
      }
    }

    unset($_line, $i, $_matches, $_team, $_category, $_columns, $_player, $_this, $j);

    /* consistency check i: sack count */

    foreach (array('away' => 'home', 'home' => 'away') as $_defense => $_offense) {

      $count[$_defense] = 0;
      $count[$_offense] = 0;

      /* sum up defenders' sack counts */

      foreach ($stats['individual'][$_defense]['defense'] as $_player) {
        $count[$_defense] += $_player['sack'];
      }

      /* sum up passers' sack counts */

      foreach ($stats['individual'][$_offense]['passing'] as $_player) {
        $count[$_offense] += $_player['sack'];
      }

      /* compare */

      if ($count[$_offense] != $count[$_defense]) {
        $this->error = 'Sack inconsistency: '.$stats['game'][$_defense].' defenders have forced '.$count[$_defense].' sack(s) but '.$stats['game'][$_offense].' passers were sacked '.$count[$_offense].' time(s).';
        return false;
      }
    }

    unset($_defense, $_offense, $count, $_player);

    /* consistency check ii: sack yards */

    foreach (array('away', 'home') as $_team) {

      $_sack_count = 0;
      $_pass_yards = 0;

      foreach ($stats['individual'][$_team]['passing'] as $_player) {
        $_sack_count += $_player['sack'];
        $_pass_yards += $_player['yds'];
      }

      $_sack_yards = $_pass_yards - $stats['team']['passing_yards'][$_team];

      if ($_sack_yards < $_sack_count) {
        $this->error = 'Sack inconsistency: '.$stats['game'][$_team].' passers were sacked '.$_sack_count.' time(s) but only lost '.$_sack_yards.' yard(s).';
        return false;
      }
    }

    unset($_team, $_sack_count, $_pass_yards, $_player, $_sack_yards);

    /* consistency check iii: efficiency */

    foreach (array('away', 'home') as $_team) {
      if ($stats['team']['third_down_conversions'][$_team] > $stats['team']['third_downs'][$_team]) {
        $this->error = 'Third down inconsistency: '.$stats['game'][$_team].' had only '.$stats['team']['third_downs'][$_team].' third down(s) but converted'.$stats['team']['third_down_conversions'][$_team];
        return false;
      }

      if ($stats['team']['fourth_down_conversions'][$_team] > $stats['team']['fourth_downs'][$_team]) {
        $this->error = 'Third down inconsistency: '.$stats['game'][$_team].' had only '.$stats['team']['fourth_downs'][$_team].' third down(s) but converted'.$stats['team']['fourth_down_conversions'][$_team];
        return false;
      }

      if ($stats['team']['two_pt_conversions_made'][$_team] > $stats['team']['two_pt_conversions_attempted'][$_team]) {
        $this->error = 'Third down inconsistency: '.$stats['game'][$_team].' had only '.$stats['team']['two_pt_conversions_attempted'][$_team].' third down(s) but converted'.$stats['team']['two_pt_conversions_made'][$_team];
        return false;
      }
    }

    /* consistency check iv: scoring */

    if (!(($stats['score']['home']['ot'] == 0 && $stats['score']['away']['ot'] == 0)
          || ($stats['score']['home']['ot'] == 0 && ($stats['score']['away']['ot'] == 2
                                                     || $stats['score']['away']['ot'] == 3
                                                     || $stats['score']['away']['ot'] == 6))
          || ($stats['score']['away']['ot'] == 0 && ($stats['score']['home']['ot'] == 2
                                                     || $stats['score']['home']['ot'] == 3
                                                     || $stats['score']['home']['ot'] == 6)))) {
        $this->error = 'Score inconsistency: invalid overtime score '.$stats['score']['away']['ot'].'-'.$stats['score']['home']['ot'];
      return false;
    }

    foreach (array('away', 'home') as $_team) {

      /* check quarter scores */

      if ($stats['score'][$_team]['q1'] + $stats['score'][$_team]['q2'] + $stats['score'][$_team]['q3'] + $stats['score'][$_team]['q4'] + $stats['score'][$_team]['ot'] != $stats['score'][$_team]['final']) {
        $this->error = 'Score inconsistency: '.$stats['game'][$_team].' quarter scores do not sum up to the final score.';
        return false;
      }

      /* check passing tds */

      $_passing_tds = 0;

      foreach ($stats['individual'][$_team]['passing'] as $_player) {
        $_passing_tds += $_player['td'];
      }

      if ($_passing_tds != $stats['team']['passing_tds'][$_team]) {
        $this->error = 'Score inconsistency: '.$stats['game'][$_team].' passers threw '.$_passing_tds.' touchdown pass(es), but game stats show '.$stats['team']['passing_tds'][$_team].' touchdown pass(es).';
        return false;
      }

      /* check rushing tds */

      $_rushing_tds = 0;

      foreach ($stats['individual'][$_team]['rushing'] as $_player) {
        $_rushing_tds += $_player['td'];
      }

      if ($_rushing_tds != $stats['team']['rushing_tds'][$_team]) {
        $this->error = 'Score inconsistency: '.$stats['game'][$_team].' rushers ran for '.$_rushing_tds.' touchdown(s), but game stats show '.$stats['team']['rushing_tds'][$_team].' touchdown(s).';
        return false;
      }

      /* check redzone success */

      if ($stats['team']['offense_redzone_tds'][$_team] > $_passing_tds + $_rushing_tds) {
        $this->error = 'Score inconsistency: '.$stats['game'][$_team].' scored '.($_passing_tds + $_rushing_tds).' offensive touchdown(s), but game stats show '.$stats['team']['offense_redzone_tds'][$_team].' redzone touchdown(s).';
        return false;
      }

      /* compare touchdown count to extra point attempts */

      $_total_tds = $_passing_tds + $_rushing_tds;
      $_safeties = 0;

      foreach ($stats['individual'][$_team]['kick_returns'] as $_player) {
        $_total_tds += $_player['td'];
      }

      foreach ($stats['individual'][$_team]['punt_returns'] as $_player) {
        $_total_tds += $_player['td'];
      }

      foreach ($stats['individual'][$_team]['defense'] as $_player) {
        $_total_tds += $_player['td'];
        $_safeties += $_player['safeties'];
      }

      $_pat_attempts        = 0;
      $_pat_made            = 0;
      $_field_goal_attempts = 0;
      $_field_goal_made     = 0;

      foreach ($stats['individual'][$_team]['kicking'] as $_player) {
        $_pat_attempts        += $_player['xpa'];
        $_pat_made            += $_player['xpm'];
        $_field_goal_attempts += $_player['fga'];
        $_field_goal_made     += $_player['fgm'];
      }

      $_twopt_attempts = $stats['team']['two_pt_conversions_attempted'][$_team];
      $_twopt_made = $stats['team']['two_pt_conversions_made'][$_team];

      if ($stats['score'][$_team]['ot'] == 6) {
        if ($_total_tds != $_pat_attempts + $_twopt_attempts + 1) {
          $this->error = 'Score inconsistency: '.$stats['game'][$_team].' scored '.$_total_tds.' touchdown(s) (1 in overtime) but had '.($_pat_attempts + $_twopt_attempts).' point after touchdown attempt(s).';
          return false;
        }
      } elseif ($_total_tds != $_pat_attempts + $_twopt_attempts) {
        $this->error = 'Score inconsistency: '.$stats['game'][$_team].' scored '.$_total_tds.' touchdown(s) but had '.($_pat_attempts + $_twopt_attempts).' point after touchdown attempt(s).';
        return false;
      }

      /* check total score */

      $_score = $_total_tds * 6 + $_pat_made + $_twopt_made * 2 + $_field_goal_made * 3 + $_safeties * 2;

      if ($_score != $stats['score'][$_team]['final']) {
        $this->error = 'Score inconsistency: '.$stats['game'][$_team].' scored '.$_total_tds.' touchdown(s) + '.$_pat_made.' extra point(s) + '.$_twopt_made.' two point conversion(s) + '.$_field_goal_made.' field goal(s) + '.$_safeties.' safeti(es) = '.$_score.' points, but score box shows '.$stats['score'][$_team]['final'].' points.';
        return false;
      }

      /* save scoring stats */

      $stats['score'][$_team]['td'] = $_total_tds;
      $stats['score'][$_team]['xpm'] = $_pat_made;
      $stats['score'][$_team]['xpa'] = $_pat_attempts;
      $stats['score'][$_team]['2pm'] = $_twopt_made;
      $stats['score'][$_team]['2pa'] = $_twopt_attempts;
      $stats['score'][$_team]['fgm'] = $_field_goal_made;
      $stats['score'][$_team]['fga'] = $_field_goal_attempts;
      $stats['score'][$_team]['safeties'] = $_safeties;
    }

    unset($_team, $_player, $_passing_tds, $_rushing_tds, $_total_tds, $_pat_attempts, $_pat_made, $_field_goal_attempts, $_field_goal_made, $_twopt_attempts, $_twopt_made, $_safeties, $_score);

    /* log parsed successfully */

    return $stats;
  } // _parse(logfile)


  /*
   * merge two individual stats columns
   *
   * $player   => all stats of this player, e.g. $player = array('att' => 7, 'yds' => 29, ...);
   * $stat     => the column which shall be merged, e.g. $stat = 'yds';
   * $value    => the value which shall be merged, e.g. $value = 4;
   * $category => the stats catgorye, e.g. $category = 'rushing';
   */
  function _merge_individual($player, $stat, $value, $category) {

    switch ($stat) {
    case 'ypa':    // yards per attempt (passing)
      $merged = $player['att'] ? round($player['yds'] / $player['att'], 1) : 0;
      break;
    case 'pct':    // completion percentage (passing)
      $merged = $player['att'] ? round($player['cmp'] / $player['att'] * 100, 0) : 0;
      break;
    case 'rating':    // passer rating (passing)
      $merged =
        $player['att'] ? round((min(max(0, 5 * $player['cmp'] / $player['att'] - 3/2), 19/8)
                               + min(max(0, $player['yds'] / 4 / $player['att'] - 3/4), 19/8)
                               + min(20 * $player['td'] / $player['att'], 19/8)
                               + max(19/8 - 25 * $player['int'] / $player['att'], 0)) / 6 * 100, 1)
        : 0;
      break;
    case 'avg':    // average run/reception/return
      switch ($category) {
      case 'rushing':
      case 'punting':
      case 'kick_returns':
      case 'punt_returns':
        $merged = $player['att'] ? round($player['yds'] / $player['att'], 1) : 0;
        break;
      case 'receiving':
        $merged = $player['rec'] ? round($player['yds'] / $player['rec'], 1) : 0;
        break;
      case 'defense':
        $merged = $player['int'] ? round($player['ret'] / $player['int'], 1) : 0;
        break;
      }
      break;
    case 'fgpct':    // field goal percentage (kicking)
      $merged = $player['fga'] ? round($player['fgm'] / $player['fga'] * 100, 0) : 0;
      break;
    case 'xppct':    // field goal percentage (kicking)
      $merged = $player['xpa'] ? round($player['xpm'] / $player['xpa'] * 100, 0) : 0;
      break;
    case 'long':    // longest attempt of a pass, rush, etc
      $merged = intval(max($value, $player[$stat]));
      break;
    case 'name':
      $merged = $value;
      break;
    default:    // default: add
      $merged = $player[$stat] + $value;
    }

    return $merged;
  } // _merge_individual()


  /*
   * performs various checks on the merged game log
   */
  function check() {

    /* check total game time */

    $_tie_game = $this->stats['score']['away']['final'] == $this->stats['score']['home']['final'];
    $_ot_game  = $this->stats['score']['away']['ot'] != 0 || $this->stats['score']['home']['ot'] != 0;

    $_total_top = $this->_min2sec($this->stats['team']['time_of_possession']['away']) + $this->_min2sec($this->stats['team']['time_of_possession']['home']);
    $_game_length = $this->stats['game']['quarter'] * ($_tie_game ? 5 : 4);

    if ($_ot_game) {
      if ($_total_top <= $_game_length * 60 || $_total_top > $_game_length * 60 * 1.25) {
        $this->error = 'Bad time of possession.  Both teams had the ball for a total of '.$this->_sec2min($_total_top).' minute(s), but should be greater than '.$_game_length.' minutes and lower than '.($_game_length * 1.25).' minutes (overtime game).';
        return false;
      }
    } else {
      if ($_total_top != $_game_length * 60) {
        $this->error = 'Bad time of possession.  Both teams had the ball for a total of '.$this->_sec2min($_total_top).' minute(s), but game length was '.$_game_length.' minutes'.($_tie_game ? ' (tie game)' : '').'.';
        return false;
      }
    }

    unset($_tie_game, $_ot_game, $_total_top, $_game_length);

    return true;
  } // check()


  /*
   * returns the string representation of this game log (ie a valid madden
   * game log)
   */
  function toString() {

    if (!$this->stats) {
      return '';
    }

    if (!$this->check()) {
      return false;
    }

    $log = '';

    /* general game information */

    $log .= $this->stats['game']['version'].' - '.$this->stats['game']['away'].' at '.$this->stats['game']['home'].'
Game Time: '.$this->stats['game']['time'].'
Skill Level: '.$this->stats['game']['level'].'
Quarter Length: '.$this->stats['game']['quarter'].' minute(s)'."\n\n";

    /* slider information */

    $log .= $this->headers['slider'];

    foreach ($this->stats['slider'] as $_slider) {
      $log .= "\n".sprintf('%-30s %5d %5d', $_slider['title'], $_slider['human'], $_slider['cpu']);
    }

    unset($_slider);

    /* weather information */

    $log .= "\n\n".'Weather
Forecast: '.$this->stats['game']['forecast'].'
Wind: '.$this->stats['game']['wind'].' mph
Temp: '.$this->stats['game']['temp'].' degrees'."\n\n";

    /* score box */

    $log .= $this->headers['scorebox']."\n";
    $log .= sprintf('%-16s %4d %4d %4d %4d %4d %10d', $this->stats['game']['away'], $this->stats['score']['away']['q1'], $this->stats['score']['away']['q2'], $this->stats['score']['away']['q3'], $this->stats['score']['away']['q4'], $this->stats['score']['away']['ot'], $this->stats['score']['away']['final'])."\n";
    $log .= sprintf('%-16s %4d %4d %4d %4d %4d %10d', $this->stats['game']['home'], $this->stats['score']['home']['q1'], $this->stats['score']['home']['q2'], $this->stats['score']['home']['q3'], $this->stats['score']['home']['q4'], $this->stats['score']['home']['ot'], $this->stats['score']['home']['final'])."\n\n";

    /* team stats */

    $log .= sprintf('%-30s %10s %10s', 'Game Statistics:', $this->stats['game']['away'], $this->stats['game']['home']);

    foreach ($this->stats['team'] as $_team) {
      $log .= "\n".sprintf('%-30s %10s %10s', $_team['title'], $_team['away'], $_team['home']);
    }

    unset($_team);

    /* individual stats */

    uksort($this->stats['individual'], array('GameLog', '_cmp_stats_ind'));

    foreach ($this->stats['individual'] as $_team => $_categories) {
      $log .= "\n\n".'Individual Stats: '.$this->stats['game'][$_team];

      foreach ($_categories as $_category => $_players) {
        $log .= "\n".$this->headers['individual'][$_category]['string']."\n";

        if (count($_players) == 0) {
          $log .= 'No stats.'."\n";
          continue;
        }

        uasort($_players, array('GameLog', '_cmp_players_'.$_category));

        foreach ($_players as $_player => $_stats) {
          array_shift($_stats);
          $_param = array_values($_stats);
          array_unshift($_param, $this->headers['individual'][$_category]['template'], $_player);
          $log .= call_user_func_array('sprintf', $_param)."\n";
        }
      }
    }

    unset($_team, $_categories, $_category, $_players, $_player, $_stats, $_param);

    /* return finished log */

    $log .= '

Game Log Ends
------------------------------------------------------------'."\n";

    return $log;
  } // toString()


  function _cmp_stats_ind($a, $b) {
    return $a == $this->stats['game']['away'] ? -1 : 1;
  }

  function _cmp_players_passing($a, $b) {
    return $b['yds'] - $a['yds'];
  }

  function _cmp_players_rushing($a, $b) {
    return $b['yds'] - $a['yds'];
  }

  function _cmp_players_receiving($a, $b) {
    return $b['yds'] - $a['yds'];;
  }

  function _cmp_players_kicking($a, $b) {
    return $b['fgm'] - $a['fgm'];
  }

  function _cmp_players_punting($a, $b) {
    return $b['yds'] - $a['yds'];
  }

  function _cmp_players_kick_returns($a, $b) {
    return $b['yds'] - $a['yds'];
  }

  function _cmp_players_punt_returns($a, $b) {
    return $b['yds'] - $a['yds'];
  }

  function _cmp_players_defense($a, $b) {
    return $b['tot'] - $a['tot'];
  }

  function _cmp_players_blocking($a, $b) {
    return $b['pancakes'] - $a['pancakes'];
  }
}

?>