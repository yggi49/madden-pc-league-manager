<?php
/**
 * @(#) news_top.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    $query = 'UPDATE '.$_SYS['table']['news'].'
              SET    top = 1 - top
              WHERE  team = 0
                     AND id = '.intval($_GET['id']);
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['news']['url']);
    exit;

    return $output;
  } // getHTML()

} // Page

?>