<?php
/*
 * @(#) pages.php
 */

$_SYS['util']->addpage(array('name'     => 'forum',
                         'url'      => 'http://www.schmejkal.de/IMT/phpBB3/portal.php',
                         'document' => '',
                         'title'    => "Forum"));

$_SYS['util']->addpage(array('name'     => 'mvp',
                         'url'      => '/mvp.html',
                         'document' => '/mvp.php',
                         'title'    => 'MVP',
//                          'access'   => $_SYS['user']['team'][$_SYS['request']['season']]));
                         'access'   => false));

?>
