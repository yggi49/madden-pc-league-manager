<?php
/**
 * @(#) rules_edit.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _postRequest() {
    global $_SYS;

    $query = 'DELETE FROM '.$_SYS['table']['comment'].' WHERE type = "Rules"';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $query = 'INSERT '.$_SYS['table']['comment'].'
              SET    game    = 0,
                     user    = '.$_SYS['user']['id'].',
                     type    = "Rules",
                     date    = NOW(),
                     comment = '.$_SYS['dbh']->escape_string($_POST['rules']);
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['rules']['url']);
    exit;

    return '';
  } // _postRequest()


  function _getRequest() {
    global $_SYS;

    $output = '';

    $query = 'SELECT *
              FROM   '.$_SYS['table']['comment'].'
              WHERE  type = "Rules"
              LIMIT  1';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    $row = $result->fetch_assoc();

    $output .= '
<p><a href="'.$_SYS['page']['rules']['url'].'">Back to Rules</a></p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<dl>
  <dt>Allowed BB-Code</dt>
  <dd>[b]bold[/b], [i]italics[/i], [url=http://www.xyz.com]hyperlink[/url]</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('frules', 'Text').'</dt>
  <dd>'.$_SYS['html']->textarea('rules', $row['comment'], 20, 50, '', 'id="frules"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->hidden('id', intval($row['id'])).'</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    return $output;
  } // _getRequest()


  function getHTML() {
    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return $this->_postRequest();
      break;
    default:
      return $this->_getRequest();
    }
  } // getHTML()


} // Page

?>