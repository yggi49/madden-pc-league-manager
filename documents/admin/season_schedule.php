<?php
/**
 * @(#) error404.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    $output = '';

    $output .= '
  <link rel="prev" href="'.$_SYS['page']['admin/season_list']['url'].'" />';   # "back"

    return $output;
  } // getHeader()


  function _getRequest() {
    global $_SYS;

    $id = intval($_GET['id']);

    /* check if season exists */

    if (!array_key_exists($id, $_SYS['season'])) {
      return $_SYS['html']->fehler('1', 'Season does not exist.');
    }

    $season = $_SYS['season'][$id];

    /* check if schedule may be uploaded */

    $query = 'SELECT *
              FROM   '.$_SYS['table']['game'].'
              WHERE  season = '.$id.'
                     AND week != 0
                     AND site != 0
              LIMIT  1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 1) {
      return $_SYS['html']->fehler('2', 'There were already games played in this season');
    }

    /* show upload form */

    $output = '
<p>Schedule '.$season['name'].' (ID: '.$season['id'].')</p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post" enctype="multipart/form-data">
<dl>
  <dt>'.$_SYS['html']->label('fschedule', 'Schedule').'</dt>
  <dd>'.$_SYS['html']->file('schedule', 0, '', 'id="fschedule" tabindex="10"').'</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Upload').'</dd>
</dl>
</form>';

    return $output;
  }


  function _postRequest() {
    global $_SYS;

    /* check if season exists */

    $id = intval($_POST['id']);

    if (!array_key_exists($id, $_SYS['season'])) {
      return $_SYS['html']->fehler('1', 'Season does not exist.');
    }

    $season = $_SYS['season'][$id];

    /* read uploaded file */

    if (!is_uploaded_file($_FILES['schedule']['tmp_name'])) {
      return $_SYS['html']->fehler('2', 'You should never ever see this error if you did not mess around!');
    }

    /* start transaction */

    $query = 'START TRANSACTION';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* fetch teams for this season */

    $query = 'SELECT id
              FROM   '.$_SYS['table']['team'].'
              WHERE  season = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $teams = array();

    while ($row = $result->fetch_assoc()) {
      $teams[] = $row['id'];
    }

    /* read uploaded file */

    $_file = file($_FILES['schedule']['tmp_name']);
    $sql = array();

    for ($i = 1; $i <= count($_file); ++$i) {
      $_line = trim($_file[$i - 1]);

      if (strlen($_line) == 0) {
        continue;
      }

      if (!preg_match('/^(\d+),(-?\d+),(\d+),(\d+)$/', $_line, $_match)) {
        return $_SYS['html']->fehler('4', 'Invalid syntax (line '.$i.'): '.$_line);
      }

      if ($_match[1] != $id) {
        return $_SYS['html']->fehler('5', 'Invalid season (line '.$i.'): '.$_line);
      }

      if (!in_array($_match[3], $teams)) {
        return $_SYS['html']->fehler('6', 'Invalid away team (line '.$i.'): '.$_line);
      }

      if (!in_array($_match[4], $teams)) {
        return $_SYS['html']->fehler('7', 'Invalid home team (line '.$i.'): '.$_line);
      }

      if ($_match[2] == 0
          || ($_match[2] < 0 && $_match[2] < -$season['pre_weeks'])
          || ($_match[2] > 0 && $_match[2] > $season['reg_weeks'])) {
        return $_SYS['html']->fehler('8', 'Invalid week (line '.$i.'): '.$_line);
      }

      $sql[] = '('.$_line.')';
    }

    unset($_file, $i, $_line, $_match);

    /* check if schedule may be uploaded */

    $query = 'SELECT id, site
              FROM   '.$_SYS['table']['game'].'
              WHERE  season = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $games = array();

    while ($row = $result->fetch_assoc()) {
      if ($row['site'] != '0') {
        return $_SYS['html']->fehler('3', 'There were already games played in this season');
      }

      $games[] = $row['id'];
    }

    /*
     * insert new schedule:
     *  - delete old schedule
     *  - delete pending entries
     *  - insert new schedule
     */

    $query = 'DELETE FROM '.$_SYS['table']['game'].' WHERE season = '.$id;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if (count($games) > 0) {
      $query = 'DELETE FROM '.$_SYS['table']['pending'].' WHERE game IN ('.join(', ', $games).')';
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    if (count($sql) > 0) {
      $query = 'INSERT '.$_SYS['table']['game'].' (season, week, away, home) VALUES '.join(', ', $sql);
      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    /* commit transaction */

    $query = 'COMMIT';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['admin/season_list']['url']);
    exit;
  }


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