<?php
/**
 * @(#) download.php
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
    $filename = $_SYS['dir']['downdir'].'/'.$id;

    $query = 'SELECT * FROM '.$_SYS['table']['download'].' WHERE id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Download not found.');
    }

    if (!file_exists($filename)) {
      return $_SYS['html']->fehler('2', 'File not found.');
    }

    $row = $result->fetch_assoc();

    header('Content-type: application/x-something');
    header('Content-Disposition: attachment; filename="'.$row['filename'].'"');

    readfile($filename);

    exit;

    return '';
  } // getHTML()

} // Page

?>