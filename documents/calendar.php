<?php
/**
 * @(#) calendar.php
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

    $query = 'SELECT   g.away        AS away,
                       na.team       AS away_team,
                       na.nick       AS away_nick,
                       na.acro       AS away_acro,
                       ta.user       AS away_hc,
                       g.away_sub    AS away_sub,
                       ta.conference AS away_conference,
                       ta.division   AS away_division,
                       g.home        AS home,
                       nh.team       AS home_team,
                       nh.nick       AS home_nick,
                       nh.acro       AS home_acro,
                       th.user       AS home_hc,
                       g.home_sub    AS home_sub,
                       th.conference AS home_conference,
                       th.division   AS home_division,
                       g.site        AS site,
                       g.week        AS week,
                       g.season      AS season,
                       s.name        AS season_name,
                       DATE_FORMAT(g.scheduled, "%b %e, %Y") AS scheduled_date,
                       DATE_FORMAT(g.scheduled, "%H:%i") AS scheduled_time
              FROM     '.$_SYS['table']['game'].' AS g
                       LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON g.away = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'    AS na ON ta.team = na.id
                       LEFT JOIN '.$_SYS['table']['team'].'   AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'    AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['season'].' AS s  ON g.season = s.id
              WHERE    g.site = 0
                       AND g.scheduled != "0000-00-00 00:00:00"
              ORDER BY g.scheduled';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if (!$result->rows()) {
      $output .= '
<p>No games scheduled.</p>';
    } else {
      $output .= '
<div class="calendar">';

      while ($row = $result->fetch_assoc()) {
        if ($_prev_date != $row['scheduled_date']) {
          if (isset($_prev_date)) {
            $output .= '
</ul>';
          }

          $output .= '
<h1>'.$row['scheduled_date'].'</h1>
<ul>';
        }

        if ($row['week'] == 0) {
          $week = 'Exhibition';
        } elseif ($row['week'] < 0) {
          $week = 'Preseason Week '.(-$row['week']);
        } elseif ($row['week'] > $_SYS['season'][$row['season']]['reg_weeks']) {
          $week = $_SYS['season'][$row['season']]['post_names'][$row['week'] - $_SYS['season'][$row['season']]['reg_weeks'] - 1]['name'];
        } else {
          $week = 'Week '.$row['week'];
        }

        $output .= '
  <li>
    '.$row['scheduled_time'].' |
    <a href="'.$_SYS['page']['schedule']['url'].'?season='.$row['season'].'&amp;week='.$row['week'].'">'.$row['season_name'].' / '.$week.'</a>:
    '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['away_acro']).'.gif" alt="'.$row['away_acro'].'" class="logo" /> ' : '').'
    <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['away'].'">'.$row['away_nick'].'</a>
    @
    '.($_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row['home_acro']).'.gif" alt="'.$row['home_acro'].'" class="logo" /> ' : '').'
    <a href="'.$_SYS['page']['team/home']['url'].'?id='.$row['home'].'">'.$row['home_nick'].'</a>
  </li>';

        $_prev_date = $row['scheduled_date'];
      }

      $output .= '
</ul>
</div>';
    }

    return $output;
  } // getHTML()

} // Page

?>