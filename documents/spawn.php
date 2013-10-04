<?php
/**
 * @(#) spawn.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $id = intval($_GET['game']);
    $filename = $_SYS['dir']['spawndir'].'/'.$id;

    $query = 'SELECT g.away        AS away,
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
                     s.name        AS season_name
              FROM   '.$_SYS['table']['game'].' AS g
                     LEFT JOIN '.$_SYS['table']['team'].'   AS ta ON g.away = ta.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'    AS na ON ta.team = na.id
                     LEFT JOIN '.$_SYS['table']['team'].'   AS th ON g.home = th.id
                     LEFT JOIN '.$_SYS['table']['nfl'].'    AS nh ON th.team = nh.id
                     LEFT JOIN '.$_SYS['table']['season'].' AS s  ON g.season = s.id
              WHERE  g.id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Download not found.');
    }

    if (!file_exists($filename)) {
      return $_SYS['html']->fehler('2', 'File not found.');
    }

    $row = $result->fetch_assoc();

    if ($row['week'] < 0) {
      $row['week'] = 'P'.(-$row['week']);
    } elseif ($row['week'] == 0) {
      $row['week'] = 'EX';
    } elseif ($row['week'] > $_SYS['season'][$row['season']]['reg_weeks']) {
      $row['week'] = $_SYS['season'][$row['season']]['post_names'][$row['week'] - $_SYS['season'][$row['season']]['reg_weeks'] - 1]['acro'];
    } else {
      $row['week'] = 'W'.$row['week'];
    }

    header('Content-type: application/x-something');
    header('Content-Disposition: attachment; filename="'.$row['week'].'_'.$row['away_nick'].'_'.$row['home_nick'].'.spr"');

    readfile($filename);

    exit;

    return '';
  } // getHTML()

} // Page

?>