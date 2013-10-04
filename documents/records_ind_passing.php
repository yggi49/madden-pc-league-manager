<?php

/* INDIVIDUAL - PASSING - PASSER RATING */

if ($season == 'all') {
  $_attempts = 1500;
  if ($period == 'bowl') $_attempts = 20;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 150;

  $_query = 'SELECT ROUND(rating, 1)  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`rating`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`rating`                                AS `set_prev`,
                              s.`rating`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, ROUND((LEAST(GREATEST(0, 5 * SUM(s.`cmp`) / SUM(s.`att`) - 3/2), 19/8) + LEAST(GREATEST(0, SUM(s.`yds`) / 4 / SUM(s.`att`) - 3/4), 19/8) + LEAST(20 * SUM(s.`td`) / SUM(s.`att`), 19/8) + GREATEST(19/8 - 25 * SUM(s.`int`) / SUM(s.`att`), 0)) / 6 * 100, 1) AS rating, SUM(att) AS att FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY rating DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Highest Passer Rating'][] = array('title' => 'Highest Passer Rating, Career ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_attempts = 200;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 50;

  $_query = 'SELECT ROUND(rating, 1)  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`rating`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`rating`                                AS `set_prev`,
                              s.`rating`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, ROUND((LEAST(GREATEST(0, 5 * SUM(s.`cmp`) / SUM(s.`att`) - 3/2), 19/8) + LEAST(GREATEST(0, SUM(s.`yds`) / 4 / SUM(s.`att`) - 3/4), 19/8) + LEAST(20 * SUM(s.`td`) / SUM(s.`att`), 19/8) + GREATEST(19/8 - 25 * SUM(s.`int`) / SUM(s.`att`), 0)) / 6 * 100, 1) AS rating, SUM(att) AS att, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY rating DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Highest Passer Rating'][] = array('title' => 'Highest Passer Rating, Season ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

/* INDIVIDUAL - PASSING - ATTEMPTS */

if ($season == 'all') {
  $_query = 'SELECT att  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`att`                                AS `set_prev`,
                              s.`att`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(att) AS att FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY att DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Attempts'][] = array('title' => 'Most Passes Attempted, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT att  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`att`                                AS `set_prev`,
                              s.`att`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(att) AS att, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY att DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Attempts'][] = array('title' => 'Most Passes Attempted, Season', 'query' => $_query);
}

$_query = 'SELECT `att` AS value,
                  name  AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`att`                                AS `set_prev`,
                            s.`att`, s.`name`, s.game, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_passing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY att DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Attempts'][] = array('title' => 'Most Passes Attempted, Game', 'query' => $_query);

/* INDIVIDUAL - PASSING - COMPLETIONS */

if ($season == 'all') {
  $_query = 'SELECT cmp  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`cmp`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`cmp`                                AS `set_prev`,
                              s.`cmp`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(cmp) AS cmp FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY cmp DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Completions'][] = array('title' => 'Most Passes Completed, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT cmp  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`cmp`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`cmp`                                AS `set_prev`,
                              s.`cmp`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(cmp) AS cmp, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY cmp DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Completions'][] = array('title' => 'Most Passes Completed, Season', 'query' => $_query);
}

$_query = 'SELECT `cmp` AS value,
                  name  AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`cmp`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`cmp`                                AS `set_prev`,
                            s.`cmp`, s.`name`, s.game, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_passing'].' AS s
                   WHERE    '.$where.'
                   ORDER BY cmp DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Completions'][] = array('title' => 'Most Passes Completed, Game', 'query' => $_query);

/* INDIVIDUAL - PASSING - COMPLETION PERCENTAGE */

if ($season == 'all') {
  $_attempts = 1500;
  if ($period == 'bowl') $_attempts = 20;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 150;

  $_query = 'SELECT ROUND(ypc, 2)  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`ypc`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypc`                                AS `set_prev`,
                              s.`ypc`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, 100 * SUM(cmp)/SUM(att) AS ypc, SUM(att) AS att FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ypc DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Completion Percentage'][] = array('title' => 'Highest Completion Percentage, Career ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_attempts = 200;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 50;

  $_query = 'SELECT ROUND(ypc, 2)  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`ypc`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypc`                                AS `set_prev`,
                              s.`ypc`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, 100 * SUM(cmp)/SUM(att) AS ypc, SUM(att) AS att, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ypc DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Completion Percentage'][] = array('title' => 'Highest Completion Percentage, Season ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

$_query = 'SELECT ROUND(`ypc`, 2) AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`ypc`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`ypc`                                AS `set_prev`,
                            s.`game`, s.`ypc` AS `ypc`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT s.name, 100 * s.cmp/s.att AS ypc, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE  ('.$where.') AND s.`att` >= 20) AS s
                   ORDER BY ypc DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Completion Percentage'][] = array('title' => 'Highest Completion Percentage, Game (20&nbsp;attempts)', 'query' => $_query);

/* INDIVIDUAL - PASSING - YARDS GAINED */

if ($season == 'all') {
  $_query = 'SELECT yds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds) AS yds FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
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
                              (SELECT name, SUM(IF(yds >= 3000, 1, 0)) AS yds FROM (SELECT name, SUM(yds) AS yds FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Seasons, 3,000 or More Passing Yards', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT yds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds) AS yds, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY yds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Yards Gained'][] = array('title' => 'Most Yards Gained, Season', 'query' => $_query);
}

$_query = 'SELECT `yds` AS value,
                  name  AS name,
                  game  AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`yds`                                AS `set_prev`,
                            s.`game`, s.`yds`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT s.name, s.yds, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE    '.$where.') AS s
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
                              (SELECT name, SUM(IF(yds >= 400, 1, 0)) AS yds FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 400 or More Passing Yards, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 400, 1, 0)) AS yds, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY myyds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 400 or More Passing Yards, Season', 'query' => $_query);
}

if ($season == 'all' && $period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 300, 1, 0)) AS yds FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 300 or More Passing Yards, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(yds >= 300, 1, 0)) AS yds, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY myyds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Yards Gained'][] = array('title' => 'Most Games, 300 or More Passing Yards, Season', 'query' => $_query);
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
                            (SELECT s.name, s.`long`, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE    '.$where.') AS s
                   ORDER BY `long` DESC, season DESC, week DESC, `name`) AS t
           WHERE  t.`rank` <= 3';
$queries['Yards Gained'][] = array('title' => 'Longest Pass Completion, Game', 'query' => $_query);

/* INDIVIDUAL - PASSING - AVERAGE GAIN */

if ($season == 'all') {
  $_attempts = 1500;
  if ($period == 'bowl') $_attempts = 20;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 150;

  $_query = 'SELECT ROUND(ypa, 2)  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`ypa`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypa`                                AS `set_prev`,
                              s.`ypa`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds)/SUM(att) AS ypa, SUM(att) AS att FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ypa DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Average Gain'][] = array('title' => 'Highest Average Gain, Career ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_attempts = 200;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 50;

  $_query = 'SELECT ROUND(ypa, 2)  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`ypa`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypa`                                AS `set_prev`,
                              s.`ypa`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(yds)/SUM(att) AS ypa, SUM(att) AS att, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ypa DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Average Gain'][] = array('title' => 'Highest Average Gain, Season ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

$_query = 'SELECT ROUND(`ypa`, 2) AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`ypa`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`ypa`                                AS `set_prev`,
                            s.`game`, s.`ypa`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT s.name, s.yds/s.att AS ypa, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE  ('.$where.') AND s.`att` >= 20) AS s
                   ORDER BY ypa DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Average Gain'][] = array('title' => 'Highest Average Gain, Game (20&nbsp;attempts)', 'query' => $_query);

/* INDIVIDUAL - PASSING - TOUCHDOWNS  */

if ($season == 'all') {
  $_query = 'SELECT `td`  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`td`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`td`                                AS `set_prev`,
                              s.`td`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`td`) AS `td` FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY `td` DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Touchdowns'][] = array('title' => 'Most Touchdown Passes, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT `td`  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`td`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`td`                                AS `set_prev`,
                              s.`td`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`td`) AS `td`, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY `td` DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Touchdowns'][] = array('title' => 'Most Touchdown Passes, Season', 'query' => $_query);
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
                            (SELECT s.name, s.td, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE    '.$where.') AS s
                   ORDER BY `td` DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Touchdowns'][] = array('title' => 'Most Touchdown Passes, Game', 'query' => $_query);

if ($season == 'all' && $period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(td >= 4, 1, 0)) AS yds FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY myyds DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Touchdowns'][] = array('title' => 'Most Games, Four or More Touchdown Passes, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT myyds  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`yds`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`yds`                                AS `set_prev`,
                              s.`yds` AS myyds, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(td >= 4, 1, 0)) AS yds, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY myyds DESC, season DESC, name) AS t
             WHERE  t.rank <= 3 AND myyds > 0';
  $queries['Touchdowns'][] = array('title' => 'Most Games, Four or More Touchdown Passes, Season', 'query' => $_query);
}

/* INDIVIDUAL - PASSING - HAD INTERCEPTED  */

if ($season == 'all') {
  $_query = 'SELECT `int`  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`int`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`int`                                AS `set_prev`,
                              s.`int`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`int`) AS `int` FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY `int` DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Had Intercepted'][] = array('title' => 'Most Passes Had Intercepted, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT `int`  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`int`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`int`                                AS `set_prev`,
                              s.`int`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`int`) AS `int`, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY `int` DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Had Intercepted'][] = array('title' => 'Most Passes Had Intercepted, Season', 'query' => $_query);
}

$_query = 'SELECT `int` AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`int`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`int`                                AS `set_prev`,
                            s.`game`, s.`int`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT s.name, s.`int`, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE    '.$where.') AS s
                   ORDER BY `int` DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Had Intercepted'][] = array('title' => 'Most Passes Had Intercepted, Game', 'query' => $_query);

$_query = 'SELECT `att` AS value,
                  name  AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`att`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`att`                                AS `set_prev`,
                            s.`att`, s.`name`, s.game, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            '.$_SYS['table']['stats_passing'].' AS s
                   WHERE    ('.$where.') AND s.`int` = 0
                   ORDER BY att DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Had Intercepted'][] = array('title' => 'Most Attempts, No Interceptions, Game', 'query' => $_query);

/* INDIVIDUAL - PASSING - LOWEST PERCENTAGE, PASSES HAD INTERCEPTED */

if ($season == 'all') {
  $_attempts = 1500;
  if ($period == 'bowl') $_attempts = 20;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 150;

  $_query = 'SELECT ROUND(ipa, 2)  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`ipa`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ipa`                                AS `set_prev`,
                              s.`ipa`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, 100 * SUM(`int`)/SUM(att) AS ipa, SUM(att) AS att FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ipa ASC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Lowest Percentage, Passes Had Intercepted'][] = array('title' => 'Lowest Percentage, Passes Had Intercepted, Career ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_attempts = 200;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 50;

  $_query = 'SELECT ROUND(ipa, 2)  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`ipa`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ipa`                                AS `set_prev`,
                              s.`ipa`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, 100 * SUM(`int`)/SUM(att) AS ipa, SUM(att) AS att, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     WHERE    s.`att` >= '.$_attempts.'
                     ORDER BY ipa ASC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Lowest Percentage, Passes Had Intercepted'][] = array('title' => 'Lowest Percentage, Passes Had Intercepted, Season ('.number_format($_attempts).'&nbsp;attempts)', 'query' => $_query);
}

/* INDIVIDUAL - PASSING - TIMES SACKED  */

if ($season == 'all') {
  $_query = 'SELECT `sack`  AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`sack`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`sack`                                AS `set_prev`,
                              s.`sack`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`sack`) AS `sack` FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name) AS s
                     ORDER BY `sack` DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Times Sacked'][] = array('title' => 'Most Times Sacked, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT `sack`  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`sack`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`sack`                                AS `set_prev`,
                              s.`sack`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(`sack`) AS `sack`, season FROM '.$_SYS['table']['stats_passing'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     ORDER BY `sack` DESC, season DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Times Sacked'][] = array('title' => 'Most Times Sacked, Season', 'query' => $_query);
}

$_query = 'SELECT `sack` AS value,
                  name AS name,
                  game AS game,
                  season AS season,
                  week AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`sack`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`sack`                                AS `set_prev`,
                            s.`game`, s.`sack`, s.`name`, s.season, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT s.name, s.sack, s.season, s.game, s.week, s.matchup
                             FROM   '.$_SYS['table']['stats_passing'].' AS s
                             WHERE    '.$where.') AS s
                   ORDER BY `sack` DESC, season DESC, week DESC, name) AS t
           WHERE  t.rank <= 3';
$queries['Times Sacked'][] = array('title' => 'Most Times Sacked, Game', 'query' => $_query);

?>