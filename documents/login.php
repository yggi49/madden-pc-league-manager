<?php
/**
 * @(#) login.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _getRequest() {

    global $_SYS;

    /* log out if cookie is set */

    if ($_SYS['user']['id'] != 0) {
      $_SYS['util']->delcookie();
      header('Location: '.$_SYS['page'][$_SYS['request']['page']]['url']);
    }

    /* login form */

    $output = '
<h1>Login</h1>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<dl>
  <dt>'.$_SYS['html']->label('fnick', 'Nick').'</dt>
  <dd>'.$_SYS['html']->textfield('nick', '', 0, 0, '', 'id="fnick"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpass', 'Pass').'</dt>
  <dd>'.$_SYS['html']->password('pass', '', 0, 0, '', 'id="fpass"').'</dd>
</dl>
<dl>
  <dd>'.$_SYS['html']->submit('submit', 'Login').'</dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  function _postRequest() {

    global $_SYS;

    $query = 'SELECT id, pwd
              FROM   '.$_SYS['table']['user'].'
              WHERE  nick = "'.$_POST['nick'].'"
                     AND pwd = SHA1("'.$_POST['pass'].'")
                     AND status = "Active"';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* login successful */

    if ($result->rows() == 1) {
      $row = $result->fetch_assoc();
      $_SYS['util']->setcookie(intval($row['id']), $row['pwd']);
      header('Location: '.($_SYS['dir']['hostdir'] ? $_SYS['dir']['hostdir'] : '/'));
    }

    /* login not successful */

    return $_SYS['html']->fehler('1', 'Login failed.', $_SYS['page'][$_SYS['request']['page']]['url']);
  } // _postRequest()


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