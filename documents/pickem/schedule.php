<?php
/**
 * @(#) pickem/schedule.php
 */

class Page {

  var $deadline;
  var $is_hc = false;

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _postRequest() {
    global $_SYS;

    /* error if user is no head coach*/

    if (!$this->is_hc) {
      return $_SYS['html']->fehler('1', 'You should never see this error if you did not mess around');
    }

    /* error if deadline is expired */

    if (!$this->deadline) {
      return $_SYS['html']->fehler('2', 'Cannot save picks - deadline expired');
    }

    /* read all games for the requested week */

    $query = 'SELECT   g.id AS id,
                       uh.id = '.$_SYS['user']['id'].' OR ua.id = '.$_SYS['user']['id'].' AS own
              FROM     '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON g.away  = ta.id
                       LEFT JOIN '.$_SYS['table']['user'].'   AS ua ON ta.user = ua.id
                       LEFT JOIN '.$_SYS['table']['team'].'   AS th ON g.home  = th.id
                       LEFT JOIN '.$_SYS['table']['user'].'   AS uh ON th.user = uh.id
              WHERE    g.season = '.$_SYS['request']['season'].'
                       AND g.week = '.$_SYS['request']['week'];
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $_games = array();    # holds the ids of all games of the requested week
    $_picks = array();    # holds which confidence points have already been awarded
    $_sql = array();      # hold the VALUES parts of the insert query
    $_max = 0;            # maximum value for confidence points

    while ($row = $result->fetch_assoc()) {
      if (!$row['own']) {
        ++$_max;
      }
    }

    $result->reset();

    while ($row = $result->fetch_assoc()) {
      $_games[] = $row['id'];

      $_points = intval($_POST['points'][$row['id']]);
      $_winner = intval($_POST['winner'][$row['id']]);

      if ($row['own'] || $_points < 1 || $_points > $_max || !in_array($_winner, array(1, 2))) {
        continue;
      }

      if (in_array($_points, $_picks)) {
        return $_SYS['html']->fehler('2', 'The '.$_points.'-point-vote has been awarded more than once');
      }

      $_picks[] = $_points;
      $_sql[] = '('.$row['id'].', '.$_SYS['user']['id'].', '.$_points.', '.$_winner.')';
    }

    unset($_points, $_winner);

    /* delete old and save new picks */

    if (count($_games)) {
      $query = 'START TRANSACTION';
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      $query = 'DELETE FROM '.$_SYS['table']['pickem'].'
                WHERE       user = '.$_SYS['user']['id'].'
                            AND game IN ('.join(', ', $_games).')';
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if (count($_sql)) {
        $query = 'INSERT INTO '.$_SYS['table']['pickem'].' (game, user, points, winner)
                VALUES      '.join(', ', $_sql);
        $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
      }

      $query = 'COMMIT';
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    unset($_games, $_picks, $_sql, $_max);

    header('Location: '.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&week='.$_SYS['request']['week'].'&saved=1');
    exit;
  } // _postRequest()


  function _getRequest() {
    global $_SYS;

    $output = '';

    /* print secondary navigation bar */

    $output .= '
<p id="navbarsec">
  Week: ';

    $_links = array();

    for ($i = 1; $i <= $_SYS['season'][$_SYS['request']['season']]['reg_weeks']; ++$i) {
      $_link = "\n";
      $_link .= $_SYS['request']['week'] == $i
        ? '  [ '.$i.' ]'
        : '  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$i.'">'.$i.'</a>';

      $_links[] = $_link;
    }

    $output .= join(' &middot;', $_links).'
</p>';

    $output .= $this->deadline ? $this->_showPicks() : $this->_showResults();

    return $output;
  } // _getRequest()


  function _showPicks() {
    global $_SYS;

    $output = '';

    /* query games and user's picks */

    $query = 'SELECT   g.id                                                               AS game_id,
                       CONCAT(na.team, " ", na.nick)                                      AS away,
                       CONCAT(nh.team, " ", nh.nick)                                      AS home,
                       uh.id = '.$_SYS['user']['id'].' OR ua.id = '.$_SYS['user']['id'].' AS own,
                       p.winner                                                           AS pick,
                       p.points                                                           AS points
              FROM     '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON g.away  = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'    AS na ON ta.team = na.id
                       LEFT JOIN '.$_SYS['table']['user'].'   AS ua ON ta.user = ua.id
                       LEFT JOIN '.$_SYS['table']['team'].'   AS th ON g.home  = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'    AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['user'].'   AS uh ON th.user = uh.id
                       LEFT JOIN '.$_SYS['table']['pickem'].' AS p  ON p.game  = g.id  AND p.user = '.$_SYS['user']['id'].'
              WHERE    g.season = '.$_SYS['request']['season'].'
                       AND g.week = '.$_SYS['request']['week'].'
              ORDER BY g.id';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output .= '
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<table class="picksked">
  <thead>
    <tr>
      <th>Away</th>';

      if ($this->is_hc) {
        $_pick_count = 0;

        while ($row = $result->fetch_assoc()) {
          if (!$row['own']) {
            ++$_pick_count;
          }
        }

        $result->reset();

        $output .= '
      <th class="pick">Pick ('.$_pick_count.')</th>';

        unset($_pick_count);
      }

    $output .= '
      <th>Home</th>
    </tr>
  </thead>';

    if ($this->is_hc) {
      $_hours = floor($this->deadline / 60 / 60);
      $_minutes = sprintf('%02d', floor(($this->deadline - $_hours * 60 * 60) / 60));

      $output .= '
  <tfoot>
    <tr>
      <td>Deadline: '.$_hours.'&nbsp;h '.$_minutes.'&nbsp;min</td>
      <td class="pick">
        '.$_SYS['html']->hidden('season', $_SYS['request']['season']).'
        '.$_SYS['html']->hidden('week', $_SYS['request']['week']).'
        '.$_SYS['html']->submit('submit', 'Save').'
      </td>
      <td>'.($_GET['saved'] ? 'Picks saved' : '&nbsp;').'</td>
    </tr>
  </tfoot>';

      unset($_hours, $_minutes);
    }

    $output .= '
  <tbody>';

    while ($row = $result->fetch_assoc()) {
      $output .= '
    <tr>';

      if ($this->is_hc && !$row['own']) {
        $output .= '
      <td>'.$_SYS['html']->label('fpick2_'.$row['game_id'], $row['away']).'</td>';
      } else {
        $output .= '
      <td>'.$row['away'].'</td>';
      }

      if ($this->is_hc) {
        $output .= '
      <td class="pick">';

        if ($row['own']) {
          $output .= '
        &nbsp;';
        } else {
          $output .= '
        '.$_SYS['html']->radio('winner['.$row['game_id'].']', '2', $row['pick'], 'fpick2_'.$row['game_id']).'
        '.$_SYS['html']->textfield('points['.$row['game_id'].']', $row['points'], 2, 2, 'pick').'
        '.$_SYS['html']->radio('winner['.$row['game_id'].']', '1', $row['pick'], 'fpick1_'.$row['game_id']);
        }

        $output .= '
      </td>';
      }

      if ($this->is_hc && !$row['own']) {
        $output .= '
      <td>'.$_SYS['html']->label('fpick1_'.$row['game_id'], $row['home']).'</td>';
      } else {
        $output .= '
      <td>'.$row['home'].'</td>';
      }

      $output .= '
    </tr>';
    }

    $output .= '
  </tbody>
</table>
</form>';

    return $output;
  } // _showPicks()


  function _showResults() {
    global $_SYS;

    $output = '';

    /* query single picks */

    $query = 'SELECT   u.id                               AS user_id,
                       u.nick                             AS nick,
                       g.id                               AS game_id,
                       na.acro                            AS away,
                       g.away_score                       AS away_score,
                       nh.acro                            AS home,
                       g.home_score                       AS home_score,
                       g.site                             AS played,
                       CASE
                         WHEN g.away_score > g.home_score
                           THEN 2
                         WHEN g.away_score < g.home_score
                           THEN 1
                         ELSE 0
                       END                                AS winner,
                       p.points                           AS points,
                       p.winner                           AS pick
              FROM     '.$_SYS['table']['user'].' AS u
                       LEFT JOIN '.$_SYS['table']['game'].'   AS g  ON 1 = 1
                       LEFT JOIN '.$_SYS['table']['team'].'   AS th ON th.id = g.home
                       LEFT JOIN '.$_SYS['table']['nfl'].'    AS nh ON nh.id = th.team
                       LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON ta.id = g.away
                       LEFT JOIN '.$_SYS['table']['nfl'].'    AS na ON na.id = ta.team
                       LEFT JOIN '.$_SYS['table']['pickem'].' AS p  ON g.id  = p.game AND u.id = p.user
              WHERE    g.season = '.$_SYS['request']['season'].'
                       AND g.week = '.$_SYS['request']['week'].'
              ORDER BY g.id, u.id';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $_games = array();          // holds all games of this week
    $_players = array();        // holds infos for each player
    $_totals = array('won' => 0, 'lost' => 0, 'points' => 0);         // holds totals stats

    while ($row = $result->fetch_assoc()) {
      if (!array_key_exists($row['game_id'], $_games)) {
        $_games[$row['game_id']] = array('away'       => $row['away'],
                                         'away_score' => $row['played'] ? $row['away_score'] : '&ndash;',
                                         'home'       => $row['home'],
                                         'home_score' => $row['played'] ? $row['home_score'] : '&ndash;',
                                         'points'     => 0,
                                         'won'        => 0,
                                         'lost'       => 0);
      }

      if (!array_key_exists($row['user_id'], $_players)) {
        $_players[$row['user_id']] = array('won' => 0, 'lost' => 0, 'points' => 0);
      }

      $_styles = array();

      $_styles[] = $row['winner'] == $row['pick'] ? 'right' : '';
      $_styles[] = $row['pick'] == '2' ? 'away' : '';

      $_styles = trim(join(' ', $_styles));

      $_players[$row['user_id']][$row['game_id']]['pick'] = '<td'.($_styles ? ' class="'.$_styles.'"' : '').'>';
      $_players[$row['user_id']][$row['game_id']]['pick'] .= $row['points']
        ? ($row['played'] ? '' : '(').$row['points'].($row['played'] ? '' : ')').'<br /><em>'.($row['pick'] == '2' ? $row['away'] : $row['home']).'</em>'
        : '&nbsp;';
      $_players[$row['user_id']][$row['game_id']]['pick'] .= '</td>';

      unset($_styles);

      if ($row['points'] && $row['played']) {
        if ($row['pick'] == $row['winner']) {
          $_players[$row['user_id']]['won']++;
          $_players[$row['user_id']]['points'] += $row['points'];
          $_totals['won']++;
          $_totals['points'] += $row['points'];
          $_games[$row['game_id']]['won']++;
          $_games[$row['game_id']]['points'] += $row['points'];
        } else {
          $_players[$row['user_id']]['lost']++;
          $_totals['lost']++;
          $_games[$row['game_id']]['lost']++;
        }
      }
    }

    /* game scores */

    $_html = array('away' => '', 'away_score' => '', 'home' => '', 'home_score' => '', 'game_points' => '', 'game_record' => '');

    foreach ($_games as $_game) {
      $_html['away']        .= "\n".'      <td>'.$_game['away'].'</td>';
      $_html['away_score']  .= "\n".'      <td>'.$_game['away_score'].'</td>';
      $_html['home']        .= "\n".'      <td>'.$_game['home'].'</td>';
      $_html['home_score']  .= "\n".'      <td>'.$_game['home_score'].'</td>';
      $_html['game_points'] .= "\n".'      <td>'.$_game['points'].'</td>';
      $_html['game_record'] .= "\n".'      <td>'.$_game['won'].'-'.$_game['lost'].'</td>';
    }

    /* print table */

    $output .= '
<table class="pickres">
  <thead>
    <tr>
      <th rowspan="4">#</th>
      <th rowspan="4">Name</th>'.$_html['away'].'
      <th rowspan="4">Pts</th>
      <th rowspan="4">Rec</th>
    </tr>
    <tr>'.$_html['away_score'].'
    </tr>
    <tr>'.$_html['home_score'].'
    </tr>
    <tr>'.$_html['home'].'
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td rowspan="2">&nbsp;</td>
      <td rowspan="2">&nbsp;</td>'.$_html['game_points'].'
      <td rowspan="2">'.$_totals['points'].'</td>
      <td rowspan="2">'.$_totals['won'].'<br />-<br />'.$_totals['lost'].'</td>
    </tr>
    <tr>'.$_html['game_record'].'
    </tr>
    <tr>
      <th rowspan="4">#</th>
      <th rowspan="4">Name</th>'.$_html['away'].'
      <th rowspan="4">Pts</th>
      <th rowspan="4">Rec</th>
    </tr>
    <tr>'.$_html['away_score'].'
    </tr>
    <tr>'.$_html['home_score'].'
    </tr>
    <tr>'.$_html['home'].'
    </tr>
  </tfoot>
  <tbody>';

    /* fetch players and their total record/score */

    $query = 'SELECT   u.id AS id,
                       u.nick AS nick,
                       SUM(CASE WHEN g.week < 1 OR g.week > '.$_SYS['request']['week'].' OR g.site = 0 OR p.winner IS NULL THEN 0 WHEN g.away_score > g.home_score AND p.winner = 2 THEN p.points WHEN g.away_score < g.home_score AND p.winner = 1 THEN p.points ELSE 0 END) AS points,
                       SUM(CASE WHEN g.week < 1 OR g.week > '.$_SYS['request']['week'].' OR g.site = 0 OR p.winner IS NULL THEN 0 WHEN g.away_score > g.home_score AND p.winner = 2 THEN 1        WHEN g.away_score < g.home_score AND p.winner = 1 THEN 1        ELSE 0 END) AS won,
                       SUM(CASE WHEN g.week < 1 OR g.week > '.$_SYS['request']['week'].' OR g.site = 0 OR p.winner IS NULL THEN 0 WHEN g.away_score > g.home_score AND p.winner = 2 THEN 0        WHEN g.away_score < g.home_score AND p.winner = 1 THEN 0        ELSE 1 END) AS lost,
                       SUM(CASE WHEN g.week < 1 OR g.week > '.$_SYS['request']['week'].' OR g.site = 0 OR p.winner IS NULL THEN 0 WHEN g.away_score > g.home_score AND p.winner = 2 THEN 1 WHEN g.away_score < g.home_score AND p.winner = 1 THEN 1 ELSE 0 END) / (SUM(CASE WHEN g.week < 1 OR g.week > '.$_SYS['request']['week'].' OR g.site = 0 OR p.winner IS NULL THEN 0 WHEN g.away_score > g.home_score AND p.winner = 2 THEN 1 WHEN g.away_score < g.home_score AND p.winner = 1 THEN 1 ELSE 0 END) + SUM(CASE WHEN g.week < 1 OR g.week > '.$_SYS['request']['week'].' OR g.site = 0 OR p.winner IS NULL THEN 0 WHEN g.away_score > g.home_score AND p.winner = 2 THEN 0 WHEN g.away_score < g.home_score AND p.winner = 1 THEN 0 ELSE 1 END)) AS pct
              FROM     '.$_SYS['table']['pickem'].' AS p
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON p.game = g.id
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON p.user = u.id
              WHERE    g.season = '.$_SYS['request']['season'].'
              GROUP BY u.id
              ORDER BY points DESC, pct DESC, nick ASC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $_counter = 0;
    $_old = -1;

    if ($result->rows() == 0) {
      $output .= '
    <tr>
      <td colspan="'.(4 + count($_games)).'">No competitors.</td>
    </tr>';
    }

    while ($row = $result->fetch_assoc()) {
      ++$_counter;
      $output .= '
    <tr'.($row['id'] == $_SYS['user']['id'] ? ' class="user"' : '').'>
      <td class="pos">'.($row['points'] == $_old ? '&nbsp;' : $_counter).'</td>
      <td class="name">'.$row['nick'].'</td>';

      foreach (array_keys($_games) as $_game) {
        $output .= "\n      ".$_players[$row['id']][$_game]['pick'];
      }

      $output .= '
      <td class="points">
        '.$_players[$row['id']]['points'].'<br />
        <em>'.$row['points'].'</em>
      </td>
      <td class="record">
        '.$_players[$row['id']]['won'].'-'.$_players[$row['id']]['lost'].'<br />
        <em>'.$row['won'].'-'.$row['lost'].'</em>
      </td>
    </tr>';

      $_old = $row['points'];
    }

    $output .= '
  </tbody>
</table>';

    return $output;
  } // _showResults()


  function getHTML() {
    global $_SYS;

    /* check week */

    if ($_SYS['request']['week'] < 1 || $_SYS['request']['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
      $_SYS['request']['week'] = 1;
    }

    /* check if user is a current head coach */

    $query = 'SELECT *
              FROM   '.$_SYS['table']['team'].'
              WHERE  user = '.$_SYS['user']['id'].'
                     AND season = '.$_SYS['request']['season'];
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $this->is_hc = $result->rows() != 0;

    /* check if week is visible; ie if deadline is expired*/

    $this->deadline = max(0, $_SYS['season'][$_SYS['request']['season']]['weeks'][$_SYS['request']['week']]['begin'] - $_SYS['time']['now']);

    /* process request */

    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return $this->_postRequest();
      break;
    default:
      return $this->_getRequest();
    }
  } // getHTML()

} // Page

?>