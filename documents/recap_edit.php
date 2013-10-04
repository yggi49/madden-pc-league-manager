<?php
/**
 * @(#) recap_edit.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _getQuery($game) {
    global $_SYS;

    return 'SELECT IF(na.team = "New York", CONCAT("NY ", na.nick), na.team) AS away,
                   IF(nh.team = "New York", CONCAT("NY ", nh.nick), nh.team) AS home,
                   IF(g.home_sub != 0, g.home_sub, g.home_hc)                AS home_hc,
                   IF(g.away_sub != 0, g.away_sub, g.away_hc)                AS away_hc,
                   IFNULL(cr.id, 0)                                          AS recap_id,
                   cr.comment                                                AS recap,
                   IFNULL(cc.id, 0)                                          AS coach_id,
                   cc.comment                                                AS coach,
                   IFNULL(cs.id, 0)                                          AS scout_id,
                   cs.comment                                                AS scout
            FROM   '.$_SYS['table']['game'].' AS g
                   LEFT JOIN '.$_SYS['table']['team'].'    AS th ON g.home = th.id
                   LEFT JOIN '.$_SYS['table']['nfl'].'     AS nh ON th.team = nh.id
                   LEFT JOIN '.$_SYS['table']['team'].'    AS ta ON g.away = ta.id
                   LEFT JOIN '.$_SYS['table']['nfl'].'     AS na ON ta.team = na.id
                   LEFT JOIN '.$_SYS['table']['comment'].' AS cr ON g.id = cr.game AND cr.type = "Recap" AND cr.user = '.$_SYS['user']['id'].'
                   LEFT JOIN '.$_SYS['table']['comment'].' AS cc ON g.id = cc.game AND cc.type = "Coach" AND cc.user = '.$_SYS['user']['id'].'
                   LEFT JOIN '.$_SYS['table']['comment'].' AS cs ON g.id = cs.game AND cs.type = "Scout" AND cs.user = '.$_SYS['user']['id'].'
            WHERE  g.id = '.intval($game).'
                   AND g.site != 0';
  } // _getQuery(game)


  function _postRequest() {
    global $_SYS;

    $game = intval($_POST['game']);

    /* fetch game */

    $query  = $this->_getQuery($game);
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $_SYS['html']->fehler('1', 'You are not allowed to do this.');
    }

    $row = $result->fetch_assoc();

    if ($row['home_hc'] != $_SYS['user']['id'] && $row['away_hc'] != $_SYS['user']['id']) {
      return $_SYS['html']->fehler('2', 'You are really not allowed to do this.');
    }

    /* save recaps */

    foreach (array('recap', 'coach', 'scout') as $_type) {
      $_text = trim($_POST[$_type]);

      if (strlen($_text) == 0) {
        if ($row[$_type.'_id'] > 0) {
          $query = 'DELETE FROM '.$_SYS['table']['comment'].' WHERE id = '.$row[$_type.'_id'];
        } else {
          continue;
        }
      } else {
        $query = $row[$_type.'_id'] > 0 ? 'UPDATE ' : 'INSERT ';
        $query .= $_SYS['table']['comment'].'
                  SET `game` = '.$game.',
                      `user` = '.$_SYS['user']['id'].',
                      `type` = "'.ucfirst($_type).'",
                      `date` = NOW(),
                      `comment` = '.$_SYS['dbh']->escape_string($_text);
        $query .= $row[$_type.'_id'] > 0 ? ' WHERE id = '.$row[$_type.'_id'] : '';
      }

      $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());
    }

    /* return */

    header('Location: '.$_SYS['page']['recaps']['url'].'?game='.$game);
    exit;
  } // _postRequest()


  function _getRequest() {
    global $_SYS;

    $game = intval($_GET['game']);

    /* fetch game */

    $query  = $this->_getQuery($game);
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $_SYS['html']->fehler('1', 'You are not allowed to do this.');
    }

    $row = $result->fetch_assoc();

    if ($row['home_hc'] != $_SYS['user']['id'] && $row['away_hc'] != $_SYS['user']['id']) {
      return $_SYS['html']->fehler('2', 'You are really not allowed to do this.');
    }

    /* display form */

    $output .= '
<p><a href="'.$_SYS['page']['recaps']['url'].'?game='.$game.'">Back to Recap: '.$row['away'].' @ '.$row['home'].'</a></p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<dl>
  <dt>Allowed BB-Code</dt>
  <dd>[b]bold[/b], [i]italics[/i], [url=http://www.xyz.com]hyperlink[/url]</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('frecap', 'Recap').'</dt>
  <dd>
    '.$_SYS['html']->textarea('recap', $row['recap'], 10, 50, '', 'id="frecap" tabindex="10"', 4).'
  </dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('fcoach', "Coach's Corner").'</dt>
  <dd>
    '.$_SYS['html']->textarea('coach', $row['coach'], 10, 50, '', 'id="fcoach" tabindex="20"', 4).'
  </dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('fscout', 'Scout Report').'</dt>
  <dd>
    '.$_SYS['html']->textarea('scout', $row['scout'], 10, 50, '', 'id="fscout" tabindex="30"', 4).'
  </dd>
</dl>

<dl>
  <dt>
    '.$_SYS['html']->hidden('game', $game).'
  </dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Save').'
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