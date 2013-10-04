<?php
/**
 * @(#) admin/season_save.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $season['id'] = intval($_POST['id']);

    /* check if season exists */

    if (!array_key_exists($season['id'], $_SYS['season'])) {
      return $_SYS['html']->fehler('1', 'Season does not exist.');
    }

    /* playoff teams */

    $season['post_teams'] = intval($_POST['post_teams']);

    if ($season['post_teams'] < 1) {
      return $_SYS['html']->fehler('3', 'Please enter how many teams per conference advance to the playoffs.');
    }

    /* postseason week names */

    $season['post_weeks'] = $_SYS['season'][$season['id']]['post_weeks'];

    $_post_names = preg_split('/[\r\n]+/', trim($_POST['post_names']), -1, PREG_SPLIT_NO_EMPTY);

    if (count($_post_names) != $season['post_weeks']) {
      return $_SYS['html']->fehler('4', 'There are '.$season['post_weeks'].' postseason weeks, but you provided '.count($_post_names).' names');
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

    if (intval($_POST['week']) < 0) {
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

    /* individual settings */

    $season['individual'] = array();

    if (array_key_exists('weeks', $_POST['individual'])) {
      foreach ($_POST['individual']['weeks'] as $_week) {
        if (intval($_POST['individual']['length'][$_week]) < 0) {
          return $_SYS['html']->fehler('8', 'Bad week length for week '.$_week);
        }

        $season['individual'][$_week]['week'] = intval($_POST['individual']['length'][$_week]) * 86400;

        if ($_POST['individual']['log_begin'][$_week] === '') {
          $season['individual'][$_week]['log_begin_offset'] = null;
        } elseif (($season['individual'][$_week]['log_begin_offset'] = $_SYS['util']->offsetToSec($_POST['individual']['log_begin'][$_week])) === false) {
          return $_SYS['html']->fehler('9', 'Bad upload begin offset for week '.$_week);
        }

        if ($_POST['individual']['log_end'][$_week] === '') {
          $season['individual'][$_week]['log_end_offset'] = null;
        } elseif (($season['individual'][$_week]['log_end_offset'] = $_SYS['util']->offsetToSec($_POST['individual']['log_end'][$_week])) === false) {
          return $_SYS['html']->fehler('10', 'Bad upload end offset for week '.$_week);
        }
      }
    }

    $season['individual'] = serialize($season['individual']);

    /* season name */

    $season['name'] = trim($_POST['name']);

    if (strlen($season['name']) < 1) {
      return $_SYS['html']->fehler('11', 'Please provide a name for this season.');
    }

    /* save season */

    $query = 'UPDATE '.$_SYS['table']['season'].'
              SET    name             = '.$_SYS['dbh']->escape_string($season['name']).',
                     post_teams       = '.$_SYS['dbh']->escape_string($season['post_teams']).',
                     post_names       = '.$_SYS['dbh']->escape_string(serialize($season['post_names'])).',
                     start            = '.$_SYS['dbh']->escape_string($season['start']).',
                     week             = '.$_SYS['dbh']->escape_string($season['week']).',
                     log_begin_offset = '.(is_null($season['log_begin_offset']) ? 'NULL' : $_SYS['dbh']->escape_string($season['log_begin_offset'])).',
                     log_end_offset   = '.(is_null($season['log_end_offset']) ? 'NULL' : $_SYS['dbh']->escape_string($season['log_end_offset'])).',
                     individual       = '.$_SYS['dbh']->escape_string($season['individual']).',
                     spawn            = '.($_POST['spawn'] ? '1' : '0').'
              WHERE  id = '.$_SYS['dbh']->escape_string($season['id']);
    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* return */

    switch ($_POST['submit']) {
    case 'Save':
      header('Location: '.$_SYS['page']['admin/season_list']['url']);
      break;
    default:
      header('Location: '.$_SYS['page']['admin/season_edit']['url'].'?id='.$season['id']);
    }
  } // getHTML()

} // Page

?>