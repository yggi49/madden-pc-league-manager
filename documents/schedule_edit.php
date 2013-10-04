<?php
/**
 * @(#) schedule_edit.php
 */

class Page {

  var $game;
  var $season;
  var $week;

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _postRequest() {

    global $_SYS;
    $output = '';

    $away_team = intval($_POST['away_team']);
    $away_hc = intval($_POST['away_hc']);
    $away_sub = intval($_POST['away_sub']);
    $home_team = intval($_POST['home_team']);
    $home_hc = intval($_POST['home_hc']);
    $home_sub = intval($_POST['home_sub']);
    $scheduled = trim($_POST['scheduled']);

    if ($scheduled) {
      if (!preg_match('/^(\d+)-(\d+)-(\d+)\s+(\d+):(\d+)$/', $scheduled, $_scheduled)) {
        return $_SYS['html']->fehler('2', 'Bad date/time');
      }

      if (!checkdate($_scheduled[2], $_scheduled[3], $_scheduled[1])) {
        return $_SYS['html']->fehler('3', 'Bad date');
      }

      if ($_scheduled[4] > 23 || $_scheduled[5] > 59) {
        return $_SYS['html']->fehler('4', 'Bad time');
      }

      $scheduled .= ':00';

      unset($_scheduled);
    } else {
      $scheduled = '0000-00-00 00:00:00';
    }

    if ($this->game) {
      $query = 'SELECT g.season, g.week, g.home, g.away, g.home_hc, g.away_hc, g.home_sub, g.away_sub, g.site, th.user AS home_owner, ta.user AS away_owner, SUBSTRING(g.scheduled, 1, 16) AS scheduled
                FROM   '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                WHERE  g.id = '.$this->game;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() == 0) {
        return $_SYS['html']->fehler('1', 'Game not found.');
      }

      $row = $result->fetch_assoc();

      $this->season = $row['season'];
      $this->week   = $row['week'];

      if (!($_SYS['user']['admin']
            || ($_SYS['user']['id'] == $row['away_owner'] && $row['site'] == 0)
            || ($_SYS['user']['id'] == $row['away_sub'] && $row['site'] == 0)
            || ($_SYS['user']['id'] == $row['home_owner'] && $row['site'] == 0)
            || ($_SYS['user']['id'] == $row['home_sub'] && $row['site'] == 0))) {
        return $_SYS['html']->fehler('2', 'You are not allowed to do this');
      }

      if (!$_SYS['user']['admin']) {
        $away_team = $row['away'];
        $away_hc = $row['away_hc'];
        $away_sub = $row['away_sub'];
        $home_team = $row['home'];
        $home_hc = $row['home_hc'];
        $home_sub = $row['home_sub'];
      }

      if ($row['site']) {
        if (($away_hc < 1 && $away_sub < 1) || ($home_hc < 1 && $home_sub < 1)) {
          return $_SYS['html']->fehler('2', 'You need a coach for BOTH teams ;-)');
        }

        $query = 'UPDATE '.$_SYS['table']['game'].'
                  SET    away_hc = '.$away_hc.',
                         away_sub = '.($away_hc == $away_sub ? 0 : $away_sub).',
                         home_hc = '.$home_hc.',
                         home_sub = '.($home_hc == $home_sub ? 0 : $home_sub).',
                         scheduled = "'.$scheduled.'"
                  WHERE  id = '.$this->game.'
                         AND site != 0';
      } else {
        $query = 'UPDATE '.$_SYS['table']['game'].'
                  SET    away = '.$away_team.',
                         away_sub = '.$away_sub.',
                         home = '.$home_team.',
                         home_sub = '.$home_sub.',
                         scheduled = "'.$scheduled.'"
                  WHERE  id = '.$this->game.'
                         AND site = 0';
      }
    } elseif (($_SYS['user']['admin'] && array_key_exists($this->week, $_SYS['season'][$this->season]['weeks']))
              || ($this->week == 0 && count($_SYS['user']['team'][$this->season]))
              || ($this->week == 0 && $_SYS['user']['admin'])) {

      if (!$_SYS['user']['admin']) {  /* check for own team if user wants to insert exhibition */
        $_is_home = in_array($home_team, $_SYS['user']['team'][$this->season]);
        $_is_away = in_array($away_team, $_SYS['user']['team'][$this->season]);

        if ($_is_home && $_is_away) {
          return $_SYS['html']->fehler('4', 'You cannot play a game against yourself');
        }

        if (!$_is_home && !$_is_away) {
          return $_SYS['html']->fehler('5', 'One of the two teams must be owned by you');
        }

        unset($_is_home, $_is_away);

        $_home_sub = 0;
        $_away_sub = 0;
      }

      $query = 'INSERT INTO '.$_SYS['table']['game'].'
                SET    season = '.$this->season.',
                       week = '.$this->week.',
                       away = '.$away_team.',
                       away_sub = '.$away_sub.',
                       home = '.$home_team.',
                       home_sub = '.$home_sub.',
                       scheduled = "'.$scheduled.'"';
    } else {
      return $_SYS['html']->fehler('1', 'You should never see this');
    }

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_POST['back']);
    #header('Location: '.$_SYS['page']['schedule']['url'].'?season='.$this->season.'&week='.$this->week);
    exit;
  } // _postRequest()


  function _getRequest() {

    global $_SYS;
    $output = '';

    $played = false;

    /* read game from db */

    if ($this->game) {
      $query = 'SELECT g.season, g.week, g.home, g.away, g.home_hc, g.away_hc, g.home_sub, g.away_sub, g.site, th.user AS home_owner, ta.user AS away_owner, SUBSTRING(g.scheduled, 1, 16) AS scheduled
                FROM   '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                WHERE  g.id = '.$this->game;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() == 0) {
        return $_SYS['html']->fehler('1', 'Game not found.');
      }

      $game = $result->fetch_assoc();

      $this->season = $game['season'];
      $this->week   = $game['week'];

      $played = $game['site'];

      if (!($_SYS['user']['admin']
            || ($_SYS['user']['id'] == $game['away_owner'] && $game['site'] == 0)
            || ($_SYS['user']['id'] == $game['away_sub'] && $game['site'] == 0)
            || ($_SYS['user']['id'] == $game['home_owner'] && $game['site'] == 0)
            || ($_SYS['user']['id'] == $game['home_sub'] && $game['site'] == 0))) {
        return $_SYS['html']->fehler('2', 'You are not allowed to do this');
      }
    } elseif (!(($_SYS['user']['admin'] && array_key_exists($this->week, $_SYS['season'][$this->season]['weeks']))
                || $this->week == 0)) {
      return $_SYS['html']->fehler('1', 'You should never see this');
    }

    /* read users */

    $query = 'SELECT * FROM '.$_SYS['table']['user'].' ORDER BY nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $user = array();
    $user_options = array();

    while ($row = $result->fetch_assoc()) {
      $user[$row['id']] = $row;

      if ($row['status'] == 'Active') {
        $user_options[] = array('display' => $row['nick'], 'value' => $row['id']);
      }
    }

    /* read teams */

    $query = 'SELECT   t.*,
                       n.team AS city,
                       n.nick AS nick,
                       n.acro AS acro
              FROM     '.$_SYS['table']['team'].' AS t
                       LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
              WHERE    t.season = '.$this->season.'
              ORDER BY n.team, n.nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $team = array();
    $team_options = array();

    while ($row = $result->fetch_assoc()) {
      $team[$row['id']] = $row;
      $team_options[] = array('display' => $row['city'].' '.$row['nick'].' ('.$user[$row['user']]['nick'].')', 'value' => $row['id']);
    }

    /* generate form */

    $output .= '
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">';

    if (($this->game && $_SYS['user']['admin']) || !$this->game) {
      $output .= '
<table>
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th>Team'.($played ? '' : ' (Owner)').'</th>
      '.($played ? '<th>Owner</th>' : '').'
      '.($_SYS['user']['admin'] ? '<th>Sub</th>' : '').'
    </tr>
  </thead>
  <tbody>
    <tr>
      <th>Away</th>
      <td>'.($played ? $team[$game['away']]['city'].' '.$team[$game['away']]['nick'] : $_SYS['html']->dropdown('away_team', $team_options, $game['away'])).'</td>
      '.($played ? '<td>'.$_SYS['html']->dropdown('away_hc', $user_options, $game['away_hc']).'</td>' : '').'
      '.($_SYS['user']['admin'] ? '<td>'.$_SYS['html']->dropdown('away_sub', $user_options, $game['away_sub']).'</td>' : '').'
    </tr>
    <tr>
      <th>Home</th>
      <td>'.($played ? $team[$game['home']]['city'].' '.$team[$game['home']]['nick'] : $_SYS['html']->dropdown('home_team', $team_options, $game['home'])).'</td>
      '.($played ? '<td>'.$_SYS['html']->dropdown('home_hc', $user_options, $game['home_hc']).'</td>' : '').'
      '.($_SYS['user']['admin'] ? '<td>'.$_SYS['html']->dropdown('home_sub', $user_options, $game['home_sub']).'</td>' : '').'
    </tr>
  </tbody>
</table>';
    }

    $output .= '
<dl>
  <dt>'.$_SYS['html']->label('fscheduled', 'Scheduled for').'</dt>
  <dd>
    '.$_SYS['html']->textfield('scheduled', $game['scheduled'] != '0000-00-00 00:00' ? $game['scheduled'] : '', 16, 16, '', 'id="fscheduled"').'
    (YYYY-MM-DD hh:mm)
  </dd>
</dl>
<dl>
  <dt>
    '.$_SYS['html']->hidden('game', $this->game).'
    '.$_SYS['html']->hidden('season', $this->season).'
    '.$_SYS['html']->hidden('week', $this->week).'
    '.$_SYS['html']->hidden('back', $_SERVER['HTTP_REFERER']).'
  </dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Save').'
  </dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  function getHTML() {
    $this->game = intval($_REQUEST['game']);
    $this->season = intval($_REQUEST['season']);
    $this->week = intval($_REQUEST['week']);

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