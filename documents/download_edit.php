<?php
/**
 * @(#) download_edit.php
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

    /* get download */

    $id = intval($_GET['id']);

    if ($id != 0) {
      $query = 'SELECT d.*, u.nick
                FROM   '.$_SYS['table']['download'].' AS d
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON d.uid = u.id
                WHERE  d.id = '.$id;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() != 1) {
        return $_SYS['html']->fehler('1', 'Download does not exist.');
      }

      $row = $result->fetch_assoc();
    }

    /* list of file types and compressions */

    $_temp = $_SYS['util']->filetypes();

    $filetypes = array();

    foreach ($_temp as $_key => $_val) {
      $filetypes[] = array('value' => $_key, 'display' => $_val);
    }

    $_temp = $_SYS['util']->compressions();

    $compressions = array();

    foreach ($_temp as $_key => $_val) {
      $compressions[] = array('value' => $_key, 'display' => $_val);
    }

    unset($_temp);

    /* assemble download edit form */

    $output .= '
<p><a href="'.$_SYS['page']['downloads']['url'].'">Back to download list</a></p>
<form action="'.$_SYS['page']['download_save']['url'].'" method="post" enctype="multipart/form-data">
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
  <dt>'.$_SYS['html']->label('fdescription', 'Description').'</dt>
  <dd>'.$_SYS['html']->textarea('description', $row['description'], 4, 50, '', 'id="fdescription"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('ffilename', 'Filename').'</dt>
  <dd>'.$_SYS['html']->textfield('filename', $row['filename'], 0, 50, '', 'id="ffilename"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('ftype', 'Type').'</dt>
  <dd>'.$_SYS['html']->dropdown('filetype', $filetypes, $row['filetype'], '', '', 'id="ftype"', 2).'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fcompression', 'Compression').'</dt>
  <dd>'.$_SYS['html']->dropdown('compression', $compressions, $row['compression'], '', '', 'id="fcompression"', 2).'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fshowfrom', 'From').'</dt>
  <dd>'.$_SYS['html']->textfield('showfrom', $row['showfrom'] ? date('Y-m-d H:i', $row['showfrom']) : '', 0, 16, '', 'id="fshowfrom"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fshowthru', 'Thru').'</dt>
  <dd>'.$_SYS['html']->textfield('showthru', $row['showthru'] ? date('Y-m-d H:i', $row['showthru']) : '', 0, 16, '', 'id="fshowthru"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('ffile', 'File').'</dt>
  <dd>'.$_SYS['html']->file('file', 0, '', 'id="ffile"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    if ($id != 0) {
      $output .= '
<p>Last modified on '.date('Y-m-d H:i:s', $row['modified']).(strlen($row['nick']) ? ' by '.$_SYS['html']->specialchars($row['nick']) : '').'</p>';
    }

    return $output;
  } // getHTML()

} // Page

?>