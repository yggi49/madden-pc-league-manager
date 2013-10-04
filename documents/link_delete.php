<?php
/**
 * @(#) link_delete.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $id = intval($_GET['id']);

    $query = 'DELETE FROM '.$_SYS['table']['link'].' WHERE id = '.$id;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['links']['url']);
    exit;

    return '';
  } // getHTML()

} // Page

?>