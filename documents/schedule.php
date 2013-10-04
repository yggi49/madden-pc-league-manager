<?php
/**
 * @(#) schedule.php
 */

class Page {


  var $visible;


  function Page() {
    global $_SYS;

    $this->visible = $_SYS['request']['week'] <= 0
                     || $_SYS['request']['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']
                     || in_array($_SYS['request']['week'], $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg']);
  } // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _fantasyLeaders() {
    global $_SYS;

    $output = '';

    $weeks = $this->visible ? '= '.$_SYS['request']['week'] : 'IS NULL';

    /* show weekly leaders offense */

    $query = 'SELECT   name,
                       SUM(points) AS points,
                       team
              FROM     (SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND(FLOOR(CAST(s.`yds` AS SIGNED) / 2) / 10 + CAST(s.`td` AS SIGNED) * 6 - CAST(s.`int` AS SIGNED) * 3, 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_passing'].'  AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND(s.yds / 10 + s.`td` * 6, 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_receiving'].'   AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND(s.yds / 10 + s.`td` * 6, 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_rushing'].'   AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.') AS tmp
              GROUP BY name, game
              ORDER BY points DESC
              LIMIT    5';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output .= '
<table class="float leaders">
  <thead>
    <tr>
      <th colspan="3">Offensive Leaders</th>
    </tr>
  </thead>
  <tbody>';

    if ($result->rows() == 0) {
      $output .= '
    <tr>
      <td colspan="3" class="empty">NO STATS.</td>
    </tr>';
    }

    for ($i = 0; $i < $result->rows(); ++$i) {
      $row = $result->fetch_assoc();
      $output .= '
    <tr>';

      if ($i == 0 && file_exists('images/players/'.$row['name'].'.jpg')) {
        $output .= '
      <td rowspan="'.$result->rows().'" class="image"><img src="images/players/'.rawurlencode($row['name']).'.jpg" alt="'.$row['name'].'" /></td>';
      }

      $output .= '
      <th scope="row">'.preg_replace('/^(.).* (.*)$/', '$1. $2', $row['name']).', '.$row['team'].'</th>
      <td>'.$row['points'].'</td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    /* show weekly leaders defense */

    $query = 'SELECT   s.name AS name,
                       ROUND(s.`tot` + s.`loss` / 2 + s.`sack` * 2 + s.`ff` * 2 + s.`frec` * 1 + s.`td` * 6 + s.`int` * 3 + s.`safeties` * 2, 1) AS points,
                       n.acro AS team
              FROM     '.$_SYS['table']['stats_defense'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.season = '.$_SYS['request']['season'].'
                       AND s.week '.$weeks.'
              ORDER BY points DESC
              LIMIT    5';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output .= '
<table class="float leaders">
  <thead>
    <tr>
      <th colspan="3">Defensive Leaders</th>
    </tr>
  </thead>
  <tbody>';

    if ($result->rows() == 0) {
      $output .= '
    <tr>
      <td colspan="3" class="empty">NO STATS.</td>
    </tr>';
    }

    for ($i = 0; $i < $result->rows(); ++$i) {
      $row = $result->fetch_assoc();
      $output .= '
    <tr>';

      if ($i == 0 && file_exists('images/players/'.$row['name'].'.jpg')) {
        $output .= '
      <td rowspan="'.$result->rows().'" class="image"><img src="images/players/'.rawurlencode($row['name']).'.jpg" alt="'.$row['name'].'" /></td>';
      }

      $output .= '
      <th scope="row">'.preg_replace('/^(.).* (.*)$/', '$1. $2', $row['name']).', '.$row['team'].'</th>
      <td>'.$row['points'].'</td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    /* show weekly leaders special teams */

    $query = 'SELECT   name,
                       points,
                       team
              FROM     (
                       SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND(s.`fgm` * 4
                                       + s.`xpm` * 1
                                       - s.`fgsblocked` * 1
                                       - s.`xpsblocked` * 2
                                       - (s.`fga` - s.`fgm`) / 2
                                       - (s.`xpa` - s.`xpm`) * 2
                                       , 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_kicking'].'  AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND((s.`yds` / s.`att`) * 25 / 100
                                       + (s.`in20` * s.`in20`) / (2 * s.`att`)
                                       + (s.`touchbacks` * s.`touchbacks` * 3) / (10 * s.`att`)
                                      , 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_punting'].'  AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND(s.`td` * 8
                                       + (s.`yds` / s.`att`) / 10
                                       + s.`long` / 10
                                      , 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_kick_returns'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.game AS game,
                                 ROUND(s.`td` * 10
                                       + (s.`yds` / s.`att`) / 10
                                       + s.`long` / 10
                                      , 1) AS points,
                                 n.acro AS team
                        FROM     '.$_SYS['table']['stats_punt_returns'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                                 AND s.week '.$weeks.'
                        )AS tmp
              GROUP BY name, game
              ORDER BY points DESC
              LIMIT    5';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output .= '
<table class="float leaders">
  <thead>
    <tr>
      <th colspan="3">S.T. Leaders</th>
    </tr>
  </thead>
  <tbody>';

    if ($result->rows() == 0) {
      $output .= '
    <tr>
      <td colspan="3" class="empty">NO STATS.</td>
    </tr>';
    }

    for ($i = 0; $i < $result->rows(); ++$i) {
      $row = $result->fetch_assoc();
      $output .= '
    <tr>';

      if ($i == 0 && file_exists('images/players/'.$row['name'].'.jpg')) {
        $output .= '
      <td rowspan="'.$result->rows().'" class="image"><img src="images/players/'.rawurlencode($row['name']).'.jpg" alt="'.$row['name'].'" /></td>';
      }

      $output .= '
      <th scope="row">'.preg_replace('/^(.).* (.*)$/', '$1. $2', $row['name']).', '.$row['team'].'</th>
      <td>'.$row['points'].'</td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';



    $output .= '
<br class="float" />';

    return $output;
  } // _fantasyLeaders()


  function _weeklyLeaders() {
    global $_SYS;

    $output = '';

    $weeks = $this->visible ? '= '.$_SYS['request']['week'] : 'IS NULL';

    foreach (array('passing', 'rushing', 'receiving') as $_category) {
      $query = 'SELECT   s.name AS name,
                         s.yds AS yards,
                         n.acro AS team
                FROM     '.$_SYS['table']['stats_'.$_category].'  AS s
                         LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                         LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                WHERE    s.season = '.$_SYS['request']['season'].'
                         AND s.week '.$weeks.'
                ORDER BY yards DESC
                LIMIT    5';
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      $output .= '
<table class="float leaders">
  <thead>
    <tr>
      <th colspan="3">'.ucfirst($_category).' Leaders</th>
    </tr>
  </thead>
  <tbody>';

      if ($result->rows() == 0) {
        $output .= '
    <tr>
      <td colspan="3" class="empty">NO STATS.</td>
    </tr>';
      }

      for ($i = 0; $i < $result->rows(); ++$i) {
        $row = $result->fetch_assoc();
        $output .= '
    <tr>';

        if ($i == 0 && file_exists('images/players/'.$row['name'].'.jpg')) {
          $output .= '
      <td rowspan="'.$result->rows().'" class="image"><img src="images/players/'.rawurlencode($row['name']).'.jpg" alt="'.$row['name'].'" /></td>';
        }

        $output .= '
      <th scope="row">'.preg_replace('/^(.).* (.*)$/', '$1. $2', $row['name']).', '.$row['team'].'</th>
      <td>'.$row['yards'].'</td>
    </tr>';
      }

      $output .= '
  </tbody>
</table>';
    }

    $output .= '
<br class="float" />';

    return $output;
  } // _weeklyLeaders()


  function getHTML() {
    global $_SYS;

    $output = '';

    /* print secondary navigation bar */

    $_leaders = $_GET['leaders'] == 'weekly' ? '&amp;leaders=weekly' : '';

    $output .= '
<p id="navbarsec">
  Exhibtions/Preseason:  ';

    $_links = array();
    $_links[] = $_SYS['request']['week'] == 0 ? '[ EX ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week=0'.$_leaders.'">EX</a>';

    for ($i = 1; $i <= $_SYS['season'][$_SYS['request']['season']]['pre_weeks']; ++$i) {
      $_link = "\n";
      $_link .= $_SYS['request']['week'] == -$i
        ? '  [ P'.$i.' ]'
        : '  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week=-'.$i.$_leaders.'">P'.$i.'</a>';

      $_links[] = $_link;
    }

    $output .= join(' &middot;', $_links).'<br />';


    $output .= '
  Week: ';

    $_links = array();

    for ($i = 1; $i <= $_SYS['season'][$_SYS['request']['season']]['reg_weeks']; ++$i) {
      $_link = "\n";
      $_link .= $_SYS['request']['week'] == $i
        ? '  [ '.$i.' ]'
        : '  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$i.$_leaders.'">'.$i.'</a>';

      $_links[] = $_link;
    }

    $output .= join(' &middot;', $_links);


    if ($_SYS['season'][$_SYS['request']['season']]['post_weeks'] > 0) {
      $output .= '<br />
  Postseason: ';

      $_links = array();

      for (; $i <= $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + $_SYS['season'][$_SYS['request']['season']]['post_weeks']; ++$i) {
        $_text = $_SYS['season'][$_SYS['request']['season']]['post_names'][$i - $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] - 1]['name'];

        $_link = "\n";
        $_link .= $_SYS['request']['week'] == $i
          ? '  [ '.$_text.' ]'
          : '  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$i.$_leaders.'">'.$_text.'</a>';

        $_links[] = $_link;
      }

      $output .= join(' &middot;', $_links);
    }

    $output .= '
</p>';

    unset($_leaders, $_links, $_link, $_text, $i);

    /* display begin and end date of week */

    if ($_SYS['request']['week'] != 0) {
      $output .= '
<div class="times">
<p>
  '.date('M j, Y', $_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['begin']).'
  &ndash;
  '.date('M j, Y', $_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['end']).'
</p>';

      if ($_SYS['user']['id']) {
        $_begin = is_null($_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['log_begin']) ? null : date('M j, Y / H:i', $_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['log_begin']);
        $_end   = is_null($_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['log_end'])   ? null : date('M j, Y / H:i', $_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['log_end']);

        $output .= '
<p class="log">
  Upload: ';

        if (is_null($_begin) && is_null($_end)) {
          $output .= 'anytime';
        } elseif (is_null($_begin)) {
          $output .= 'until '.$_end;
        } elseif (is_null($_end)) {
          $output .= 'from '.$_begin;
        } else {
          $output .= $_begin.' &ndash; '.$_end;
        }

        $output .= '
</p>';
      }

      $output .= '
</div>';

      unset($_begin, $_end);

      /* open dates */

      if ($_SYS['request']['week'] <= $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
        $query = 'SELECT   t.id                                                   AS `id`
                  FROM     '.$_SYS['table']['team'].' AS t
                           LEFT JOIN '.$_SYS['table']['game'].' AS g ON g.season = t.season AND g.week = '.$_SYS['request']['week'].' AND (g.home = t.id OR g.away = t.id)
                           LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                  WHERE    t.season = '.$_SYS['request']['season'].' AND g.id IS NULL';
        $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

        if ($result->rows() > 0) {
          while ($row = $result->fetch_assoc()) {
            $_teams[] = '<a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['id'].'">'.$_SYS['util']->team_name($row['id'], 'team').'</a>';
          }

          $output .= '
<p class="opendate">
  <strong>Open Date:</strong> '.join(', ', $_teams).'
</p>';
        }

        unset($_teams);
      }

      $output .= '
<br class="float" />';
    }

    /* show leaders */

    $output .= $_GET['leaders'] == 'weekly' ? $this->_weeklyLeaders() : $this->_fantasyLeaders();

    /* display leaders toggle */

    $output .= '
<p>
  '.($_GET['leaders'] == 'weekly' ? '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$_SYS['request']['week'].'&amp;leaders=fantasy">Fantasy Leaders</a>' : '[ Fantasy Leaders ]').' &middot;
  '.($_GET['leaders'] != 'weekly' ? '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$_SYS['request']['week'].'&amp;leaders=weekly">Weekly Leaders</a>'   : '[ Weekly Leaders ]').'
</p>';

    /* query teams' records */

    $query = 'SELECT   IF(t.id = g.home, g.home, g.away) AS `team`,
                       SUM(IF((t.id = g.home AND g.home_score > g.away_score) OR (t.id = g.away AND g.away_score > g.home_score), 1, 0)) AS `won`,
                       SUM(IF((t.id = g.home AND g.home_score < g.away_score) OR (t.id = g.away AND g.away_score < g.home_score), 1, 0)) AS `lost`,
                       SUM(IF((t.id = g.home AND g.home_score = g.away_score) OR (t.id = g.away AND g.away_score = g.home_score), 1, 0)) AS `tied`
              FROM     '.$_SYS['table']['game'].' AS g,
                       '.$_SYS['table']['team'].' AS t
              WHERE    g.week '.($_SYS['request']['week'] < 0 ? '>=' : '<=').' '.$_SYS['request']['week'].'
                       AND g.week '.($_SYS['request']['week'] < 0 ? '< 0' : '> 0').'
                       AND g.season = '.$_SYS['request']['season'].'
                       AND g.site != 0
              GROUP BY `team`';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $record = $_SYS['util']->get_records($_SYS['request']['season'], $_SYS['request']['week']);

    /* read schedule */

    $query = 'SELECT   g.id                                                                                  AS `game_id`,
                       IF(g.site = 0, "&nbsp;",
                          CONCAT(DATE_FORMAT(g.`date`, "%c/%e/%Y"),
                                 " @ ",
                                 IF(g.site = g.away, IFNULL(na.acro, "&nbsp;"), IFNULL(nh.acro, "&nbsp;")))) AS `site`,
                       IF(g.scheduled != "0000-00-00 00:00:00", DATE_FORMAT(g.scheduled, "%m/%d/%Y %H:%i"), "&nbsp;")     AS `scheduled`,
                       g.site                                                                                AS `site_id`,
                       g.away                                                                                AS `away_id`,
                       IFNULL(na.team, "&nbsp;")                                                             AS `away_team`,
                       IFNULL(na.nick, "&nbsp;")                                                             AS `away_nick`,
                       IFNULL(na.acro, "&nbsp;")                                                             AS `away_acro`,
                       ta.user                                                                               AS `away_hc`,
                       g.away_sub                                                                            AS `away_sub`,
                       IF(g.site = 0, "&nbsp;", g.away_q1)                                                   AS `away_q1`,
                       IF(g.site = 0, "&nbsp;", g.away_q2)                                                   AS `away_q2`,
                       IF(g.site = 0, "&nbsp;", g.away_q3)                                                   AS `away_q3`,
                       IF(g.site = 0, "&nbsp;", g.away_q4)                                                   AS `away_q4`,
                       IF(g.site = 0, "&nbsp;", g.away_ot)                                                   AS `away_ot`,
                       IF(g.site = 0, "&nbsp;", g.away_score)                                                AS `away_score`,
                       g.home                                                                                AS `home_id`,
                       IFNULL(nh.team, "&nbsp;")                                                             AS `home_team`,
                       IFNULL(nh.nick, "&nbsp;")                                                             AS `home_nick`,
                       IFNULL(nh.acro, "&nbsp;")                                                             AS `home_acro`,
                       th.user                                                                               AS `home_hc`,
                       g.home_sub                                                                            AS `home_sub`,
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
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON th.id = g.home
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON nh.id = th.team
              WHERE    g.season = '.$_SYS['request']['season'].'
                       AND g.week = '.$_SYS['request']['week'].'
              ORDER BY g.id'.($_SYS['request']['week'] == 0 ? ' DESC' : '');
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* generate score boxes */

    for ($i = 1; $i <= $result->rows(); ++$i) {
      $row = $result->fetch_assoc();

      $show = $this->visible;

      $_invisible = !$this->visible
                    && $row['played']
                    && !($row['home_sub'] == '0' && $row['home_hc'] == $_SYS['user']['id'])
                    && !($row['home_sub'] != '0' && $row['home_sub'] == $_SYS['user']['id'])
                    && !($row['away_sub'] == '0' && $row['away_hc'] == $_SYS['user']['id'])
                    && !($row['away_sub'] != '0' && $row['away_sub'] == $_SYS['user']['id']);

      $output .= '
<table class="score float">
  <thead>
    <tr>
      <td>'.($row['played'] ? ($_invisible ? '&nbsp;' : $row['site']) : $row['scheduled']).'</td>
      <th scope="col">1</th>
      <th scope="col">2</th>
      <th scope="col">3</th>
      <th scope="col">4</th>';

      if ($row['overtime_game'] && !$_invisible) {
        $output .= '
      <th scope="col">OT</th>';
      }

      $output .= '
      <th scope="col">T</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">
        '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['away_acro']).'.gif" alt="'.$row['away_acro'].'" class="logo" /> ' : '').'
        <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['away_id'].'">'.$_SYS['util']->team_name($row['away_id'], 'team').'</a>'.($row['away_sub'] != '0' ? '*' : '').'
        '.$record[$row['away_id']].'
        '.(!$_invisible && $row['away_score'] > $row['home_score'] ? '<em>&laquo;</em>' : '');

      if ($_SYS['util']->can_upload($_SYS['request']['season'], $_SYS['request']['week']) && !$row['played'] && $_SYS['user']['id'] && ($_SYS['user']['id'] == intval($row['away_hc']) || $_SYS['user']['id'] == intval($row['away_sub']))) {
        $output .= '
        <a href="'.$_SYS['page']['upload']['url'].'?game='.$row['game_id'].'&amp;team='.$row['away_id'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/up.gif" alt="U" /></a>';
      }

      $output .= '
      </th>
      <td>'.($_invisible ? '&nbsp;' : $row['away_q1']).'</td>
      <td>'.($_invisible ? '&nbsp;' : $row['away_q2']).'</td>
      <td>'.($_invisible ? '&nbsp;' : $row['away_q3']).'</td>
      <td>'.($_invisible ? '&nbsp;' : $row['away_q4']).'</td>';

      if ($row['overtime_game'] && !$_invisible) {
        $output .= '
      <td>'.$row['away_ot'].'</td>';
      }

      $output .= '
      <td class="final">'.($_invisible ? '&nbsp;' : $row['away_score']).'</td>
    </tr>
    <tr>
      <th scope="row">
        '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['home_acro']).'.gif" alt="'.$row['home_acro'].'" class="logo" /> ' : '').'
        <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['home_id'].'">'.$_SYS['util']->team_name($row['home_id'], 'team').'</a>'.($row['home_sub'] != '0' ? '*' : '').'
        '.$record[$row['home_id']].'
        '.(!$_invisible && $row['home_score'] > $row['away_score'] ? '<em>&laquo;</em>' : '');

      if ($_SYS['util']->can_upload($_SYS['request']['season'], $_SYS['request']['week']) && $_SYS['user']['id'] && ($_SYS['user']['id'] == intval($row['home_hc']) || $_SYS['user']['id'] == intval($row['home_sub']))) {
        if ($row['played']) {
          if ($row['home_id'] == $row['site_id'] && $_SYS['season'][$_SYS['request']['season']]['spawn']) {
            $output .= '
        <a href="'.$_SYS['page']['spawn_upload']['url'].'?game='.$row['game_id'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/up.gif" alt="U" /></a>';
          }
        } else {
          $output .= '
        <a href="'.$_SYS['page']['upload']['url'].'?game='.$row['game_id'].'&amp;team='.$row['home_id'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/up.gif" alt="U" /></a>';
        }
      }

      $output .= '
      </th>
      <td>'.($_invisible ? '&nbsp;' : $row['home_q1']).'</td>
      <td>'.($_invisible ? '&nbsp;' : $row['home_q2']).'</td>
      <td>'.($_invisible ? '&nbsp;' : $row['home_q3']).'</td>
      <td>'.($_invisible ? '&nbsp;' : $row['home_q4']).'</td>';

      if ($row['overtime_game'] && !$_invisible) {
        $output .= '
      <td>'.$row['home_ot'].'</td>';
      }

      $output .= '
      <td class="final">'.($_invisible ? '&nbsp;' : $row['home_score']).'</td>
    </tr>
  </tbody>';

      if ($row['played'] && !$_invisible) {
        $output .= '
  <tbody class="stats">';

        foreach (array('away', 'home') as $_team) {
          $output .= '
    <tr>
      <td colspan="'.($row['overtime_game'] ? '7' : '6').'">
        <strong>'.$row[$_team.'_acro'].':</strong> ';

          $query = 'SELECT   CONCAT(LEFT(name, 1), ".&nbsp;", SUBSTRING(name, LENGTH(name) - LOCATE(" ", REVERSE(name)) + 2), " (", cmp, "-", att, ", ", yds, ")") AS stat
                    FROM     '.$_SYS['table']['stats_passing'].'
                    WHERE    game = '.$row['game_id'].' AND team = '.$row[$_team.'_id'].'
                    ORDER BY yds DESC
                    LIMIT    1';
          $result_stat = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row_stat = $result_stat->fetch_assoc();

          $output .= $row_stat['stat'].', ';

          $query = 'SELECT   CONCAT(LEFT(name, 1), ".&nbsp;", SUBSTRING(name, LENGTH(name) - LOCATE(" ", REVERSE(name)) + 2), " (", att, "-", yds, ")") AS stat
                    FROM     '.$_SYS['table']['stats_rushing'].'
                    WHERE    game = '.$row['game_id'].' AND team = '.$row[$_team.'_id'].'
                    ORDER BY yds DESC
                    LIMIT    1';
          $result_stat = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row_stat = $result_stat->fetch_assoc();

          $output .= $row_stat['stat'].', ';

          $query = 'SELECT   CONCAT(LEFT(name, 1), ".&nbsp;", SUBSTRING(name, LENGTH(name) - LOCATE(" ", REVERSE(name)) + 2), " (", rec, "-", yds, ")") AS stat
                    FROM     '.$_SYS['table']['stats_receiving'].'
                    WHERE    game = '.$row['game_id'].' AND team = '.$row[$_team.'_id'].'
                    ORDER BY yds DESC
                    LIMIT    1';
          $result_stat = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row_stat = $result_stat->fetch_assoc();

          $output .= $row_stat['stat'].'
      </td>
    </tr>';
        }

        $output .= '
  </tbody>';
      }

      $output .= '
  <tbody class="links">
    <tr>
      <td colspan="'.($row['overtime_game'] ? '7' : '6').'">';

      if (!$row['played'] || $_invisible) {
        if ($_SYS['request']['week'] == 0) {
          $_period = 'ex';
        } elseif ($_SYS['request']['week'] < 0) {
          $_period = 'pre';
        } elseif ($_SYS['request']['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
          $_period = 'post';
        } else {
          $_period = 'reg';
        }

        $output .= '
        <a href="'.$_SYS['page']['matchup']['url'].'?away='.$row['away_id'].'&amp;home='.$row['home_id'].'&amp;period='.$_period.'">Matchup</a>';
        unset($_period);
      } else {
        $query = 'SELECT COUNT(*) AS `count`
                  FROM   '.$_SYS['table']['comment'].'
                  WHERE  game = '.$row['game_id'];
        $result2 = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
        $row2 = $result2->fetch_assoc();

        $_recaps = '(Recaps)';

        if ($row2['count'] > 0) {
          $_recaps = 'Recaps';

          if ($_SYS['user']['id']) {
            $query = 'SELECT COUNT(*) AS `count`
                      FROM   '.$_SYS['table']['comment'].'
                      WHERE  game = '.$row['game_id'].'
                             AND UNIX_TIMESTAMP(`date`) > '.$_SYS['user']['last_visit'];
            $result2 = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
            $row2 = $result2->fetch_assoc();

            if ($row2['count'] > 0) {
              $_recaps .= '*';
            }
          }
        }

        $output .= '
        <a href="'.$_SYS['page']['recaps']['url'].'?game='.$row['game_id'].'">'.$_recaps.'</a>
        &middot; <a href="'.$_SYS['page']['boxscore']['url'].'?game='.$row['game_id'].'">Box Score</a>
        &middot; <a href="'.$_SYS['page']['gamelog']['url'].'?game='.$row['game_id'].'">Game Log</a>';

        if (file_exists($_SYS['dir']['spawndir'].'/'.$row['game_id'])) {
          $output .= '
        &middot; <a href="'.$_SYS['page']['spawn']['url'].'?game='.$row['game_id'].'">Spawn</a>';
        }

        unset($result2, $row2, $_recaps);
      }

      if ($_SYS['user']['admin']) {
        if (!$row['played']) {
          $output .= '
        &middot; <a href="'.$_SYS['page']['upload']['url'].'?game='.$row['game_id'].'">Admin Upload</a>';
        }
      }

      if ($_SYS['user']['admin'] || ($_SYS['user']['id'] != 0 && ($_SYS['user']['id'] == $row['away_hc'] || $_SYS['user']['id'] == $row['away_sub'] || $_SYS['user']['id'] == $row['home_hc'] || $_SYS['user']['id'] == $row['home_sub']))) {
        $output .= '
        &middot; <a href="'.$_SYS['page']['schedule_edit']['url'].'?game='.$row['game_id'].'&amp;season='.$_SYS['request']['season'].'">Edit</a>';
      }

      if ($_SYS['user']['admin']) {
        if (!$row['played']) {
          $output .= '
        &middot; <a href="'.$_SYS['page']['schedule_delete']['url'].'?game='.$row['game_id'].'">Delete</a>';
        } else {
          $output .= '
        &middot; <a href="'.$_SYS['page']['clear']['url'].'?game='.$row['game_id'].'">Clear</a>';
        }
      }

      $output .= '
      </td>
    </tr>
  </tbody>
</table>';

      if ($i % 2 == 0 || $i == $result->rows()) {
        $output .= '
<br class="float" />';
      }
    }

    unset($i);

    if ($_SYS['user']['admin'] || ($_SYS['request']['week'] == 0 && count($_SYS['user']['team'][$_SYS['request']['season']]))) {
      $output .= '
<p class="addgame"><a href="'.$_SYS['page']['schedule_edit']['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$_SYS['request']['week'].'">Add Game</a></p>';
    }

    return $output;
  } // getHTML()

} // Page

?>
