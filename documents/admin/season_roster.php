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

    /* show upload form */

    $output = '
<p>Roster '.$season['name'].'</p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post" enctype="multipart/form-data">
<dl>
  <dt>'.$_SYS['html']->label('froster', 'Roster').'</dt>
  <dd>'.$_SYS['html']->file('roster', 0, '', 'id="froster" tabindex="10"').'</dd>
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

    if (!is_uploaded_file($_FILES['roster']['tmp_name'])) {
      return $_SYS['html']->fehler('2', 'You should never ever see this error if you did not mess around!');
    }

    /* start transaction */

//     $query = 'START TRANSACTION';
//     $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* fetch teams for this season */

    $query = 'SELECT t.id AS id, n.nick AS nick
              FROM   '.$_SYS['table']['team'].' AS t
                     LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
              WHERE  t.season = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $teams = array();

    while ($row = $result->fetch_assoc()) {
      $teams[$row['nick']] = $row['id'];
    }

    /* read uploaded file */

    $_file = fopen($_FILES['roster']['tmp_name'], 'r');
    $sql = array();

    while (!feof($_file)) {
      $_line = explode(',', fgets($_file));

      if (count($_line) != 31) {
        continue;
      }

      $_row = array();

      for ($i = 0; $i < count($_line); ++$i) {
        $_line[$i] = trim($_line[$i]);
        $_line[$i] = trim($_line[$i], '"');
      }

      /* check team */

      $_tmp = array_shift($_line);

      if (!array_key_exists($_tmp, $teams)) {
        continue;
      }

      $_row[] = $teams[$_tmp];

      /* first name - last name - position */

      $_row[] = '"'.array_shift($_line).'"';
      $_row[] = '"'.array_shift($_line).'"';
      $_row[] = '"'.array_shift($_line).'"';

      /* ovr - yrl */

      $_row[] = array_shift($_line);
      $_row[] = array_shift($_line);

      /* topt - bon - sal */

      for ($i = 1; $i <= 3; ++$i) {
        $_tmp = substr(array_shift($_line), 1);
        $_row[] = substr($_tmp, 0, -1) * (substr($_tmp, -1) == 'M' ? 1000 : 1);
      }

      $sql[] = '('.$id.', '.join(', ', array_merge($_row, $_line)).')';

      unset($_line, $_row, $_tmp, $i);
    }

    fclose($_file);
    unset($_file);

    /*
     * insert new schedule:
     *  - delete old schedule
     *  - delete pending entries
     *  - insert new schedule
     */

    $query = 'DELETE FROM '.$_SYS['table']['roster'].' WHERE season = '.$id;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if (count($sql) > 0) {
      $query = 'INSERT '.$_SYS['table']['roster'].' (season, team, firstname, lastname, pos, ovr, yrl, tot, bon, sal, age, spd, str, awr, agi, acc, cth, car, jmp, btk, tak, thp, tha, pbk, rbk, kpw, kac, kr, imp, sta, inj, tgh) VALUES '.join(', ', $sql);
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