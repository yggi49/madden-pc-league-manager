<?php
/**
 * @(#) comment.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _postRequest() {
    global $_SYS;

    $id = intval($_POST['id']);
    $game = intval($_POST['game']);

    /* check request */

    if ($id > 0) {
      $query = 'SELECT game
                FROM   '.$_SYS['table']['comment'].'
                WHERE  id = '.$id.'
                       AND type = "Comment"
                       AND user = '.$_SYS['user']['id'];
    } else {
      $query = 'SELECT id AS game
                FROM   '.$_SYS['table']['game'].'
                WHERE  id = '.$game.'
                       AND site != 0';
    }

    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $id > 0 ? $_SYS['html']->fehler('1', 'Bad request.') : $_SYS['html']->fehler('2', 'You cannot post to this game any longer.');
    }

    $row = $result->fetch_assoc();

    /* save/delete comment */

    if ($id > 0) {
      if ($_POST['submit'] == 'Delete') {
        $query = 'DELETE FROM '.$_SYS['table']['comment'].' WHERE id = '.$id;
      } else {
        $query = 'UPDATE '.$_SYS['table']['comment'].'
                  SET    `comment` = '.$_SYS['dbh']->escape_string(trim($_POST['comment'])).',
                         `date`    = NOW()
                  WHERE  id = '.$id;
      }
    } else {
      $query = 'INSERT '.$_SYS['table']['comment'].'
                SET    `game`    = '.$row['game'].',
                       `user`    = '.$_SYS['user']['id'].',
                       `type`    = "Comment",
                       `date`    = NOW(),
                       `comment` = '.$_SYS['dbh']->escape_string(trim($_POST['comment']));
    }

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* return */

    header('Location: '.$_SYS['page']['recaps']['url'].'?game='.$row['game']);
    exit;
  } // _postRequest()


  function _getRequest() {
    global $_SYS;

    $id = intval($_GET['id']);
    $game = intval($_GET['game']);

    /* fetch comment if id is given; check game otherwise */

    if ($id > 0) {
      $query = 'SELECT c.comment                                                  AS comment,
                       c.game                                                     AS game,
                       IF(na.team = "New York", CONCAT("NY ", na.nick), na.team)  AS away,
                       IF(nh.team = "New York", CONCAT("NY ", nh.nick), nh.team)  AS home
                FROM   '.$_SYS['table']['comment'].' AS c
                       LEFT JOIN '.$_SYS['table']['game'].' AS g  ON c.game = g.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON ta.team = na.id
                WHERE  c.id = '.$id.'
                       AND c.type = "Comment"
                       AND c.user = '.$_SYS['user']['id'];
    } else {
      $query = 'SELECT g.id                                                       AS game,
                       IF(na.team = "New York", CONCAT("NY ", na.nick), na.team)  AS away,
                       IF(nh.team = "New York", CONCAT("NY ", nh.nick), nh.team)  AS home
                FROM   '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON ta.team = na.id
                WHERE  g.id = '.$game.'
                       AND g.site != 0';
    }

    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $_SYS['html']->fehler('1', 'Bad request');
    }

    $row = $result->fetch_assoc();

    /* display form */

    $output .= '
<p><a href="'.$_SYS['page']['recaps']['url'].'?game='.$row['game'].'">Back to Recap: '.$row['away'].' @ '.$row['home'].'</a></p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<dl>
  <dt>Allowed BB-Code</dt>
  <dd>[b]bold[/b], [i]italics[/i], [url=http://www.xyz.com]hyperlink[/url]</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('fcomment', 'Comment').'</dt>
  <dd>
    '.$_SYS['html']->textarea('comment', $row['comment'], 10, 50, '', 'id="fcomment" tabindex="10"', 4).'
  </dd>
</dl>

<dl>
  <dt>
    '.$_SYS['html']->hidden('id', $id).'
    '.$_SYS['html']->hidden('game', $game).'
  </dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Save').'
    '.($id > 0 ? $_SYS['html']->submit('submit', 'Delete') : '').'
  </dd>
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