<?php
/**
 * @(#) team/home.php
 */

class Page {


  function Page() {} // constructor


  function getHeader() {
    return '';
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
<div class="teamnav">
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  [ Home ]
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$id.'">News</a>
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$id.'">Roster</a>
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$id.'">Schedule</a>
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$id.'">Stats</a>
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$id.'">Scouts</a>
</p>
</div>';

    /* get last 5 games; but at least all from within the last 24 hours */

    $latest_games = array();

    $_where = '
                          (home = '.$id.' OR away = '.$id.') AND site != 0 AND
                          (week <= 0';

    foreach (array_keys($_SYS['season']) as $_season) {
      $_where .= '
                           OR (season = '.$_season.' AND week > '.$_SYS['season'][$_season]['reg_weeks'].')
                           OR (season = '.$_season.' AND week IN ('.join(', ', $_SYS['season'][$_season]['visible_weeks']['reg']).'))';
    }

    $_where .= ')';

    $query = 'SELECT
              DISTINCT g.id         AS id,
                       g.season     AS season,
                       g.week       AS week,
                       na.acro      AS away_acro,
                       g.away_score AS away_score,
                       nh.acro      AS home_acro,
                       g.home_score AS home_score,
                       g.inserted   AS inserted
              FROM     (
                         (SELECT   *
                          FROM     '.$_SYS['table']['game'].'
                          WHERE    '.$_where.'
                          ORDER BY inserted DESC
                          LIMIT 5)
                         UNION ALL
                         (SELECT   *
                          FROM     '.$_SYS['table']['game'].'
                          WHERE    '.$_where.'
                                   AND inserted > '.($_SYS['time']['now'] - 86400).')
                       ) AS g
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON ta.team = na.id
              ORDER BY inserted DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output .= '
<div class="latest">
  <h1>Latest Results</h1>';

    if ($result->rows() == 0) {
      $output .= '
  <p>No results available.</p>';
    } else {

      $output .= '
  <ul>';

      while ($row = $result->fetch_assoc()) {
        $latest_games[] = $row;

        $output .= '
    <li>
      <a href="'.$_SYS['page']['schedule']['url'].'?season='.$row['season'].'&amp;week='.$row['week'].'">';

        if ($row['week'] < 0) {
          $output .= 'P'.(-$row['week']);
        } elseif ($row['week'] == 0) {
          $output .= 'EX';
        } elseif ($row['week'] > $_SYS['season'][$row['season']]['reg_weeks']) {
          $output .= $_SYS['season'][$row['season']]['post_names'][$row['week'] - $_SYS['season'][$row['season']]['reg_weeks'] - 1]['acro'];
        } else {
          $output .= 'W'.$row['week'];
        }

        $output .= '</a>:
      <a href="'.$_SYS['page']['boxscore']['url'].'?game='.$row['id'].'">
        '.$row['away_acro'].'
        '.$row['away_score'].',
        '.$row['home_acro'].'
        '.$row['home_score'].'
      </a>
    </li>';
      }

      $output .= '
  </ul>';
    }

    $output .= '
</div>';

    unset($i, $_where, $_season, $_last);

    /* fetch latest 10 news; but at least all from within last 24 hours */

    $query = 'SELECT
              DISTINCT m.`id`       AS `news_id`,
                       m.`title`    AS `title`,
                       m.`news`     AS `news`,
                       m.`date`     AS `date`,
                       m.`team`     AS `team_id`,
                       n.`acro`     AS `team_acro`,
                       n.`team`     AS `team_name`,
                       n.`nick`     AS `team_nick`,
                       u.`nick`     AS `user_nick`
              FROM     (
                         (SELECT   *
                          FROM     '.$_SYS['table']['news'].'
                          WHERE    team = '.$id.'
                          ORDER BY `date` DESC
                          LIMIT    10)
                         UNION ALL
                         (SELECT   *
                          FROM     '.$_SYS['table']['news'].'
                          WHERE    team = '.$id.' AND `date` > '.($_SYS['time']['now'] - 86400).')
                       ) AS m
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON m.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                       LEFT JOIN '.$_SYS['table']['user'].' AS u ON m.user = u.id
              ORDER BY `date` DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* get news and results */

    $output .= '
<div class="news">
  <h1>News</h1>';

    if ($result->rows() == 0) {
      $output .= '
  <div class="message">
    <p>No news available.</p>
  </div>';
    }

    while ($row = $result->fetch_assoc()) {
      /* image available? */

      $image_files = glob($_SYS['dir']['imgdir'].'/news/'.$row['news_id'].'.*');
      if (!$image_files) $image_files = array();
      foreach ($image_files as $_filename) {
        $row['image'] = $_SYS['dir']['hostdir'].'/images/news/'.basename($_filename);
        break;
      }

      unset($_filename);

      $output .= '
  <div class="message">
    <div class="head">
      <p class="date">'.date('M j, Y, g:i A', $row['date']).'</p>
      <h2>
        '.$_SYS['html']->specialchars($row['title']).'
      </h2>
    </div>
    <p>';

      if ($row['image']) {
        $output .= '
      <img src="'.$row['image'].'" alt="" />';
      }

      $output .= '
      <strong>by '.$row['user_nick'].'</strong>
'.$_SYS['html']->bbcode($_SYS['html']->specialchars($row['news'])).'
      '.($row['image'] ? '<br class="float" />' : '').'
    </p>
  </div>';
    }

    $output .= '
</div>';




    return $output;
  } // getHTML()

} // Page

?>