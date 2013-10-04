<?php
/**
 * @(#) functions.php
 */

class Util {


  var $teams;


  /**
   *
   */
  function team_name($id, $key=null) {
    global $_SYS;

    if (!is_array($this->teams)) {
      $this->teams = array();

      $team_count = array();
      $nick_count = array();

      $query = 'SELECT t.id   AS id,
                       n.team AS team,
                       n.nick AS nick,
                       n.acro AS acro
                FROM   '.$_SYS['table']['team'].' AS t
                       LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
                WHERE  t.season = '.$_SYS['request']['season'];
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      while ($row = $result->fetch_assoc()) {
        $row['full'] = $row['team'] . ' ' . $row['nick'];
        $this->teams[$row['id']] = $row;

        ++$team_count[$row['team']];
        ++$nick_count[$row['nick']];
      }

      foreach (array_keys($this->teams) as $_id) {
        if ($this->teams[$_id]['team'] == 'New York') {
          $my_team = 'NY '.$this->teams[$_id]['nick'];
        } elseif ($team_count[$this->teams[$_id]['team']] > 1) {
          $my_team = explode(' ', $this->teams[$_id]['team']);

          if (count($my_team) == 1) {
            $my_team = strtoupper(substr($my_team[0], 0, 3));
          } else {
            foreach (array_keys($my_team) as $_item) {
              $my_team[$_item] = strtoupper(substr($my_team[$_item], 0, 1));
            }

            $my_team = join('', $my_team);
          }

          $my_team .= ' '.$this->teams[$_id]['nick'];
        }

        if ($nick_count[$this->teams[$_id]['nick']] > 1) {
          $my_nick = strtoupper(substr($this->teams[$_id]['team'], 0, 3)).' '.$this->teams[$_id]['nick'];
        }

        if ($my_team) {
          $this->teams[$_id]['team'] = $my_team;
        }

        if ($my_nick) {
          $this->teams[$_id]['nick'] = $my_nick;
        }

        unset($_item, $my_team, $my_nick);
      }
    }

    if (!array_key_exists($id, $this->teams)) {
      return '';
    } else {
      return array_key_exists($key, $this->teams[$id]) ? $this->teams[$id][$key] : $this->teams[$id];
    }
  } // team_name(id)


  /**
   * encrypt cookie data
   */
  function cookie_encrypt($data, $key) {

    $data = serialize($data);

    if (function_exists('mcrypt_encrypt') && $data) {
      $cipher = MCRYPT_TRIPLEDES;
      $mode = MCRYPT_MODE_CFB;

      $key = substr($key, 0, mcrypt_get_key_size($cipher, $mode));
      $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), MCRYPT_RAND);

      return base64_encode($iv . mcrypt_encrypt($cipher, $key, $data, $mode, $iv));
    } else {
      return base64_encode($data);
    }
  } // cookie_encrypt()


  /**
   * decrypt cookie data
   */
  function cookie_decrypt($data, $key) {

    if (function_exists('mcrypt_decrypt') && $data) {
      $cipher = MCRYPT_TRIPLEDES;
      $mode = MCRYPT_MODE_CFB;

      if (!$data) {
        return;
      }

      $data = (base64_decode($data));

      return unserialize(mcrypt_decrypt($cipher, substr($key, 0, mcrypt_get_key_size($cipher, $mode)),
                                        substr($data, mcrypt_get_iv_size($cipher, $mode)), $mode,
                                        substr($data, 0, mcrypt_get_iv_size($cipher, $mode))));
    } else {
      return unserialize(base64_decode($data));
    }
  } // cookie_decrypt()

  /**
   * set the cookie with given id and password
   */
  function setcookie($id, $pwd) {

    global $_SYS;

    return setcookie($_SYS['cookie']['name'],
                     $this->cookie_encrypt(array('id' => $id, 'pwd' => $pwd), $_SYS['cookie']['key']),
                     $_SYS['time']['now'] + $_SYS['cookie']['expire'],
                     $_SYS['dir']['hostdir'] ? $_SYS['dir']['hostdir'] : '/');
  } // setcookie()


  /**
   * delete the cookie
   */
  function delcookie() {

    global $_SYS;

    return setcookie($_SYS['cookie']['name'],
                     '',
                     $_SYS['time']['now'] - 3600,
                     $_SYS['dir']['hostdir'] ? $_SYS['dir']['hostdir'] : '/');
  } // delcookie()


  function addpage($page) {
    global $_SYS;

    if (!is_array($_SYS['rewrite'])) {
      $_SYS['rewrite'] = array();
    }

    $_SYS['page'][$page['name']]['url']         = strpos($page['url'], 'http://') === 0 ? $page['url'] : $_SYS['dir']['hostdir'] . $page['url'];
    $_SYS['page'][$page['name']]['document']    = $_SYS['dir']['docdir'] . $page['document'];
    $_SYS['page'][$page['name']]['title']       = $_SYS['site']['title'] . (strlen($page['title']) ? ' - ' . $page['title'] : '');
    $_SYS['page'][$page['name']]['linktext']    = strlen($page['linktext']) ? $page['linktext'] : $page['title'];
    $_SYS['page'][$page['name']]['keywords']    = strlen($page['keywords']) ? $page['keywords'] : '';
    $_SYS['page'][$page['name']]['description'] = strlen($page['description']) ? $page['description'] : '';
    $_SYS['page'][$page['name']]['access']      = array_key_exists('access', $page) ? $page['access'] : true;

    $_SYS['url'][$_SYS['page'][$page['name']]['url']] = $page['name'];

    if (is_array($page['rewrite'])) {
      foreach ($page['rewrite'] as $url) {
        $_SYS['rewrite'][$_SYS['dir']['hostdir'].$url] = $_SYS['page'][$page['name']]['url'];
      }
    }
  } // addpage(page)


  /**
   * checks if a user may be deleted.  accepts a single id or an array of ids.
   * returns true or false if a single id was provided.  returns an array of
   * true and false values if an array was passed; the keys of the array are
   * the ids passed.
   */
  function is_removable($id) {
    global $_SYS;

    $id = intval($id);

    $query = 'SELECT   id
              FROM     '.$_SYS['table']['game'].' AS g
              WHERE    site != 0
                       AND (home_hc = '.$id.'
                            OR home_sub = '.$id.'
                            OR away_hc = '.$id.'
                            OR away_sub = '.$id.')
              UNION
              SELECT   id
              FROM     '.$_SYS['table']['team'].' AS t
              WHERE    user = '.$id.'
              LIMIT    1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    return $id != $_SYS['user']['id'] && $result->rows() == 0;

    /*
     * check if an array was passed; transform to a single-element array if
     * not
     */

    if (!is_array($id)) {
      $id = array($id);
    }

    /*
     * convert all array values to integers, check if each is positive, and
     * remove duplicates afterwards
     */

    $_new = array();

    foreach ($id as $_id) {
      $_id = intval($_id);

      if ($_id < 1) {
        return false;
      }

      $_new[] = $_id;
    }

    unset($_new, $_id);

    /* check for a valid id */

//     $query = 'SELECT ';

  } // is_removable()


  /**
   *
   */
  function offsetToSec($string) {

    if (preg_match('/^([+-]?)(\d+):(\d+)$/', $string, $time)) {
      if ($time[3] > 59) {
        return false;
      }

      return ($time[2] * 3600 + $time[3] * 60) * ($time[1] == '-' ? -1 : 1);
    } elseif (preg_match('/^([+-]?)(\d+)$/', $string, $time)) {
      return $time[2] * 3600 * ($time[1] == '-' ? -1 : 1);
    } else {
      return false;
    }

  } // _offsetToSec



  /*
   * returns true if users can now upload logs for week $week in season $season
   */
  function can_upload($season, $week) {
    global $_SYS;

    $season = intval($season);
    $week = intval($week);

    if (!array_key_exists($season, $_SYS['season'])) {
      return false;
    }

    if ($week != 0 && !array_key_exists($week, $_SYS['season'][$season]['weeks'])) {
      return false;
    }

    $now   = $_SYS['time']['now'];
    $begin = $_SYS['season'][$season]['weeks'][$week]['log_begin'];
    $end   = $_SYS['season'][$season]['weeks'][$week]['log_end'];

    return ($week == 0
            || (!is_null($begin) && !is_null($end) && $now >= $begin && $now <= $end)
            || (!is_null($begin) && is_null($end) && $now >= $begin)
            || (is_null($begin) && !is_null($end) && $now <= $end)
            || (is_null($begin) && is_null($end)));
  } // can_upload()


  function time_diff($seconds) {
    $seconds = intval($seconds);

    $output = $seconds > 0 ? '+' : ($seconds < 0 ? '-' : '');

    $seconds = abs($seconds);
    $hours = floor($seconds / 3600);
    $seconds = $seconds - 3600 * $hours;
    $minutes = floor($seconds / 60);
    $seconds = $seconds - 60 * $minutes;

    $output .= $hours.':'.sprintf('%02d', $minutes);#.':'.sprintf('%02d', $seconds);

    return $output;
  }


  function get_records($season, $week) {
    global $_SYS;

    $season = intval($season);
    $week = intval($week);

    $_pre_weeks = $_SYS['season'][$_SYS['request']['season']]['pre_weeks'] > 0 ? range(-1, -$_SYS['season'][$_SYS['request']['season']]['pre_weeks']) : array();
    $_reg_weeks = $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg'];
    $_post_weeks = $_SYS['season'][$_SYS['request']['season']]['post_weeks'] > 0 ? range($_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + 1, $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + $_SYS['season'][$_SYS['request']['season']]['post_weeks']) : array();

    /* query records */

    $query = 'SELECT   IF(t.id = g.home, g.home, g.away) AS `team`,
                       SUM(IF(((t.id = g.home AND g.home_score > g.away_score) OR (t.id = g.away AND g.away_score > g.home_score)) AND (g.week IN ('.join(', ', array_merge($_pre_weeks, $_reg_weeks, $_post_weeks)).') OR (g.home_sub = 0 AND g.home_hc = '.$_SYS['user']['id'].') OR (g.home_sub > 0 AND g.home_sub = '.$_SYS['user']['id'].') OR (g.away_sub = 0 AND g.away_hc = '.$_SYS['user']['id'].') OR (g.away_sub > 0 AND g.away_sub = '.$_SYS['user']['id'].')), 1, 0)) AS `won`,
                       SUM(IF(((t.id = g.home AND g.home_score < g.away_score) OR (t.id = g.away AND g.away_score < g.home_score)) AND (g.week IN ('.join(', ', array_merge($_pre_weeks, $_reg_weeks, $_post_weeks)).') OR (g.home_sub = 0 AND g.home_hc = '.$_SYS['user']['id'].') OR (g.home_sub > 0 AND g.home_sub = '.$_SYS['user']['id'].') OR (g.away_sub = 0 AND g.away_hc = '.$_SYS['user']['id'].') OR (g.away_sub > 0 AND g.away_sub = '.$_SYS['user']['id'].')), 1, 0)) AS `lost`,
                       SUM(IF(((t.id = g.home AND g.home_score = g.away_score) OR (t.id = g.away AND g.away_score = g.home_score)) AND (g.week IN ('.join(', ', array_merge($_pre_weeks, $_reg_weeks, $_post_weeks)).') OR (g.home_sub = 0 AND g.home_hc = '.$_SYS['user']['id'].') OR (g.home_sub > 0 AND g.home_sub = '.$_SYS['user']['id'].') OR (g.away_sub = 0 AND g.away_hc = '.$_SYS['user']['id'].') OR (g.away_sub > 0 AND g.away_sub = '.$_SYS['user']['id'].')), 1, 0)) AS `tied`
              FROM     '.$_SYS['table']['game'].' AS g,
                       '.$_SYS['table']['team'].' AS t
              WHERE    g.week '.($week < 0 ? '>=' : '<=').' '.$week.'
                       AND g.week '.($week < 0 ? '< 0' : '> 0').'
                       AND g.season = '.$season.'
                       AND g.site != 0
                       AND (g.home = t.id OR g.away = t.id)
              GROUP BY `team`';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $record = array();

    while ($row = $result->fetch_assoc()) {
      if ($row['won'] + $row['lost'] + $row['tied'] > 0) {
        $record[$row['team']] = '<span class="record">('.$row['won'] . '-' . $row['lost'];

        if (intval($row['tied']) > 0) {
          $record[$row['team']] .= '-' . $row['tied'];
        }

        $record[$row['team']] .= ')</span>';
      }
    }

    return $record;
  } // get_records()


  function update_matchups($season) {
    global $_SYS;

    $tables = array('stats_blocking', 'stats_defense', 'stats_kicking',
                    'stats_kick_returns', 'stats_passing', 'stats_punting',
                    'stats_punt_returns', 'stats_receiving', 'stats_rushing',
                    'stats_scoring_defense', 'stats_scoring_offense',
                    'stats_team_defense', 'stats_team_offense');

    $query = 'START TRANSACTION';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    foreach ($tables as $table) {
      $query = 'UPDATE '.$_SYS['table'][$table].' AS s
                       LEFT JOIN '.$_SYS['table']['game'].' AS g  ON s.game = g.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS t1 ON IF(g.home = s.team, g.home = t1.id, g.away = t1.id)
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n1 ON t1.team = n1.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS t2 ON IF(g.home = s.team, g.away = t2.id, g.home = t2.id)
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n2 ON t2.team = n2.id
                SET    s.matchup = CONCAT(n1.acro, IF(g.home = s.team, " vs ", " @ "), n2.acro)
                WHERE  s.season = '.intval($season);
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    $query = 'COMMIT';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
  } // update_matchups()


  function get_prev_next($game) {
    global $_SYS;

    $game = intval($game);

    $query = 'SELECT id, season, week, away, home
              FROM   '.$_SYS['table']['game'].'
              WHERE  id = '.$game;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return false;
    }

    $game = $result->fetch_assoc();

    if ($game['week'] == 0) {
      return false;
    }

    if ($game['week'] < 0) {
      $game['week'] = ($_SYS['season'][$game['season']]['pre_weeks'] + 1) * (-1) - $game['week'];
    }

    /* previous game for away team */

    $games = array();

    foreach (array('prev', 'next') as $_type) {
      foreach (array('away', 'home') as $_team) {
        $query = 'SELECT   g.id                                      AS game,
                           IF(g.home = '.$game[$_team].', "vs", "@") AS ha,
                           n.acro                                    AS opp
                  FROM     '.$_SYS['table']['game'].' AS g
                           LEFT JOIN '.$_SYS['table']['team'].' AS t ON t.id = IF(g.home = '.$game[$_team].', g.away, g.home)
                           LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON n.id = t.team
                  WHERE    g.season = '.$game['season'].'
                           AND IF(g.week < 0, '.(($_SYS['season'][$game['season']]['pre_weeks'] + 1) * (-1)).' - g.week, g.week) '.($_type == 'prev' ? '<' : '>').' '.$game['week'].'
                           AND g.week != 0
                           AND (g.home = '.$game[$_team].' OR g.away = '.$game[$_team].')
                  ORDER BY IF(g.week < 0, '.(($_SYS['season'][$game['season']]['pre_weeks'] + 1) * (-1)).' - g.week, g.week) '.($_type == 'prev' ? 'DESC' : 'ASC').'
                  LIMIT    1';
        $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        if ($row = $result->fetch_assoc()) {
          $games[$game[$_team]][$_type] = array('game' => $row['game'], 'opp' => $row['opp'], 'ha' => $row['ha']);
        } else {
          $games[$game[$_team]][$_type] = array('game' => 0, 'opp' => '', 'ha' => '');
        }
      }
    }

    return $games;
  } // get_prev_next


  function filetypes() {
    $filetypes = array();
    $filetypes['ros']  = 'Roster';
    $filetypes['fra']  = 'Franchise';
    $filetypes['html'] = 'HTML';
    $filetypes['csv']  = 'CSV';
    $filetypes['doc']  = 'Word';
    $filetypes['xls']  = 'Excel';
    $filetypes['pdf']  = 'PDF';
    $filetypes['xxx']  = 'Other';

    return $filetypes;
  }


  function compressions() {
    $compressions = array();
    $compressions['ace'] = 'ace';
    $compressions['arj'] = 'arj';
    $compressions['bz2'] = 'bzip2';
    $compressions['gz']  = 'gzip';
    $compressions['lha'] = 'lha';
    $compressions['rar'] = 'rar';
    $compressions['tbz'] = 'tar.bz2';
    $compressions['tgz'] = 'tar.gz';
    $compressions['zip'] = 'zip';
    $compressions['xxx'] = 'None';

    return $compressions;
  }


  /*
   * RFC822 Email Parser
   *
   * (C)2005 Cal Henderson <cal@iamcal.com>
   *
   * Revision 2
   */
  function valid_email($email) {
    $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
    $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
    $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
    $quoted_pair = '\\x5c\\x00-\\x7f';
    $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
    $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
    $domain_ref = $atom;
    $sub_domain = "($domain_ref|$domain_literal)";
    $word = "($atom|$quoted_string)";
    $domain = "$sub_domain(\\x2e$sub_domain)*";
    $local_part = "$word(\\x2e$word)*";
    $addr_spec = "$local_part\\x40$domain";

    return preg_match("!^$addr_spec$!", $email) ? true : false;
  } // valid_email(email)
}

?>