<?php
/**
 * @(#) link_edit.php
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

    /* get link */

    $id = intval($_GET['id']);

    if ($id != 0) {
      $query = 'SELECT l.*, u.nick
                FROM   '.$_SYS['table']['link'].' AS l
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON l.uid = u.id
                WHERE  l.id = '.$id;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() != 1) {
        return $_SYS['html']->fehler('1', 'Link does not exist.');
      }

      $row = $result->fetch_assoc();
    } else {
      $row['status'] = '1';
    }

    /* assemble link edit form */

    $output .= '
<p><a href="'.$_SYS['page']['links']['url'].'">Back to link list</a></p>
<form action="'.$_SYS['page']['link_save']['url'].'" method="post">
<dl>
  <dt>Status</dt>
  <dd>
    '.$_SYS['html']->radio('status', '1', $row['status'], 'fstatus1').'
    '.$_SYS['html']->label('fstatus1', 'online').'
    '.$_SYS['html']->radio('status', '0', $row['status'], 'fstatus0').'
    '.$_SYS['html']->label('fstatus0', 'offline').'
  </dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('ftitle', 'Title').'</dt>
  <dd>'.$_SYS['html']->textfield('title', $row['title'], 0, 100, '', 'id="ftitle"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fhref', 'Link').'</dt>
  <dd>'.$_SYS['html']->textfield('href', $row['href'], 0, 255, '', 'id="fhref"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fdescription', 'Description').'</dt>
  <dd>'.$_SYS['html']->textarea('description', $row['description'], 4, 50, '', 'id="fdescription"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    if ($id != 0) {
      $output .= '
<p>Last modified by '.$_SYS['html']->specialchars($row['nick']).'</p>';
    }

    return $output;
  } // getHTML()

} // Page

?>