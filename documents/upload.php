<?php
/**
 * @(#) upload.php
 */

class Page {

  var $_game;    // game data


  /** ------------------------------------------------------------------------
   * constructor
   * -------------------------------------------------------------------------
   */
  function Page() {} // constructor


  /** ------------------------------------------------------------------------
   * returns header information for this page
   * -------------------------------------------------------------------------
   */
  function getHeader() {
    return '';
  } // getHeader()


  /** ------------------------------------------------------------------------
   * checks if the current user is authorized to upload a game log for a
   * certain game for a certain team.  returns an empty string if successful;
   * returns an error message if not.
   * -------------------------------------------------------------------------
   */
  function _checkGame($game, $team) {
    global $_SYS;

    $game = intval($game);
    $team = intval($team);

    /* fetch game from db */

    $query = 'SELECT g.away        AS away,
                     na.team       AS away_team,
                     na.nick       AS away_nick,
                     na.acro       AS away_acro,
                     ta.user       AS away_hc,
                     g.away_sub    AS away_sub,
                     ta.conference AS away_conference,
                     ta.division   AS away_division,
                     g.home        AS home,
                     nh.team       AS home_team,
                     nh.nick       AS home_nick,
                     nh.acro       AS home_acro,
                     th.user       AS home_hc,
                     g.home_sub    AS home_sub,
                     th.conference AS home_conference,
                     th.division   AS home_division,
                     g.site        AS site,
                     g.week        AS week,
                     g.season      AS season,
                     s.name        AS season_name
              FROM   '.$_SYS['table']['game'].' AS g
                     LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON g.away = ta.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'    AS na ON ta.team = na.id
                     LEFT JOIN '.$_SYS['table']['team'].'   AS th ON g.home = th.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'    AS nh ON th.team = nh.id
                     LEFT JOIN '.$_SYS['table']['season'].' AS s  ON g.season = s.id
              WHERE  g.id = '.$game;

    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* check if game exists */

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Game does not exist.');
    }

    /* check if game was already played */

    $row = $result->fetch_assoc();

    if ($row['site'] != 0) {
      return $_SYS['html']->fehler('2', 'Game was already played.');
    }

    /* allow if user is admin OR user is hc or sub of the team */

    if (!(($team == 0 && $_SYS['user']['admin'])
          || ($team == $row['away'] && ($_SYS['user']['id'] == $row['away_hc'] || $_SYS['user']['id'] == $row['away_sub']))
          || ($team == $row['home'] && ($_SYS['user']['id'] == $row['home_hc'] || $_SYS['user']['id'] == $row['home_sub'])))) {
      return $_SYS['html']->fehler('3', 'You cannot upload a log for this game.');
    }

    $this->_game = $row;

    return '';
  } // _checkGame(game, team)


  /** ------------------------------------------------------------------------
   * handles a POST request
   * -------------------------------------------------------------------------
   */
  function _postRequest($admin=null) {
    global $_SYS;

    /* get parameters */

    $game = intval($_POST['game']);
    $team = intval($_POST['team']);

    if (is_array($admin)) {
      $_SYS['user']['admin'] = 1;
      $_SYS['request']['season'] = 1;
      $_SYS['season'][1]['reg_weeks'] = 17;
      $game = intval($admin['game']);
      $team = 0;
    }

    $admin_upload = $team == 0 && $_SYS['user']['admin'];

    /* check authorization */

    if ($error = $this->_checkGame($game, $team)) {
      return $error;
    }

    /* determine time constraints for this upload */

    if (!$admin_upload && !$_SYS['util']->can_upload($this->_game['season'], $this->_game['week'])) {
      return $_SYS['html']->fehler('5', 'You cannot upload a log for this game now.');
    }

    /* check which form fields contain game logs */

    $logfiles = array();

    for ($i = 1; $i <= 5; ++$i) {
      if (is_uploaded_file($_FILES['log'.$i]['tmp_name'])) {
        $logfiles[] = $i;
      }
    }

    /* instantiate new gamelog object */

    $gamelog = new GameLog();

    if (is_array($admin)) {
      if (!$gamelog->add($admin['log'])) {
        return 'FEHLER!!! '.$gamelog->error().'<h1>log:</h1>'.$_SYS['html']->dump($admin['log']);
      }
    } else {

      /* add first log */

      $current_log = array_shift($logfiles);

      if (!$gamelog->addFile($_FILES['log'.$current_log]['tmp_name'])) {
        return $_SYS['html']->fehler('1', 'Error when trying to add '.$_FILES['log'.$current_log]['name'].': '.$gamelog->error());
      }

      /* check if teams in log correspond to teams in database */

      if (!(($this->_game['away_nick'] == $gamelog->stats['game']['away'] && $this->_game['home_nick'] == $gamelog->stats['game']['home'])
            || ($this->_game['away_nick'] == $gamelog->stats['game']['home'] && $this->_game['home_nick'] == $gamelog->stats['game']['away']))) {
        return $_SYS['html']->fehler('2', 'Bad game log '.$_FILES['log'.$current_log]['name'].'; teams do not correspond');
      }

      /* add other game logs */

      while ($current_log = array_shift($logfiles)) {
        if (!$gamelog->addFile($_FILES['log'.$current_log]['tmp_name'])) {
          return $_SYS['html']->fehler('3', 'Error when trying to add '.$_FILES['log'.$current_log]['name'].': '.$gamelog->error());
        }
      }
    }

    /* perform final checks on log */

    if (!$gamelog->check()) {
      return $_SYS['html']->fehler('4', 'Game Log contains errors: '.$gamelog->error());
    }

    /* yes we have a valid game log */

    if (!$admin_upload) {

      $rate = 0;

      /* check if opponent was rated */

      if ($_SYS['site']['title'] == 'FFML') {
          $rate = intval($_POST['rate']);

          if ($rate < 1 || $rate > 3) {
              return $_SYS['html']->fehler('99', 'Es wurde keine Bewertung &uuml;ber den Gegner abgegeben!');
          }
      }

      /* check if there is a gamelog of the other team */

      $query = 'SELECT `game`, `team`, `user`, `date`, `rate`, `log`
                FROM   '.$_SYS['table']['pending'].'
                WHERE  game = '.$game.'
                       AND team != '.$team;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() == 0) {    /* save log in 'pending' */
        $query = 'REPLACE '.$_SYS['table']['pending'].'
                  SET     `game` = '.$game.',
                          `team` = '.$team.',
                          `user` = '.$_SYS['user']['id'].',
                          `date` = NOW(),
                          `rate` = '.intval($rate).',
                          `log`  = '.$_SYS['dbh']->escape_string($gamelog->toString());
        $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        return '
<p>Log Upload successful and waiting for acknowledgement.</p>
<p><a href="'.$_SYS['page']['schedule']['url'].'?season='.$this->_game['season'].'&amp;week='.$this->_game['week'].'">Back</a></p>';
      }

      /* check if logs are equal */

      $row = $result->fetch_assoc();

      $log_away = $team == $this->_game['away'] ? $gamelog->toString() : $row['log'];
      $log_home = $team == $this->_game['home'] ? $gamelog->toString() : $row['log'];

      $log_away = preg_replace('/Game Time: [A-Z][a-z]{2} [A-Z][a-z]{2} \d{2} \d{2}:\d{2}:\d{2} \d{4}/', '', $log_away);
      $log_home = preg_replace('/Game Time: [A-Z][a-z]{2} [A-Z][a-z]{2} \d{2} \d{2}:\d{2}:\d{2} \d{4}/', '', $log_home);

      if (strcmp($log_away, $log_home) != 0) {
        $query = 'REPLACE '.$_SYS['table']['pending'].'
                  SET     `game` = '.$game.',
                          `team` = '.$team.',
                          `user` = '.$_SYS['user']['id'].',
                          `date` = NOW(),
                          `rate` = '.intval($rate).',
                          `log`  = '.$_SYS['dbh']->escape_string($gamelog->toString());
        $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        $log_away = explode("\n", $log_away);
        $log_home = explode("\n", $log_home);

        $_len = max(count($log_away), count($log_home));
        $output = '
<p>
  Game Log does not match pending log.<br />
  A notification email has been sent to the league commissioners.
</p>
<table class="diff">
  <thead>
    <tr>
      <th>'.$this->_game['away_team'].' '.$this->_game['away_nick'].'</th>
      <th>Line</th>
      <th>'.$this->_game['home_team'].' '.$this->_game['home_nick'].'</th>
    </tr>
  </thead>
  <tbody>';

        $email = 'Logs for Game #'.$game.' ('.$this->_game['season_name'].' / Week '.$this->_game['week'].': '.$this->_game['away_nick'].' @ '.$this->_game['home_nick'].') did not match.'."\n\n";

        for ($i = 0; $i < $_len; ++$i) {
          if (strcmp($log_away[$i], $log_home[$i]) != 0) {
            $email .= sprintf('%-3s', $this->_game['away_acro']).': '.$log_away[$i]."\n";
            $email .= sprintf('%-3s', $this->_game['home_acro']).': '.$log_home[$i]."\n\n";

            $output .= '
    <tr>
      <td>'.$_SYS['html']->specialchars($log_away[$i]).'</td>
      <td class="line">'.$i.'</td>
      <td>'.$_SYS['html']->specialchars($log_home[$i]).'</td>
    </tr>';
          }
        }

        $output .= '
  </tbody>
</table>
<p><a href="'.$_SYS['page']['schedule']['url'].'?season='.$this->_game['season'].'&amp;week='.$this->_game['week'].'">Back</a></p>';

        /* send notification email */

        $query = 'SELECT   CONCAT(nick, " <", email, ">") AS email
                  FROM     '.$_SYS['table']['user'].'
                  WHERE    admin = 1
                           AND status = "Active"
                           AND email != ""
                  ORDER BY nick';
        $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        if ($result->rows() > 0) {
          $mailto = array();

          while ($row = $result->fetch_assoc()) {
            $mailto[] = $row['email'];
          }

          mail(join(', ', $mailto), '['.$_SYS['mail']['league'].'] Differing Logs', $email, 'From: '.$_SYS['mail']['from']);
        }

        unset($i, $_len, $email, $mailto);

        return $output;
      }

      unset($log_away, $log_home);

      /*
       * logs are equal - determine if game was played by a substitution hc or
       * not and continue
       */

      if ($team == $this->_game['away']) {
        if ($_SYS['user']['id'] != $this->_game['away_sub']) {
          $this->_game['away_sub'] = 0;
        }

        if ($row['user'] != $this->_game['home_sub']) {
          $this->_game['home_sub'] = 0;
        }
      } else {
        if ($_SYS['user']['id'] != $this->_game['home_sub']) {
          $this->_game['home_sub'] = 0;
        }

        if ($row['user'] != $this->_game['away_sub']) {
          $this->_game['away_sub'] = 0;
        }
      }

      // determine ratings for home_hc/sub and away_hc/sub

      if ($team == $this->_game['away']) {
          $this->_game['away_rate'] = intval($row['rate']);
          $this->_game['home_rate'] = intval($rate);
      }
      else {
          $this->_game['away_rate'] = intval($rate);
          $this->_game['home_rate'] = intval($row['rate']);
      }
    }
    /* admin upload -- no subs; no rating */
    else {
      $this->_game['away_sub'] = 0;
      $this->_game['home_sub'] = 0;
      $this->_game['away_rate'] = 0;
      $this->_game['home_rate'] = 0;
    }

    /*
     * insert results into db:
     *  1 - insert game results in table "game"
     *  2 - save gamelog
     *  3 - update table "standings" for regular season and preseason games
     *  4 - update 9 individual stats tables
     *  5 - update 4 team stats tables
     *  6 - delete pending entries for this game
     */

    $_reversed = $this->_game['home_nick'] != $gamelog->stats['game']['home'];
    $_winner   =  $gamelog->stats['score']['away']['final'] > $gamelog->stats['score']['home']['final'] ? 'away' :
                 ($gamelog->stats['score']['away']['final'] < $gamelog->stats['score']['home']['final'] ? 'home' :
                                                                                                          'tie');

    /* start transaction */

    $query = 'START TRANSACTION';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* insert game results in table "game" */

    $query = 'UPDATE '.$_SYS['table']['game'].'
              SET    `away_hc`    = '.$this->_game['away_hc'].',
                     `away_sub`   = '.$this->_game['away_sub'].',
                     `away_rate`  = '.intval($this->_game['away_rate']).',
                     `home_hc`    = '.$this->_game['home_hc'].',
                     `home_sub`   = '.$this->_game['home_sub'].',
                     `home_rate`  = '.intval($this->_game['home_rate']).',
                     `site`       = '.($_reversed ? $this->_game['away'] : $this->_game['home']).',
                     `date`       = "'.date('Y-m-d H:i:s', strtotime($gamelog->stats['game']['time'])).'",
                     `away_q1`    = '.($_reversed ? $gamelog->stats['score']['home']['q1'] : $gamelog->stats['score']['away']['q1']).',
                     `away_q2`    = '.($_reversed ? $gamelog->stats['score']['home']['q2'] : $gamelog->stats['score']['away']['q2']).',
                     `away_q3`    = '.($_reversed ? $gamelog->stats['score']['home']['q3'] : $gamelog->stats['score']['away']['q3']).',
                     `away_q4`    = '.($_reversed ? $gamelog->stats['score']['home']['q4'] : $gamelog->stats['score']['away']['q4']).',
                     `away_ot`    = '.($_reversed ? $gamelog->stats['score']['home']['ot'] : $gamelog->stats['score']['away']['ot']).',
                     `away_score` = '.($_reversed ? $gamelog->stats['score']['home']['final'] : $gamelog->stats['score']['away']['final']).',
                     `home_q1`    = '.($_reversed ? $gamelog->stats['score']['away']['q1'] : $gamelog->stats['score']['home']['q1']).',
                     `home_q2`    = '.($_reversed ? $gamelog->stats['score']['away']['q2'] : $gamelog->stats['score']['home']['q2']).',
                     `home_q3`    = '.($_reversed ? $gamelog->stats['score']['away']['q3'] : $gamelog->stats['score']['home']['q3']).',
                     `home_q4`    = '.($_reversed ? $gamelog->stats['score']['away']['q4'] : $gamelog->stats['score']['home']['q4']).',
                     `home_ot`    = '.($_reversed ? $gamelog->stats['score']['away']['ot'] : $gamelog->stats['score']['home']['ot']).',
                     `home_score` = '.($_reversed ? $gamelog->stats['score']['away']['final'] : $gamelog->stats['score']['home']['final']).',
                     `forecast`   = "'.$gamelog->stats['game']['forecast'].'",
                     `wind`       = '.$gamelog->stats['game']['wind'].',
                     `temp`       = '.$gamelog->stats['game']['temp'].',
                     `inserted`   = '.$_SYS['time']['now'].'
              WHERE  `id`         = '.$game;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* store gamelog */

    $query = 'INSERT INTO '.$_SYS['table']['log'].'
              SET         game = '.$game.',
                          log  = "'.$gamelog->toString().'"';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* update 9 individual stats tables */

    foreach (array_keys($gamelog->stats['individual']) as $_team) {
      $_opp = $_team == 'home' ? 'away' : 'home';

      foreach (array_keys($gamelog->stats['individual'][$_team]) as $_category) {
        $query = '';
        $_sql = array();

        foreach ($gamelog->stats['individual'][$_team][$_category] as $_player) {
          foreach (array_keys($_player) as $_key) {
            if (in_array($_key, array('pct', 'ypa', 'rating', 'avg', 'fgpct', 'xppct'))) {
              unset($_player[$_key]);
            }
          }

          $_player['season']  = $this->_game['season'];
          $_player['week']    = $this->_game['week'];
          $_player['game']    = $game;
          $_player['team']    = $_reversed ? $this->_game[$_opp] : $this->_game[$_team];
          $_player['matchup'] = $this->_game[$_team.'_acro'].($_team == 'home' ? ' vs ' : ' @ ').$this->_game[$_opp.'_acro'];

          if ($query == '') {
            $query = 'INSERT INTO '.$_SYS['table']['stats_'.$_category].' (`'.join('`, `', array_keys($_player)).'`) VALUES'."\n";
          }

          $_sql[] = '("'.join('", "', $_player).'")';
        }

        $query .= join(",\n", $_sql);

        if ($query) {
          $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
        }
      }
    }

    unset($_team, $_opp, $_sql, $_category, $_player, $_key);

    /* update 2 team scoring tables */

    foreach (array_keys($gamelog->stats['score']) as $_team) {
      $_scores = $gamelog->stats['score'][$_team];

      $_scores['season'] = $this->_game['season'];
      $_scores['week']   = $this->_game['week'];
      $_scores['game']   = $game;
      unset($_scores['final']);

      foreach (array('home', 'away') as $_team2) {
        $_opp2  = $_team2 == 'home' ? 'away' : 'home';
        $_table = $_team == $_team2 ? 'offense' : 'defense';

        $_scores['team']    = $_reversed ? $this->_game[$_opp2] : $this->_game[$_team2];
        $_scores['matchup'] = $_SYS['dbh']->escape_string($this->_game[$_team2.'_acro'].($_team2 == 'home' ? ' vs ' : ' @ ').$this->_game[$_opp2.'_acro']);

        $query = 'INSERT INTO '.$_SYS['table']['stats_scoring_'.$_table].' (`'.join('`, `', array_keys($_scores)).'`) VALUES'."\n";
        $query .= '('.join(', ', $_scores).')';

        $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
      }
    }

    unset($_team, $_scores, $_team2, $_opp2, $_table);

    /* update 2 team stats tables */

    $gamelog->stats['team']['time_of_possession']['home'] = '00:'.$gamelog->stats['team']['time_of_possession']['home'];
    $gamelog->stats['team']['time_of_possession']['away'] = '00:'.$gamelog->stats['team']['time_of_possession']['away'];

    foreach (array('away', 'home') as $_team) {
      foreach (array('away', 'home') as $_team2) {
        $_table = $_reversed ? ($_team == $_team2 ? 'defense' : 'offense') : ($_team == $_team2 ? 'offense' : 'defense');

        /* calculate sack stats */

        $_sacks = 0;
        $_pass_yds = 0;

        foreach ($gamelog->stats['individual'][$_team2]['passing'] as $_player) {
          $_sacks    += $_player['sack'];
          $_pass_yds += $_player['yds'];
        }

        $query = 'INSERT INTO '.$_SYS['table']['stats_team_'.$_table].' SET
                  season      = '.$this->_game['season'].',
                  week        = '.$this->_game['week'].',
                  game        = '.$game.',
                  team        = '.$this->_game[$_team].',
                  matchup     = '.$_SYS['dbh']->escape_string($this->_game[$_team.'_acro'].($_team == 'home' ? ' vs ' : ' @ ').$this->_game[($_team == 'home' ? 'away' : 'home').'_acro']).",\n";

        foreach (array_keys($gamelog->stats['team']) as $_category) {
          $_col_name = $_category;

          switch ($_category) {
          case 'third_down_percentage':
          case 'fourth_down_percentage':
          case 'two_pt_conversion_percentage':
          case 'offense_redzone_percentage':
          case 'rushing_average':
          case 'completion_percentage':
          case 'passing_average':
          case 'total_offense':
          case 'defensive_pass_interceptions':
          case 'punt_return_yards':
          case 'kick_return_yards':
          case 'punts':
          case 'punt_avg':
          case 'turnovers':
            continue 2;
          case 'third_down_conversions':       $_col_name = 'third_down_conv';  break;
          case 'forth_down_conversions':       $_col_name = 'fourth_down_conv'; break;
          case 'fourth_down_conversions':      $_col_name = 'fourth_down_conv'; break;
          case 'two_pt_conversions_made':      $_col_name = 'two_pt_conv_made'; break;
          case 'two_pt_conversions_attempted': $_col_name = 'two_pt_conv_att';  break;
          case 'offense_redzone_num':          $_col_name = 'redzone_num';      break;
          case 'offense_redzone_tds':          $_col_name = 'redzone_td';       break;
          case 'offense_redzone_fgs':          $_col_name = 'redzone_fg';       break;
          case 'rushing_attempts':             $_col_name = 'rushing_att';      break;
          case 'rushing_yards':                $_col_name = 'rushing_yds';      break;
          case 'rushing_tds':                  $_col_name = 'rushing_td';       break;
          case 'passing_yards':                $_col_name = 'passing_yds';      break;
          case 'passing_completions':          $_col_name = 'passing_cmp';      break;
          case 'passing_attempts':             $_col_name = 'passing_att';      break;
          case 'passing_tds':                  $_col_name = 'passing_td';       break;
          case 'offensive_pass_interceptions': $_col_name = 'interceptions';    break;
          case 'time_of_possession':           $_col_name = 'top';              break;
          case 'fumbles':                      $_col_name = $_table == 'offense' ? 'fumbles'      : 'fumbles_forced';    break;
          case 'fumbles_lost':                 $_col_name = $_table == 'offense' ? 'fumbles_lost' : 'fumbles_recovered'; break;
          }

          $query .= '`'.$_col_name.'` = "'.$gamelog->stats['team'][$_category][$_team2].'",'."\n";
        }

        $query .= '`sacks` = '.$_sacks.',
                   `sack_yds` = '.($_pass_yds - $gamelog->stats['team']['passing_yards'][$_team2]);

        $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
      }
    }

    unset($_team, $_team2, $_table, $_sql, $_category, $_col_name);

    /* delete pending entries for this game */

    $query = 'DELETE FROM '.$_SYS['table']['pending'].' WHERE game = '.$game;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* commit transaction */

    $query = 'COMMIT';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* send notification emails */

    if ($this->_game['week'] <= 0
        || $this->_game['week'] > $_SYS['season'][$this->_game['season']]['reg_weeks']
        || ($this->_game['week'] >= 1
            && $this->_game['week'] <= $_SYS['season'][$this->_game['season']]['reg_weeks']
            && in_array($this->_game['week'], $_SYS['season'][$this->_game['season']]['visible_weeks']['reg']))) {

      $query = 'SELECT CONCAT(nick, " <", email, ">") AS mailto
                FROM   '.$_SYS['table']['user'].'
                WHERE  email != ""
                       AND notify = 1
                       AND status = "Active"';
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($this->_game['week'] == 0) {
        $_week = 'EX';
      } elseif ($this->_game['week'] < 0) {
        $_week = 'P'.(-$this->_game['week']);
      } elseif ($this->_game['week'] > $_SYS['season'][$this->_game['season']]['reg_weeks']) {
        $_week = $_SYS['season'][$this->_game['season']]['post_names'][$this->_game['week'] - $_SYS['season'][$this->_game['season']]['reg_weeks'] - 1]['acro'];
      } else {
        $_week = 'W'.$this->_game['week'];
      }

      $_subject = "[{$_SYS['mail']['league']}] {$this->_game['season_name']}/$_week: ";
      $_subject .= $_reversed
        ? "{$gamelog->stats['game']['home']} {$gamelog->stats['score']['home']['final']} @ {$gamelog->stats['game']['away']} {$gamelog->stats['score']['away']['final']}"
        : "{$gamelog->stats['game']['away']} {$gamelog->stats['score']['away']['final']} @ {$gamelog->stats['game']['home']} {$gamelog->stats['score']['home']['final']}";

      $_text = 'Box Score: http://'.$_SERVER['SERVER_NAME'].$_SYS['page']['boxscore']['url'].'?game='.$game;

      while ($row = $result->fetch_assoc()) {
        mail($row['mailto'], $_subject, $_text, 'From: '.$_SYS['mail']['from']);
      }

      unset($_week, $_subject, $_text);
    }

    /* back to schedule */

    return '
<p>Game Log upload successful'.($admin_upload ? '' : ' and acknowledged').'.</p>
<p><a href="'.$_SYS['page']['schedule']['url'].'?season='.$this->_game['season'].'&amp;week='.$this->_game['week'].'">Back</a></p>';
  } // _postRequest()


  /** ------------------------------------------------------------------------
   * handles a GET request
   * -------------------------------------------------------------------------
   */
  function _getRequest() {
    global $_SYS;

    /* get parameters */

    $game = intval($_GET['game']);
    $team = intval($_GET['team']);

    $admin_upload = $team == 0 && $_SYS['user']['admin'];

    /* check authorization */

    if ($error = $this->_checkGame($game, $team)) {
      return $error;
    }

    /* check time constraints for this upload */

    if (!$admin_upload && !$_SYS['util']->can_upload($this->_game['season'], $this->_game['week'])) {
      return $_SYS['html']->fehler('5', 'You cannot upload a log for this game now.');
    }

    /* show form */

    $output = '
<p>
  '.$_SYS['season'][$this->_game['season']]['name'].',
  '.($this->_game['week'] < 0 ? 'Preseason Week '.(-$this->_game['week']) : ($this->_game['week'] > $_SYS['season'][$this->_game['season']]['reg_weeks'] ? $_SYS['season'][$this->_game['season']]['post_names'][$this->_game['week'] - $_SYS['season'][$this->_game['season']]['reg_weeks'] - 1]['name'] : ($this->_game['week'] == 0 ? 'Exhibition' : 'Week '.$this->_game['week']))).':
  '.$this->_game['away_team'].' '.$this->_game['away_nick'].'
  @
  '.$this->_game['home_team'].' '.$this->_game['home_nick'].'
  ('.($team == 0 ? 'Admin Upload' : ($team == $this->_game['home'] ? $this->_game['home_acro'] : $this->_game['away_acro'])).')
</p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post" enctype="multipart/form-data">
<dl>
  <dt>'.$_SYS['html']->label('flog1', 'Log 1').'</dt>
  <dd>'.$_SYS['html']->file('log1', 0, '', 'id="flog1" tabindex="10"').'</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('flog2', 'Log 2').'</dt>
  <dd>'.$_SYS['html']->file('log2', 0, '', 'id="flog2" tabindex="20"').'</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('flog3', 'Log 3').'</dt>
  <dd>'.$_SYS['html']->file('log3', 0, '', 'id="flog3" tabindex="30"').'</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('flog4', 'Log 4').'</dt>
  <dd>'.$_SYS['html']->file('log4', 0, '', 'id="flog4" tabindex="40"').'</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('flog5', 'Log 5').'</dt>
  <dd>'.$_SYS['html']->file('log5', 0, '', 'id="flog5" tabindex="50"').'</dd>
</dl>

';

    if ($_SYS['site']['title'] == 'FFML' && $team != 0) {
        $output .= '
<dl>
  <dt>Bewertung</dt>
  <dd>
    '.$_SYS['html']->radio('rate', '3', '', 'frate2').'
    '.$_SYS['html']->label('frate2', 'Mein Gegner hat &uuml;beraus respektvoll und fair gespielt.').'
  </dd>
  <dd>
    '.$_SYS['html']->radio('rate', '1', '', 'frate0').'
    '.$_SYS['html']->label('frate0', 'Mein Gegner hat extrem unfair gespielt und sich sehr h&auml;ufig nicht gerne gesehener Spielweisen bedient.').'
  </dd>
  <dd>
    '.$_SYS['html']->radio('rate', '2', '', 'frate1').'
    '.$_SYS['html']->label('frate1', 'Keiner der beiden obigen Punkte trifft zu.').'
  </dd>
  <dd>
    <em>Hinweis: Dieses Feature befindet sich in der Probephase.  Die
    Bewertung bleibt geheim und kann nur von den neutralen
    (nicht-mitspielenden) Mitgliedern der Liga-Leitung eingesehen werden.</em>
  </dd>
</dl>';
    }

    $output .= '
<dl>
  <dt>
    '.$_SYS['html']->hidden('game', $game).'
    '.$_SYS['html']->hidden('team', $team).'
  </dt>
  <dd>'.$_SYS['html']->submit('submit', 'Upload').'</dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  /** ------------------------------------------------------------------------
   * handles a request to this page
   * -------------------------------------------------------------------------
   */
  function getHTML() {
    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return $this->_postRequest();
      break;
    default:
      return $this->_getRequest();
    }
  } // getHTML()
}
?>