<?php
/**
 * @(#) admin/user_list.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    $output = '';

    return $output;
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    $season = array_key_exists('season', $_GET) ? intval($_GET['season']) : 'all';

    /* season header */

    $output .= '
<p>
  '.($season == 'all' ? '[ All Users ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season=all">All Users</a>').' &middot;';

    $_period = array();

    foreach ($_SYS['season'] as $_key => $_val) {
      if ($season == $_key) {
        $_period[] = '
  [ '.$_val['name'].' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_key.'">'.$_val['name'].'</a>';
      }
    }

    $output .= join(' &middot;', $_period).'
</p>';

    /* get user records */

    $query = 'SELECT *
              FROM   '.$_SYS['table']['game'].'
              WHERE  site != 0';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $records = array();

    while ($row = $result->fetch_assoc()) {
        $away = $row['away_sub'] ? $row['away_sub'] : $row['away_hc'];
        $home = $row['home_sub'] ? $row['home_sub'] : $row['home_hc'];

        if ($row['away_score'] > $row['home_score']) {
            $records[$away]['won']++;
            $records[$home]['lost']++;
        }
        elseif ($row['away_score'] < $row['home_score']) {
            $records[$away]['lost']++;
            $records[$home]['won']++;
        }
        else {
            $records[$away]['tied']++;
            $records[$home]['tied']++;
        }
    }

    foreach (array_keys($records) as $uid) {
        $records[$uid]['pct'] = ltrim(sprintf('%.3f', ($records[$uid]['won'] + $records[$uid]['tied'] / 2) / ($records[$uid]['won'] + $records[$uid]['lost'] + $records[$uid]['tied'])), '0');
    }

    /* query user table */

    $query = 'SELECT DISTINCT u.id, u.nick, u.pwd, u.admin, u.email, u.show_email, u.notify, u.phone, u.icq, u.ip, u.show_ip, u.status
              FROM     '.$_SYS['table']['user'].' AS u
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON t.user = u.id
              '.($season != 'all' ? 'WHERE t.season = '.$season : '').'
              ORDER BY u.nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($_SYS['user']['admin']) {
      $output .= '
<p><a href="'.$_SYS['page']['admin/user_edit']['url'].'">Create new user</a></p>';
    }

    $output .= '
<table>
  <thead>
    <tr>
      '.($_SYS['user']['admin'] ? '<th scope="col">Status</th>' : '').'
      <th scope="col">Nick</th>
      <th scope="col">Pct</th>';

    if ($_SYS['user']['id']) {
      $output .= '
      <th scope="col">eMail</th>
      <th scope="col">Phone</th>
      <th scope="col">ICQ</th>
      <th scope="col">Last IP</th>
      '.($_SYS['user']['admin'] ? '<th scope="col">Actions</th>' : '');
    }

    $output .= '
    </tr>
  </thead>
  <tbody>';

    if ($result->rows() == 0) {
      $output .= '
    <tr>
      <td colspan="'.($_SYS['user']['admin'] ? '11' : ($_SYS['user']['id'] ? '6' : '2')).'">No users.</td>
    </tr>';
    }

    while ($row = $result->fetch_assoc()) {

      $row['nick'] = $_SYS['html']->specialchars($row['nick']);

      /* print table row */

      $output .= '
    <tr'.($row['admin'] ? ' class="admin"' : '').'>
      '.($_SYS['user']['admin'] ? '<td>'.($row['id'] == $_SYS['user']['id'] ? $row['status'] : '<a href="'.$_SYS['page']['admin/user_status']['url'].'?id='.$row['id'].'" title="Change status">'.$row['status'].'</a>').'</td>' : '').'
      <td><a href="'.$_SYS['page']['profile']['url'].'?id='.$row['id'].'" title="Profile for user &ldquo;'.$row['nick'].'&rdquo;">'.$row['nick'].'</a></td>
      <td>'.($records[$row['id']] ? $records[$row['id']]['pct'] : '&ndash;').'</td>';

      if ($_SYS['user']['id']) {
        $output .= '
      <td>'.($row['email'] && ($row['show_email'] || $_SYS['user']['admin']) ? '<a href="mailto:'.$row['email'].'">'.$row['email'].'</a>' : '&nbsp;').'</td>
      <td>'.($row['phone'] ? $row['phone'] : '&nbsp;').'</td>
      <td>'.($row['icq'] ? $row['icq'] : '&nbsp;').'</td>
      <td>'.($row['ip'] && ($row['show_ip'] || $_SYS['user']['admin']) ? $row['ip'] : '&nbsp;').'</td>';

        if ($_SYS['user']['admin']) {
          $output .= '
      <td>
        <a href="'.$_SYS['page']['admin/user_edit']['url'].'?id='.$row['id'].'" title="Edit user &ldquo;'.$row['nick'].'&rdquo;">Edit</a>
        '.($_SYS['util']->is_removable($row['id']) ? '<a href="'.$_SYS['page']['admin/user_delete']['url'].'?id='.$row['id'].'" title="Delete user &ldquo;'.$row['nick'].'&rdquo;" class="del">Delete</a>' : '&nbsp;').'
      </td>';
        }
      }

      $output .= '
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    return $output;
  } // getHTML()

} // Page

?>