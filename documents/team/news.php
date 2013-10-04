<?php
/**
 * @(#) team/news.php
 */

class Page {


  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';
    $id = intval($_GET['id']);
    $coach = false;

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

    $row = $result->fetch_assoc();

    $coach = $_SYS['user']['id'] == $row['uid'];
    $_SYS['request']['season'] = $row['season'];

    $output .= '
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$id.'">Home</a>
  &middot; [ News ]
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$id.'">Roster</a>
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$id.'">Schedule</a>
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$id.'">Stats</a>
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$id.'">Scouts</a>
</p>';

    if ($coach) {
      $output .= '
<p>
  <a href="'.$_SYS['page']['team/news_edit']['url'].'?team='.$id.'">New&hellip;</a>
</p>';
    }

    /* read news */

    $query = 'SELECT   *
              FROM     '.$_SYS['table']['news'].'
              WHERE    team = '.$id.'
              ORDER BY `date` DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      $output .= '
<p>No news available.</p>';
    } else {
      $output .= '
<table class="news">
  <colgroup>
    <col class="title" />
    <col class="date" />
    '.($coach ? '<col class="actions" />' : '').'
  </colgroup>';

      while ($row = $result->fetch_assoc()) {
        $output .= '
  <tr>
    <td><a href="'.$_SYS['page']['team/news_detail']['url'].'?id='.$row['id'].'">'.$_SYS['html']->specialchars($row['title']).'</a></td>
    <td>'.date('M j, Y, g:i A', $row['date']).'</td>';

        if ($coach) {
          $output .= '
    <td><a href="'.$_SYS['page']['team/news_edit']['url'].'?team='.$id.'&amp;id='.$row['id'].'">Edit</a></td>';
        }

        $output .= '
  </tr>';
      }

      $output .= '
</table>';
    }

    return $output;
  } // getHTML()

} // Page

?>