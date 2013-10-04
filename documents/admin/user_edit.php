<?php
/**
 * @(#) admin/user_edit.php
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

    /* get all styles */

    $styles = array();

    $style_files = glob('styles/*.css');
    if (!$style_files) $style_files = array();
    foreach ($style_files as $_style) {
      $_style = substr($_style, 7, -4);

      if ($_style == 'default') {
        continue;
      }

      $styles[] = array('display' => ''.$_style, 'value' => ''.$_style);
    }

    /* team logos */

    $logos = array();
    $logos[] = array('display' => '-- KEINE --',       'value' => '-- NONE --');
    $logos[] = array('display' => 'Buttons',           'value' => 'buttons_klein');
    $logos[] = array('display' => 'Halb',              'value' => 'halb_klein');
    $logos[] = array('display' => 'Klein/Bunt',        'value' => '18x18');
    $logos[] = array('display' => 'Klein/Transparent', 'value' => 'klein_transparent');
    $logos[] = array('display' => 'Klein/Weiss',       'value' => 'klein_weiss');

    /* determine user id */

    $id = intval($_GET['id']);

    if ($id != 0) {
      $query = 'SELECT *
                FROM   '.$_SYS['table']['user'].'
                WHERE  id = '.$id;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() != 1) {
        return $_SYS['html']->fehler('#1', 'User does not exist.');
      }

      $row = $result->fetch_assoc();
    }

    /* assemble user edit form */

    $output = '
<h1>'.($id != 0 ? 'Edit user &ldquo;'.$row['nick'].'&rdquo;' : 'Create new user').'</h1>
<p><a href="'.$_SYS['page']['coaches']['url'].'">Back to user list</a></p>
<form action="'.$_SYS['page']['admin/user_save']['url'].'" method="post">';

    if ($id != $_SYS['user']['id']) {
      $options = array();
      $options[] = array('value' => 'Active',   'display' => 'Active');
      $options[] = array('value' => 'Disabled', 'display' => 'Disabled');

      $output .= '
<dl>
  <dt>'.$_SYS['html']->label('fstatus', 'Status').'</dt>
  <dd>'.$_SYS['html']->dropdown('status', $options, $row['status'], '', '', 'id="fstatus"', 2).'</dd>
</dl>';
    }

    $output .= '
<dl>
  <dt>'.$_SYS['html']->label('fnick', 'Nick').'</dt>
  <dd>'.$_SYS['html']->textfield('nick', $row['nick'], 0, 20, '', 'id="fnick"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpass', 'Pass').'</dt>
  <dd>'.$_SYS['html']->password('pass', '', 0, 0, '', 'id="fpass"').'</dd>
</dl>';

    if ($id != $_SYS['user']['id']) {
      $output .= '
<dl>
  <dt>'.$_SYS['html']->label('fadmin', 'Admin').'</dt>
  <dd>'.$_SYS['html']->checkbox('admin', '1', $row['admin'], 'fadmin').'</dd>
</dl>';
    }

    $output .= '
<dl>
  <dt>'.$_SYS['html']->label('femail', 'eMail').'</dt>
  <dd>'.$_SYS['html']->textfield('email', $row['email'], 0, 50, '', 'id="femail"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fshowemail', 'Show eMail').'</dt>
  <dd>'.$_SYS['html']->checkbox('show_email', '1', $row['show_email'], 'fshowemail').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fnotify', 'Notify').'</dt>
  <dd>'.$_SYS['html']->checkbox('notify', '1', $row['notify'], 'fnotify').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fphone', 'Phone').'</dt>
  <dd>'.$_SYS['html']->textfield('phone', $row['phone'], 0, 20, '', 'id="fphone"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('ficq', 'ICQ').'</dt>
  <dd>'.$_SYS['html']->textfield('icq', $row['icq'], 0, 10, '', 'id="ficq"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fxfire', 'XFire-Nick').'</dt>
  <dd>'.$_SYS['html']->textfield('xfire', $row['xfire'], 0, 30, '', 'id="fxfire"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fshowip', 'Show IP').'</dt>
  <dd>'.$_SYS['html']->checkbox('show_ip', '1', $row['show_ip'], 'fshowip').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fstyle', 'Style').'</dt>
  <dd>
    '.$_SYS['html']->dropdown('style', $styles, $row['style'], '', '', 'id="fstyle"', 4).'
  </dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('flogos', 'Logos').'</dt>
  <dd>
    '.$_SYS['html']->dropdown('logos', $logos, $row['logos'], '', '', 'id="flogos"', 4).'
  </dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fusertext', 'Usertext').'</dt>
  <dd>'.$_SYS['html']->textarea('usertext', $row['usertext'], 10, 80, '', 'id="fusertext"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    return $output;
  } // getHTML()

} // Page

?>