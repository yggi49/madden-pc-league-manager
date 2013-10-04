<?php
/**
 * @(#) boxscore.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $game = intval($_GET['game']);

    /* query score */

    $query = 'SELECT   g.id                                                                                  AS `game_id`,
                       g.season                                                                              AS `season`,
                       g.week                                                                                AS `week`,
                       IF(g.site = 0, "&nbsp;",
                          CONCAT(DATE_FORMAT(g.`date`, "%c/%e/%Y"),
                                 " @ ",
                                 IF(g.site = g.away, IFNULL(na.acro, "&nbsp;"), IFNULL(nh.acro, "&nbsp;")))) AS `site`,
                       g.away                                                                                AS `away_id`,
                       na.team                                                                               AS `away_team`,
                       na.nick                                                                               AS `away_nick`,
                       na.acro                                                                               AS `away_acro`,
                       ta.user                                                                               AS `away_hc`,
                       g.away_sub                                                                            AS `away_sub`,
                       ua.nick                                                                               AS `away_hc_name`,
                       uas.nick                                                                              AS `away_sub_name`,
                       IF(g.site = 0, "&nbsp;", g.away_q1)                                                   AS `away_q1`,
                       IF(g.site = 0, "&nbsp;", g.away_q2)                                                   AS `away_q2`,
                       IF(g.site = 0, "&nbsp;", g.away_q3)                                                   AS `away_q3`,
                       IF(g.site = 0, "&nbsp;", g.away_q4)                                                   AS `away_q4`,
                       IF(g.site = 0, "&nbsp;", g.away_ot)                                                   AS `away_ot`,
                       IF(g.site = 0, "&nbsp;", g.away_score)                                                AS `away_score`,
                       g.home                                                                                AS `home_id`,
                       nh.team                                                                               AS `home_team`,
                       nh.nick                                                                               AS `home_nick`,
                       nh.acro                                                                               AS `home_acro`,
                       th.user                                                                               AS `home_hc`,
                       g.home_sub                                                                            AS `home_sub`,
                       uh.nick                                                                               AS `home_hc_name`,
                       uhs.nick                                                                              AS `home_sub_name`,
                       IF(g.site = 0, "&nbsp;", g.home_q1)                                                   AS `home_q1`,
                       IF(g.site = 0, "&nbsp;", g.home_q2)                                                   AS `home_q2`,
                       IF(g.site = 0, "&nbsp;", g.home_q3)                                                   AS `home_q3`,
                       IF(g.site = 0, "&nbsp;", g.home_q4)                                                   AS `home_q4`,
                       IF(g.site = 0, "&nbsp;", g.home_ot)                                                   AS `home_ot`,
                       IF(g.site = 0, "&nbsp;", g.home_score)                                                AS `home_score`,
                       (g.site != 0 AND (g.away_ot != g.home_ot OR g.away_score = g.home_score))             AS `overtime_game`,
                       (g.site != 0)                                                                         AS `played`
              FROM     '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON ta.id = g.away
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON na.id = ta.team
                       LEFT JOIN '.$_SYS['table']['user'].' AS ua ON ua.id = g.away_hc
                       LEFT JOIN '.$_SYS['table']['user'].' AS uas ON uas.id = g.away_sub
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON th.id = g.home
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON nh.id = th.team
                       LEFT JOIN '.$_SYS['table']['user'].' AS uh ON uh.id = g.home_hc
                       LEFT JOIN '.$_SYS['table']['user'].' AS uhs ON uhs.id = g.home_sub
                       LEFT JOIN '.$_SYS['table']['team'].' AS ts ON ts.id = g.site
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS ns ON ns.id = ts.team
              WHERE    g.id = '.$game;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $_SYS['html']->fehler('1', 'Game not found');
    }

    $row = $result->fetch_assoc();
    $row['invisible'] = $row['site'] != 0
                        && $row['week'] > 0
                        && $row['week'] <= $_SYS['season'][$row['season']]['reg_weeks']
                        && !in_array($row['week'], $_SYS['season'][$row['season']]['visible_weeks']['reg'])
                        && !($row['home_sub'] == '0' && $row['home_hc'] == $_SYS['user']['id'])
                        && !($row['home_sub'] != '0' && $row['home_sub'] == $_SYS['user']['id'])
                        && !($row['away_sub'] == '0' && $row['away_hc'] == $_SYS['user']['id'])
                        && !($row['away_sub'] != '0' && $row['away_sub'] == $_SYS['user']['id']);

    $_SYS['request']['season'] = $row['season'];
    $_SYS['request']['week']   = $row['week'];

    $team = array('away' => array('id'   => $row['away_id'],
                                  'team' => $row['away_team'],
                                  'nick' => $row['away_nick'],
                                  'acro' => $row['away_acro'],
                                  'user' => array()),
                  'home' => array('id'   => $row['home_id'],
                                  'team' => $row['home_team'],
                                  'nick' => $row['home_nick'],
                                  'acro' => $row['home_acro'],
                                  'user' => array()));

    /* query teams' records */

    $record = $_SYS['util']->get_records($row['season'], $row['week']);

    /* get previous and next game id for teams */

    $links = $_SYS['util']->get_prev_next($game);

    /* print back link */

    $output .= '
<p><a href="'.$_SYS['page']['schedule']['url'].'?season='.$row['season'].'&amp;week='.$row['week'].'">Back to '.($row['week'] == 0 ? 'Exhibitions' : ($row['week'] < 0 ? 'Preseason Week '.(-$row['week']) : ($row['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] ? $_SYS['season'][$_SYS['request']['season']]['post_names'][$row['week'] - $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] - 1]['name'] : 'Week '.$row['week']))).'</a></p>';

    /* print score box */

    $output .= '
<table class="score extended">
  <thead>
    <tr>
      <th scope="col" class="prev game">Previous</th>
      <th scope="col" class="coach">Owner (Sub)</th>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['site']).'</td>
      <th scope="col">1</th>
      <th scope="col">2</th>
      <th scope="col">3</th>
      <th scope="col">4</th>';

    if ($row['overtime_game'] && !$row['invisible']) {
      $output .= '
      <th scope="col">OT</th>';
    }

    $output .= '
      <th scope="col">T</th>
      <th scope="col" class="next game">Next</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>'.($links[$row['away_id']]['prev']['game'] ? $links[$row['away_id']]['prev']['ha'].' <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?game='.$links[$row['away_id']]['prev']['game'].'">'.$links[$row['away_id']]['prev']['opp'].'</a>' : '&nbsp;').'</td>
      <td><a href="'.$_SYS['page']['profile']['url'].'?id='.$row['away_hc'].'">'.$row['away_hc_name'].'</a>'.($row['away_sub'] != 0 ? ' (<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['away_sub'].'">'.$row['away_sub_name'].'</a>)' : '').'</td>
      <th scope="row">
        '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['away_acro']).'.gif" alt="'.$row['away_acro'].'" class="logo" /> ' : '').'
        <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['away_id'].'">'.($row['away_team'] == 'New York' ? 'NY '.$row['away_nick'] : $row['away_team']).'</a>
        '.$record[$row['away_id']].'
        '.(!$row['invisible'] && $row['away_score'] > $row['home_score'] ? '&laquo;' : '');

    if ($_SYS['util']->can_upload($_SYS['request']['season'], $_SYS['request']['week']) && $row['site'] == 0 && $_SYS['user']['id'] && ($_SYS['user']['id'] == intval($row['away_hc']) || $_SYS['user']['id'] == intval($row['away_sub']))) {
      $output .= '
        <a href="'.$_SYS['page']['upload']['url'].'?game='.$row['game_id'].'&amp;team='.$row['away_id'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/up.gif" alt="U" /></a>';
    }

    $output .= '
      </th>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['away_q1']).'</td>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['away_q2']).'</td>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['away_q3']).'</td>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['away_q4']).'</td>';

    if ($row['overtime_game'] && !$row['invisible']) {
      $output .= '
      <td>'.$row['away_ot'].'</td>';
    }

    $output .= '
      <td class="final">'.($row['invisible'] ? '&nbsp;' : $row['away_score']).'</td>
      <td>'.($links[$row['away_id']]['next']['game'] ? $links[$row['away_id']]['next']['ha'].' <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?game='.$links[$row['away_id']]['next']['game'].'">'.$links[$row['away_id']]['next']['opp'].'</a>' : '&nbsp;').'</td>
    </tr>
    <tr>
      <td>'.($links[$row['home_id']]['prev']['game'] ? $links[$row['home_id']]['prev']['ha'].' <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?game='.$links[$row['home_id']]['prev']['game'].'">'.$links[$row['home_id']]['prev']['opp'].'</a>' : '&nbsp;').'</td>
      <td><a href="'.$_SYS['page']['profile']['url'].'?id='.$row['home_hc'].'">'.$row['home_hc_name'].'</a>'.($row['home_sub'] != 0 ? ' (<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['home_sub'].'">'.$row['home_sub_name'].'</a>)' : '').'</td>
      <th scope="row">
        '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['home_acro']).'.gif" alt="'.$row['home_acro'].'" class="logo" /> ' : '').'
        <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['home_id'].'">'.($row['home_team'] == 'New York' ? 'NY '.$row['home_nick'] : $row['home_team']).'</a>
        '.$record[$row['home_id']].'
        '.(!$row['invisible'] && $row['home_score'] > $row['away_score'] ? '&laquo;' : '');

    if ($_SYS['util']->can_upload($_SYS['request']['season'], $_SYS['request']['week']) && $row['site'] == 0 && $_SYS['user']['id'] && ($_SYS['user']['id'] == intval($row['away_hc']) || $_SYS['user']['id'] == intval($row['away_sub']))) {
      $output .= '
        <a href="'.$_SYS['page']['upload']['url'].'?game='.$row['game_id'].'&amp;team='.$row['home_id'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/up.gif" alt="U" /></a>';
    }

    $output .= '
      </th>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['home_q1']).'</td>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['home_q2']).'</td>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['home_q3']).'</td>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['home_q4']).'</td>';

    if ($row['overtime_game'] && !$row['invisible']) {
      $output .= '
      <td>'.$row['home_ot'].'</td>';
    }

    $output .= '
      <td class="final">'.($row['invisible'] ? '&nbsp;' : $row['home_score']).'</td>
      <td>'.($links[$row['home_id']]['next']['game'] ? $links[$row['home_id']]['next']['ha'].' <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?game='.$links[$row['home_id']]['next']['game'].'">'.$links[$row['home_id']]['next']['opp'].'</a>' : '&nbsp;').'</td>
    </tr>
  </tbody>
</table>';

    if (!$row['played']) {
      $output .= '
<p>The game has not been played yet.</p>';
      return $output;
    }

    if ($row['invisible']) {
      $output .= '
<p>The result of this game is invisible.</p>';
      return $output;
    }

    /* print links to recaps and game log */

    $query = 'SELECT COUNT(*) AS `count`
              FROM   '.$_SYS['table']['comment'].'
              WHERE  game = '.$row['game_id'];
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    $row2 = $result->fetch_assoc();

    $_recaps = '(Recaps)';

    if ($row2['count'] > 0) {
      $_recaps = 'Recaps';

      if ($_SYS['user']['id']) {
        $query = 'SELECT COUNT(*) AS `count`
                  FROM   '.$_SYS['table']['comment'].'
                  WHERE  game = '.$row['game_id'].'
                         AND UNIX_TIMESTAMP(`date`) > '.$_SYS['user']['last_visit'];
        $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
        $row2 = $result->fetch_assoc();

        if ($row2['count'] > 0) {
          $_recaps .= '*';
        }
      }
    }

    $output .= '
<p>
  <a href="'.$_SYS['page']['recaps']['url'].'?game='.$row['game_id'].'">'.$_recaps.'</a>
  &middot; [ Box Score ]
  &middot; <a href="'.$_SYS['page']['gamelog']['url'].'?game='.$row['game_id'].'">Game Log</a>';

    if ($_SYS['user']['admin']) {
      $output .= '
        &middot; <a href="'.$_SYS['page']['clear']['url'].'?game='.$row['game_id'].'">Clear Game</a>';
    }

    $output .= '
</p>';

    unset($_recaps);

    /* query team stats */

    $stats = array();

    $query = 'SELECT   team                                                                                                              AS `team`,
                       first_downs                                                                                                       AS `01:m:First Downs`,
                       CONCAT(third_down_conv, "-", third_downs, "-", ROUND(IFNULL(100 * third_down_conv / third_downs, 0), 0), "%")     AS `02:m:Third Down Efficiency`,
                       CONCAT(fourth_down_conv, "-", fourth_downs, "-", ROUND(IFNULL(100 * fourth_down_conv / fourth_downs, 0), 0), "%") AS `03:m:Fourth Down Efficiency`,
                       rushing_yds + passing_yds                                                                                         AS `04:m:Total Net Yards`,
                       rushing_att + passing_att + sacks                                                                                 AS `05:s:Total Plays`,
                       ROUND(IFNULL((rushing_yds + passing_yds) / (rushing_att + passing_att + sacks), 0), 1)                            AS `06:s:Yards Per Play`,
                       rushing_yds                                                                                                       AS `07:m:Net Yards Rushing`,
                       rushing_att                                                                                                       AS `08:s:Attempts`,
                       ROUND(IFNULL(rushing_yds / rushing_att, 0), 1)                                                                    AS `09:s:Yards Per Rush`,
                       passing_yds                                                                                                       AS `10:m:Net Yards Passing`,
                       CONCAT(passing_cmp, "-", passing_att, "-", ROUND(IFNULL(100 * passing_cmp / passing_att, 0), 0), "%")             AS `11:s:Completions-Attempts`,
                       ROUND(IFNULL(passing_yds / (passing_att + sacks), 0), 1)                                                          AS `12:s:Yards Per Pass`,
                       CONCAT(sacks, "-", sack_yds)                                                                                      AS `13:s:Sacked-Yards Lost`,
                       interceptions                                                                                                     AS `14:s:Had Intercepted`,
                       0                                                                                                                 AS `15:m:Return Yards`,
                       "0-0"                                                                                                             AS `16:s:Punts-Returns`,
                       "0-0"                                                                                                             AS `17:s:Kickoffs-Returns`,
                       "0-0"                                                                                                             AS `18:s:Int-Returns`,
                       CONCAT(penalties, "-", penalty_yds)                                                                               AS `19:m:Penalties-Yards`,
                       CONCAT(fumbles, "-", fumbles_lost)                                                                                AS `20:m:Fumbles-Lost`,
                       rushing_td + passing_td                                                                                           AS `21:m:Touchdowns`,
                       rushing_td                                                                                                        AS `22:s:Rushing`,
                       passing_td                                                                                                        AS `23:s:Passing`,
                       0                                                                                                                 AS `24:s:Defensive`,
                       0                                                                                                                 AS `25:s:Returns`,
                       CONCAT(two_pt_conv_made, "-", two_pt_conv_att)                                                                    AS `26:m:Extra Points`,
                       "0-0"                                                                                                             AS `27:s:Kicks`,
                       CONCAT(two_pt_conv_made, "-", two_pt_conv_att)                                                                    AS `28:s:Two Point Conversions`,
                       "0-0"                                                                                                             AS `29:m:Field Goals`,
                       0                                                                                                                 AS `30:m:Safeties`,
                       CONCAT(ROUND(IFNULL(100 * (redzone_td + redzone_fg) / redzone_num, 0), 0), "%")                                   AS `31:m:Red Zone Efficiency`,
                       redzone_num                                                                                                       AS `32:s:Attempts`,
                       redzone_td                                                                                                        AS `33:s:Touchdowns`,
                       redzone_fg                                                                                                        AS `34:s:Field Goals`,
                       interceptions + fumbles_lost                                                                                      AS `35:m:Turnover`,
                       interceptions                                                                                                     AS `36:s:Interceptions`,
                       fumbles_lost                                                                                                      AS `37:s:Fumbles Lost`,
                       TRIM(LEADING "0" FROM TIME_FORMAT(top, "%i:%s"))                                                                  AS `38:m:Time of Possession`
              FROM     '.$_SYS['table']['stats_team_offense'].'
              WHERE    game = '.$game;
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

    $query = 'SELECT   `team`     AS `team`,
                       SUM(`att`) AS `att`,
                       SUM(`yds`) AS `yds`,
                       SUM(`td`)  AS `td`
              FROM     '.$_SYS['table']['stats_punt_returns'].'
              WHERE    `game` = '.$game.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $stats[15][$row['team']] += $row['yds'];    // total return yards
      $stats[16][$row['team']] = $row['att'].'-'.$row['yds'];    // punt returns
      $stats[21][$row['team']] += $row['td'];    // total touchdowns
      $stats[25][$row['team']] += $row['td'];    // return touchdowns
    }

    /* get kick return stats */

    $query = 'SELECT   `team`     AS `team`,
                       SUM(`att`) AS `att`,
                       SUM(`yds`) AS `yds`,
                       SUM(`td`)  AS `td`
              FROM     '.$_SYS['table']['stats_kick_returns'].'
              WHERE    `game` = '.$game.'
              GROUP BY 1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $stats[15][$row['team']] += $row['yds'];    // total return yards
      $stats[17][$row['team']] = $row['att'].'-'.$row['yds'];    // kick returns
      $stats[21][$row['team']] += $row['td'];    // total touchdowns
      $stats[25][$row['team']] += $row['td'];    // return touchdowns
    }

    /* get defensive return and safeties stats */

    $query = 'SELECT   `team`          AS `team`,
                       SUM(`int`)      AS `att`,
                       SUM(`ret`)      AS `yds`,
                       SUM(`td`)       AS `td`,
                       SUM(`safeties`) AS `safeties`
              FROM     '.$_SYS['table']['stats_defense'].'
              WHERE    `game` = '.$game.'
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

    $query = 'SELECT   `team`                              AS `team`,
                       CONCAT(SUM(`fgm`), "-", SUM(`fga`)) AS `fg`,
                       CONCAT(SUM(`xpm`), "-", SUM(`xpa`)) AS `xp`
              FROM     '.$_SYS['table']['stats_kicking'].'
              WHERE    `game` = '.$game.'
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
      <th scope="col">'.$team['away']['nick'].'</th>
      <th scope="col">'.$team['home']['nick'].'</th>
    </tr>
  </thead>
  <tbody>';

    foreach ($stats as $_stat) {
      $output .= '
    <tr'.($_stat['type'] == 's' ? ' class="sub"' : '').'>
      <th scope="row">'.$_stat['title'].'</th>
      <td>'.$_stat[$team['away']['id']].'</td>
      <td>'.$_stat[$team['home']['id']].'</td>
    </tr>';
    }

    unset($stats, $_stat);

    $output .= '
  </tbody>
</table>';

    /* print individual stats */

    $output .= '
<table class="float boxind head">
  <tr>
    <td>'.$team['away']['team'].' '.$team['away']['nick'].'</td>
  </tr>
</table>
<table class="float boxind head">
  <tr>
    <td>'.$team['home']['team'].' '.$team['home']['nick'].'</td>
  </tr>
</table>
<br class="float" />';

    $categories = array('passing'      => array('fields' => 'CONCAT(`cmp`, "/", `att`) AS `cp/at`, `yds`, `td`, `int`,
                                                             ROUND((LEAST(GREATEST(0, 5 * `cmp` / `att` - 3/2), 19/8)
                                                                    + LEAST(GREATEST(0, `yds` / 4 / `att` - 3/4), 19/8)
                                                                    + LEAST(20 * `td` / `att`, 19/8)
                                                                    + GREATEST(19/8 - 25 * `int` / `att`, 0))
                                                                   / 6 * 100, 1) AS `rat`',
                                                'where'  => '',
                                                'order'  => '`yds` DESC, `att` DESC, `cmp` DESC, `td` DESC, `int` DESC, `rat` DESC',),
                        'rushing'      => array('fields' => '`att`, `yds`, `td`, `fum`, `long` AS `lg`',
                                                'where'  => '',
                                                'order'  => '`yds` DESC, `att` DESC, `td` DESC, `fum` ASC, `long` DESC'),
                        'receiving'    => array('fields' => '`rec`, `yds`, `td`, `drop` AS `drp`, `long` AS `lg`',
                                                'where'  => '(`rec` > 0 OR `drop` > 0)',
                                                'order'  => '`yds` DESC, `rec` DESC, `td` DESC, `drp` ASC, `long` DESC'),
                        'kicking'      => array('fields' => 'CONCAT(`fgm`, "/", `fga`) AS `fg`, CONCAT(`xpm`, "/", `xpa`) AS `xp`, (3 * `fgm` + `xpm`) AS `pts`',
                                                'where'  => '(`fga` > 0 OR `xpa` > 0)',
                                                'order'  => '`pts` DESC, `fgm` DESC'),
                        'punting'      => array('fields' => '`att` as `no`, ROUND(`yds` / `att`, 1) AS `avg`, `in20` AS `i20`, `touchbacks` AS `tb`, `long` AS `lg`',
                                                'where'  => '`att` > 0',
                                                'order'  => '`att` DESC, (`yds` / `att`) DESC'),
                        'kick_returns' => array('fields' => '`att` AS `no`, ROUND(IFNULL(`yds` / `att`, 0), 1) AS `avg`, `td`, `long` AS `lg`',
                                                'where'  => '`att` > 0',
                                                'order'  => '`att` DESC, (`yds` / `att`) DESC'),
                        'punt_returns' => array('fields' => '`att` AS `no`, ROUND(IFNULL(`yds` / `att`, 0), 1) AS `avg`, `td`, `long` AS `lg`',
                                                'where'  => '`att` > 0',
                                                'order'  => '`att` DESC, (`yds` / `att`) DESC'),
                        'defense'      => array('fields' => 'CONCAT(`tot`, "-", `loss`) AS `t-l`, `sack` AS `sck`, `int`, `ff`, `td`',
                                                'where'  => '(`tot` > 0 OR `loss` > 0 OR `sack` > 0 OR `int` > 0 OR `ff` > 0 OR `td` > 0)',
                                                'order'  => '`tot` DESC, `loss` DESC, `sack` DESC, `int` DESC, `ff` DESC, `td` DESC'),
                        'blocking'     => array('fields' => '`pancakes` AS `pan`, `sacks_allowed` AS `sack`',
                                                'where'  => '',
                                                'order'  => '`pancakes` DESC, `sacks_allowed` DESC'));

    foreach ($categories as $_cat => $_sql) {
      foreach (array_keys($team) as $_team) {
        $query = 'SELECT   `name`, '.$_sql['fields'].'
                  FROM     '.$_SYS['table']['stats_'.$_cat].'
                  WHERE    game = '.$game.' AND team = '.$team[$_team]['id'].($_sql['where'] ? ' AND '.$_sql['where'] : '').'
                  ORDER BY '.$_sql['order'];
        $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        $info = $result->info();

        $output .= '
<table class="float boxind">
  <thead>
    <tr>
      <td>'.strtoupper(str_replace('_', ' ', $_cat)).'</td>';

        foreach ($info['name'] as $_info) {
          if ($_info == 'name') {
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
            if ($_key == 'name') {
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
      }

      $output .= '
<br class="float" />';
    }

    return $output;
  } // getHTML()

} // Page

?>