<?php
/**
 * @(#) pickem/standings.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '
<p>Pickem Standings</p>';

    return $output;
  } // getHTML()

} // Page

?>