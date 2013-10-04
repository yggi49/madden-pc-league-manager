<?php
/**
 * @(#) download_delete.php
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
    $success = unlink($filename);

    if (!$success) {
      if (file_exists($filename)) {
        return $_SYS['html']->fehler('1', 'Could not delete file.');
      }
    }

    $query = 'DELETE FROM '.$_SYS['table']['download'].' WHERE id = '.$id;
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['downloads']['url']);
    exit;

    return '';
  } // getHTML()

} // Page

?>