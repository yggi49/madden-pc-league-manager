<?php
/**
 * @(#) teams.php
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

    /* query teams */

    $query = 'SELECT   t.id                                  AS id,
                       CONCAT(n.team, " ", n.nick)           AS team,
                       n.acro                                AS acro,
                       IFNULL(u.id, 0)                       AS uid,
                       u.nick                                AS nick,
                       t.conference                          AS conference,
                       t.division                            AS division,
                       IFNULL(CONCAT(n.team, n.nick), "ZZZ") AS team_order
              FROM     '.$_SYS['table']['team'].' AS t
                       LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON t.user = u.id
              WHERE    season = '.$_SYS['request']['season'].'
              ORDER BY conference, division, team_order, nick';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* fetch results and order */

    $_teams = array();
    $i = -1;

    while ($row = $result->fetch_assoc()) {
      if ($row['conference'] != $_conf || $row['division'] != $_div) {
        $_conf = $row['conference'];
        $_div = $row['division'];
        ++$i;
      }

      $_teams[$i][] = $row;
    }

    $teams = array();
    $j = count($_teams);

    for ($i = 0; $i < $j; ++$i) {
      $x = $i % 2 ? ($i - 1) / 2 + $j / 2 : $i / 2;

      // FIX ME!
      $x = $i;

      foreach ($_teams[$x] as $_team) {
        $teams[] = $_team;
      }
    }

    unset($i, $j, $x, $_conf, $_div, $_teams, $_team);

    /* print table */

    $i = 0;

    foreach ($teams as $row) {
      if ($row['division'] != $_div || $row['conference'] != $_conf) {
        if (isset($_div) && isset($_conf)) {
          $output .= '
  </tbody>
</table>';

          if ($i++ % 2) {
            $output .= '
<br class="float" />';
          }
        }

        $output .= '
<table class="teams float">
  <thead>
    <tr>
      <th colspan="2" scope="colgroup">'.$row['conference'].' '.$row['division'].'</th>
    </tr>
  </thead>
  <tbody>';
      }

      $output .= '
    <tr>
      <th scope="row">'.($row['team'] != '&nbsp;' ? ($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['acro']).'.gif" alt="'.$row['acro'].'" class="logo" /> ' : '').'<a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['id'].'">'.$row['team'].'</a>' : '&nbsp;').'</th>
      <td>'.(strlen($row['nick']) > 0 ? ($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['nick']).'</a>' : $_SYS['html']->specialchars($row['nick'])) : '&nbsp;').'</td>
    </tr>';

      $_div = $row['division'];
      $_conf = $row['conference'];
    }

    $output .= '
  </tbody>
</table>
<br class="float" />';

    return $output;
  } // getHTML()

} // Page

?>