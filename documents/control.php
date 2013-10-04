<?php
/**
 * @(#) control.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _postRequest() {
    global $_SYS;

    /* check for valid user name */

    $_POST['nick'] = trim($_POST['nick']);

    if (strlen($_POST['nick']) == 0) {
      return $_SYS['html']->fehler('2', 'Bad nick.');
    }

    $query = 'SELECT * FROM '.$_SYS['table']['user'].' WHERE nick = '.$_SYS['dbh']->escape_string($_POST['nick']).' AND id != '.$_SYS['user']['id'];
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 1) {
      return $_SYS['html']->fehler('2', 'Nick exists already.');
    }

    /* check for new password */

    if (strlen($_POST['pass']) > 0 && $_POST['pass'] != $_POST['passconfirm']) {
      return $_SYS['html']->fehler('5', 'Password confirmation failed.');
    }

    /* check for valid email address */

    $_POST['email'] = trim($_POST['email']);

    if (strlen($_POST['email']) > 0 && !$_SYS['util']->valid_email($_POST['email'])) {
      return $_SYS['html']->fehler('4', 'Invalid email address.');
    }

    /* notify can only work w/ valid email address */

    if (intval($_POST['notify']) && !$_SYS['util']->valid_email($_POST['email'])) {
      return $_SYS['html']->fehler('5', 'Notify can only work with a valid email address');
    }

    /* check for valid icq number */

    $_POST['icq'] = trim(preg_replace('/\D/', '', $_POST['icq']));

    if (strlen($_POST['icq']) > 0) {
      $_POST['icq'] = intval($_POST['icq']);

      if ($_POST['icq'] < 1 || $_POST['icq'] > 9999999999) {
        return $_SYS['html']->fehler('5', 'Invalid ICQ number.');
      }
    }

    /* assemble query */

    $query = 'UPDATE '.$_SYS['table']['user'].'
              SET    nick       = '.$_SYS['dbh']->escape_string($_POST['nick']).',';

    if (strlen($_POST['pass']) > 0) {
      $query .= '
                     pwd        = SHA1("'.$_POST['pass'].'"),';
    }

    $query .= '
                     email      = '.$_SYS['dbh']->escape_string($_POST['email']).',
                     show_email = '.intval($_POST['show_email']).',
                     notify     = '.intval($_POST['notify']).',
                     phone      = '.$_SYS['dbh']->escape_string($_POST['phone']).',
                     icq        = '.$_SYS['dbh']->escape_string($_POST['icq']).',
                     xfire      = '.$_SYS['dbh']->escape_string($_POST['xfire']).',
                     show_ip    = '.intval($_POST['show_ip']).',
                     style      = '.$_SYS['dbh']->escape_string($_POST['style']).',
                     logos      = '.$_SYS['dbh']->escape_string($_POST['logos']).',
                     usertext   = '.$_SYS['dbh']->escape_string($_POST['usertext']).'
              WHERE  id = '.$_SYS['user']['id'];

    /* perform update */

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* update cookie if password was changed */

    if (strlen($_POST['pass']) > 0) {
      $query = 'SELECT pwd FROM '.$_SYS['table']['user'].' WHERE id = '.$_SYS['user']['id'];
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
      $row = $result->fetch_assoc();
      $_SYS['user']['pwd'] = $row['pwd'];
      $_SYS['util']->setcookie($_SYS['user']['id'], $_SYS['user']['pwd']);
    }

    /* success -> return to profile form */

    header('Location: '.$_SYS['page'][$_SYS['request']['page']]['url'].'?success=1');
  } // _postRequest()


  function _getRequest() {
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

    /* fetch user data */

    $query = 'SELECT *
              FROM   '.$_SYS['table']['user'].'
              WHERE  id = '.$_SYS['user']['id'];
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $_SYS['html']->fehler('1', 'User does not exist.');
    }

    $row = $result->fetch_assoc();

    /* display success message if profile was updated */

    $output = '';

    if (array_key_exists('success', $_GET)) {
      $output .= '
<p>Profile update successful.</p>';
    }

    /* display user info form */

    $output .= '
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<dl>
  <dt>'.$_SYS['html']->label('fnick', 'Nick').'</dt>
  <dd>'.$_SYS['html']->textfield('nick', $row['nick'], 0, 20, '', 'id="fnick"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpass', 'New Pass').'</dt>
  <dd>'.$_SYS['html']->password('pass', '', 0, 0, '', 'id="fpass"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpassconfirm', 'Confirm Pass').'</dt>
  <dd>'.$_SYS['html']->password('passconfirm', '', 0, 0, '', 'id="fpassconfirm"').'</dd>
</dl>
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
  <dd>
    '.$_SYS['html']->checkbox('show_ip', '1', $row['show_ip'], 'fshowip').'
    (Current IP: '.$_SYS['user']['ip'].')
  </dd>
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
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  function getHTML() {
    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return $this->_postRequest();
      break;
    default:
      return $this->_getRequest();
    }
  } // getHTML()

} // Page

?>