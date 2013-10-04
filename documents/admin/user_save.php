<?php
/**
 * @(#) admin/user_save.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    /* error if this is not a POST request */

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      return $_SYS['html']->fehler('1', 'Bad request.');
    }

    /* determine user id */

    $id = intval($_POST['id']);

    /* check for valid user name */

    $_POST['nick'] = trim($_POST['nick']);

    if (strlen($_POST['nick']) == 0) {
      return $_SYS['html']->fehler('2', 'Bad nick.');
    }

    if ($id != 0) {
      $query = 'SELECT * FROM '.$_SYS['table']['user'].' WHERE nick = '.$_SYS['dbh']->escape_string($_POST['nick']).' AND id != '.$id;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() == 1) {
        return $_SYS['html']->fehler('2', 'Nick exists already.');
      }
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

    /* check for valid status */

    if ($id != $_SYS['user']['id'] && $_POST['status'] == '') {
      return $_SYS['html']->fehler('6', 'Please assign a status.');
    }

    /* assemble query */

    $query = ' SET nick       = '.$_SYS['dbh']->escape_string($_POST['nick']).',';

    if (strlen($_POST['pass']) > 0) {
      $query .= '
                   pwd        = SHA1("'.$_POST['pass'].'"),';
    }

    if ($id != $_SYS['user']['id']) {
      $query .= '
                   status     = '.$_SYS['dbh']->escape_string($_POST['status']).',
                   admin      = '.intval($_POST['admin']).',';
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
                   usertext   = '.$_SYS['dbh']->escape_string($_POST['usertext']);

    $query = $id != 0
             ? 'UPDATE '.$_SYS['table']['user'].$query.' WHERE id ='.$id
             : 'INSERT '.$_SYS['table']['user'].$query;

    /* perform insert/update */

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* update cookie if password was changed */

    if ($id == $_SYS['user']['id'] && strlen($_POST['pass']) > 0) {
      $query = 'SELECT pwd FROM '.$_SYS['table']['user'].' WHERE id = '.$_SYS['user']['id'];
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
      $row = $result->fetch_assoc();
      $_SYS['user']['pwd'] = $row['pwd'];
      $_SYS['util']->setcookie($_SYS['user']['id'], $_SYS['user']['pwd']);
    }

    /* success -- return to user list */

    header('Location: '.$_SYS['page']['coaches']['url']);
  } // getHTML()

} // Page