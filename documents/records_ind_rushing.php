<?php

/* INDIVIDUAL - RUSHING - ATTEMPTS */

if ($season == 'all') {
  $_query = 'SELECT att  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`att`                                AS `set_prev`,
                              s.`att`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(att) AS att FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY att DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Attempts'][] = array('title' => 'Most Attempts, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT att  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`att`                                AS `set_prev`,
                              s.`att`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(att) AS att, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY att DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Attempts'][] = array('title' => 'Most Attempts, Season', 'query' => $_query);
}

$_query = 'SELECT `att` AS value,
                  name  AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`att`                                AS `set_prev`,
                            s.`game`, s.`att`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_rushing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY att DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Attempts'][] = array('title' => 'Most Attempts, Game', 'query' => $_query);

/* INDIVIDUAL - RUSHING - YARDS GAINED */

if ($season == 'all') {
  $_query = 'SELECT yds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds) AS yds FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY yds DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Yards Gained'][] = array('title' => 'Most Yards Gained, Career', 'query' => $_query);
}

if ($season == 'all' && ($period == 'reg' || $period == 'all')) {
  $_query = 'SELECT myyds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 1000, 1, 0)) AS yds FROM (SELECT name, SUM(yds) AS yds FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Seasons, 1,000 or More Yards Rushing', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT yds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds) AS yds, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY yds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Yards Gained'][] = array('title' => 'Most Yards Gained, Season', 'query' => $_query);
}

$_query = 'SELECT `yds` AS value,
                  name  AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`yds`                                AS `set_prev`,
                            s.`game`, s.`yds`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_rushing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY yds DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Yards Gained'][] = array('title' => 'Most Yards Gained, Game', 'query' => $_query);

if ($season == 'all' && $period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 200, 1, 0)) AS yds FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 200 or More Yards Rushing, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 200, 1, 0)) AS yds, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY myyds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 200 or More Yards Rushing, Season', 'query' => $_query);
}

if ($season == 'all' && $period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 100, 1, 0)) AS yds FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 100 or More Yards Rushing, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 100, 1, 0)) AS yds, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY myyds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 100 or More Yards Rushing, Season', 'query' => $_query);
}

$_query = 'SELECT `long` AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`long`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`long`                                AS `set_prev`,
                            s.`game`, s.`long`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 100) AS r,
                            '.$_SYS['table']['stats_rushing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY `long` DESC, season DESC, week DESC, `name`) AS t
           WHERE  t.`rank` <= 3';
$queries['Yards Gained'][] = array('title' => 'Longest Run From Scrimmage, Game', 'query' => $_query);

/* INDIVIDUAL - RUSHING - AVERAGE GAIN */

if ($season == 'all') {
  $_attempts = 750;
  if ($period == 'bowl') $_attempts = 10;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 75;

  $_query = 'SELECT ROUND(ypa, 2)  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`ypa`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypa`                                AS `set_prev`,
                              s.`ypa`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds)/SUM(att) AS ypa, SUM(att) AS att FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ypa DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Highest Average Gain'][] = array('title' => 'Highest Average Gain, Career ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_attempts = 100;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 25;

  $_query = 'SELECT ROUND(ypa, 2)  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`ypa`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypa`                                AS `set_prev`,
                              s.`ypa`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds)/SUM(att) AS ypa, SUM(att) AS att, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ypa DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Highest Average Gain'][] = array('title' => 'Highest Average Gain, Season ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

$_query = 'SELECT ROUND(`ypa`, 2) AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`yds`/s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`yds`/s.`att`                                AS `set_prev`,
                            s.`game`, s.`yds`/s.`att` AS `ypa`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_rushing'].' AS s
                   WHERE    ('.$where.') AND s.`att` >= 10
                   ORDER BY ypa DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Highest Average Gain'][] = array('title' => 'Highest Average Gain, Game (10&nbsp;attempts)', 'query' => $_query);

/* INDIVIDUAL - RUSHING - TOUCHDOWNS  */

if ($season == 'all') {
  $_query = 'SELECT `td`  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`td`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`td`                                AS `set_prev`,
                              s.`td`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`td`) AS `td` FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY `td` DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Touchdowns'][] = array('title' => 'Most Touchdowns, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT `td`  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`td`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`td`                                AS `set_prev`,
                              s.`td`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`td`) AS `td`, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY `td` DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Touchdowns'][] = array('title' => 'Most Touchdowns, Season', 'query' => $_query);
}

$_query = 'SELECT `td` AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`td`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`td`                                AS `set_prev`,
                            s.`game`, s.`td`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_rushing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY `td` DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Touchdowns'][] = array('title' => 'Most Touchdowns, Game', 'query' => $_query);

/* INDIVIDUAL - RUSHING - FUMBLES  */

if ($season == 'all') {
  $_query = 'SELECT `fum`  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`fum`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`fum`                                AS `set_prev`,
                              s.`fum`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`fum`) AS `fum` FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY `fum` DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Fumbles'][] = array('title' => 'Most Fumbles, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT `fum`  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`fum`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`fum`                                AS `set_prev`,
                              s.`fum`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`fum`) AS `fum`, season FROM '.$_SYS['table']['stats_rushing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY `fum` DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Fumbles'][] = array('title' => 'Most Fumbles, Season', 'query' => $_query);
}

$_query = 'SELECT `fum` AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`fum`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`fum`                                AS `set_prev`,
                            s.`game`, s.`fum`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_rushing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY `fum` DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Fumbles'][] = array('title' => 'Most Fumbles, Game', 'query' => $_query);

?>