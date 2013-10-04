<?php
/**
 * @(#) news.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    if ($_SYS['user']['admin']) {
      $output .= '
<p>
  <a href="'.$_SYS['page']['news_edit']['url'].'">New&hellip;</a>
</p>';
    }

    /* read news */

    $query = 'SELECT   *
              FROM     '.$_SYS['table']['news'].'
              WHERE    team = 0
              ORDER BY `date` DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      $output .= '
<p>No news available.</p>';
    } else {
      $output .= '
<table class="news">
  <thead>
    <tr>
      <th scope="col">(Un)Top</th>
      <th scope="col">Title</th>
      <th scope="col">Date</th>
      '.($_SYS['user']['admin'] ? '<th scope="col">Actions</th>' : '').'
    </tr>
  </thead>
  <tbody>';

      while ($row = $result->fetch_assoc()) {
        $output .= '
    <tr>
      <td><a href="'.$_SYS['page']['news_top']['url'].'?id='.$row['id'].'">'.($row['top'] ? 'Topped' : 'Untopped').'</a></td>
      <td><a href="'.$_SYS['page']['news_detail']['url'].'?id='.$row['id'].'">'.$_SYS['html']->specialchars($row['title']).'</a></td>
      <td class="date">'.date('M j, Y, g:i A', $row['date']).'</td>';

        if ($_SYS['user']['admin']) {
          $output .= '
      <td class="actions"><a href="'.$_SYS['page']['news_edit']['url'].'?id='.$row['id'].'">Edit</a></td>';
        }

        $output .= '
    </tr>';
      }

      $output .= '
  </tbody>
</table>';
    }

    return $output;
  } // getHTML()

} // Page

?>