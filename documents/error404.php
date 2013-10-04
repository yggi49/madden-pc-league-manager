<?php
/**
 * @(#) error404.php
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
<p>Error 404</p>';

    return $output;
  } // getHTML()

} // Page

?>