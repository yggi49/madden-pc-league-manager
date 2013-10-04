<?php
/**
 * @(#) links.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    /* query link table */

    $query = 'SELECT   *
              FROM     '.$_SYS['table']['link'].'
              ORDER BY title';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($_SYS['user']['admin']) {
    $output .= '
<p><a href="'.$_SYS['page']['link_edit']['url'].'">New Link</a></p>';
    }

    $output .= '
<table class="links">
  <thead>
    <tr>
      '.($_SYS['user']['admin'] ? '<th scope="col">Status</th>' : '').'
      <th scope="col">Link</th>
      <th scope="col">Description</th>
      '.($_SYS['user']['admin'] ? '<th scope="colgroup" colspan="2">Actions</th>' : '').'
    </tr>
  </thead>
  <tbody>';

    $count = 0;

    while ($row = $result->fetch_assoc()) {

      /* don't show link for normal users if link status is offline*/

      if (!$_SYS['user']['admin'] && $row['status'] == 0) {
        continue;
      }

      ++$count;

      $output .= '
    <tr>';

      if ($_SYS['user']['admin']) {
        $output .= '
      <td><a href="'.$_SYS['page']['link_status']['url'].'?id='.$row['id'].'">'.($row['status'] ? 'Online' : 'Offline').'</a></td>';
      }

      $output .= '
      <td><a href="'.$_SYS['html']->specialchars($row['href']).'">'.$_SYS['html']->specialchars($row['title']).'</a></td>
      <td>'.$_SYS['html']->specialchars($row['description']).'</td>';

      if ($_SYS['user']['admin']) {
        $output .= '
      <td><a href="'.$_SYS['page']['link_edit']['url'].'?id='.$row['id'].'">Edit</a></td>
      <td><a href="'.$_SYS['page']['link_delete']['url'].'?id='.$row['id'].'">Delete</a></td>';
      }

      $output .= '
    </tr>';
    }

    if ($count == 0) {
      $output .= '
    <tr>
      <td colspan="'.($_SYS['user']['admin'] ? '5' : '2').'">No links.</td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    return $output;
  } // getHTML()

} // Page

?>