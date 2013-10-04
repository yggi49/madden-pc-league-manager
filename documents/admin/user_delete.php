<?php
/**
 * @(#) admin/user_delete.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    /* determine user id */

    $id = intval($_GET['id']);

    /* check for valid user id */

    if ($id < 1) {
      return $_SYS['html']->fehler('1', 'Bad user id.');
    }

    /* check if user may be deleted -- NEEDS IMPROVEMENT */

    if (!$_SYS['util']->is_removable($id)) {
      return $_SYS['html']->fehler('2', 'User cannot be deleted.');
    }

    /* delete user */

    $query = 'DELETE
              FROM   '.$_SYS['table']['user'].'
              WHERE  id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* success -- return to user list */

    header('Location: '.$_SYS['page']['coaches']['url']);
  } // getHTML()

} // Page