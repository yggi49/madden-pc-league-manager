<?php
/**
 * @(#) download_save.php
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

    /* determine download id */

    $id = intval($_POST['id']);

    /* check if a file has been uploaded if id = 0 */

    if ($id == 0) {
      if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
        return $_SYS['html']->fehler('2', 'Please select a file to upload.');
      }
    }

    /* show from */

    $showfrom = $_POST['showfrom'];

    if (strlen($showfrom) > 0) {
      if (!preg_match('/^(\d+)-(\d+)-(\d+)\s+(\d+):(\d+)$/', $showfrom, $_temp)) {
        return $_SYS['html']->fehler('2', 'Bad FROM date');
      }

      if (!checkdate($_temp[2], $_temp[3], $_temp[1])) {
        return $_SYS['html']->fehler('3', 'Bad FROM date');
      }

      if ($_temp[4] > 23 || $_temp[5] > 59) {
        return $_SYS['html']->fehler('4', 'Bad FROM time');
      }

      $showfrom = mktime($_temp[4], $_temp[5], 0, $_temp[2], $_temp[3], $_temp[1]);
    } else {
      $showfrom = 0;
    }

    unset($_temp);

    /* show thru */

    $showthru = $_POST['showthru'];

    if (strlen($showthru) > 0) {
      if (!preg_match('/^(\d+)-(\d+)-(\d+)\s+(\d+):(\d+)$/', $showthru, $_temp)) {
        return $_SYS['html']->fehler('2', 'Bad THRU date');
      }

      if (!checkdate($_temp[2], $_temp[3], $_temp[1])) {
        return $_SYS['html']->fehler('3', 'Bad THRU date');
      }

      if ($_temp[4] > 23 || $_temp[5] > 59) {
        return $_SYS['html']->fehler('4', 'Bad THRU time');
      }

      $showthru = mktime($_temp[4], $_temp[5], 0, $_temp[2], $_temp[3], $_temp[1]);
    } else {
      $showthru = 0;
    }

    unset($_temp);

    /* check filename */

    $filename = trim($_POST['filename']);

    if (strlen($filename) == 0) {
      return $_SYS['html']->fehler('5', 'Please provide a filename');
    }

    /* assemble query */

    $query = ($id == 0 ? 'INSERT ' : 'UPDATE ').$_SYS['table']['download'].' ';

    $query .= 'SET title       = '.$_SYS['dbh']->escape_string(trim($_POST['title'])).',
                   description = '.$_SYS['dbh']->escape_string(trim($_POST['description'])).',
                   filename    = '.$_SYS['dbh']->escape_string(trim($filename)).',
                   filetype    = '.$_SYS['dbh']->escape_string(trim($_POST['filetype'])).',
                   compression = '.$_SYS['dbh']->escape_string(trim($_POST['compression'])).',
                   status      = '.intval($_POST['status']).',
                   showfrom    = '.$showfrom.',
                   showthru    = '.$showthru.',
                   modified    = '.$_SYS['time']['now'].',
                   uid         = '.$_SYS['user']['id'];

    if ($id != 0) {
      $query .= ' WHERE id = '.$id;
    }

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $new = $id == 0;

    if ($id == 0) {
      $id = $_SYS['dbh']->insert_id();
    }

    /* save file */

    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
      if (!move_uploaded_file($_FILES['file']['tmp_name'], $_SYS['dir']['downdir'].'/'.$id)) {
        if ($new) {
          $query = 'DELETE FROM '.$_SYS['table']['download'].' WHERE id = '.$id;
          $_SYS['dbh']->query($query);
        }

        return $_SYS['html']->fehler('3', 'File could not be stored.');
      }
    }

    header('Location: '.$_SYS['page']['downloads']['url']);
    exit;

    return '';
  } // getHTML()

} // Page

?>