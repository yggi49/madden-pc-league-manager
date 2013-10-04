<?php
/**
 * @(#) admin/season_new.php
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


  function _getRequest() {
    global $_SYS;

    $output = '
<h1>Create New Season</h1>
<p><a href="'.$_SYS['page']['admin/season_list']['url'].'">Back to season list</a></p>
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="post">
<dl>
  <dt>'.$_SYS['html']->label('fname', 'Season Name').'</dt>
  <dd>'.$_SYS['html']->textfield('name', '', 0, 0, '', 'id="fname"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpre_weeks', 'Preseason Weeks').'</dt>
  <dd>'.$_SYS['html']->textfield('pre_weeks', '4', 2, 2, '', 'id="fpre_weeks"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('freg_weeks', 'Regular Season Weeks').'</dt>
  <dd>'.$_SYS['html']->textfield('reg_weeks', '17', 2, 2, '', 'id="freg_weeks"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpost_weeks', 'Postseason Weeks').'</dt>
  <dd>'.$_SYS['html']->textfield('post_weeks', '4', 2, 2, '', 'id="fpost_weeks"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpost_names', 'Postseason Week Names').'</dt>
  <dd>'.$_SYS['html']->textarea('post_names', "WC; Wild Cards\nDIV; Divisionals\nCCG; Championships\nSB; Super Bowl", 4, 80, '', 'id="fpost_names"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fpost_teams', 'Playoff Teams').'</dt>
  <dd>'.$_SYS['html']->textfield('post_teams', '6', 2, 2, '', 'id="fpost_teams"').' per conference</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fstart', 'Season Start').'</dt>
  <dd>'.$_SYS['html']->textfield('start', date('Y-m-d H:i', mktime(0, 0, 0, date('m', $_SYS['time']['now']) + 1, 1, date('Y', $_SYS['time']['now']))), 0, 16, '', 'id="fstart"').'</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('fweek', 'Week Length').'</dt>
  <dd>'.$_SYS['html']->textfield('week', '7', 0, 2, '', 'id="fweek"').' days</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('flogbegin', 'Begin Upload').'</dt>
  <dd>'.$_SYS['html']->textfield('log_begin', '0:00', 0, 7, '', 'id="flogbegin"').' hours</dd>
</dl>
<dl>
  <dt>'.$_SYS['html']->label('flogend', 'End Upload').'</dt>
  <dd>'.$_SYS['html']->textfield('log_end', '0:00', 0, 7, '', 'id="flogend"').' hours</dd>
</dl>
<table>
  <thead>
    <tr>
      <th scope="col">Conference</th>
      <th scope="col">Division</th>
      <th scope="col">Teams</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>'.$_SYS['html']->textfield('division[0][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[0][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[0][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[1][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[1][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[1][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[2][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[2][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[2][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[3][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[3][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[3][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[4][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[4][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[4][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[5][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[5][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[5][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[6][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[6][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[6][teams]', '', 2, 2).'</td>
    </tr>
    <tr>
      <td>'.$_SYS['html']->textfield('division[7][conf]', '', 3, 3).'</td>
      <td>'.$_SYS['html']->textfield('division[7][div]', '', 10, 10).'</td>
      <td>'.$_SYS['html']->textfield('division[7][teams]', '', 2, 2).'</td>
    </tr>
  </tbody>
</table>
<dl>
  <dt>&nbsp;</dt>
  <dd>'.$_SYS['html']->submit('submit', 'Save').'</dd>
</dl>
</form>';

    return $output;
  }


  function _postRequest() {
    global $_SYS;

    $season['name'] = trim($_POST['name']);

    if (strlen($season['name']) < 1) {
      return $_SYS['html']->fehler('1', 'Please provide a name for the season.');
    }

    /* weeks */

    $season['pre_weeks']  = max(0, intval($_POST['pre_weeks']));
    $season['reg_weeks']  = max(0, intval($_POST['reg_weeks']));
    $season['post_weeks'] = max(0, intval($_POST['post_weeks']));

    if ($season['pre_weeks'] + $season['reg_weeks'] + $season['post_weeks'] < 1) {
      return $_SYS['html']->fehler('2', 'Please provide lengths of preseason, regular season, and postseason.');
    }

    /* playoff teams */

    $season['post_teams'] = intval($_POST['post_teams']);

    if ($season['post_teams'] < 1) {
      return $_SYS['html']->fehler('3', 'Please enter how many teams per conference advance to the playoffs.');
    }

    /* postseason week names */

    $_post_names = preg_split('/[\r\n]+/', trim($_POST['post_names']), -1, PREG_SPLIT_NO_EMPTY);

    if (count($_post_names) != $season['post_weeks']) {
      return $_SYS['html']->fehler('4', 'There shall be '.$season['post_weeks'].' postseason weeks, but you provided '.count($_post_names).' names');
    }

    $season['post_names'] = array();

    foreach ($_post_names as $_name) {
      if (strpos($_name, ';') === false) {
        return $_SYS['html']->fehler('5', 'Names for postseason weeks must be specified with format: Acronym; Title');
      }

      $_name = explode(';', $_name, 2);

      $_name[0] = trim($_name[0]);
      $_name[1] = trim($_name[1]);

      if (strlen($_name[0]) < 1 || strlen($_name[1]) < 1) {
        return $_SYS['html']->fehler('6', 'Please provide acronym and title for each postseason week.');
      }

      $season['post_names'][] = array('name' => $_name[1], 'acro' => $_name[0]);
    }

    unset($_post_names, $_name);

    /* check start of season */

    if (!preg_match('/^(\d+)-(\d+)-(\d+)\s+(\d+):(\d+)$/', $_POST['start'], $_start)) {
      return $_SYS['html']->fehler('2', 'Bad season start date');
    }

    if (!checkdate($_start[2], $_start[3], $_start[1])) {
      return $_SYS['html']->fehler('3', 'Bad season start date');
    }

    if ($_start[4] > 23 || $_start[5] > 59) {
      return $_SYS['html']->fehler('4', 'Bad season start time');
    }

    $season['start'] = mktime($_start[4], $_start[5], 0, $_start[2], $_start[3], $_start[1]);

    unset($_start);

    /* check week length */

    if (intval($_POST['week']) < 1) {
      return $_SYS['html']->fehler('5', 'Bad default week length');
    }

    $season['week'] = intval($_POST['week']) * 86400;

    /* begin upload offset */

    if (trim($_POST['log_begin']) == '') {
      $season['log_begin_offset'] = null;
    } elseif (($season['log_begin_offset'] = $_SYS['util']->offsetToSec($_POST['log_begin'])) === false) {
      return $_SYS['html']->fehler('6', 'Bad upload begin offset');
    }

    /* end upload offset */

    if (trim($_POST['log_end']) == '') {
      $season['log_end_offset'] = null;
    } elseif (($season['log_end_offset'] = $_SYS['util']->offsetToSec($_POST['log_end'])) === false) {
      return $_SYS['html']->fehler('7', 'Bad upload end offset');
    }

    /* check divisions */

    $season['divisions'] = array();

    foreach ($_POST['division'] as $_division) {
      $_conf  = trim($_division['conf']);
      $_div   = trim($_division['div']);
      $_teams = intval($_division['teams']);

      if (strlen($_conf) > 0 && strlen($_div) > 0 && $_teams > 0) {
        for ($i = 0; $i < $_teams; ++$i) {
          $season['divisions'][] = '(--ID--, "'.$_conf.'", "'.$_div.'", 0, 0)';
        }
      }
    }

    if (count($season['divisions']) == 0) {
      return $_SYS['html']->fehler('2', 'Please create some divisions.');
    }

    unset($_division, $_conf, $_div, $_teams);

    /* tie breakers? */

    $season['tiebreaker'] = $_SYS['mail']['league'] == 'FFML'
                          ? 'a:2:{s:3:"div";a:2:{s:3:"two";a:8:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:3:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}i:2;a:1:{s:5:"equal";i:1;}}i:4;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:5;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:6;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:7;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}s:4:"more";a:8:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:3:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}i:2;a:1:{s:5:"equal";i:1;}}i:4;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:5;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:6;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:7;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}}s:4:"conf";a:2:{s:3:"two";a:7:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:2;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:3;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:6;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}s:4:"more";a:7:{i:0;a:3:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}i:2;a:1:{s:5:"sweep";i:1;}}i:1;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:2;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:3;a:2:{i:0;s:14:"_pointsAgainst";i:1;a:2:{i:0;s:2:"PA";i:1;s:14:"Points Against";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}i:6;a:2:{i:0;s:9:"_coinToss";i:1;a:2:{i:0;s:4:"COIN";i:1;s:9:"Coin Toss";}}}}}'
                          : 'a:2:{s:3:"div";a:2:{s:3:"two";a:6:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}}s:4:"more";a:6:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:15:"_divisionRecord";i:1;a:2:{i:0;s:3:"DIV";i:1;s:15:"Division Record";}}i:2;a:2:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}}i:3;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:4;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:5;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}}}s:4:"conf";a:2:{s:3:"two";a:5:{i:0;a:2:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}}i:1;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:2;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:3;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:4;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}}s:4:"more";a:5:{i:0;a:3:{i:0;s:10:"_head2head";i:1;a:2:{i:0;s:3:"H2H";i:1;s:12:"Head to Head";}i:2;a:1:{s:5:"sweep";i:1;}}i:1;a:2:{i:0;s:17:"_conferenceRecord";i:1;a:2:{i:0;s:4:"CONF";i:1;s:17:"Conference Record";}}i:2;a:3:{i:0;s:12:"_commonGames";i:1;a:2:{i:0;s:2:"CG";i:1;s:12:"Common Games";}i:2;a:1:{s:3:"min";i:4;}}i:3;a:2:{i:0;s:18:"_strengthOfVictory";i:1;a:2:{i:0;s:3:"SOV";i:1;s:19:"Strength of Victory";}}i:4;a:2:{i:0;s:19:"_strengthOfSchedule";i:1;a:2:{i:0;s:3:"SOS";i:1;s:20:"Strength of Schedule";}}}}}';

    /* start transaction */

    $query = 'START TRANSACTION';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* insert season */

    $query = 'INSERT '.$_SYS['table']['season'].'
              SET    name             = '.$_SYS['dbh']->escape_string($season['name']).',
                     pre_weeks        = '.$_SYS['dbh']->escape_string($season['pre_weeks']).',
                     reg_weeks        = '.$_SYS['dbh']->escape_string($season['reg_weeks']).',
                     post_weeks       = '.$_SYS['dbh']->escape_string($season['post_weeks']).',
                     post_names       = '.$_SYS['dbh']->escape_string(serialize($season['post_names'])).',
                     post_teams       = '.$_SYS['dbh']->escape_string($season['post_teams']).',
                     start            = '.$_SYS['dbh']->escape_string($season['start']).',
                     week             = '.$_SYS['dbh']->escape_string($season['week']).',
                     log_begin_offset = '.(is_null($season['log_begin_offset']) ? 'NULL' : $_SYS['dbh']->escape_string($season['log_begin_offset'])).',
                     log_end_offset   = '.(is_null($season['log_end_offset']) ? 'NULL' : $_SYS['dbh']->escape_string($season['log_end_offset'])).',
                     individual       = "a:0:{}",
                     tiebreaker       = '.$_SYS['dbh']->escape_string($season['tiebreaker']);
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $season['id'] = $_SYS['dbh']->insert_id();

    /* add divisions */

    $query = str_replace('(--ID--,', '('.$season['id'].',', 'INSERT '.$_SYS['table']['team'].'
                                                                    (season, conference, division, team, user)
                                                             VALUES '.join(', ', $season['divisions']));
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* commit transaction */

    $query = 'COMMIT';
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    header('Location: '.$_SYS['page']['admin/season_teams']['url'].'?id='.$season['id']);
  }


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