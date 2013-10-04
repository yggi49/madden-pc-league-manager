<?php
/**
 * @(#) mvp.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _getRequest() {
    global $_SYS;

    /* read all players */

    $elect['mvp'] = array();

    $query = 'SELECT   *
              FROM     (
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_passing'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_rushing'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_receiving'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_blocking'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_defense'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_kick_returns'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_kicking'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_punt_returns'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_punting'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                       ) AS t
              ORDER BY name, week DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      if (!array_key_exists($row['choice'], $elect['mvp'])) {
        $elect['mvp'][$row['choice']] = array('display' => $row['choice'], 'value' => $row['choice']);
      }
    }

    /* read all offense players */

    $elect['offense'] = array();

    $query = 'SELECT   *
              FROM     (
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_passing'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_rushing'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_receiving'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_blocking'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                       ) AS t
              ORDER BY name, week DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      if (!array_key_exists($row['choice'], $elect['offense'])) {
        $elect['offense'][$row['choice']] = array('display' => $row['choice'], 'value' => $row['choice']);
      }
    }

    /* read all defense players */

    $elect['defense'] = array();

    $query = 'SELECT   s.name AS name,
                       CONCAT(s.name, " (", n.acro, ")") AS choice
              FROM     '.$_SYS['table']['stats_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.season = '.$_SYS['request']['season'].'
              ORDER BY s.name, s.week DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      if (!array_key_exists($row['choice'], $elect['defense'])) {
        $elect['defense'][$row['choice']] = array('display' => $row['choice'], 'value' => $row['choice']);
      }
    }

    /* read all special teams players */

    $elect['special'] = array();

    $query = 'SELECT   *
              FROM     (
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_kick_returns'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_kicking'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_punt_returns'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.name AS name,
                                 s.week AS week,
                                 CONCAT(s.name, " (", n.acro, ")") AS choice
                                 FROM     '.$_SYS['table']['stats_punting'].' AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.season = '.$_SYS['request']['season'].'
                       ) AS t
              ORDER BY name, week DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      if (!array_key_exists($row['choice'], $elect['special'])) {
        $elect['special'][$row['choice']] = array('display' => $row['choice'], 'value' => $row['choice']);
      }
    }

    /* read all coaches */

    $elect['coach'] = array();

    $query = 'SELECT   DISTINCT u.nick AS nick
              FROM     (
                        SELECT   IF(away_sub != 0, away_sub, away_hc) AS uid
                        FROM     game
                        WHERE    season = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   IF(home_sub != 0, home_sub, home_hc) AS uid
                        FROM     game
                        WHERE    season = '.$_SYS['request']['season'].'
                       ) AS t
                       LEFT JOIN user AS u ON t.uid = u.id
              WHERE    u.id IS NOT NULL
              ORDER BY u.nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $elect['coach'][] = array('display' => $row['nick'], 'value' => $row['nick']);
    }

    /* print form */

    $output = '
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<h1>Most Valuable Player</h1>
<dl class="profile">
  <dt>3 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('mvp3', $elect['mvp']).'</dd>

  <dt>2 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('mvp2', $elect['mvp']).'</dd>

  <dt>1 Punkt</dt>
  <dd>'.$_SYS['html']->dropdown('mvp1', $elect['mvp']).'</dd>
</dl>

<h1>Offensive Player of the Year</h1>
<dl class="profile">
  <dt>3 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('offense3', $elect['offense']).'</dd>

  <dt>2 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('offense2', $elect['offense']).'</dd>

  <dt>1 Punkt</dt>
  <dd>'.$_SYS['html']->dropdown('offense1', $elect['offense']).'</dd>
</dl>

<h1>Defensive Player of the Year</h1>
<dl class="profile">
  <dt>3 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('defense3', $elect['defense']).'</dd>

  <dt>2 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('defense2', $elect['defense']).'</dd>

  <dt>1 Punkt</dt>
  <dd>'.$_SYS['html']->dropdown('defense1', $elect['defense']).'</dd>
</dl>

<h1>Special Teams Player of the Year</h1>
<dl class="profile">
  <dt>1 Punkt</dt>
  <dd>'.$_SYS['html']->dropdown('special1', $elect['special']).'</dd>
</dl>

<h1>Coach of the Year</h1>
<dl class="profile">
  <dt>2 Punkte</dt>
  <dd>'.$_SYS['html']->dropdown('coach2', $elect['coach']).'</dd>

  <dt>1 Punkt</dt>
  <dd>'.$_SYS['html']->dropdown('coach1', $elect['coach']).'</dd>
</dl>

<p><input type="submit" value="Absenden" /></p>
</form>';

    return $output;
  } // _getRequest()


  function _postRequest() {
    global $_SYS;

    $text = 'Wahl von '.$_SYS['user']['nick'].':

Most Valuable Player

3 Punkte: '.$_POST['mvp3'].'
2 Punkte: '.$_POST['mvp2'].'
1 Punkt: '.$_POST['mvp1'].'

Offensive Player of the Year

3 Punkte: '.$_POST['offense3'].'
2 Punkte: '.$_POST['offense2'].'
1 Punkt: '.$_POST['offense1'].'

Defensive Player of the Year

3 Punkte: '.$_POST['defense3'].'
2 Punkte: '.$_POST['defense2'].'
1 Punkt: '.$_POST['defense1'].'

Special Teams Player of the Year

1 Punkt: '.$_POST['special1'].'

Coach of the Year

2 Punkte: '.$_POST['coach2'].'
1 Punkt: '.$_POST['coach1'].'
';

    if ($_SYS['mail']['mvp']) {
        mail($_SYS['mail']['mvp'], '['.$_SYS['mail']['league'].'] MVP-Wahl', $text, 'From: '.$_SYS['mail']['from']);
    }

    $output = '
<p>Stimme gez&auml;hlt.</p>';

    return $output;
  } // _postRequest()


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