<?php
/**
 * @(#) team/roster.php
 */

class Page {


  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '
  <script type="text/javascript" src="'.$_SYS['dir']['hostdir'].'/scripts/depth.js"></script>';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    $id = intval($_GET['id']);

    /* read team info */

    $query = 'SELECT u.id                        AS uid,
                     u.nick                      AS user,
                     CONCAT(n.team, " ", n.nick) AS team,
                     t.season                    AS season
              FROM   '.$_SYS['table']['team'].' AS t
                     LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
                     LEFT JOIN '.$_SYS['table']['user'].' AS u ON t.user = u.id
              WHERE  t.id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Team does not exist.');
    }

    $row = $result->fetch_assoc();

    $_SYS['request']['season'] = $row['season'];

    $output .= '
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$id.'">Home</a>
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$id.'">News</a>
  &middot; [ Roster ]
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$id.'">Schedule</a>
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$id.'">Stats</a>
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$id.'">Scouts</a>
</p>';

    /* read roster */

    $query = 'SELECT   firstname, lastname,
                       CONCAT(firstname, " ", lastname) AS name,
                       pos, ovr, yrl,
                       IF(tot > 1000, CONCAT("$", ROUND(tot/1000, 2), "M"), CONCAT("$", tot, "K")) AS tot,
                       IF(bon > 1000, CONCAT("$", ROUND(bon/1000, 2), "M"), CONCAT("$", bon, "K")) AS bon,
                       IF(sal > 1000, CONCAT("$", ROUND(sal/1000, 2), "M"), CONCAT("$", sal, "K")) AS sal,
                       age, spd, str, awr, agi, acc, cth, car, jmp, btk, tak, thp, tha, pbk, rbk, kpw, kac, kr, imp, sta, inj, tgh
              FROM     '.$_SYS['table']['roster'].'
              WHERE    team = '.$id.'
              ORDER BY pos ASC, ovr DESC, name ASC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      $output .= '
<p>No data available.</p>';
      return $output;
    }

    $info = $result->info();

    /* print graphical roster */

    $output .= '
<h1>Depth Chart</h1>
<div class="roster">';

    $_prev = null;
    $_count = 0;
    $_cache = array();
    $_meta = array('WR' => array('class' => array('wr1', 'wr2'),
                                 'disp'  => array('WR 1', 'WR 2')),
                   'DT' => array('class' => array('rdt', 'ldt'),
                                 'disp'  => array('DT', 'DT')),
                   'CB' => array('class' => array('cb1', 'cb2'),
                                 'disp'  => array('CB 1', 'CB 2')));
    $_info = array('QB'   => array('THA', 'THP', 'AWR', 'AGI', 'ACC'),
                   'HB'   => array('AWR', 'SPD', 'ACC', 'AGI', 'CAR'),
                   'FB'   => array('AWR', 'SPD', 'ACC', 'AGI', 'CAR'),
                   'WR'   => array('SPD', 'ACC', 'CTH', 'AGI', 'AWR'),
                   'TE'   => array('RBK', 'CTH', 'STR', 'AWR', 'BTK'),
                   'LT'   => array('RBK', 'PBK', 'STR', 'AWR', 'AGI'),
                   'LG'   => array('RBK', 'PBK', 'STR', 'AWR', 'AGI'),
                   'C'    => array('RBK', 'PBK', 'STR', 'AWR', 'AGI'),
                   'RG'   => array('RBK', 'PBK', 'STR', 'AWR', 'AGI'),
                   'RT'   => array('RBK', 'PBK', 'STR', 'AWR', 'AGI'),
                   'LE'   => array('STR', 'TAK', 'AWR', 'AGI', 'ACC'),
                   'DT'   => array('STR', 'TAK', 'AWR', 'AGI', 'ACC'),
                   'RE'   => array('STR', 'TAK', 'AWR', 'AGI', 'ACC'),
                   'LOLB' => array('TAK', 'STR', 'AWR', 'ACC', 'SPD'),
                   'MLB'  => array('TAK', 'STR', 'AWR', 'ACC', 'SPD'),
                   'ROLB' => array('TAK', 'STR', 'AWR', 'ACC', 'SPD'),
                   'CB'   => array('ACC', 'SPD', 'CTH', 'TAK', 'AWR'),
                   'FS'   => array('ACC', 'SPD', 'CTH', 'TAK', 'AWR'),
                   'SS'   => array('ACC', 'SPD', 'CTH', 'TAK', 'AWR'),
                   'K'    => array('KPW', 'KAC', 'AWR', 'STR', 'AGI'),
                   'P'    => array('KPW', 'KAC', 'AWR', 'STR', 'AGI'));

    while ($row = $result->fetch_assoc()) {
      if (array_key_exists($row['pos'], $_meta)) {
        if ($row['pos'] !== $_prev) {
          $_cache = array();

          $output .= '
    </ol>
  </div>';
        }

        $_item = '
      <li title="'.$row['pos'].' '.$row['firstname'].' '.$row['lastname'].' ('.$row['ovr'].')" onclick="showhide(this);">
        '.$row['lastname'].' ('.$row['ovr'].')
        <div class="info">
          <p>'.$row['pos'].' '.$row['firstname'].' '.$row['lastname'].' ('.$row['ovr'].')</p>';

        if (file_exists('images/players/'.$row['firstname'].' '.$row['lastname'].'.jpg')) {
          $_item .= '
          <img src="'.$_SYS['dir']['hostdir'].'/images/players/'.rawurlencode($row['firstname'].' '.$row['lastname']).'.jpg" alt="'.$row['firstname'].' '.$row['lastname'].'" />';
        }

        $_item .= '
          <ul>';

        foreach ($_info[$row['pos']] as $_col) {
          $_item .= '
            <li>'.$_col.': '.$row[strtolower($_col)].'</li>';
        }

        $_item .= '
          </ul>
        </div>
      </li>';

        $_cache[] = $_item;

        $_prev = $row['pos'];

        continue;
      }

      if ($row['pos'] === $_prev && $_count == 2) {
        continue;
      }

      if ($row['pos'] !== $_prev || $_count == 2) {
        if (array_key_exists($_prev, $_meta)) {
          for ($i = 0; $i < 2; ++$i) {
            $output .= '
  <div class="'.$_meta[$_prev]['class'][$i].'">
    <p>'.$_prev.'</p>
    <ol>';

            for ($j = $i; $j < count($_cache) && $j < 4; $j += 2) {
              $output .= $_cache[$j];
            }

            $output .= '
    </ol>
  </div>';
          }
        } elseif (!is_null($_prev)) {
          $output .= '
    </ol>
  </div>';
        }

        $output .= '
  <div class="'.strtolower($row['pos']).'">
    <p>'.$row['pos'].'</p>
    <ol>';

        $_count = 0;
      }

      $output .= '
      <li title="'.$row['pos'].' '.$row['firstname'].' '.$row['lastname'].' ('.$row['ovr'].')" onclick="showhide(this);">
        '.$row['lastname'].' ('.$row['ovr'].')
        <div class="info">
          <p>'.$row['pos'].' '.$row['firstname'].' '.$row['lastname'].' ('.$row['ovr'].')</p>';

      if (file_exists('images/players/'.$row['firstname'].' '.$row['lastname'].'.jpg')) {
        $output .= '
          <img src="'.$_SYS['dir']['hostdir'].'/images/players/'.rawurlencode($row['firstname'].' '.$row['lastname']).'.jpg" alt="'.$row['firstname'].' '.$row['lastname'].'" />';
      }

      $output .= '
          <ul>';

      foreach ($_info[$row['pos']] as $_col) {
        $output .= '
            <li>'.$_col.': '.$row[strtolower($_col)].'</li>';
      }

      $output .= '
          </ul>
        </div>
      </li>';

      ++$_count;
      $_prev = $row['pos'];
    }

    unset($_prev, $_count, $_cache, $_meta, $i, $j);

    $output .= '
    </ol>
  </div>
</div>';

    /* print table */

    $output .= '
<h1>Roster</h1>
<table class="roster">
  <thead>
    <tr>';

    foreach ($info['name'] as $_info) {
      if (in_array($_info, array('firstname', 'lastname'))) {
        continue;
      }

      $output .= '
      <th scope="col"'.(in_array($_info, array('name', 'pos', 'ovr', 'yrl', 'tot', 'bon', 'sal')) ? ' rowspan="2"' : '').'>'.strtoupper($_info).'</th>';

      if ($_info == 'tak') {
        $output .= '
    </tr>
    <tr>';
      }
    }

    $output .= '
    </tr>
  </thead>';

    $result->reset();

    while ($row = $result->fetch_assoc()) {
      $output .= '
  <tbody>
    <tr>';

      foreach ($info['name'] as $_info) {
        if (in_array($_info, array('firstname', 'lastname'))) {
          continue;
        } elseif ($_info == 'name') {
          $output .= '
      <th scope="rowgroup" rowspan="2">'.$row[$_info].'</th>';
        } else {
          $output .= '
      <td'.(in_array($_info, array('pos', 'ovr', 'yrl', 'tot', 'bon', 'sal')) ? ' rowspan="2"' : '').'>'.$row[$_info].'</td>';
        }

        if ($_info == 'tak') {
          $output .= '
    </tr>
    <tr>';
        }
      }

      $output .= '
    </tr>
  </tbody>';
    }

    unset($_info);

    $output .= '
</table>';

    return $output;
  } // getHTML()

} // Page

?>