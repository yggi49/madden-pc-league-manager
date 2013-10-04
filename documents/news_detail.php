<?php
/**
 * @(#) news_detail.php
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
<h2 class="boxed">'.$_SYS['html']->specialchars($row['title']).'</h2>
<p class="boxed">';

    if ($row['image']) {
      $output .= '
<img src="'.$row['image'].'?cache='.$_SYS['time']['now'].'" alt="" class="news" />';
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