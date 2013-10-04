<?php
/**
 * @(#) spawn_upload.php
 */

class Page {

  var $_game;    // game data


  /** ------------------------------------------------------------------------
   * constructor
   * -------------------------------------------------------------------------
   */
  function Page() {} // constructor


  /** ------------------------------------------------------------------------
   * returns header information for this page
   * -------------------------------------------------------------------------
   */
  function getHeader() {
    return '';
  } // getHeader()


  /** ------------------------------------------------------------------------
   * checks if the current user is authorized to upload a game log for a
   * certain game for a certain team.  returns an empty string if successful;
   * returns an error message if not.
   * -------------------------------------------------------------------------
   */
  function _checkGame($game) {
    global $_SYS;

    $game = intval($game);

    /* fetch game from db */

    $query = 'SELECT g.away        AS away,
                     na.team       AS away_team,
                     na.nick       AS away_nick,
                     na.acro       AS away_acro,
                     ta.user       AS away_hc,
                     g.away_sub    AS away_sub,
                     ta.conference AS away_conference,
                     ta.division   AS away_division,
                     g.home        AS home,
                     nh.team       AS home_team,
                     nh.nick       AS home_nick,
                     nh.acro       AS home_acro,
                     th.user       AS home_hc,
                     g.home_sub    AS home_sub,
                     th.conference AS home_conference,
                     th.division   AS home_division,
                     g.site        AS site,
                     g.week        AS week,
                     g.season      AS season,
                     s.name        AS season_name
              FROM   '.$_SYS['table']['game'].' AS g
                     LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON g.away = ta.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'    AS na ON ta.team = na.id
                     LEFT JOIN '.$_SYS['table']['team'].'   AS th ON g.home = th.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'    AS nh ON th.team = nh.id
                     LEFT JOIN '.$_SYS['table']['season'].' AS s  ON g.season = s.id
              WHERE  g.id = '.$game;

    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* check if game exists */

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Game does not exist.');
    }

    /* check if game was already played */

    $row = $result->fetch_assoc();

    if ($row['site'] == 0) {
      return $_SYS['html']->fehler('2', 'Game has not been played yet.');
    }

    /* check if spawn games are allowed for this season */

    if (!$_SYS['season'][$_SYS['request']['season']]['spawn']) {
      return $_SYS['html']->fehler('3', 'You cannot upload a spawn result file for this game.');
    }

    /* allow if user is user is home_hc or home_sub of the team */

    if (!($row['site'] == $row['home'] && ($_SYS['user']['id'] == $row['home_hc'] || $_SYS['user']['id'] == $row['home_sub']))) {
      return $_SYS['html']->fehler('3', 'You cannot upload a spawn result file for this game.');
    }

    $this->_game = $row;

    return '';
  } // _checkGame(game, team)


  /** ------------------------------------------------------------------------
   * handles a POST request
   * -------------------------------------------------------------------------
   */
  function _postRequest($admin=null) {
    global $_SYS;

    /* get parameters */

    $game = intval($_POST['game']);

    /* check authorization */

    if ($error = $this->_checkGame($game)) {
      return $error;
    }

    /* determine time constraints for this upload */

    if (!$_SYS['util']->can_upload($this->_game['season'], $this->_game['week'])) {
      return $_SYS['html']->fehler('5', 'You cannot upload a log for this game now.');
    }

    if (move_uploaded_file($_FILES['spawn']['tmp_name'], $_SYS['dir']['spawndir'].'/'.$game)) {
      return '
<p>Spawn Result File upload successful.</p>
<p><a href="'.$_SYS['page']['schedule']['url'].'?season='.$this->_game['season'].'&amp;week='.$this->_game['week'].'">Back</a></p>';
    } else {
      return '
<p>Upload failed!</p>
<p><a href="'.$_SYS['page']['schedule']['url'].'?season='.$this->_game['season'].'&amp;week='.$this->_game['week'].'">Back</a></p>';
    }
  } // _postRequest()


  /** ------------------------------------------------------------------------
   * handles a GET request
   * -------------------------------------------------------------------------
   */
  function _getRequest() {
    global $_SYS;

    /* get parameters */

    $game = intval($_GET['game']);

    /* check authorization */

    if ($error = $this->_checkGame($game)) {
      return $error;
    }

    /* check time constraints for this upload */

    if (!$_SYS['util']->can_upload($this->_game['season'], $this->_game['week'])) {
      return $_SYS['html']->fehler('5', 'You cannot upload a spawn result file for this game now.');
    }

    /* show form */

    $output = '
<p>
  '.$_SYS['season'][$this->_game['season']]['name'].',
  '.($this->_game['week'] < 0 ? 'Preseason Week '.(-$this->_game['week']) : ($this->_game['week'] > $_SYS['season'][$this->_game['season']]['reg_weeks'] ? $_SYS['season'][$this->_game['season']]['post_names'][$this->_game['week'] - $_SYS['season'][$this->_game['season']]['reg_weeks'] - 1]['name'] : ($this->_game['week'] == 0 ? 'Exhibition' : 'Week '.$this->_game['week']))).':
  '.$this->_game['away_team'].' '.$this->_game['away_nick'].'
  @
  '.$this->_game['home_team'].' '.$this->_game['home_nick'].'
  ('.($team == $this->_game['home'] ? $this->_game['home_acro'] : $this->_game['away_acro']).')
</p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post" enctype="multipart/form-data">
<dl>
  <dt>'.$_SYS['html']->label('fspawn', 'Spawn Result').'</dt>
  <dd>'.$_SYS['html']->file('spawn', 0, '', 'id="fspawn" tabindex="10"').'</dd>
</dl>

<dl>
  <dt>
    '.$_SYS['html']->hidden('game', $game).'
  </dt>
  <dd>'.$_SYS['html']->submit('submit', 'Upload').'</dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  /** ------------------------------------------------------------------------
   * handles a request to this page
   * -------------------------------------------------------------------------
   */
  function getHTML() {
    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return $this->_postRequest();
      break;
    default:
      return $this->_getRequest();
    }
  } // getHTML()
}
?>