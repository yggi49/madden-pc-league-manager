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

    if (!array_key_exists($id, $_SYS['season'])) {
      return $_SYS['html']->fehler('1', 'Season does not exist.');
    }

    $season = $_SYS['season'][$id];

    /* read teams */

    $query = 'SELECT   id,
                       CONCAT(team, " ", nick) AS display
              FROM     '.$_SYS['table']['nfl'].'
              ORDER BY display';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $teams[] = array('display' => $row['display'], 'value' => $row['id']);
    }

    /* read users */

    $query = 'SELECT   id, nick
              FROM     '.$_SYS['table']['user'].'
              ORDER BY nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $users[] = array('display' => $row['nick'], 'value' => $row['id']);
    }

    /* read teams and conferences */

    $query = 'SELECT   id, conference, division, team, user
              FROM     '.$_SYS['table']['team'].'
              WHERE    season = '.$id.'
              ORDER BY conference, division, id';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output = '
<h1>Edit Teams for '.$_SYS['html']->specialchars($season['name']).'</h1>
<p><a href="'.$_SYS['page']['admin/season_list']['url'].'">Back to season list</a></p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">';

    while ($row = $result->fetch_assoc()) {
      if ($row['conference'].$row['division'] != $_prev) {
        if (isset($_prev)) {
          $output .= '
  </tbody>
</table>';
        }

        $output .= '
<table>
  <thead>
    <tr>
      <td colspan="3">
        '.$_SYS['html']->textfield('conference['.$row['id'].']', $row['conference'], 3, 3).'
        '.$_SYS['html']->textfield('division['.$row['id'].']', $row['division'], 10, 10).'
      </td>
    </tr>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">User</th>
      <th scope="col">Team</th>
    </tr>
  </thead>
  <tbody>';

        $_prev = $row['conference'].$row['division'];
      }

      $output .= '
    <tr>
      <td>'.$row['id'].'</td>
      <td>'.$_SYS['html']->dropdown('user['.$row['id'].']', $users, $row['user']).'</td>
      <td>'.$_SYS['html']->dropdown('team['.$row['id'].']', $teams, $row['team']).'</td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    $output .= '
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    return $output;
  }


  function _postRequest() {
    global $_SYS;

    $id = intval($_POST['id']);

    if (!array_key_exists($id, $_SYS['season'])) {
      return $_SYS['html']->fehler('1', 'Season does not exist.');
    }

    $season = $_SYS['season'][$id];

    /* start transaction */

    $query = 'START TRANSACTION';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* read teams and conferences */

    $query = 'SELECT   id, conference, division, team, user
              FROM     '.$_SYS['table']['team'].'
              WHERE    season = '.$id.'
              ORDER BY conference, division, id';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    while ($row = $result->fetch_assoc()) {
      $teams[$row['id']] = array('id'         => $row['id'],
                                 'conference' => $row['conference'],
                                 'division'   => $row['division'],
                                 'team'       => intval($_POST['team'][$row['id']]),
                                 'user'       => intval($_POST['user'][$row['id']]));
    }

    /* new conference and division names */

    foreach (array_keys($_POST['conference']) as $_id) {
      if (array_key_exists($_id, $_POST['division']) && array_key_exists($_id, $teams)) {
        if (strlen(trim($_POST['conference'][$_id])) > 0 && strlen(trim($_POST['division'][$_id])) > 0) {
          $_old_conf = $teams[$_id]['conference'];
          $_old_div  = $teams[$_id]['division'];

          foreach (array_keys($teams) as $_team) {
            if ($teams[$_team]['conference'] == $_old_conf && $teams[$_team]['division'] == $_old_div) {
              $teams[$_team]['conference'] = trim($_POST['conference'][$_id]);
              $teams[$_team]['division']   = trim($_POST['division'][$_id]);
            }
          }
        }
      }
    }

    unset($_id, $_old_conf, $_old_div, $_team);

    /* update */

    foreach ($teams as $_team) {
      $_sql[] = '('.$id.', "'.join('", "', $_team).'")';
    }

    $query = 'REPLACE '.$_SYS['table']['team'].'
                      (season, id, conference, division, team, user)
              VALUES  '.join(', ', $_sql);
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* commit transaction */

    $query = 'COMMIT';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* update stats tables */

    $_SYS['util']->update_matchups($id);

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