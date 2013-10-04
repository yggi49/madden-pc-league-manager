<?php
/*
 * @(#) vars.php
 */

/* time zone settings */

$now = date('Z');
putenv('TZ=Europe/Vienna');

$_SYS['time']['now'] = time();
$_SYS['time']['diff'] = intval(round((date('Z', $_SYS['time']['now']) - $now) / 3600)) * 3600;

unset($now);

/* database connection settings */

if ($_SERVER['SERVER_NAME'] != 'imt.localhost') {
  $_SYS['db']['type']     = 'mysql';
  $_SYS['db']['host']     = 'localhost';
  $_SYS['db']['user']     = 'imt';
  $_SYS['db']['pass']     = 'maddentrophy';
  $_SYS['db']['database'] = 'imt_obda_net';
  $_SYS['db']['prefix']   = '';
} else {
  $_SYS['db']['type']     = 'mysql';
  $_SYS['db']['host']     = 'localhost';
  $_SYS['db']['user']     = 'obda_igor';
  $_SYS['db']['pass']     = 'imt';
  $_SYS['db']['database'] = 'obda_imt';
  $_SYS['db']['prefix']   = '';
}

/* database table names */

$_SYS['table']['comment']               = 'comment';
$_SYS['table']['download']              = 'download';
$_SYS['table']['game']                  = 'game';
$_SYS['table']['link']                  = 'link';
$_SYS['table']['log']                   = 'log';
$_SYS['table']['news']                  = 'news';
$_SYS['table']['nfl']                   = 'nfl';
$_SYS['table']['pending']               = 'pending';
$_SYS['table']['pickem']                = 'pickem';
$_SYS['table']['roster']                = 'roster';
$_SYS['table']['season']                = 'season';
$_SYS['table']['stats_blocking']        = 'stats_blocking';
$_SYS['table']['stats_defense']         = 'stats_defense';
$_SYS['table']['stats_kicking']         = 'stats_kicking';
$_SYS['table']['stats_kick_returns']    = 'stats_kick_returns';
$_SYS['table']['stats_passing']         = 'stats_passing';
$_SYS['table']['stats_punting']         = 'stats_punting';
$_SYS['table']['stats_punt_returns']    = 'stats_punt_returns';
$_SYS['table']['stats_receiving']       = 'stats_receiving';
$_SYS['table']['stats_rushing']         = 'stats_rushing';
$_SYS['table']['stats_scoring_defense'] = 'stats_scoring_defense';
$_SYS['table']['stats_scoring_offense'] = 'stats_scoring_offense';
$_SYS['table']['stats_team_defense']    = 'stats_team_defense';
$_SYS['table']['stats_team_offense']    = 'stats_team_offense';
$_SYS['table']['team']                  = 'team';
$_SYS['table']['user']                  = 'user';

foreach (array_keys($_SYS['table']) as $_table) {
  $_SYS['table'][$_table] = $_SYS['db']['prefix'] . $_SYS['table'][$_table];
}

unset($_table);

/* homepage title and navigation bar */

$_SYS['site']['style']  = 'IMT';
$_SYS['site']['title']  = 'IMT';
$_SYS['site']['navbar'] = array(
                                'home',
                                'news',
                                'teams',
                                'coaches',
                                'schedule',
                                'standings',
                                'stats',
                                'records',
                                'pickem/schedule',
                                'forum',
                                'rules',
                                'downloads',
                                'links',
                                'calendar',
                                'STATIC',
                                'admin/index',
                                'control',
                                'help',
                                'login',
                                );

/* directories */

$_SYS['dir']['hostdir'] = '';
$_SYS['dir']['docdir']  = $_SERVER['DOCUMENT_ROOT'] . $_SYS['dir']['hostdir'] . '/documents';
$_SYS['dir']['imgdir']  = $_SERVER['DOCUMENT_ROOT'] . $_SYS['dir']['hostdir'] . '/images';
$_SYS['dir']['downdir'] = $_SERVER['DOCUMENT_ROOT'] . $_SYS['dir']['hostdir'] . '/downloads';
$_SYS['dir']['spawndir']= $_SERVER['DOCUMENT_ROOT'] . $_SYS['dir']['hostdir'] . '/spawn';

/* cookie name and expiration time */

$_SYS['cookie']['name']   = 'imt';
$_SYS['cookie']['expire'] = 30 * 86400;
$_SYS['cookie']['key']    = 'imt';

/* variables */

$_SYS['mail']['league'] = 'IMT';
$_SYS['mail']['from']   = 'IMT <webmaster@imt.obda.net>';
$_SYS['mail']['mvp']    = '';

$_SYS['var']['logos'] = 'klein_transparent';

?>
