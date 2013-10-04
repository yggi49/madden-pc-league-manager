<?php
/**
 * @(#) clear.php
 */

class Page {

  var $_game;    // game data

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _checkGame($game) {
    global $_SYS;

    /* fetch game from db */

    $query = 'SELECT g.away        AS away,
                     na.team       AS away_team,
                     na.nick       AS away_nick,
                     na.acro       AS away_acro,
                     ta.user       AS away_hc,
                     g.away_sub    AS away_sub,
                     ta.conference AS away_conference,
                     ta.division   AS away_division,
                     g.away_score  AS away_score,
                     g.home        AS home,
                     nh.team       AS home_team,
                     nh.nick       AS home_nick,
                     nh.acro       AS home_acro,
                     th.user       AS home_hc,
                     g.home_sub    AS home_sub,
                     th.conference AS home_conference,
                     th.division   AS home_division,
                     g.home_score  AS home_score,
                     g.site        AS site,
                     g.week        AS week,
                     g.season      AS season
              FROM   '.$_SYS['table']['game'].' AS g
                     LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON ta.team = na.id
                     LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON th.team = nh.id
              WHERE  g.id = '.intval($game);
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* check if game exists */

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Game does not exist.');
    }

    /* check if game was already played */

    $row = $result->fetch_assoc();

    if ($row['site'] == 0) {
      return $_SYS['html']->fehler('2', 'There is nothing to clear.');
    }

    $this->_game = $row;

    return '';
  } // _checkGame()


  function _getRequest() {
    global $_SYS;

    $game = intval($_GET['game']);

    /* check authorization */

    if ($error = $this->_checkGame($game)) {
      return $error;
    }

    /* confirmation form */

    $output = '
<h1>Clear Game</h1>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<p>
  Do you really want to clear the game from
  '.$_SYS['season'][$this->_game['season']]['name'].',
  '.($this->_game['week'] < 0 ? 'Preseason Week '.(-$this->_game['week']) : ($this->_game['week'] > $_SYS['season'][$this->_game['season']]['reg_weeks'] ? $_SYS['season'][$this->_game['season']]['post_names'][$this->_game['week'] - $_SYS['season'][$this->_game['season']]['reg_weeks'] - 1]['name'] : ($this->_game['week'] == 0 ? 'Exhibition' : 'Week '.$this->_game['week']))).':
  '.$this->_game['away_team'].' '.$this->_game['away_nick'].'
  @
  '.$this->_game['home_team'].' '.$this->_game['home_nick'].'?
</p>
<dl>
  <dt>
    '.$_SYS['html']->hidden('game', $game).'
    '.$_SYS['html']->hidden('back', $_SERVER['HTTP_REFERER']).'
  </dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Yes').'
    '.$_SYS['html']->submit('submit', 'No').'
  </dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  function _postRequest() {
    global $_SYS;

    $game = intval($_POST['game']);
    $error = $this->_checkGame($game);

    /* check if log shall really be deleted and if there are errors */

    if ($_POST['submit'] == 'Yes') {
      if ($error) {
        return $error;
      }
    } else {
      header('Location: '.$_POST['back']);
      exit;
    }

    /* delete the log - start transaction */

    $query = 'START TRANSACTION';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* clear game from stats tables + log */

    foreach (array('stats_blocking', 'stats_defense', 'stats_kick_returns', 'stats_kicking', 'stats_passing',
                   'stats_punt_returns', 'stats_punting', 'stats_receiving', 'stats_rushing',
                   'stats_scoring_defense', 'stats_scoring_offense', 'stats_team_defense', 'stats_team_offense', 'log') as $_table) {
      $query = 'DELETE FROM '.$_SYS['table'][$_table].' WHERE game = '.$game;
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    /* reset game */

    $query = 'UPDATE '.$_SYS['table']['game'].'
              SET    `away_hc`    = 0,
                     `away_sub`   = 0,
                     `away_rate`  = 0,
                     `home_hc`    = 0,
                     `home_sub`   = 0,
                     `home_rate`  = 0,
                     `site`       = 0,
                     `date`       = "0000-00-00 00:00:00",
                     `away_q1`    = 0,
                     `away_q2`    = 0,
                     `away_q3`    = 0,
                     `away_q4`    = 0,
                     `away_ot`    = 0,
                     `away_score` = 0,
                     `home_q1`    = 0,
                     `home_q2`    = 0,
                     `home_q3`    = 0,
                     `home_q4`    = 0,
                     `home_ot`    = 0,
                     `home_score` = 0,
                     `forecast`   = "",
                     `wind`       = 0,
                     `temp`       = 0,
                     `inserted`   = 0
              WHERE  `id`         = '.$game;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* commit transaction */

    $query = 'COMMIT';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['schedule']['url'].'?season='.$this->_game['season'].'&week='.$this->_game['week']);
    exit;
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