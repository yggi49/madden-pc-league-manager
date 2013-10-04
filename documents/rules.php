<?php
/**
 * @(#) rules.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    $query = 'SELECT *
              FROM   '.$_SYS['table']['comment'].'
              WHERE  type = "Rules"
              LIMIT  1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      $text = 'No rules available ;-)';
      $modified = '&mdash;';
    } else {
      $row = $result->fetch_assoc();
      $text = $_SYS['html']->bbcode($_SYS['html']->specialchars($row['comment']));
      $modified = $row['date'];
    }

    if ($_SYS['page']['rules_edit']['access']) {
      $output .= '
<p><a href="'.$_SYS['page']['rules_edit']['url'].'">Edit Rules</a></p>';
    }

    $output .= '
<p>Last Modified: '.$modified.'</p>
<p class="boxed">
'.$text.'
</p>';

    return $output;
  } // getHTML()

} // Page

?>