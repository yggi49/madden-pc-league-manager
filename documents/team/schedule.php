<?php
/**
 * @(#) team/schedule.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function _postRequest() {
    global $_SYS;

    $output = '';

    $id = intval($_POST['id']);

    /* check who is current coach of the team */

    $query = 'SELECT id, user
              FROM   '.$_SYS['table']['team'].'
              WHERE  id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Team does not exist');
    }

    $team = $result->fetch_assoc();

    /* check if we're allowed to change the assignment: we must be admin or hc of the team */

    if (!$_SYS['user']['admin'] && $team['user'] != $_SYS['user']['id']) {
      return $_SYS['html']->fehler('1', 'You are not allowed to do this');
    }

    /* read all games that we want to and are allowed to save */

    $query = 'SELECT `id`,
                     IF(away = '.$id.', "away", "home") AS `my`
              FROM   '.$_SYS['table']['game'].'
              WHERE  site = 0
                     AND id IN ('.join(', ', array_keys($_POST['sub'])).')';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $_sub = intval($_POST['sub'][$row['id']]) == $team['user'] ? 0 : intval($_POST['sub'][$row['id']]);

      $query = 'UPDATE '.$_SYS['table']['game'].'
                SET    '.$row['my'].'_sub = '.$_sub.'
                WHERE  id = '.$row['id'];
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      $_sql[] = $query;
    }

    header('Location: '.$_SYS['page'][$_SYS['request']['page']]['url'].'?id='.$id);
    exit;
  } // _postRequest()


  function _isInvisible($row) {
    global $_SYS;

    return $row['site'] != 0
      && $row['week'] > 0
      && $row['week'] <= $_SYS['season'][$row['season']]['reg_weeks']
      && !in_array($row['week'], $_SYS['season'][$row['season']]['visible_weeks']['reg'])
      && !($row['sub_id'] == '0' && $row['hc_id'] == $_SYS['user']['id'])
      && !($row['sub_id'] != '0' && $row['sub_id'] == $_SYS['user']['id'])
      && !($row['opp_sub'] == '0' && $row['opp_hc'] == $_SYS['user']['id'])
      && !($row['opp_sub'] != '0' && $row['opp_sub'] == $_SYS['user']['id']);
  }


  function _getRequest() {
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
    $_subs = ($_SYS['user']['id'] && $row['uid'] == $_SYS['user']['id']) || $_SYS['user']['admin'];
    $_subs_count = 0;

    $output .= '
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$id.'">Home</a>
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$id.'">News</a>
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$id.'">Roster</a>
  &middot; [ Schedule ]
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$id.'">Stats</a>
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$id.'">Scouts</a>
</p>';

    /* read all users (for sub-dropdown) */

    $query = 'SELECT   id, nick
              FROM     '.$_SYS['table']['user'].'
              WHERE    status = "Active" AND id != '.intval($row['uid']).'
              ORDER BY nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $users = array();

    while ($row = $result->fetch_assoc()) {
      $users[] = array('display' => $row['nick'], 'value' => $row['id']);
    }

    /* read games */

    $query = 'SELECT   IF(g.home = '.$id.', g.away,  g.home)                         AS `opp_id`,
                       IF(g.home = '.$id.', na.nick, nh.nick)                        AS `opp_team`,
                       IF(g.home = '.$id.', g.away_hc, g.home_hc)                    AS `opp_hc`,
                       IF(g.home = '.$id.', g.away_sub, g.home_sub)                  AS `opp_sub`,
                       g.id                                                          AS `game`,
                       g.season                                                      AS `season`,
                       g.week                                                        AS `week`,
                       g.site                                                        AS `site`,
                       IF(g.site != 0, DATE_FORMAT(g.`date`, "%b %e, %Y"), "&nbsp;") AS `date`,
                       IF(g.home = '.$id.', "vs", "@")                               AS `where`,
                       IFNULL(us.id, 0)                                              AS `sub_id`,
                       IFNULL(us.nick, "&nbsp;")                                     AS `sub_nick`,
                       IFNULL(uh.id, 0)                                              AS `hc_id`,
                       IFNULL(uh.nick, "&nbsp;")                                     AS `hc_nick`,
                       IF(g.site != 0, IF(g.home = '.$id.', CONCAT(CASE WHEN g.home_score > g.away_score THEN "W"
                                                                        WHEN g.home_score < g.away_score THEN "L"
                                                                        ELSE "T" END,
                                                                   " ", g.home_score, "-", g.away_score),
                                                            CONCAT(CASE WHEN g.home_score < g.away_score THEN "W"
                                                                        WHEN g.home_score > g.away_score THEN "L"
                                                                        ELSE "T" END,
                                                                   " ", g.away_score, "-", g.home_score)), "&nbsp;") AS `result`
              FROM     '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home  = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away  = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON ta.team = na.id
                       LEFT JOIN '.$_SYS['table']['user'].' AS us ON IF(g.home = '.$id.', g.home_sub = us.id, g.away_sub = us.id)
                       LEFT JOIN '.$_SYS['table']['user'].' AS uh ON IF(g.home = '.$id.', g.home_hc = uh.id, g.away_hc = uh.id)
              WHERE    g.week != 0 AND (g.home = '.$id.' OR g.away = '.$id.')
              ORDER BY IF(g.week < 0, -5 - g.week, g.week)';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($_subs) {
      $output .= '
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">';
    }

    $output .= '
<table class="teamsked">
  <colgroup>
    <col width="50" />
    <col width="100" />
    <col width="100" />
    <col width="80" />
    <col width="260" />
    <col width="120" />
  </colgroup>
  <thead>
    <tr>
      <th scope="col">Week</th>
      <th scope="col">Date</th>
      <th scope="col">Opponent</th>
      <th scope="col">Result</th>
      <th scope="col">Reports</th>
      <th scope="col">Owner (Sub)</th>
    </tr>
  </thead>
  <tbody>';

    $row = $result->fetch_assoc();
    $row['invisible'] = $this->_isInvisible($row);

    foreach (array_keys($_SYS['season'][$_SYS['request']['season']]['weeks']) as $_week) {
      if ($_week > $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] && $row == false) {
        continue;
      }

      if ($_week < 0) {
        $_week_print = 'P'.(-$_week);
      } elseif ($_week > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
        $_week_print = $_SYS['season'][$_SYS['request']['season']]['post_names'][$_week - 1 - $_SYS['season'][$_SYS['request']['season']]['reg_weeks']]['acro'];
      } else {
        $_week_print = $_week;
      }

      $output .= '
    <tr>
      <td><a href="'.$_SYS['page']['schedule']['url'].'?season='.$_SYS['request']['season'].'&amp;week='.$_week.'">'.$_week_print.'</a></td>';

      if ($_week == $row['week']) {
        $output .= '
      <td>'.($row['invisible'] ? '&nbsp;' : $row['date']).'</td>
      <th scope="row">'.$row['where'].' <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?id='.$row['opp_id'].'">'.$row['opp_team'].'</a></th>
      <td>'.($row['invisible'] ? '&nbsp;' : $row['result']).'</td>
      <td>';

        if ($row['site'] != 0 && !$row['invisible']) {
          $query = 'SELECT COUNT(*) AS `count`
                    FROM   '.$_SYS['table']['comment'].'
                    WHERE  game = '.$row['game'];
          $result2 = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
          $row2 = $result2->fetch_assoc();

          $_recaps = '(Recaps)';

          if ($row2['count'] > 0) {
            $_recaps = 'Recaps';

            if ($_SYS['user']['id']) {
              $query = 'SELECT COUNT(*) AS `count`
                        FROM   '.$_SYS['table']['comment'].'
                        WHERE  game = '.$row['game'].'
                               AND UNIX_TIMESTAMP(`date`) > '.$_SYS['user']['last_visit'];
              $result2 = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
              $row2 = $result2->fetch_assoc();

              if ($row2['count'] > 0) {
                $_recaps .= '*';
              }
            }
          }

          $output .= '
        <a href="'.$_SYS['page']['recaps']['url'].'?game='.$row['game'].'">'.$_recaps.'</a>
        &middot; <a href="'.$_SYS['page']['boxscore']['url'].'?game='.$row['game'].'">Box Score</a>
        &middot; <a href="'.$_SYS['page']['gamelog']['url'].'?game='.$row['game'].'">Game Log</a>';

          if ($_SYS['user']['admin']) {
            $output .= '
        &middot; <a href="'.$_SYS['page']['schedule_edit']['url'].'?game='.$row['game'].'">Edit</a>
        &middot; <a href="'.$_SYS['page']['clear']['url'].'?game='.$row['game'].'">Clear</a>';
          }
        } else {
          if ($row['week'] == 0) {
            $_period = 'ex';
          } elseif ($row['week'] < 0) {
            $_period = 'pre';
          } elseif ($row['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
            $_period = 'post';
          } else {
            $_period = 'reg';
          }

          $output .= '
        <a href="'.$_SYS['page']['matchup']['url'].'?away='.($row['where'] == '@' ? $id : $row['opp_id']).'&amp;home='.($row['where'] == '@' ? $row['opp_id'] : $id).'&amp;period='.$_period.'">Matchup</a>';

          unset($_period);

          if ($_SYS['user']['admin']) {
            $output .= '
        &middot; <a href="'.$_SYS['page']['upload']['url'].'?game='.$row['game'].'">Admin Upload</a>
        &middot; <a href="'.$_SYS['page']['schedule_edit']['url'].'?game='.$row['game'].'">Edit</a>
        &middot; <a href="'.$_SYS['page']['schedule_delete']['url'].'?game='.$row['game'].'">Delete</a>';
          }
        }

        $output .= '
      </td>
      <td>';

        if ($row['site'] == '0' && $_subs) {
          ++$_subs_count;
          $output .= '
        '.$_SYS['html']->dropdown('sub['.$row['game'].']', $users, $row['sub_id']);
        } else {
          $output .= '
        '.($row['invisible'] ? '&nbsp;' : $row['hc_nick'].($row['sub_id'] != '0' ? ' ('.$row['sub_nick'].')' : ''));
        }

        $output .= '
      </td>';

        $row = $result->fetch_assoc();
        if ($row != false) {
          $row['invisible'] = $this->_isInvisible($row);
        }
      } else {
        $output .= '
      <td>&nbsp;</td>
      <th scope="row">Bye</th>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>';
      }

      $output .= '
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    if ($_subs) {
      if ($_subs_count > 0) {
        $output .= '
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>';
      }

      $output .= '
</form>';
    }

    return $output;
  } // _getRequest()

  function getHTML() {
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