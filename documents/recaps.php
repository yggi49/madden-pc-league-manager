<?php
/**
 * @(#) recaps.php
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

    $team = array('away' => array('id'    => $row['away_id'],
                                  'team'  => $row['away_team'],
                                  'nick'  => $row['away_nick'],
                                  'acro'  => $row['away_acro'],
                                  'coach' => $row['away_sub'] > 0 ? $row['away_sub'] : $row['away_hc'],
                                  'user'  => array()),
                  'home' => array('id'    => $row['home_id'],
                                  'team'  => $row['home_team'],
                                  'nick'  => $row['home_nick'],
                                  'acro'  => $row['home_acro'],
                                  'coach' => $row['home_sub'] > 0 ? $row['home_sub'] : $row['home_hc'],
                                  'user'  => array()));

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
  [ '.$_recaps.' ]
  &middot; <a href="'.$_SYS['page']['boxscore']['url'].'?game='.$row['game_id'].'">Box Score</a>
  &middot; <a href="'.$_SYS['page']['gamelog']['url'].'?game='.$row['game_id'].'">Game Log</a>';

    if ($_SYS['user']['admin']) {
      $output .= '
  &middot; <a href="'.$_SYS['page']['schedule_edit']['url'].'?game='.$row['game_id'].'">Edit Game</a>
  &middot; <a href="'.$_SYS['page']['clear']['url'].'?game='.$row['game_id'].'">Clear Game</a>';
    }

    $output .= '
</p>';

    /* recaps */

    $recap_types = array('Recap'   => array('title' => 'Recap',          'empty' => 'No recaps.'),
                         'Coach'   => array('title' => "Coach's Corner", 'empty' => 'No interviews.'),
                         'Scout'   => array('title' => 'Scout Reports',  'empty' => 'No scout reports.'),
                         'Comment' => array('title' => 'Comments',       'empty' => 'No comments.'));

    foreach (array_keys($recap_types) as $_type) {
      $query = 'SELECT   c.`id`      AS id,
                         u.`id`      AS uid,
                         u.`nick`    AS user,
                         c.`date`    AS date,
                         c.`comment` AS comment
                FROM     '.$_SYS['table']['comment'].' AS c
                         LEFT JOIN '.$_SYS['table']['user'].' AS u ON c.`user` = u.`id`
                WHERE    c.`game` = '.$game.'
                         AND c.`type` = "'.$_type.'"
                ORDER BY c.`id`';
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      $output .= '
<h2 class="boxed">'.$recap_types[$_type]['title'].'</h2>';

      if ($_type == 'Comment' && $_SYS['page']['comment']['access']) {
        $output .= '
<p><a href="'.$_SYS['page']['comment']['url'].'?game='.$game.'">Post comment</a></p>';
      }

      if ($result->rows() == 0) {
        $output .= '
<p class="no boxed">'.$recap_types[$_type]['empty'].'</p>';
      }

      while ($row = $result->fetch_assoc()) {
        $output .= '
<p class="boxed">';

        if ($_type == 'Comment' && $row['uid'] == $_SYS['user']['id']) {
          $output .= '
<span class="edit"><a href="'.$_SYS['page']['comment']['url'].'?id='.$row['id'].'">Edit</a></span>';
        }

        $output .= '
<strong>by '.$row['user'].'</strong>
'.$_SYS['html']->bbcode($_SYS['html']->specialchars($row['comment']));


        $output .= '
</p>';
      }

      if ($_type == 'Comment' && $_SYS['page']['comment']['access']) {
        $output .= '
<p><a href="'.$_SYS['page']['comment']['url'].'?game='.$game.'">Post comment</a></p>';
      } elseif ($_type != 'Comment' && in_array($_SYS['user']['id'], array($team['home']['coach'], $team['away']['coach']))) {
        $output .= '
<p><a href="'.$_SYS['page']['recap_edit']['url'].'?game='.$game.'">Add/Edit</a></p>';
      }
    }

    return $output;
  } // getHTML()

} // Page

?>