<?php
/**
 * @(#) records.php
 */

class Page {

  var $categories = array('ind'  => array('name'       => 'Individual',
                                          'categories' => array('scoring'   => 'Scoring',
                                                                'passing'   => 'Passing',
                                                                'rushing'   => 'Rushing',
                                                                'receiving' => 'Receiving',
//                                                                 'defense'   => 'Defense',
                                                                )),
//                           'team' => array('name'       => 'Team',
//                                           'categories' => array('scoring'   => 'Scoring',
//                                                                 'passing'   => 'Passing',
//                                                                 'rushing'   => 'Rushing',
//                                                                 'receiving' => 'Receiving'))
                          );

  var $periods = array('all'  => 'Overall',
                       'ex'   => 'Exhibitions',
                       'pre'  => 'Preseason',
                       'reg'  => 'Regular Season',
                       'post' => 'Postseason');


  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function _getQueries($type, $category, $period, $season) {
    global $_SYS;

    if ($period == 'ex') {
      $where = 's.`week` = 0'.($season != 'all' ? ' AND s.`season` = '.$season : '');
    } elseif ($period == 'pre') {
      $where = 's.`week` < 0'.($season != 'all' ? ' AND s.`season` = '.$season : '');
    } elseif ($period == 'reg') {
      if ($season == 'all') {
        $where = array();

        foreach (array_keys($_SYS['season']) as $_season) {
          $where[] = '(s.`week` IN ('.join(', ', $_SYS['season'][$_season]['visible_weeks']['reg']).') AND s.`season` = '.$_season.')';
        }

        $where = join(' OR ', $where);
      } else {
        $where = 's.`week` IN ('.join(', ', $_SYS['season'][$season]['visible_weeks']['reg']).') AND s.`season` = '.$season;
      }
    } elseif ($period == 'post') {
      if ($season == 'all') {
        $where = array();

        foreach (array_keys($_SYS['season']) as $_season) {
          $where[] = '(s.`week` > '.$_SYS['season'][$_season]['reg_weeks'].' AND s.`season` = '.$_season.')';
        }

        $where = join(' OR ', $where);
      } else {
        $where = 's.`week` > '.$_SYS['season'][$season]['reg_weeks'].' AND s.`season` = '.$season;
      }
    } elseif ($period == 'bowl') {
      $where = array();

      foreach (array_keys($_SYS['season']) as $_season) {
        if ($_SYS['season'][$_season]['post_weeks'] > 0) {
          $where[] = '(s.`week` = '.array_pop(array_keys($_SYS['season'][$_season]['weeks'])).' AND s.`season` = '.$_season.')';
        }
      }

      $where = join(' OR ', $where);
    } elseif ($period == 'all') {
      if ($season == 'all') {
        $where = array();

        foreach (array_keys($_SYS['season']) as $_season) {
          $_pre_weeks = $_SYS['season'][$_season]['pre_weeks'] > 0 ? range(-1, -$_SYS['season'][$_season]['pre_weeks']) : array();
          $_reg_weeks = $_SYS['season'][$_season]['visible_weeks']['reg'];
          $_post_weeks = $_SYS['season'][$_season]['post_weeks'] > 0 ? range($_SYS['season'][$_season]['reg_weeks'] + 1, $_SYS['season'][$_season]['reg_weeks'] + $_SYS['season'][$_season]['post_weeks']) : array();
          $_ex_weeks = array(0);

          $where[] = '(s.`week` IN ('.join(', ', array_merge($_ex_weeks, $_pre_weeks, $_reg_weeks, $_post_weeks)).') AND s.`season` = '.$_season.')';
          unset($_ex_weeks, $_pre_weeks, $_reg_weeks, $_post_weeks);
        }

        $where = join(' OR ', $where);
      } else {
        $_pre_weeks = $_SYS['season'][$season]['pre_weeks'] > 0 ? range(-1, -$_SYS['season'][$season]['pre_weeks']) : array();
        $_reg_weeks = $_SYS['season'][$season]['visible_weeks']['reg'];
        $_post_weeks = $_SYS['season'][$season]['post_weeks'] > 0 ? range($_SYS['season'][$season]['reg_weeks'] + 1, $_SYS['season'][$season]['reg_weeks'] + $_SYS['season'][$season]['post_weeks']) : array();
        $_ex_weeks = array(0);

        $where = 's.`week` IN ('.join(', ', array_merge($_ex_weeks, $_pre_weeks, $_reg_weeks, $_post_weeks)).') AND s.`season` = '.$season;
        unset($_ex_weeks, $_pre_weeks, $_reg_weeks, $_post_weeks);
      }
    } elseif (intval($period) != 0) {
      $period = intval($period);

      if ($season == 'all') {
        return false;
      }

      if ($period > 0 && $period <= $_SYS['season'][$season]['reg_weeks'] && !in_array($period, $_SYS['season'][$season]['visible_weeks']['reg'])) {
        $where = 's.`week` IS NULL AND s.`season` = '.$season;
      } else {
        $where = 's.`week` = '.$period.' AND s.`season` = '.$season;
      }
    } else {
      return false;
    }

    $queries = array();
    include('records_'.$type.'_'.$category.'.php');

    return $queries;
  } // _getQueries()


  function getHTML() {
    global $_SYS;

    if (in_array($_SERVER['HTTP_USER_AGENT'], array('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Mozilla/5.0 (Twiceler-0.9 http://www.cuil.com/twiceler/robot.html)'))) {
        return $output;
    }

    /* determine category and period */

    $type = $_GET['type'];

    if (!array_key_exists($type, $this->categories)) {
      $type = 'ind';
    }

    $category = $_GET['category'];

    if (!array_key_exists($category, $this->categories[$type]['categories'])) {
      $category = 'scoring';
    }

    $season = $_GET['season'];

    if (!array_key_exists($season, $_SYS['season'])) {
      $season = 'all';
    }

    $period = $_GET['period'];

    if (!array_key_exists($period, $this->periods) && intval($period) == 0) {
      $period = 'all';
    }

    if ($season == 'all' && intval($period) != 0) {
      $period = 'all';
    }

    if ($_GET['period'] == 'bowl' && $season == 'all') {
      $period = 'bowl';
    }

    /* output */

    $output = '';

    /* period header */

    $output .= '
<p>
  '.($season == 'all' ? '[ All Seasons ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?type='.$type.'&amp;category='.$category.'&amp;period='.$period.'&amp;season=all">All Seasons</a>').' &middot;';

    $_period = array();

    foreach ($_SYS['season'] as $_key => $_val) {
      if ($season == $_key) {
        $_period[] = '
  [ '.$_val['name'].' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?type='.$type.'&amp;category='.$category.'&amp;period='.$period.'&amp;season='.$_key.'">'.$_val['name'].'</a>';
      }
    }

    $output .= join(' &middot;', $_period).'
</p>
<p>';

    $_period = array();

    foreach ($this->periods as $_key => $_val) {
      if ($period == $_key) {
        $_period[] = '
  [ '.$_val.' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?type='.$type.'&amp;category='.$category.'&amp;period='.$_key.'&amp;season='.$season.'">'.$_val.'</a>';
      }
    }

    if ($season == 'all') {
      if ($period == 'bowl') {
        $_period[] = '
  [ Bowls ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?type='.$type.'&amp;category='.$category.'&amp;period=bowl&amp;season='.$season.'">Bowls</a>';
      }
    }

    $output .= join(' &middot;', $_period).'<br />';

    if ($season != 'all') {
      $_period = array();

      foreach ($_SYS['season'][$season]['weeks'] as $_key => $_val) {
        if ($_key < 0) {
          $_val = 'P'.(-$_key);
        } elseif ($_key > $_SYS['season'][$season]['reg_weeks']) {
          $_val = $_SYS['season'][$season]['post_names'][$_key - $_SYS['season'][$season]['reg_weeks'] - 1]['acro'];
        } else {
          $_val = $_key;
        }

        if ($period == $_key) {
          $_period[] = '
  [ '.$_val.' ]';
        } else {
          $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?type='.$type.'&amp;category='.$category.'&amp;period='.$_key.'&amp;season='.$season.'">'.$_val.'</a>';
        }
      }

      $output .= join(' &middot;', $_period);
    }
    $output .= '
</p>';

    unset($_period, $_key, $_val);

    /* category header */

    foreach ($this->categories as $_type => $_cat) {
      $output .= '
<p>'.$_cat['name'].':';

      $_temp = array();

      foreach ($_cat['categories'] as $_category => $_name) {
        if ($type == $_type && $category == $_category) {
          $_temp[] = '
  [ '.$_name.' ]';
        } else {
          $_temp[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?type='.$_type.'&amp;category='.$_category.'&amp;period='.$period.'&amp;season='.$season.'">'.$_name.'</a>';
        }
      }

      $output .= join(' &middot;', $_temp).'
</p>';
    }

    unset($_type, $_cat, $_category, $_name, $_temp);

    /* fetch queries */

    $queries = $this->_getQueries($type, $category, $period, $season);

    $output .= '
<div class="index">
  <ul>';

    foreach (array_keys($queries) as $section) {
      if ($section != '__init__') {
        $output .= '
    <li><a href="#records_'.preg_replace('/[^A-Za-z]/', '_', $section).'">'.$section.'</a></li>';
      }
    }

    $output .= '
  </ul>
  <br class="float" />
</div>
<div class="records">';

    $count = 0;

    foreach (array_keys($queries) as $section) {

      if ($section != '__init__') {
        $output .= '
<h1 id="records_'.preg_replace('/[^A-Za-z]/', '_', $section).'">'.$section.'</h1>';
      }

      $_counter = 0;

      foreach ($queries[$section] as $query) {
        $result = $_SYS['dbh']->query($query['query']) or die($_SYS['dbh']->error());

        if ($section == '__init__') {
          continue;
        }

        $info = $result->info();

        $output .= '
<table class="float">
  <thead>
    <tr>
      <th colspan="'.($type == 'ind' ? '3' : '2').'">'.$query['title'].'</th>
    </tr>
  </thead>
  <tbody>';

        if ($result->rows() == 0) {
          $output .= '
    <tr>
      <td colspan="'.($type == 'ind' ? '3' : '2').'">No data available.</td>
    </tr>';
        }

        for ($i = 0; $i < $result->rows(); ++$i) {
          $row = $result->fetch_assoc();
          $row['value'] = strpos($row['value'], '.') === false ? number_format($row['value']) : number_format($row['value'], strlen($row['value']) - strpos($row['value'], '.') - 1);

          $output .= '
    <tr>';

          if ($i == 0 && $type == 'ind') {
            $output .= '
      <td rowspan="'.$result->rows().'" class="image">'.(file_exists('images/players/'.$row['name'].'.jpg') ? '<img src="images/players/'.rawurlencode($row['name']).'.jpg" alt="'.$row['name'].'" />' : 'No image available.').'</td>';
          }

          $_annotate = '';

          if (in_array('game', $info['name'])) {
            if ($row['week'] == 0) {
              $_week = 'EX';
            } elseif ($row['week'] < 0) {
              $_week = 'P'.(-$row['week']);
            } elseif ($row['week'] > $_SYS['season'][$row['season']]['reg_weeks']) {
              $_week = $_SYS['season'][$row['season']]['post_names'][$row['week'] - 1 - $_SYS['season'][$row['season']]['reg_weeks']]['acro'];
            } else {
              $_week = 'W'.$row['week'];
            }
            $_annotate = '<br /><span class="annotate">('.$_SYS['season'][$row['season']]['name'].'/'.$_week.': <a href="'.$_SYS['page']['boxscore']['url'].'?game='.$row['game'].'">'.str_replace(' ', '&nbsp;', $row['matchup']).'</a>)</span>';
          } elseif ($season == 'all' && in_array('season', $info['name'])) {
            $_annotate = '<br /><span class="annotate">('.$_SYS['season'][$row['season']]['name'].')</span>';
          }

          $output .= '
      <td class="name">'.$row['name'].$_annotate.'</td>
      <td class="value">'.$row['value'].'</td>
    </tr>';
        }

        $output .= '
  </tbody>
</table>';

        ++$_counter;

        if ($_counter % 3 == 0 && $_counter < count($queries[$section])) {
          $output .= '
<br class="float" />';
        }
      }
    }

    $output .= '
</div>';

    return $output;
  } // getHTML()

} // Page

?>