<?php
/**
 * @(#) link_status.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $query = 'UPDATE '.$_SYS['table']['link'].'
              SET    status = 1 - status
              WHERE  id = '.intval($_GET['id']);
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['links']['url']);
    exit;

    return '';
  } // getHTML()

} // Page

?>