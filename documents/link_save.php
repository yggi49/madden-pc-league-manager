<?php
/**
 * @(#) link_save.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    /* error if this is not a POST request */

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      return $_SYS['html']->fehler('1', 'Bad request.');
    }

    /* determine link id */

    $id = intval($_POST['id']);

    /* assemble query */

    $query = ($id == 0 ? 'INSERT ' : 'UPDATE ').$_SYS['table']['link'].' ';

    $query .= 'SET title       = '.$_SYS['dbh']->escape_string(trim($_POST['title'])).',
                   description = '.$_SYS['dbh']->escape_string(trim($_POST['description'])).',
                   href        = '.$_SYS['dbh']->escape_string(trim($_POST['href'])).',
                   status      = '.intval($_POST['status']).',
                   uid         = '.$_SYS['user']['id'];

    if ($id != 0) {
      $query .= ' WHERE id = '.$id;
    }

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['links']['url']);
    exit;

    return '';
  } // getHTML()

} // Page

?>