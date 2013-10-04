<?php
/**
 * @(#) sorry.php
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
<p>Sorry -- there are no seasons yet.</p>';

    return $output;
  } // getHTML()

} // Page

?>