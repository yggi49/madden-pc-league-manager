<?php
/**
 * @(#) gamelog.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $query = 'SELECT log AS log
              FROM   '.$_SYS['table']['log'].'
              WHERE  game = '.intval($_GET['game']);
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'There is no log for this game.');
    }

    $row = $result->fetch_assoc();

    $lines = explode("\n", $row['log']);
    preg_match('/^.* - (.*)$/', $lines[0], $matchup);
    preg_match('/^.* ([0-9]{2}):([0-9]{2}):/', $lines[1], $gametime);

    header('Content-Type: text/plain');
    header('Content-Length: '.strlen($row['log']));
    header('Content-Disposition: attachment; filename='.$matchup[1].' - '.$gametime[1].$gametime[2].'.txt');

    echo $row['log'];

    exit;
  } // getHTML()

} // Page

?>