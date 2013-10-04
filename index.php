<?php
/**
 * @(#) index.php
 */

/* load system variables */

require('system/vars.php');

/* send no-cache information */

header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Expires: Sat, 14 Mar 1998 11:15:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

/* determine content type */

$_SYS['var']['content_type'] = strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') === false ? 'text/html' : 'application/xhtml+xml';
header('Content-type: '.$_SYS['var']['content_type']);

/* connect to database and delete connection info variables (security) */

require('system/db.php');

$_SYS['dbh'] = new DB();
$_SYS['dbh']->connect();

if (!$_SYS['dbh']->is_connected()) {
  echo 'could not connect to database: '.$_SYS['dbh']->error();
//  echo '<p>'.var_export($_SYS['db'], 1).'</p>';
  exit;
}

unset($_SYS['db']);

$query = 'SET NAMES "utf8"';
$_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

/* load and initiate html factory */

require('system/html.php');

$_SYS['html'] = new HTML();

/* get request url */

$_SYS['request'] = parse_url($_SERVER['REQUEST_URI']);

/* read season-specific and general settings from database */

$query = 'SELECT * FROM '.$_SYS['table']['season'].' ORDER BY start';
$result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

$info = $result->info();

$_SYS['season'] = array();

while ($row = $result->fetch_assoc()) {
  for ($i = 0; $i < count($info['name']); ++$i) {
    $_SYS['season'][intval($row['id'])][$info['name'][$i]] = $info['type'][$i] === 'int' ? (is_null($row[$info['name'][$i]]) ? null : intval($row[$info['name'][$i]])) : strval($row[$info['name'][$i]]);

    if (in_array($info['name'][$i], array('post_names', 'individual', 'tiebreaker'))) {
      $_SYS['season'][intval($row['id'])][$info['name'][$i]] = unserialize($_SYS['season'][intval($row['id'])][$info['name'][$i]]);
    }
  }
}

unset($info, $i);

/* calculate start and end of each season and each week */

$_SYS['var']['season'] = 0;
$_SYS['var']['week']   = 0;

foreach (array_keys($_SYS['season']) as $_season) {

  $_now = $_SYS['season'][$_season]['start'];
  $_weeks = array();

  $_SYS['season'][$_season]['visible_weeks']['pre'] = array('NULL');
  $_SYS['season'][$_season]['visible_weeks']['reg'] = array('NULL');
  $_SYS['season'][$_season]['visible_weeks']['post'] = array('NULL');

  if ($_SYS['season'][$_season]['pre_weeks'] > 0) {
    $_weeks = array_merge($_weeks, range(-1, -$_SYS['season'][$_season]['pre_weeks']));
  }

  if ($_SYS['season'][$_season]['reg_weeks'] > 0) {
    $_weeks = array_merge($_weeks, range(1, $_SYS['season'][$_season]['reg_weeks']));
  }

  if ($_SYS['season'][$_season]['post_weeks'] > 0) {
    $_weeks = array_merge($_weeks, range($_SYS['season'][$_season]['reg_weeks'] + 1, $_SYS['season'][$_season]['reg_weeks'] + $_SYS['season'][$_season]['post_weeks']));
  }

  foreach ($_weeks as $_week) {
    $_SYS['season'][$_season]['weeks'][$_week]['begin'] = $_now;

    if (array_key_exists($_week, $_SYS['season'][$_season]['individual'])) {
      if (is_null($_SYS['season'][$_season]['individual'][$_week]['log_begin_offset'])) {
        $_SYS['season'][$_season]['weeks'][$_week]['log_begin'] = null;
      } else {
        $_SYS['season'][$_season]['weeks'][$_week]['log_begin'] = $_now + $_SYS['season'][$_season]['individual'][$_week]['log_begin_offset'];
      }

      $_SYS['season'][$_season]['weeks'][$_week]['end'] = $_now + $_SYS['season'][$_season]['individual'][$_week]['week'] - 1;

      if (is_null($_SYS['season'][$_season]['individual'][$_week]['log_end_offset'])) {
        $_SYS['season'][$_season]['weeks'][$_week]['log_end'] = null;
      } else {
        $_SYS['season'][$_season]['weeks'][$_week]['log_end'] = $_SYS['season'][$_season]['weeks'][$_week]['end'] + $_SYS['season'][$_season]['individual'][$_week]['log_end_offset'];
      }
    } else {
      if (is_null($_SYS['season'][$_season]['log_begin_offset'])) {
        $_SYS['season'][$_season]['weeks'][$_week]['log_begin'] = null;
      } else {
        $_SYS['season'][$_season]['weeks'][$_week]['log_begin'] = $_now + $_SYS['season'][$_season]['log_begin_offset'];
      }

      $_SYS['season'][$_season]['weeks'][$_week]['end'] = $_now + $_SYS['season'][$_season]['week'] - 1;

      if (is_null($_SYS['season'][$_season]['log_end_offset'])) {
        $_SYS['season'][$_season]['weeks'][$_week]['log_end'] = null;
      } else {
        $_SYS['season'][$_season]['weeks'][$_week]['log_end'] = $_SYS['season'][$_season]['weeks'][$_week]['end'] + $_SYS['season'][$_season]['log_end_offset'];
      }
    }

    $_now = $_SYS['season'][$_season]['weeks'][$_week]['end'] + 1;
  }

  /* dst correction */

  $_dst = date('I', $_SYS['season'][$_season]['start']);

  foreach (array_keys($_SYS['season'][$_season]['weeks']) as $_week) {
    foreach (array_keys($_SYS['season'][$_season]['weeks'][$_week]) as $_key) {
      if (!is_null($_SYS['season'][$_season]['weeks'][$_week][$_key])) {
        $_SYS['season'][$_season]['weeks'][$_week][$_key] += 3600 * ($_dst - date('I', $_SYS['season'][$_season]['weeks'][$_week][$_key]));
      }
    }

    /* visible? */

    if ($_SYS['season'][$_season]['weeks'][$_week]['begin'] <= $_SYS['time']['now']) {
      if ($_week < 0) {
        $_period = 'pre';
      } elseif ($_week > 0 && $_week <= $_SYS['season'][$_season]['reg_weeks']) {
        $_period = 'reg';
      } elseif ($_week > $_SYS['season'][$_season]['reg_weeks'] && $_week <= $_SYS['season'][$_season]['reg_weeks'] + $_SYS['season'][$_season]['post_weeks']) {
        $_period = 'post';
      }

      if (isset($_period)) {
        $_SYS['season'][$_season]['visible_weeks'][$_period][] = $_week;
      }

      unset($_period);
    }

    /* current season/week? */

    if ($_SYS['season'][$_season]['weeks'][$_week]['begin'] <= $_SYS['time']['now']
        && $_SYS['season'][$_season]['weeks'][$_week]['end'] >= $_SYS['time']['now']) {
      $_SYS['var']['season'] = $_season;
      $_SYS['var']['week']   = $_week;
    }
  }

  /* set end of season */

  $_SYS['season'][$_season]['end'] = $_SYS['season'][$_season]['weeks'][max(array_keys($_SYS['season'][$_season]['weeks']))]['end'];
}

unset($_season, $_now, $_weeks, $_week, $i);

if ($_SYS['var']['season'] == 0) {
  $_prev_season = null;
  $_prev_season_end = null;
  $_next_season = null;
  $_next_season_start = null;

  foreach (array_keys($_SYS['season']) as $_season) {
    if ($_SYS['season'][$_season]['end'] < $_SYS['time']['now']) {
      if (is_null($_prev_season_end) or $_SYS['season'][$_season]['end'] > $_prev_season_end) {
        $_prev_season = $_season;
        $_prev_season_end = $_SYS['season'][$_season]['end'];
        continue;
      }
    }

    if ($_SYS['season'][$_season]['start'] > $_SYS['time']['now']) {
      if (is_null($_next_season_start) or $_SYS['season'][$_season]['start'] < $_next_season_start) {
        $_next_season = $_season;
        $_next_season_start = $_SYS['season'][$_season]['start'];
        continue;
      }
    }
  }

  if ($_prev_season && $_next_season) {
    $_SYS['var']['season'] = $_SYS['time']['now'] < $_prev_season_end + ($_next_season_start - $_prev_season_end) / 2
                           ? $_prev_season
                           : $_next_season;
  }
  elseif (is_null($_next_season)) {
    $_SYS['var']['season'] = $_prev_season;
  }
  elseif (is_null($_prev_season)) {
    $_SYS['var']['season'] = $_next_season;
  }
  else {
    $_SYS['var']['season'] = max(array_keys($_SYS['season']));
  }

  unset ($_season, $_prev_season, $_prev_season_end, $_next_season, $_next_season_start);

  if ($_SYS['time']['now'] > $_SYS['season'][$_SYS['var']['season']]['end']) {
    $_SYS['var']['week']   = max(array_keys($_SYS['season'][$_SYS['var']['season']]['weeks']));
  } else {
    $_SYS['var']['week']   = min(array_keys($_SYS['season'][$_SYS['var']['season']]['weeks']));

    if ($_SYS['var']['week'] < 0) {
      $_SYS['var']['week'] = -1;
    }
  }
}

/* get requested season */

$_SYS['request']['season'] = $_SYS['var']['season'];
$_SYS['request']['week']   = $_SYS['var']['week'];

if (array_key_exists(intval($_REQUEST['season']), $_SYS['season'])) {
  $_SYS['request']['season'] = intval($_REQUEST['season']);
}

if (array_key_exists(intval($_REQUEST['week']), $_SYS['season'][$_SYS['request']['season']]['weeks'])
    || (array_key_exists('week', $_REQUEST) && $_REQUEST['week'] === '0')) {
  $_SYS['request']['week'] = intval($_REQUEST['week']);
} elseif (!array_key_exists('week', $_REQUEST) && !array_key_exists($_SYS['request']['week'], $_SYS['season'][$_SYS['request']['season']]['weeks'])) {
  if ($_SYS['time']['now'] > $_SYS['season'][$_SYS['request']['season']]['end']) {
    $_SYS['request']['week'] = max(array_keys($_SYS['season'][$_SYS['request']['season']]['weeks']));
  } else {
    $_SYS['request']['week']   = min(array_keys($_SYS['season'][$_SYS['request']['season']]['weeks']));

    if ($_SYS['request']['week'] < 0) {
      $_SYS['request']['week'] = -1;
    }
  }

  if (!array_key_exists($_SYS['request']['week'], $_SYS['season'][$_SYS['request']['season']]['weeks'])) {
    $_SYS['request']['week'] = max(array_keys($_SYS['season'][$_SYS['request']['season']]['weeks']));
  }
}

/* load utility functions */

require('system/functions.php');

$_SYS['util'] = new Util();

/* read user data */

$_SYS['user']['id']  = 0;
$_SYS['user']['pwd'] = '';

if (array_key_exists($_SYS['cookie']['name'], $_COOKIE)) {
  $cookie = $_SYS['util']->cookie_decrypt($_COOKIE[$_SYS['cookie']['name']], $_SYS['cookie']['key']);
}

$query = 'SELECT *
          FROM   '.$_SYS['table']['user'].'
          WHERE  id = '.intval($cookie['id']).'
                 AND pwd = "'.$cookie['pwd'].'"
                 AND status = "Active"';
$result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

$info = $result->info();
$row = $result->rows() == 1 ? $result->fetch_assoc() : array();

for ($i = 0; $i < count($info['name']); ++$i) {
  $_SYS['user'][$info['name'][$i]] = $info['type'][$i] === 'int' ? intval($row[$info['name'][$i]]) : strval($row[$info['name'][$i]]);
}

$_SYS['user']['team'] = array();

/* check logo style */

if (!$_SYS['user']['logos']) {
  $_SYS['user']['logos'] = $_SYS['var']['logos'];
}

if (!is_dir($_SYS['dir']['imgdir'].'/logos/'.$_SYS['user']['logos'])) {
  $_SYS['user']['logos'] = '';
}

unset($cookie, $info, $i);

/* set cookie */

if ($_SYS['user']['id']) {

  /* read teams */

  $query = 'SELECT season, id
            FROM   '.$_SYS['table']['team'].'
            WHERE  user = '.$_SYS['user']['id'];
  $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

  while ($row = $result->fetch_assoc()) {
    $_SYS['user']['team'][$row['season']][] = $row['id'];
  }

  /* determine ip and save hit to db */

  $_SYS['user']['ip'] = $_SERVER['REMOTE_ADDR'];

  if ($_SYS['time']['now'] > $_SYS['user']['last_hit'] + 15 * 60) {
    $_SYS['user']['last_visit'] = $_SYS['user']['last_hit'];
  }

  $_SYS['user']['last_hit'] = $_SYS['time']['now'];

  $query = 'UPDATE '.$_SYS['table']['user'].'
            SET    ip = "'.$_SYS['user']['ip'].'",
                   last_hit = '.$_SYS['user']['last_hit'].',
                   last_visit = '.$_SYS['user']['last_visit'].'
            WHERE  id = '.$_SYS['user']['id'];
  $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

  $_SYS['util']->setcookie($_SYS['user']['id'], $_SYS['user']['pwd']);
} else {
  $_SYS['util']->delcookie();
}

/* load page tree */

require('system/pages.php');

if (file_exists('system/my_pages.php')) {
  require('system/my_pages.php');
}

/* search for requested url */

if (array_key_exists($_SYS['request']['path'], $_SYS['url'])) {  /* url found */
  if (!$_SYS['page'][$_SYS['url'][$_SYS['request']['path']]]['access']) {  /* forbidden */
    header('HTTP/1.0 403 Access denied');
    $_SYS['request']['page'] = 'error403';
  } else {
    $_SYS['request']['page'] = $_SYS['url'][$_SYS['request']['path']];

    if (!is_readable($_SYS['page'][$_SYS['request']['page']]['document']) && strpos($_SYS['request']['page'], 'error') !== 0) {  /* document not found */
      header('HTTP/1.0 404 Not Found');
      $_SYS['request']['page'] = 'error404';
    }
  }
} elseif (array_key_exists($_SYS['request']['path'], $_SYS['rewrite'])) {  /* rewrite request */
  header('Location: http://' . $_SERVER['SERVER_NAME'] . $_SYS['rewrite'][$_SYS['request']['path']] . (strlen($_SYS['request']['query']) ? '?'.$_SYS['request']['query'] : '') . (strlen($_SYS['request']['fragment']) ? '#'.$_SYS['request']['fragment'] : ''));
  exit;
} elseif (strpos($_SYS['request']['path'], $_SYS['dir']['hostdir'].'/static/') === 0
          && file_exists($_SERVER['DOCUMENT_ROOT'].$_SYS['request']['path'])) {  /* static page */
  $_SYS['request']['page'] = 'static://'.$_SYS['request']['path'];
} else {  /* url not found */
  header('HTTP/1.0 404 Not Found');
  $_SYS['request']['page'] = 'error404';
}

/* include template + parsing engine and generate page */

require('system/parser.php');
require('template.php');

$_SYS['template'] = new Template();
echo $_SYS['template']->getHTML();

/* close database connection */

$_SYS['dbh']->disconnect();

?>
