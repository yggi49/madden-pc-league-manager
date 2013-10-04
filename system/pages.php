<?php
/*
 * @(#) pages.php
 */

$_SYS['util']->addpage(array('name'     => 'home',
                         'url'      => '/index.html',
                         'rewrite'  => array('', '/', '/home.html'),
                         'document' => '/home.php',
                         'title'    => 'Home'));

$_SYS['util']->addpage(array('name'     => 'news',
                         'url'      => '/news.html',
                         'document' => '/news.php',
                         'title'    => 'News'));

$_SYS['util']->addpage(array('name'     => 'news_top',
                         'url'      => '/news_top.html',
                         'document' => '/news_top.php',
                         'title'    => '(Un)Top News',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'news_detail',
                         'url'      => '/news_detail.html',
                         'document' => '/news_detail.php',
                         'title'    => 'League News Detail'));

$_SYS['util']->addpage(array('name'     => 'news_edit',
                         'url'      => '/news_edit.html',
                         'document' => '/news_edit.php',
                         'title'    => 'Edit League News',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'news_save',
                         'url'      => '/news_save.html',
                         'document' => '/news_save.php',
                         'title'    => 'Save League News',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'teams',
                         'url'      => '/teams.html',
                         'document' => '/teams.php',
                         'title'    => 'Teams'));

$_SYS['util']->addpage(array('name'     => 'coaches',
                         'url'      => '/coaches.html',
                         'document' => '/coaches.php',
                         'title'    => 'Coaches'));

$_SYS['util']->addpage(array('name'     => 'schedule',
                         'url'      => '/schedule.html',
                         'document' => '/schedule.php',
                         'title'    => 'Schedule'));

$_SYS['util']->addpage(array('name'     => 'schedule_edit',
                         'url'      => '/schedule_edit.html',
                         'document' => '/schedule_edit.php',
                         'title'    => 'Add/Edit Game',
                         'access'   => $_SYS['user']['admin'] || count($_SYS['user']['team'][$_SYS['request']['season']])));

$_SYS['util']->addpage(array('name'     => 'schedule_delete',
                         'url'      => '/schedule_delete.html',
                         'document' => '/schedule_delete.php',
                         'title'    => 'Delete Game',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'upload',
                         'url'      => '/upload.html',
                         'document' => '/upload.php',
                         'title'    => 'Game Log Upload',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'spawn',
                         'url'      => '/spawn.html',
                         'document' => '/spawn.php',
                         'title'    => 'Spawn Download'));

$_SYS['util']->addpage(array('name'     => 'calendar',
                         'url'      => '/calendar.html',
                         'document' => '/calendar.php',
                         'title'    => 'Calendar'));

$_SYS['util']->addpage(array('name'     => 'spawn_upload',
                         'url'      => '/spawn_upload.html',
                         'document' => '/spawn_upload.php',
                         'title'    => 'Spawn Upload',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'clear',
                         'url'      => '/clear.html',
                         'document' => '/clear.php',
                         'title'    => 'Clear Game',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'matchup',
                         'url'      => '/matchup.html',
                         'document' => '/matchup.php',
                         'title'    => 'Matchup'));

$_SYS['util']->addpage(array('name'     => 'recaps',
                         'url'      => '/recaps.html',
                         'document' => '/recaps.php',
                         'title'    => 'Recaps'));

$_SYS['util']->addpage(array('name'     => 'recap_edit',
                         'url'      => '/recap_edit.html',
                         'document' => '/recap_edit.php',
                         'title'    => 'Edit Recaps',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'comment',
                         'url'      => '/comment.html',
                         'document' => '/comment.php',
                         'title'    => 'Comment',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'boxscore',
                         'url'      => '/boxscore.html',
                         'document' => '/boxscore.php',
                         'title'    => 'Box Score'));

$_SYS['util']->addpage(array('name'     => 'gamelog',
                         'url'      => '/gamelog.html',
                         'document' => '/gamelog.php',
                         'title'    => 'Game Log'));

$_SYS['util']->addpage(array('name'     => 'standings',
                         'url'      => '/standings.html',
                         'document' => '/standings.php',
                         'title'    => 'Standings'));

$_SYS['util']->addpage(array('name'     => 'stats',
                         'url'      => '/stats.html',
                         'document' => '/stats.php',
                         'title'    => 'Stats'));

$_SYS['util']->addpage(array('name'     => 'records',
                         'url'      => '/records.html',
                         'document' => '/records.php',
                         'title'    => 'Records'));

$_SYS['util']->addpage(array('name'     => 'pickem/schedule',
                         'url'      => '/pickem/schedule.html',
                         'document' => '/pickem/schedule.php',
                         'title'    => "Pick'em"));

$_SYS['util']->addpage(array('name'     => 'pickem/standings',
                         'url'      => '/pickem/standings.html',
                         'document' => '/pickem/standings.php',
                         'title'    => "Pick'em Standings"));

$_SYS['util']->addpage(array('name'     => 'rules',
                         'url'      => '/rules.html',
                         'document' => '/rules.php',
                         'title'    => 'Rules'));

$_SYS['util']->addpage(array('name'     => 'rules_edit',
                         'url'      => '/rules_edit.html',
                         'document' => '/rules_edit.php',
                         'title'    => 'Edit Rules',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'team/home',
                         'url'      => '/team/home.html',
                         'document' => '/team/home.php',
                         'title'    => 'Team Home'));

$_SYS['util']->addpage(array('name'     => 'team/schedule',
                         'url'      => '/team/schedule.html',
                         'document' => '/team/schedule.php',
                         'title'    => 'Team Schedule'));

$_SYS['util']->addpage(array('name'     => 'team/roster',
                         'url'      => '/team/roster.html',
                         'document' => '/team/roster.php',
                         'title'    => 'Team Roster'));

$_SYS['util']->addpage(array('name'     => 'team/stats',
                         'url'      => '/team/stats.html',
                         'document' => '/team/stats.php',
                         'title'    => 'Team Stats'));

$_SYS['util']->addpage(array('name'     => 'team/scouts',
                         'url'      => '/team/scouts.html',
                         'document' => '/team/scouts.php',
                         'title'    => 'Team Scouts'));

$_SYS['util']->addpage(array('name'     => 'team/news',
                         'url'      => '/team/news.html',
                         'document' => '/team/news.php',
                         'title'    => 'Team News'));

$_SYS['util']->addpage(array('name'     => 'team/news_detail',
                         'url'      => '/team/news_detail.html',
                         'document' => '/team/news_detail.php',
                         'title'    => 'Team News Detail'));

$_SYS['util']->addpage(array('name'     => 'team/news_edit',
                         'url'      => '/team/news_edit.html',
                         'document' => '/team/news_edit.php',
                         'title'    => 'Edit Team News',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'team/news_save',
                         'url'      => '/team/news_save.html',
                         'document' => '/team/news_save.php',
                         'title'    => 'Save Team News',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'profile',
                         'url'      => '/profile.html',
                         'document' => '/profile.php',
                         'title'    => 'Profile'));

$_SYS['util']->addpage(array('name'     => 'control',
                         'url'      => '/control.html',
                         'document' => '/control.php',
                         'title'    => 'Setup',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'help',
                         'url'      => '/help.html',
                         'document' => '/help.php',
                         'title'    => 'Help',
                         'access'   => $_SYS['user']['id']));

$_SYS['util']->addpage(array('name'     => 'download',
                         'url'      => '/download.html',
                         'document' => '/download.php',
                         'title'    => 'Download'));

$_SYS['util']->addpage(array('name'     => 'downloads',
                         'url'      => '/downloads.html',
                         'document' => '/downloads.php',
                         'title'    => 'Downloads'));

$_SYS['util']->addpage(array('name'     => 'download_edit',
                         'url'      => '/download_edit.html',
                         'document' => '/download_edit.php',
                         'title'    => 'Edit Download',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'download_status',
                         'url'      => '/download_status.html',
                         'document' => '/download_status.php',
                         'title'    => 'Toggle Download Status',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'download_save',
                         'url'      => '/download_save.html',
                         'document' => '/download_save.php',
                         'title'    => 'Save Download',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'download_delete',
                         'url'      => '/download_delete.html',
                         'document' => '/download_delete.php',
                         'title'    => 'Delete Download',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'links',
                         'url'      => '/links.html',
                         'document' => '/links.php',
                         'title'    => 'Links'));

$_SYS['util']->addpage(array('name'     => 'link_edit',
                         'url'      => '/link_edit.html',
                         'document' => '/link_edit.php',
                         'title'    => 'Edit Link',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'link_status',
                         'url'      => '/link_status.html',
                         'document' => '/link_status.php',
                         'title'    => 'Toggle Link Status',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'link_save',
                         'url'      => '/link_save.html',
                         'document' => '/link_save.php',
                         'title'    => 'Save Link',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'link_delete',
                         'url'      => '/link_delete.html',
                         'document' => '/link_delete.php',
                         'title'    => 'Delete Link',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'login',
                         'url'      => $_SYS['user']['id'] ? '/logout.html' : '/login.html',
                         'rewrite'  => $_SYS['user']['id'] ? array('/login.html') : array('/logout.html'),
                         'document' => '/login.php',
                         'title'    => $_SYS['user']['id'] ? 'Logout' : 'Login'));

/*** admin pages ***/

$_SYS['util']->addpage(array('name'     => 'admin',
                         'url'      => '/admin.html',
                         'document' => '/admin.php',
                         'title'    => 'Administration',
                         'linktext' => 'Admin',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/index',
                         'url'      => '/admin/index.html',
                         'rewrite'  => array('/admin', '/admin/'),
                         'document' => '/admin/index.php',
                         'title'    => 'Administration',
                         'linktext' => 'Admin',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/user_status',
                         'url'      => '/admin/user_status.html',
                         'document' => '/admin/user_status.php',
                         'title'    => 'Change User Status',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/user_edit',
                         'url'      => '/admin/user_edit.html',
                         'document' => '/admin/user_edit.php',
                         'title'    => 'Edit User',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/user_save',
                         'url'      => '/admin/user_save.html',
                         'document' => '/admin/user_save.php',
                         'title'    => 'Save User',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/user_delete',
                         'url'      => '/admin/user_delete.html',
                         'document' => '/admin/user_delete.php',
                         'title'    => 'Delete User',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_list',
                         'url'      => '/admin/season.html',
                         'document' => '/admin/season_list.php',
                         'title'    => 'Season Management',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_new',
                         'url'      => '/admin/season_new.html',
                         'document' => '/admin/season_new.php',
                         'title'    => 'Create New Season',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_edit',
                         'url'      => '/admin/season_edit.html',
                         'document' => '/admin/season_edit.php',
                         'title'    => 'Edit Season',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_save',
                         'url'      => '/admin/season_save.html',
                         'document' => '/admin/season_save.php',
                         'title'    => 'Save Season',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_teams',
                         'url'      => '/admin/season_teams.html',
                         'document' => '/admin/season_teams.php',
                         'title'    => 'Edit Season Teams',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_schedule',
                         'url'      => '/admin/season_schedule.html',
                         'document' => '/admin/season_schedule.php',
                         'title'    => 'Upload Schedule',
                         'access'   => $_SYS['user']['admin']));

$_SYS['util']->addpage(array('name'     => 'admin/season_roster',
                         'url'      => '/admin/season_roster.html',
                         'document' => '/admin/season_roster.php',
                         'title'    => 'Upload Roster',
                         'access'   => $_SYS['user']['admin']));

/*** error pages ***/

$_SYS['util']->addpage(array('name'     => 'error404',
                         'url'      => '/error404.html',
                         'document' => '/error404.php',
                         'title'    => 'Error 404'));

$_SYS['util']->addpage(array('name'     => 'error403',
                         'url'      => '/error403.html',
                         'document' => '/error403.php',
                         'title'    => 'Error 403'));

$_SYS['util']->addpage(array('name'     => 'sorry',
                         'url'      => '/sorry.html',
                         'document' => '/sorry.php',
                         'title'    => 'Sorry'));


?>