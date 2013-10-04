<?php
/**
 * @(#) schedule_delete.php
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

    /* read game from db */

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

    $row = $result->fetch_assoc();

    /* check if game was already played */

    if ($row['site'] != 0) {
      return $_SYS['html']->fehler('2', 'Game has already been played!');
    }

    $this->_game = $row;

    return '';
  } // _checkGame()


  function _getRequest() {

    global $_SYS;
    $output = '';

    $game = intval($_GET['game']);

    /* check game */

    if ($error = $this->_checkGame($game)) {
      return $error;
    }

    /* confirmation form */

    $output = '
<h1>Delete Game</h1>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<p>
  Do you really want to delete the game from
  '.$_SYS['season'][$this->_game['season']]['name'].',
  Week '.$this->_game['week'].':
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
    $output = '';

    $game = intval($_POST['game']);

    if ($_POST['submit'] == 'Yes') {
      if ($error = $this->_checkGame($game)) {
        return $error;
      }

      /* delete the game */

      $query = 'DELETE FROM '.$_SYS['table']['game'].' WHERE id = '.$game;
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    header('Location: '.$_POST['back']);
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