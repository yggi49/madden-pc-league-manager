<?php
/**
 * @(#) error404.php
 */

class Page {

  var $periods = array('all'  => 'Overall',
                       'ex'   => 'Exhibitions',
                       'pre'  => 'Preseason',
                       'reg'  => 'Regular Season',
                       'post' => 'Postseason');


  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    $id = intval($_GET['id']);

    /* read team info */

    $query = 'SELECT u.id                        AS uid,
                     u.nick                      AS user,
                     CONCAT(n.team, " ", n.nick) AS team,
                     t.season                    AS season
              FROM   '.$_SYS['table']['team'].' AS t
                     LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
                     LEFT JOIN '.$_SYS['table']['user'].' AS u ON t.user = u.id
              WHERE  t.id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Team does not exist.');
    }

    $row = $result->fetch_assoc();

    $_SYS['request']['season'] = $row['season'];

    $output .= '
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$id.'">Home</a>
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$id.'">News</a>
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$id.'">Roster</a>
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$id.'">Schedule</a>
  &middot; [ Stats ]
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$id.'">Scouts</a>
</p>';

    /* determine period */

    $period = $_GET['period'];

    if (!array_key_exists($period, $this->periods) && intval($period) == 0) {
      if ($_SYS['request']['week'] == 0) {
        $period = 'ex';
      } elseif ($_SYS['request']['week'] < 0) {
        $period = 'pre';
      } elseif ($_SYS['request']['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
        $period = 'post';
      } else {
        $period = 'reg';
      }
    }

    $_period = array();

    foreach ($this->periods as $_key => $_val) {
      if ($period == $_key) {
        $_period[] = '
  [ '.$_val.' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?period='.$_key.'&amp;id='.$id.'">'.$_val.'</a>';
      }
    }

    $output .= '
<p>
  '.join(' &middot;', $_period).'<br />';

    $_period = array();

    foreach ($_SYS['season'][$_SYS['request']['season']]['weeks'] as $_key => $_val) {
      if ($_key < 0) {
        $_val = 'P'.(-$_key);
      } elseif ($_key > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
        $_val = $_SYS['season'][$_SYS['request']['season']]['post_names'][$_key - $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] - 1]['acro'];
      } else {
        $_val = $_key;
      }

      if ($period == $_key) {
        $_period[] = '
  [ '.$_val.' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?id='.$id.'&amp;period='.$_key.'">'.$_val.'</a>';
      }
    }

    $output .= join(' &middot;', $_period);
    $output .= '
</p>';

    unset($_period);

    /* determine weeks */

    if ($period == 'ex') {
      $weeks = '= 0';
    } elseif ($period == 'pre') {
      $weeks = '< 0';
    } elseif ($period == 'reg') {
      $weeks = 'IN ('.join(', ', $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg']).')';
    } elseif ($period == 'post') {
      $weeks = '> '.$_SYS['season'][$_SYS['request']['season']]['reg_weeks'];
    } elseif ($period == 'all') {
      $_pre_weeks = $_SYS['season'][$_SYS['request']['season']]['pre_weeks'] > 0 ? range(-1, -$_SYS['season'][$_SYS['request']['season']]['pre_weeks']) : array();
      $_reg_weeks = $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg'];
      $_post_weeks = $_SYS['season'][$_SYS['request']['season']]['post_weeks'] > 0 ? range($_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + 1, $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + $_SYS['season'][$_SYS['request']['season']]['post_weeks']) : array();

      $weeks = 'IN (0, '.join(', ', array_merge($_pre_weeks, $_reg_weeks, $_post_weeks)).')';
      unset($_pre_weeks, $_reg_weeks, $_post_weeks);
    } elseif (intval($period) != 0) {
      $period = intval($period);

      if ($period > 0 && $period <= $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] && !in_array($period, $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg'])) {
        $weeks = 'IS NULL';
      } else {
        $weeks = '= '.$period;
      }
    } else {
      return $_SYS['html']->fehler('1', 'You should never see this if there is no programming error.');
    }

    /* query team stats */

    $stats = array();

    $query = 'SELECT   IF(team = '.$id.', "team", "opp")                                                                                                     AS `team`,
                       SUM(first_downs)                                                                                                                      AS `01:m:First Downs`,
                       CONCAT(SUM(third_down_conv), "-", SUM(third_downs), "-", ROUND(IFNULL(100 * SUM(third_down_conv) / SUM(third_downs), 0), 0), "%")     AS `02:m:Third Down Efficiency`,
                       CONCAT(SUM(fourth_down_conv), "-", SUM(fourth_downs), "-", ROUND(IFNULL(100 * SUM(fourth_down_conv) / SUM(fourth_downs), 0), 0), "%") AS `03:m:Fourth Down Efficiency`,
                       SUM(rushing_yds) + SUM(passing_yds)                                                                                                   AS `04:m:Total Net Yards`,
                       SUM(rushing_att) + SUM(passing_att) + SUM(sacks)                                                                                      AS `05:s:Total Plays`,
                       ROUND(IFNULL((SUM(rushing_yds) + SUM(passing_yds)) / (SUM(rushing_att) + SUM(passing_att) + SUM(sacks)), 0), 1)                       AS `06:s:Yards Per Play`,
                       SUM(rushing_yds)                                                                                                                      AS `07:m:Net Yards Rushing`,
                       SUM(rushing_att)                                                                                                                      AS `08:s:Attempts`,
                       ROUND(IFNULL(SUM(rushing_yds) / SUM(rushing_att), 0), 1)                                                                              AS `09:s:Yards Per Rush`,
                       SUM(passing_yds)                                                                                                                      AS `10:m:Net Yards Passing`,
                       CONCAT(SUM(passing_cmp), "-", SUM(passing_att), "-", ROUND(IFNULL(100 * SUM(passing_cmp) / SUM(passing_att), 0), 0), "%")             AS `11:s:Completions-Attempts`,
                       ROUND(IFNULL(SUM(passing_yds) / (SUM(passing_att) + SUM(sacks)), 0), 1)                                                               AS `12:s:Yards Per Pass`,
                       CONCAT(SUM(sacks), "-", SUM(sack_yds))                                                                                                AS `13:s:Sacked-Yards Lost`,
                       SUM(interceptions)                                                                                                                    AS `14:s:Had Intercepted`,
                       0                                                                                                                                     AS `15:m:Return Yards`,
                       "0-0"                                                                                                                                 AS `16:s:Punts-Returns`,
                       "0-0"                                                                                                                                 AS `17:s:Kickoffs-Returns`,
                       "0-0"                                                                                                                                 AS `18:s:Int-Returns`,
                       CONCAT(SUM(penalties), "-", SUM(penalty_yds))                                                                                         AS `19:m:Penalties-Yards`,
                       CONCAT(SUM(fumbles), "-", SUM(fumbles_lost))                                                                                          AS `20:m:Fumbles-Lost`,
                       SUM(rushing_td) + SUM(passing_td)                                                                                                     AS `21:m:Touchdowns`,
                       SUM(rushing_td)                                                                                                                       AS `22:s:Rushing`,
                       SUM(passing_td)                                                                                                                       AS `23:s:Passing`,
                       0                                                                                                                                     AS `24:s:Defensive`,
                       0                                                                                                                                     AS `25:s:Returns`,
                       CONCAT(SUM(two_pt_conv_made), "-", SUM(two_pt_conv_att))                                                                              AS `26:m:Extra Points`,
                       "0-0"                                                                                                                                 AS `27:s:Kicks`,
                       CONCAT(SUM(two_pt_conv_made), "-", SUM(two_pt_conv_att))                                                                              AS `28:s:Two Point Conversions`,
                       "0-0"                                                                                                                                 AS `29:m:Field Goals`,
                       0                                                                                                                                     AS `30:m:Safeties`,
                       CONCAT(ROUND(IFNULL(100 * (SUM(redzone_td) + SUM(redzone_fg)) / SUM(redzone_num), 0), 0), "%")                                        AS `31:m:Red Zone Efficiency`,
                       SUM(redzone_num)                                                                                                                      AS `32:s:Attempts`,
                       SUM(redzone_td)                                                                                                                       AS `33:s:Touchdowns`,
                       SUM(redzone_fg)                                                                                                                       AS `34:s:Field Goals`,
                       SUM(interceptions) + SUM(fumbles_lost)                                                                                                AS `35:m:Turnover`,
                       SUM(interceptions)                                                                                                                    AS `36:s:Interceptions`,
                       SUM(fumbles_lost)                                                                                                                     AS `37:s:Fumbles Lost`,
                       SEC_TO_TIME(SUM(TIME_TO_SEC(top)))                                                                                                    AS `38:m:Time of Possession`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON s.game = g.id
              WHERE    (g.home = '.$id.' OR g.away = '.$id.') AND g.week '.$weeks.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $info = $result->info();

    while ($row = $result->fetch_assoc()) {
      for ($i = 1; $i < $result->cols(); ++$i) {
        $_col = explode(':', $info['name'][$i]);
        $stats[intval($_col[0])]['type'] = $_col[1];
        $stats[intval($_col[0])]['title'] = $_col[2];
        $stats[intval($_col[0])][$row['team']] = $row[$info['name'][$i]];
      }
    }

    unset($info, $i, $_col);

    /* get punt return stats */

    $query = 'SELECT   IF(team = '.$id.', "team", "opp") AS `team`,
                       SUM(`att`) AS `att`,
                       SUM(`yds`) AS `yds`,
                       SUM(`td`)  AS `td`
              FROM     '.$_SYS['table']['stats_punt_returns'].' AS s
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON s.game = g.id
              WHERE    (g.home = '.$id.' OR g.away = '.$id.') AND g.week '.$weeks.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $stats[15][$row['team']] += $row['yds'];    // total return yards
      $stats[16][$row['team']] = $row['att'].'-'.$row['yds'];    // punt returns
      $stats[21][$row['team']] += $row['td'];    // total touchdowns
      $stats[25][$row['team']] += $row['td'];    // return touchdowns
    }

    /* get kick return stats */

    $query = 'SELECT   IF(team = '.$id.', "team", "opp") AS `team`,
                       SUM(`att`) AS `att`,
                       SUM(`yds`) AS `yds`,
                       SUM(`td`)  AS `td`
              FROM     '.$_SYS['table']['stats_kick_returns'].' AS s
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON s.game = g.id
              WHERE    (g.home = '.$id.' OR g.away = '.$id.') AND g.week '.$weeks.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $stats[15][$row['team']] += $row['yds'];    // total return yards
      $stats[17][$row['team']] = $row['att'].'-'.$row['yds'];    // kick returns
      $stats[21][$row['team']] += $row['td'];    // total touchdowns
      $stats[25][$row['team']] += $row['td'];    // return touchdowns
    }

    /* get defensive return and safeties stats */

    $query = 'SELECT   IF(team = '.$id.', "team", "opp") AS `team`,
                       SUM(`int`)      AS `att`,
                       SUM(`ret`)      AS `yds`,
                       SUM(`td`)       AS `td`,
                       SUM(`safeties`) AS `safeties`
              FROM     '.$_SYS['table']['stats_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON s.game = g.id
              WHERE    (g.home = '.$id.' OR g.away = '.$id.') AND g.week '.$weeks.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $stats[15][$row['team']] += $row['yds'];    // total return yards
      $stats[18][$row['team']] = $row['att'].'-'.$row['yds'];    // int returns
      $stats[21][$row['team']] += $row['td'];    // total touchdowns
      $stats[24][$row['team']] += $row['td'];    // defensive touchdowns
      $stats[30][$row['team']] = $row['safeties'];    // safeties
    }

    /* get kicking stats */

    $query = 'SELECT   IF(team = '.$id.', "team", "opp") AS `team`,
                       CONCAT(SUM(`fgm`), "-", SUM(`fga`)) AS `fg`,
                       CONCAT(SUM(`xpm`), "-", SUM(`xpa`)) AS `xp`
              FROM     '.$_SYS['table']['stats_kicking'].' AS s
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON s.game = g.id
              WHERE    (g.home = '.$id.' OR g.away = '.$id.') AND g.week '.$weeks.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $stats[27][$row['team']] = $row['xp'];    // extra point kicks
      $stats[29][$row['team']] = $row['fg'];    // field goals

      $_xp = explode('-', $row['xp']);
      $_2p = explode('-', $stats[28][$row['team']]);

      $stats[26][$row['team']] = ($_xp[0] + $_2p[0]).'-'.($_xp[1] + $_2p[1]);
      unset($_fg, $_2p);
    }

    /* print team stats */

    $output .= '
<table class="boxteam">
  <thead>
    <tr>
      <td>TEAM STATISTICS</td>
      <th scope="col">Team</th>
      <th scope="col">Opponents</th>
    </tr>
  </thead>
  <tbody>';

    if (count($stats) == 0) {
      $output .= '
    <tr>
      <td colspan="3">No stats available.</td>
    </tr>';
    }

    foreach ($stats as $_stat) {
      $output .= '
    <tr'.($_stat['type'] == 's' ? ' class="sub"' : '').'>
      <th scope="row">'.$_stat['title'].'</th>
      <td>'.$_stat['team'].'</td>
      <td>'.$_stat['opp'].'</td>
    </tr>';
    }

    unset($stats, $_stat);

    $output .= '
  </tbody>
</table>';

    /* print individual stats */

    $categories = array('passing'      => array('fields' => 'SUM(`att`) AS `_att`, SUM(`cmp`) AS `_cmp`, CONCAT(SUM(`cmp`), "/", SUM(`att`)) AS `cp/at`, SUM(`yds`) AS `yds`, SUM(`td`) AS `td`, SUM(`int`) AS `int`,
                                                             ROUND((LEAST(GREATEST(0, 5 * SUM(`cmp`) / SUM(`att`) - 3/2), 19/8)
                                                                    + LEAST(GREATEST(0, SUM(`yds`) / 4 / SUM(`att`) - 3/4), 19/8)
                                                                    + LEAST(20 * SUM(`td`) / SUM(`att`), 19/8)
                                                                    + GREATEST(19/8 - 25 * SUM(`int`) / SUM(`att`), 0))
                                                                   / 6 * 100, 1) AS `rat`',
                                                'where'  => '',
                                                'order'  => '`yds` DESC, `_att` DESC, `_cmp` DESC, `td` DESC, `int` DESC, `rat` DESC'),
                        'rushing'      => array('fields' => 'SUM(`att`) AS `att`, SUM(`yds`) AS `yds`, SUM(`td`) AS `td`, SUM(`fum`) AS `fum`, MAX(`long`) AS `lg`',
                                                'where'  => '',
                                                'order'  => '`yds` DESC, `att` DESC, `td` DESC, `fum` ASC, `lg` DESC'),
                        'receiving'    => array('fields' => 'SUM(`rec`) AS `rec`, SUM(`yds`) AS `yds`, SUM(`td`) AS `td`, SUM(`drop`) AS `drp`, MAX(`long`) AS `lg`',
                                                'where'  => '`rec` > 0',
                                                'order'  => '`yds` DESC, `rec` DESC, `td` DESC, `drp` ASC, `lg` DESC'),
                        'kicking'      => array('fields' => 'SUM(`fgm`) AS `_fgm`, SUM(`fga`) AS `_fga`, SUM(`xpa`) AS `_xpa`, CONCAT(SUM(`fgm`), "/", SUM(`fga`)) AS `fg`, CONCAT(SUM(`xpm`), "/", SUM(`xpa`)) AS `xp`, (3 * SUM(`fgm`) + SUM(`xpm`)) AS `pts`',
                                                'where'  => '(`_fga` > 0 OR `_xpa` > 0)',
                                                'order'  => '`pts` DESC, `_fgm` DESC'),
                        'punting'      => array('fields' => 'SUM(`att`) AS `no`, ROUND(SUM(`yds`) / SUM(`att`), 1) AS `avg`, SUM(`in20`) AS `i20`, SUM(`touchbacks`) AS `tb`, MAX(`long`) AS `lg`',
                                                'where'  => '`no` > 0',
                                                'order'  => '`no` DESC, `avg` DESC'),
                        'kick_returns' => array('fields' => 'SUM(`att`) AS `no`, ROUND(IFNULL(SUM(`yds`) / SUM(`att`), 0), 1) AS `avg`, SUM(`td`) AS `td`, MAX(`long`) AS `lg`',
                                                'where'  => '`no` > 0',
                                                'order'  => '`no` DESC, `avg` DESC'),
                        'punt_returns' => array('fields' => 'SUM(`att`) AS `no`, ROUND(IFNULL(SUM(`yds`) / SUM(`att`), 0), 1) AS `avg`, SUM(`td`) AS `td`, MAX(`long`) AS `lg`',
                                                'where'  => '`no` > 0',
                                                'order'  => '`no` DESC, `avg` DESC'),
                        'defense'      => array('fields' => 'SUM(`tot`) AS `_tot`, SUM(`loss`) AS `_loss`, CONCAT(SUM(`tot`), "-", SUM(`loss`)) AS `t-l`, SUM(`sack`) AS `sck`, SUM(`int`) AS `int`, SUM(`ff`) AS `ff`, SUM(`td`) AS `td`',
                                                'where'  => '(`_tot` > 0 OR `_loss` > 0 OR `sck` > 0 OR `int` > 0 OR `ff` > 0 OR `td` > 0)',
                                                'order'  => '`_tot` DESC, `_loss` DESC, `sck` DESC, `int` DESC, `ff` DESC, `td` DESC'),
                        'blocking'     => array('fields' => 'SUM(`pancakes`) AS `pan`, SUM(`sacks_allowed`) AS `sack`',
                                                'where'  => '',
                                                'order'  => '`pan` DESC, `sack` DESC'));

    $i = 0;

    foreach ($categories as $_cat => $_sql) {
      $query = 'SELECT   `name`, '.$_sql['fields'].'
                FROM     '.$_SYS['table']['stats_'.$_cat].' AS s
--                         LEFT JOIN '.$_SYS['table']['game'].' AS g ON s.game = g.id
                WHERE    team = '.$id.' AND week '.$weeks.'
                GROUP BY `name`
                '.($_sql['where'] ? 'HAVING '.$_sql['where'] : '').'
                ORDER BY '.$_sql['order'];
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      $info = $result->info();

      $output .= '
<table class="boxind float">
  <thead>
    <tr>
      <td>'.strtoupper(str_replace('_', ' ', $_cat)).'</td>';

      foreach ($info['name'] as $_info) {
        if ($_info == 'name' || $_info[0] == '_') {
          continue;
        }

        $output .= '
      <th scope="col">'.strtoupper($_info).'</th>';
      }

      $output .= '
    </tr>
  </thead>
  <tbody>';

      if (!$result->rows()) {
        $output .= '
    <tr class="no">
      <td colspan="'.count($info['name']).'">NO STATS.</td>
    </tr>';
      }

      while ($row = $result->fetch_assoc()) {
        $output .= '
    <tr>';

        foreach ($row as $_key => $_val) {
          if ($_key[0] == '_') {
            continue;
          } elseif ($_key == 'name') {
            $output .= '
      <th scope="row">'.$_val.'</th>';
          } else {
            $output .= '
      <td>'.$_val.'</td>';
          }
        }

        $output .= '
    </tr>';
      }

      $output .= '
  </tbody>
</table>';

      if (++$i % 2) {
        $output .= '
<br class="float" />';
      }
    }

    $output .= '
<br class="float" />';

    return $output;
  } // getHTML()

} // Page

?>