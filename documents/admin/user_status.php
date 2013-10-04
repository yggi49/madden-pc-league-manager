<?php
/**
 * @(#) admin/user_status.php
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

    $id = intval($_GET['id']);

    /* check for own id -> prevent lockout */

    if ($id == $_SYS['user']['id']) {
      return $_SYS['html']->fehler('1', 'You cannot change your own status.');
    }

    /* change status */

    $query = 'UPDATE '.$_SYS['table']['user'].'
              SET    status = IF(status = "Active", "Disabled", "Active")
              WHERE  id = '.$id;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* success -- return to user list */

    header('Location: '.$_SYS['page']['coaches']['url']);
  } // getHTML()

} // Page

?>