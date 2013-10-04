<?php
/**
 * @(#) error404.php
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

    /* query database */

    $query = 'SELECT   u.id                        AS uid,
                       u.nick                      AS user,
                       CONCAT(n.team, " ", n.nick) AS team,
                       t.id                        AS team_id,
                       t.season                    AS season,
                       m.title                     AS title,
                       m.news                      AS news,
                       a.nick                      AS author,
                       m.date                      AS date
              FROM     '.$_SYS['table']['news'].' AS m
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON m.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON t.user = u.id
                       LEFT JOIN '.$_SYS['table']['user'].' AS a ON m.user = a.id
              WHERE    m.id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Article does not exist');
    }

    $row = $result->fetch_assoc();
    $_SYS['request']['season'] = $row['season'];

      /* image available? */

    $image_files = glob($_SYS['dir']['imgdir'].'/news/'.$id.'.*');
    if (!$image_files) $image_files = array();
    foreach ($image_files as $_filename) {
      $row['image'] = $_SYS['dir']['hostdir'].'/images/news/'.basename($_filename);
      break;
    }

    unset($_filename);

    /* print article */

    $output .= '
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['team_id'].'">Home</a>
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$row['team_id'].'">News</a>
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$row['team_id'].'">Roster</a>
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$row['team_id'].'">Schedule</a>
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$row['team_id'].'">Stats</a>
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$row['team_id'].'">Scouts</a>
</p>
<h2 class="boxed">'.$_SYS['html']->specialchars($row['title']).'</h2>
<p class="boxed">';

    if ($row['image']) {
      $output .= '
<img src="'.$row['image'].'" alt="" class="news" />';
    }

    $output .= '
<strong>by '.$_SYS['html']->specialchars($row['author']).' ('.date('M j, Y', $row['date']).')</strong>
'.$_SYS['html']->bbcode($_SYS['html']->specialchars($row['news'])).'
<br class="float" />
</p>';

    return $output;
  } // getHTML()

} // Page

?>