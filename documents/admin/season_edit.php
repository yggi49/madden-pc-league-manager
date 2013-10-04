<?php
/**
 * @(#) error404.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    $output = '';

    $output .= '
  <link rel="prev" href="'.$_SYS['page']['admin/season_list']['url'].'" />';   # "back"

    return $output;
  } // getHeader()


  function getHTML() {
    global $_SYS;

    /* query season */

    $id = intval($_GET['id']);

    if (!array_key_exists($id, $_SYS['season'])) {
      return $_SYS['html']->fehler('1', 'Season not found.');
    }

    $season = $_SYS['season'][$id];

    $_post_names = array();

    foreach ($season['post_names'] as $_name) {
      $_post_names[] = $_name['acro'].'; '.$_name['name'];
    }

    unset($_name);

    $output = '
<h1>Edit '.$_SYS['html']->specialchars($season['name']).'</h1>
<p><a href="'.$_SYS['page']['admin/season_list']['url'].'">Back to season list</a></p>
<form action="'.$_SYS['page']['admin/season_save']['url'].'" method="post">
<dl>
  <dt>'.$_SYS['html']->label('fname', 'Season Name').'</dt>
  <dd>'.$_SYS['html']->textfield('name', $season['name'], 0, 0, '', 'id="fname"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpost_names', 'Postseason Week Names').'</dt>
  <dd>'.$_SYS['html']->textarea('post_names', join("\n", $_post_names), 4, 80, '', 'id="fpost_names"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpost_teams', 'Playoff Teams').'</dt>
  <dd>'.$_SYS['html']->textfield('post_teams', $season['post_teams'], 2, 2, '', 'id="fpost_teams"').' per conference</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fstart', 'Season Start').'</dt>
  <dd>'.$_SYS['html']->textfield('start', date('Y-m-d H:i', $season['start']), 0, 16, '', 'id="fstart"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fweek', 'Week Length').'</dt>
  <dd>'.$_SYS['html']->textfield('week', $season['week'] / 86400, 0, 2, '', 'id="fweek"').' days</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('flogbegin', 'Begin Upload').'</dt>
  <dd>'.$_SYS['html']->textfield('log_begin', is_null($season['log_begin_offset']) ? '' : $_SYS['util']->time_diff($season['log_begin_offset']), 0, 7, '', 'id="flogbegin"').' hours</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('flogend', 'End Upload').'</dt>
  <dd>'.$_SYS['html']->textfield('log_end', is_null($season['log_end_offset']) ? '' : $_SYS['util']->time_diff($season['log_end_offset']), 0, 7, '', 'id="flogend"').' hours</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fspawn', 'Spawn Games').'</dt>
  <dd>'.$_SYS['html']->checkbox('spawn', 1, $season['spawn'], 'fspawn').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Apply').'
    '.$_SYS['html']->submit('submit', 'Save').'
  </dd>
</dl>
<h2>Weeks</h2>
<table>
  <thead>
    <tr>
      <th scope="col">Week</th>
      <th scope="col">Week Start</th>
      <th scope="col">Week End</th>
      <th scope="col">Begin Upload</th>
      <th scope="col">End Upload</th>
    </tr>
  </thead>';

    unset($_post_names);

    foreach (array_keys($season['weeks']) as $_week) {
      $_individual = array_key_exists($_week, $season['individual']);

      $output .= '
  <tbody>
    <tr>
      <td>'.($_week < 0 ? 'P'.-$_week : ($_week > $season['reg_weeks'] ? $season['post_names'][$_week - $season['reg_weeks'] - 1]['acro'] : $_week)).'</td>
      <td>'.date('D, Y-m-d, H:i', $season['weeks'][$_week]['begin']).'</td>
      <td>'.date('D, Y-m-d, H:i', $season['weeks'][$_week]['end']).'</td>
      <td>'.(is_null($season['weeks'][$_week]['log_begin']) ? '&mdash;' : date('D, Y-m-d, H:i', $season['weeks'][$_week]['log_begin'])).'</td>
      <td>'.(is_null($season['weeks'][$_week]['log_end']) ? '&mdash;' : date('D, Y-m-d, H:i', $season['weeks'][$_week]['log_end'])).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->checkbox('individual[weeks][]', $_week, $_individual ? $_week : '', 'findividual'.$_week).'</td>
      <td colspan="4">
        '.$_SYS['html']->label('fweek'.$_week, 'Week Length').':
        '.$_SYS['html']->textfield('individual[length]['.$_week.']', $_individual ? $season['individual'][$_week]['week'] / 86400 : '', 2, 2, '', 'id="fweek'.$_week.'"').' days &middot;
        '.$_SYS['html']->label('flogbegin'.$_week, 'Begin Upload').':
        '.$_SYS['html']->textfield('individual[log_begin]['.$_week.']', $_individual && !is_null($season['individual'][$_week]['log_begin_offset']) ? $_SYS['util']->time_diff($season['individual'][$_week]['log_begin_offset']) : '', 8, 8, '', 'id="flogbegin'.$_week.'"').' hours &middot;
        '.$_SYS['html']->label('flogend'.$_week, 'End Upload').':
        '.$_SYS['html']->textfield('individual[log_end]['.$_week.']', $_individual && !is_null($season['individual'][$_week]['log_end_offset']) ? $_SYS['util']->time_diff($season['individual'][$_week]['log_end_offset']) : '', 8, 8, '', 'id="flogend'.$_week.'"').' hours
      </td>
    </tr>
  </tbody>';
    }

    $output .= '
</table>
<dl>
  <dt>'.$_SYS['html']->hidden('id', $id).'</dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Apply').'
    '.$_SYS['html']->submit('submit', 'Save').'
  </dd>
</dl>
</form>';

    return $output;
  } // getHTML()

} // Page

?>