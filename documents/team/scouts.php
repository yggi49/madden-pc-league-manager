<?php

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


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

    $team = $result->fetch_assoc();

    $_SYS['request']['season'] = $team['season'];

    $output .= '
<p>
  '.$team['team'].'
  '.(strlen($team['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$team['uid'].'">'.$_SYS['html']->specialchars($team['user']).'</a>' : $_SYS['html']->specialchars($team['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$id.'">Home</a>
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$id.'">News</a>
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$id.'">Roster</a>
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$id.'">Schedule</a>
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$id.'">Stats</a>
  &middot; [ Scouts ]
</p>';

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

    $games = array();

    while ($row = $result->fetch_assoc()) {
        if ($row['site'] != 0 && !$this->_isInvisible($row)) {
            $games[$row['game']] = $row;
        }
    }

    if (!$games) {
        $output .= '
<p>Keine Scout Reports gefunden.</p>';
        return $output;
    }

    /* get scouts */

    $query = 'SELECT   DATE_FORMAT(c.`date`, "%b %e, %Y") AS `date`, c.`user`, c.`comment`, u.`nick`, c.`game`
              FROM     '.$_SYS['table']['comment'].' AS c
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON c.user = u.id
              WHERE    c.`game` IN ('.join(', ', array_keys($games)).')
                       AND c.`type` = "Scout"
              ORDER BY c.`date` DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $scouts = array();

    while ($row = $result->fetch_assoc()) {
        if (($games[$row['game']]['opp_sub'] > 0 && $games[$row['game']]['opp_sub'] == $row['user'])
            || (!$games[$row['game']]['opp_sub'] && $games[$row['game']]['opp_hc'] == $row['user'])) {
            $scouts[] = $row;
        }
    }

    if (!$scouts) {
        $output .= '
<p>Keine Scout Reports gefunden.</p>';
        return $output;
    }

    /* =OUTPUT */

    foreach ($scouts as $scout) {
        $output .= '
<h2 class="boxed"><a href="'.$_SYS['page']['boxscore']['url'].'?game='.$scout['game'].'">vs '.$games[$scout['game']]['opp_team'].'</a>'.($games[$scout['game']]['sub_id'] ? ' ('.$_SYS['html']->specialchars($games[$scout['game']]['sub_nick']).')' : '').'</h2>
<p class="boxed">
<strong>by '.$_SYS['html']->specialchars($scout['nick']).' ('.$scout['date'].')</strong>
'.$_SYS['html']->bbcode($_SYS['html']->specialchars($scout['comment'])).'
</p>';
    }

    return $output;
  } // getHTML()

} // Page

?>