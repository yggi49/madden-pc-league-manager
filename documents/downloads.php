<?php
/**
 * @(#) downloads.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    /* get file and compression types */

    $filetypes = $_SYS['util']->filetypes();
    $compressions = $_SYS['util']->compressions();

    /* query download table */

    $query = 'SELECT   id, title, description, filename, filetype, compression, status, showfrom, showthru, modified
              FROM     '.$_SYS['table']['download'].'
              ORDER BY title';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($_SYS['user']['admin']) {
    $output .= '
<p><a href="'.$_SYS['page']['download_edit']['url'].'">New Download</a></p>';
    }

    $output .= '
<table class="downloads">
  <thead>
    <tr>
      '.($_SYS['user']['admin'] ? '<th scope="col">Status</th>' : '').'
      <th scope="col">Download</th>
      <th scope="col">Type</th>
      <th scope="col">Size</th>
      <th scope="col">Compression</th>
      <th scope="col">Date</th>
      '.($_SYS['user']['admin'] ? '<th scope="col">From</th>' : '').'
      '.($_SYS['user']['admin'] ? '<th scope="col">Thru</th>' : '').'
      <th scope="col">Description</th>
      '.($_SYS['user']['admin'] ? '<th scope="colgroup" colspan="2">Actions</th>' : '').'
    </tr>
  </thead>
  <tbody>';

    $count = 0;

    while ($row = $result->fetch_assoc()) {

      /* determine status */

      $row['visible'] = ($row['showfrom'] != 0 && $row['showthru'] != 0 && $_SYS['time']['now'] >= $row['showfrom'] && $_SYS['time']['now'] <= $row['showthru'])
                        || ($row['showfrom'] != 0 && $row['showthru'] == 0 && $_SYS['time']['now'] >= $row['showfrom'])
                        || ($row['showfrom'] == 0 && $row['showthru'] != 0 && $_SYS['time']['now'] <= $row['showthru'])
                        || ($row['showfrom'] == 0 && $row['showthru'] == 0);

      /* don't show download for normal users if download is invisible or status is offline*/

      if (!$_SYS['user']['admin'] && (!$row['visible'] || $row['status'] == 0)) {
        continue;
      }

      $filename = $_SYS['dir']['downdir'].'/'.$row['id'];

      ++$count;

      $output .= '
    <tr>';

      if ($_SYS['user']['admin']) {
        $output .= '
      <td><a href="'.$_SYS['page']['download_status']['url'].'?id='.$row['id'].'">'.($row['status'] ? 'Online' : 'Offline').'</a>'.($row['visible'] ? '' : ' (invisible)').'</td>';
      }

      $output .= '
      <td><a href="'.$_SYS['page']['download']['url'].'?id='.$row['id'].'">'.$_SYS['html']->specialchars($row['title']).'</a></td>
      <td>'.$_SYS['html']->specialchars($filetypes[$row['filetype']] ? $filetypes[$row['filetype']] : 'Unknown').'</td>
      <td>'.sprintf('%.2f', filesize($filename)/1024).' kB</td>
      <td>'.$_SYS['html']->specialchars($compressions[$row['compression']] ? $compressions[$row['compression']] : 'Unknown').'</td>
      <td>'.date('Y-m-d', $row['modified']).'</td>';

      if ($_SYS['user']['admin']) {
        $output .= '
      <td>'.($row['showfrom'] ? date('Y-m-d H:i', $row['showfrom']) : '&mdash;').'</td>
      <td>'.($row['showthru'] ? date('Y-m-d H:i', $row['showthru']) : '&mdash;').'</td>';
      }

      $output .= '
      <td>'.$_SYS['html']->specialchars($row['description']).'</td>';

      if ($_SYS['user']['admin']) {
        $output .= '
      <td><a href="'.$_SYS['page']['download_edit']['url'].'?id='.$row['id'].'">Edit</a></td>
      <td><a href="'.$_SYS['page']['download_delete']['url'].'?id='.$row['id'].'">Delete</a></td>';
      }

      $output .= '
    </tr>';
    }

    if ($count == 0) {
      $output .= '
    <tr>
      <td colspan="'.($_SYS['user']['admin'] ? '11' : '7').'">No downloads.</td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    return $output;
  } // getHTML()

} // Page

?>