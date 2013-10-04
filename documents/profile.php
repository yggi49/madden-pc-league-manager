<?php
/**
 * @(#) profile.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '
  <script type="text/javascript" src="'.$_SYS['dir']['hostdir'].'/scripts/head2head.js"></script>';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $id = intval($_GET['id']);

    /* user details */

    $query = 'SELECT *
              FROM   '.$_SYS['table']['user'].'
              WHERE  id = '.$id;
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() != 1) {
      return $_SYS['html']->fehler('1', 'User does not exist');
    }

    $user = $result->fetch_assoc();

    /* user records */

    $query = 'SELECT   u.id                                                                                               AS uid,
                       u.nick                                                                                             AS nick,
                       g.id                                                                                               AS gid,
                       g.season                                                                                           AS season,
                       g.week                                                                                             AS week,
                       g.site                                                                                             AS site,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), "vs", "@")                  AS homeaway,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), g.home_score, g.away_score) AS my_score,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), g.home_hc, g.away_hc)       AS my_hc,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), g.home_sub, g.away_sub)     AS my_sub,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), nh.acro, na.acro)           AS my_team,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), g.away_score, g.home_score) AS opp_score,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), g.away_hc, g.home_hc)       AS opp_hc,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), g.away_sub, g.home_sub)     AS opp_sub,
                       IF(((g.home_hc = '.$id.' AND g.home_sub = 0) OR g.home_sub = '.$id.'), na.acro, nh.acro)           AS opp_team
              FROM     '.$_SYS['table']['user'].' AS u
                       LEFT JOIN '.$_SYS['table']['game'].' AS g ON g.site != 0 AND ((g.home_hc = '.$id.' AND g.home_sub = 0 AND g.away_hc = u.id AND g.away_sub = 0) OR (g.home_sub = '.$id.' AND g.away_hc = u.id AND g.away_sub = 0) OR (g.home_hc = '.$id.' AND g.home_sub = 0 AND g.away_sub = u.id) OR (g.home_sub = '.$id.' AND g.away_sub = u.id) OR (g.away_hc = '.$id.' AND g.away_sub = 0 AND g.home_hc = u.id AND g.home_sub = 0) OR (g.away_sub = '.$id.' AND g.home_hc = u.id AND g.home_sub = 0) OR (g.away_hc = '.$id.' AND g.away_sub = 0 AND g.home_sub = u.id) OR (g.away_sub = '.$id.' AND g.home_sub = u.id))
                       LEFT JOIN '.$_SYS['table']['team'].' AS th ON g.home = th.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS nh ON th.team = nh.id
                       LEFT JOIN '.$_SYS['table']['team'].' AS ta ON g.away = ta.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS na ON ta.team = na.id
              WHERE    u.id != '.$id.'
              ORDER BY u.nick, g.season DESC, g.week DESC';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $record = array('total' => array('won' => 0, 'lost' => 0, 'tied' => 0),
                    'ex'    => array('won' => 0, 'lost' => 0, 'tied' => 0),
                    'pre'   => array('won' => 0, 'lost' => 0, 'tied' => 0),
                    'reg'   => array('won' => 0, 'lost' => 0, 'tied' => 0),
                    'post'  => array('won' => 0, 'lost' => 0, 'tied' => 0));

    $head2head = array();

    while ($row = $result->fetch_assoc()) {
      if ($row['site']) {
        if (!array_key_exists($row['nick'], $head2head)) {
          $head2head[$row['nick']] = array('uid'   => $row['uid'],
                                           'total' => array('won' => 0, 'lost' => 0, 'tied' => 0, 'games' => array()),
                                           'ex'    => array('won' => 0, 'lost' => 0, 'tied' => 0, 'games' => array()),
                                           'pre'   => array('won' => 0, 'lost' => 0, 'tied' => 0, 'games' => array()),
                                           'reg'   => array('won' => 0, 'lost' => 0, 'tied' => 0, 'games' => array()),
                                           'post'  => array('won' => 0, 'lost' => 0, 'tied' => 0, 'games' => array()));
        }

        $_field = $row['my_score'] > $row['opp_score'] ? 'won' : ($row['my_score'] < $row['opp_score'] ? 'lost' : 'tied');
        $_season = $row['week'] < 0 ? 'pre' : ($row['week'] == 0 ? 'ex' : ($row['week'] <= $_SYS['season'][$row['season']]['reg_weeks'] ? 'reg' : 'post'));

        $record['total'][$_field]++;
        $record[$_season][$_field]++;

        $head2head[$row['nick']]['total'][$_field]++;
        $head2head[$row['nick']][$_season][$_field]++;

        $_linktext = $row['my_team'].($row['my_hc'] == $id ? '' : '*').' '.$row['my_score'].' '.$row['homeaway'].' '.$row['opp_team'].($row['opp_hc'] == $row['uid'] ? '' : '*').' '.$row['opp_score'];

        $head2head[$row['nick']]['total']['games'][] = array('gid'      => $row['gid'],
                                                             'linktext' => $_linktext);
        $head2head[$row['nick']][$_season]['games'][] = array('gid'      => $row['gid'],
                                                              'linktext' => $_linktext);
      }
    }

    unset($_field, $_season, $_linktext);

    /* output */

    $output = '
<h1>'.$user['nick'].'</h1>';

    if ($_SYS['user']['id']) {
      $output .= '
<h1>Profile</h1>
<dl class="profile">
  <dt>Nick</dt>
  <dd>'.$_SYS['html']->specialchars($user['nick']).'</dd>

  <dt>eMail</dt>
  <dd>'.($user['show_email'] && strlen($user['email']) > 0 ? '<a href="mailto:'.$_SYS['html']->specialchars($user['email']).'">'.$_SYS['html']->specialchars($user['email']).'</a>' : '&nbsp;').'</dd>

  <dt>Phone</dt>
  <dd>'.(strlen($user['phone']) > 0 ? $_SYS['html']->specialchars($user['phone']) : '&nbsp;').'</dd>

  <dt>ICQ</dt>
  <dd>'.($user['icq'] ? '<img src="http://web.icq.com/whitepages/online?icq='.$user['icq'].'&amp;img=5" alt="'.$user['icq'].'" /> '.$user['icq'] : '&nbsp;').'</dd>

  <dt>XFire</dt>
  <dd>'.($user['xfire'] ? $user['xfire'] : '&nbsp;').'</dd>

  <dt>Last IP</dt>
  <dd>'.($user['show_ip'] ? $user['ip'] : '&nbsp;').'</dd>
</dl>';
    }

    if ($user['usertext']) {
      $output .= '
<p class="profile">'.$_SYS['html']->bbcode($_SYS['html']->specialchars($user['usertext'])).'</p>';
    }

    $output .= '
<h1>Records</h1>
<dl class="profile">
  <dt>Total</dt>
  <dd>'.$record['total']['won'].'-'.$record['total']['lost'].($record['total']['tied'] ? '-'.$record['total']['tied'] : '').' ('.ltrim(sprintf('%.3f', ($record['total']['won'] + $record['total']['tied'] / 2) / max($record['total']['won'] + $record['total']['lost'] + $record['total']['tied'], 1)), '0').')</dd>

  <dt>Exhibitions</dt>
  <dd>'.$record['ex']['won'].'-'.$record['ex']['lost'].($record['ex']['tied'] ? '-'.$record['ex']['tied'] : '').' ('.ltrim(sprintf('%.3f', ($record['ex']['won'] + $record['ex']['tied'] / 2) / max($record['ex']['won'] + $record['ex']['lost'] + $record['ex']['tied'], 1)), '0').')</dd>

  <dt>Preseason</dt>
  <dd>'.$record['pre']['won'].'-'.$record['pre']['lost'].($record['pre']['tied'] ? '-'.$record['pre']['tied'] : '').' ('.ltrim(sprintf('%.3f', ($record['pre']['won'] + $record['pre']['tied'] / 2) / max($record['pre']['won'] + $record['pre']['lost'] + $record['pre']['tied'], 1)), '0').')</dd>

  <dt>Regular Season</dt>
  <dd>'.$record['reg']['won'].'-'.$record['reg']['lost'].($record['reg']['tied'] ? '-'.$record['reg']['tied'] : '').' ('.ltrim(sprintf('%.3f', ($record['reg']['won'] + $record['reg']['tied'] / 2) / max($record['reg']['won'] + $record['reg']['lost'] + $record['reg']['tied'], 1)), '0').')</dd>

  <dt>Postseason</dt>
  <dd>'.$record['post']['won'].'-'.$record['post']['lost'].($record['post']['tied'] ? '-'.$record['post']['tied'] : '').' ('.ltrim(sprintf('%.3f', ($record['post']['won'] + $record['post']['tied'] / 2) / max($record['post']['won'] + $record['post']['lost'] + $record['post']['tied'], 1)), '0').')</dd>
</dl>';

    $output .= '
<h1>Head-To-Head</h1>
<p>(click a record to view details)</p>
<table class="head2head">
  <thead>
    <tr>
      <th scope="col">Coach</th>
      <th scope="col">Total</th>
      <th scope="col">Exhibitions</th>
      <th scope="col">Preseason</th>
      <th scope="col">Regular Season</th>
      <th scope="col">Postseason</th>
    </tr>
  </thead>
  <tbody>';

    if (count($head2head) == 0) {
      $output .= '
    <tr><td colspan="6">No games played yet.</td></tr>';
    }

    foreach ($head2head as $_coach => $_record) {
      $output .= '
    <tr>
      <th scope="row"><a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?id='.$_record['uid'].'">'.$_SYS['html']->specialchars($_coach).'</a></th>';

      foreach (array('total', 'ex', 'pre', 'reg', 'post') as $_season) {
        $output .= '
      <td>
        '.(count($_record[$_season]['games']) ? '<span onclick="showh2h(this);">' : '').$_record[$_season]['won'].'-'.$_record[$_season]['lost'].($_record[$_season]['tied'] ? '-'.$_record[$_season]['tied'] : '').(count($_record[$_season]['games']) ? '</span>' : '');

        if (count($_record[$_season]['games'])) {
          $output .= '
        <ul class="info">';

          foreach ($_record[$_season]['games'] as $_game) {
            $output .= '<li><a href="'.$_SYS['page']['boxscore']['url'].'?game='.$_game['gid'].'">'.$_game['linktext'].'</a></li>';
          }

          $output .= '
        </ul>';
        }

        $output .= '
      </td>';
      }

      $output .= '
    </tr>';
    }

    unset($_coach, $_record, $_season);

    $output .= '
  </tbody>
</table>';

    return $output;
  } // getHTML()

} // Page

?>