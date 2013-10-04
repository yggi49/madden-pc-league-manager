<?php
/**
 * @(#) admin/season_list.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    $output = '';

    $output .= '
  <link rel="start" href="'.$_SYS['page']['admin/index']['url'].'" />';   # "up" -- NEEDS IMPROVEMENT

    return $output;
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '
<h1>Season Management</h1>
<p><a href="'.$_SYS['page']['admin/season_new']['url'].'">Create new season</a></p>';

    /* query seasons */

    $query = 'SELECT   s.id                                                  AS `id`,
                       s.name                                                AS `name`,
                       s.pre_weeks                                           AS `pre_weeks`,
                       s.reg_weeks                                           AS `reg_weeks`,
                       s.post_weeks                                          AS `post_weeks`,
                       s.post_teams                                          AS `post_teams`,
                       s.start                                               AS `start`,
                       COUNT(t.id)                                           AS `teams`,
                       COUNT(DISTINCT t.conference)                          AS `conferences`,
                       COUNT(DISTINCT CONCAT(t.conference, " ", t.division)) AS `divisions`,
                       (SELECT SUM(g.site != 0)
                        FROM   '.$_SYS['table']['game'].' AS g
                        WHERE  s.id = g.season AND g.week != 0)                              AS `games`
              FROM     '.$_SYS['table']['season'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.id = t.season
              GROUP BY t.season
              ORDER BY s.start';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $output .= '
<table>
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Season</th>
      <th scope="col">Teams</th>
      <th scope="col">Playoff</th>
      <th scope="col">Conf</th>
      <th scope="col">Div</th>
      <th scope="col">Start</th>
      <th scope="col">Pre</th>
      <th scope="col">Reg</th>
      <th scope="col">Post</th>
      <th scope="col">End</th>
      <th scope="col">Roster</th>
    </tr>
  </thead>
  <tbody>';

    while ($row = $result->fetch_assoc()) {
      $output .= '
    <tr>
      <td>'.$row['id'].'</td>
      <td><a href="'.$_SYS['page']['admin/season_edit']['url'].'?id='.$row['id'].'">'.$_SYS['html']->specialchars($row['name']).'</a></td>
      <td><a href="'.$_SYS['page']['admin/season_teams']['url'].'?id='.$row['id'].'">'.$row['teams'].'</a></td>
      <td>'.$row['post_teams'].'</td>
      <td>'.$row['conferences'].'</td>
      <td>'.$row['divisions'].'</td>
      <td>'.date('D, M j Y, H:i:s', $_SYS['season'][$row['id']]['start']).'</td>
      <td>'.$row['pre_weeks'].'</td>
      <td>'.($row['games'] == 0 ? '<a href="'.$_SYS['page']['admin/season_schedule']['url'].'?id='.$row['id'].'">'.$row['reg_weeks'].'</a>' : $row['reg_weeks']).'</td>
      <td>'.$row['post_weeks'].'</td>
      <td>'.date('D, M j Y, H:i:s', $_SYS['season'][$row['id']]['end']).'</td>
      <td><a href="'.$_SYS['page']['admin/season_roster']['url'].'?id='.$row['id'].'">Upload</a></td>
    </tr>';
    }

    $output .= '
  </tbody>
</table>';

    return $output;
  } // getHTML()

} // Page

?>