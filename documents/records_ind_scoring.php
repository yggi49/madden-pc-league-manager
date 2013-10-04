<?php

/* create temporary table for points overall */

$_query = 'CREATE TEMPORARY TABLE tmp_points (
             name    VARCHAR(50)           NOT NULL  DEFAULT "",
             season  TINYINT(3)  UNSIGNED  NOT NULL  DEFAULT 0,
             week    TINYINT(4)            NOT NULL  DEFAULT 0,
             game    INT(10)     UNSIGNED  NOT NULL  DEFAULT 0,
             matchup VARCHAR(10)           NOT NULL  DEFAULT "",
             points  INT(10)     UNSIGNED  NOT NULL  DEFAULT 0)';
$queries['__init__'][] = array('title' => '', 'query' => $_query);

$_query = 'INSERT INTO tmp_points
           SELECT name, season, week, game, matchup, td * 6 AS points FROM '.$_SYS['table']['stats_rushing'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td * 6 AS points FROM '.$_SYS['table']['stats_receiving'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td * 6 AS points FROM '.$_SYS['table']['stats_kick_returns'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td * 6 AS points FROM '.$_SYS['table']['stats_punt_returns'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td * 6 AS points FROM '.$_SYS['table']['stats_defense'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, safeties * 2 AS points FROM '.$_SYS['table']['stats_defense'].' WHERE safeties > 0
           UNION ALL
           SELECT name, season, week, game, matchup, fgm * 3 AS points FROM '.$_SYS['table']['stats_kicking'].' WHERE fgm > 0
           UNION ALL
           SELECT name, season, week, game, matchup, xpm * 1 AS points FROM '.$_SYS['table']['stats_kicking'].' WHERE xpm > 0';
$queries['__init__'][] = array('title' => '', 'query' => $_query);

/* create temporary table for touchdowns */

$_query = 'CREATE TEMPORARY TABLE tmp_touchdowns (
             name    VARCHAR(50)           NOT NULL  DEFAULT "",
             season  TINYINT(3)  UNSIGNED  NOT NULL  DEFAULT 0,
             week    TINYINT(4)            NOT NULL  DEFAULT 0,
             game    INT(10)     UNSIGNED  NOT NULL  DEFAULT 0,
             matchup VARCHAR(10)           NOT NULL  DEFAULT "",
             points  INT(10)     UNSIGNED  NOT NULL  DEFAULT 0)';
$queries['__init__'][] = array('title' => '', 'query' => $_query);

$_query = 'INSERT INTO tmp_touchdowns
           SELECT name, season, week, game, matchup, td FROM '.$_SYS['table']['stats_rushing'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td FROM '.$_SYS['table']['stats_receiving'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td FROM '.$_SYS['table']['stats_kick_returns'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td FROM '.$_SYS['table']['stats_punt_returns'].' WHERE td > 0
           UNION ALL
           SELECT name, season, week, game, matchup, td FROM '.$_SYS['table']['stats_defense'].' WHERE td > 0';
$queries['__init__'][] = array('title' => '', 'query' => $_query);

/* create temporary table for no-td points */

$_query = 'CREATE TEMPORARY TABLE tmp_no_td (
             name    VARCHAR(50)           NOT NULL  DEFAULT "",
             season  TINYINT(3)  UNSIGNED  NOT NULL  DEFAULT 0,
             week    TINYINT(4)            NOT NULL  DEFAULT 0,
             game    INT(10)     UNSIGNED  NOT NULL  DEFAULT 0,
             matchup VARCHAR(10)           NOT NULL  DEFAULT "",
             points  INT(10)     UNSIGNED  NOT NULL  DEFAULT 0)';
$queries['__init__'][] = array('title' => '', 'query' => $_query);

$_query = 'INSERT INTO tmp_no_td
           SELECT name, season, week, game, matchup, safeties * 2 AS points FROM '.$_SYS['table']['stats_defense'].' WHERE safeties > 0
           UNION ALL
           SELECT name, season, week, game, matchup, fgm * 3 AS points FROM '.$_SYS['table']['stats_kicking'].' WHERE fgm > 0
           UNION ALL
           SELECT name, season, week, game, matchup, xpm * 1 AS points FROM '.$_SYS['table']['stats_kicking'].' WHERE xpm > 0';
$queries['__init__'][] = array('title' => '', 'query' => $_query);

/* INDIVIDUAL - SCORING - POINTS */

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(points) AS points
                               FROM     tmp_points AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points'][] = array('title' => 'Most Points, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(points) AS points, season
                               FROM     tmp_points AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points'][] = array('title' => 'Most Points, Season', 'query' => $_query);

  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(points) AS points, season
                               FROM     tmp_no_td AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points'][] = array('title' => 'Most Points, No Touchdowns, Season', 'query' => $_query);
}

if ($season == 'all' && ($period == 'reg' || $period == 'all')) {
  $_query = 'SELECT points AS value,
                    name AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points` AS points, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, SUM(IF(points >= 100, 1, 0)) AS points FROM (SELECT name, SUM(points) AS points FROM tmp_points AS s WHERE '.$where.' GROUP BY name, season) AS s GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
             WHERE  t.rank <= 3 AND t.points > 0';
  $queries['Points'][] = array('title' => 'Most Seasons, 100 or More Points', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(points) AS points, season, game, week, matchup
                             FROM     tmp_points AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Points'][] = array('title' => 'Most Points, Game', 'query' => $_query);

/* INDIVIDUAL - SCORING - TOUCHDOWNS */

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(points) AS points
                               FROM     tmp_touchdowns AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Touchdowns'][] = array('title' => 'Most Touchdowns, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(points) AS points, season
                               FROM     tmp_touchdowns AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Touchdowns'][] = array('title' => 'Most Touchdowns, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(points) AS points, season, game, week, matchup
                             FROM     tmp_touchdowns AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Touchdowns'][] = array('title' => 'Most Touchdowns, Game', 'query' => $_query);

/* INDIVIDUAL - SCORING - POINTS AFTER TOUCHDOWN */

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(xpa) AS points
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points After Touchdown'][] = array('title' => 'Most (Kicking) Points After Touchdown Attempted, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(xpa) AS points, season
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points After Touchdown'][] = array('title' => 'Most (Kicking) Points After Touchdown Attempted, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(xpa) AS points, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_kicking'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Points After Touchdown'][] = array('title' => 'Most (Kicking) Points After Touchdown Attempted, Game', 'query' => $_query);

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(xpm) AS points
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points After Touchdown'][] = array('title' => 'Most (One-Point) Points After Touchdown, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(xpm) AS points, season
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Points After Touchdown'][] = array('title' => 'Most (One-Point) Points After Touchdown, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(xpm) AS points, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_kicking'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Points After Touchdown'][] = array('title' => 'Most (One-Point) Points After Touchdown, Game', 'query' => $_query);

if ($season == 'all' && ($period == 'all' || $period == 'reg')) {
  $_query = 'SELECT ROUND(points, 2) AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, 100 * SUM(xpm)/SUM(xpa) AS points, SUM(xpm) AS xpm
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     WHERE    s.xpm >= 200
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3';
  $queries['Points After Touchdown'][] = array('title' => 'Highest (Kicking) Points After Touchdown Percentage, Career (200 points after touchdown)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(xpm) AS points, SUM(xpa) AS xpa, season
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     WHERE    s.points = s.xpa AND s.points > 0
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3';
  $queries['Points After Touchdown'][] = array('title' => 'Most (Kicking) Points After Touchdown, No Misses, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(xpm) AS points, SUM(xpa) AS xpa, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_kicking'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   WHERE    s.points = s.xpa AND s.points > 0
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3';
$queries['Points After Touchdown'][] = array('title' => 'Most (Kicking) Points After Touchdown, No Misses, Game', 'query' => $_query);

/* INDIVIDUAL - SCORINT - FIELD GOALS */

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(fga) AS points
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Field Goals'][] = array('title' => 'Most Field Goals Attempted, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(fga) AS points, season
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Field Goals'][] = array('title' => 'Most Field Goals Attempted, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(fga) AS points, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_kicking'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Field Goals'][] = array('title' => 'Most Field Goals Attempted, Game', 'query' => $_query);

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(fgm) AS points
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Field Goals'][] = array('title' => 'Most Field Goals, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(fgm) AS points, season
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Field Goals'][] = array('title' => 'Most Field Goals, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(fgm) AS points, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_kicking'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Field Goals'][] = array('title' => 'Most Field Goals, Game', 'query' => $_query);

if ($season == 'all' && ($period == 'all' || $period == 'reg')) {
  $_query = 'SELECT ROUND(points, 2) AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, 100 * SUM(fgm)/SUM(fga) AS points, SUM(fgm) AS fgm
                               FROM     '.$_SYS['table']['stats_kicking'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     WHERE    s.fgm >= 100
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3';
  $queries['Field Goals'][] = array('title' => 'Highest Field Goal Percentage, Career (100 field goals)', 'query' => $_query);
}

if ($period != 'bowl') {
  $_attempts = 16;
  if ($period == 'pre' || $period == 'post' || $period == 'ex') $_attempts = 4;

  $_query = 'SELECT ROUND(ypc, 2)  AS value,
                    name AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`ypc`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`ypc`                                AS `set_prev`,
                              s.`ypc`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT name, 100 * SUM(fgm)/SUM(fga) AS ypc, SUM(fgm) AS fgm, season FROM '.$_SYS['table']['stats_kicking'].' AS s WHERE '.$where.' GROUP BY name, season) AS s
                     WHERE    s.`fgm` >= '.$_attempts.'
                     ORDER BY ypc DESC, name) AS t
             WHERE  t.rank <= 3';
  $queries['Field Goals'][] = array('title' => 'Highest Field Goal Percentage, Season ('.number_format($_attempts).'&nbsp;field goals)', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(fgm) AS points, SUM(fga) AS fga, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_kicking'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   WHERE    s.points = s.fga AND s.points > 0
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3';
$queries['Field Goals'][] = array('title' => 'Most Field Goals, No Misses, Game', 'query' => $_query);

/* INDIVIDUAL - SCORING - SAFETIES */

if ($season == 'all') {
  $_query = 'SELECT points AS value,
                    name   AS name
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(safeties) AS points
                               FROM     '.$_SYS['table']['stats_defense'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name) AS s
                     ORDER BY points DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Safeties'][] = array('title' => 'Most Safeties, Career', 'query' => $_query);
}

if ($period != 'bowl') {
  $_query = 'SELECT points AS value,
                    name   AS name,
                    season AS season
             FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                              @prev := s.`points`                                AS `set_prev`,
                              s.`points`, s.`name`, s.season
                     FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                              (SELECT   name, SUM(safeties) AS points, season
                               FROM     '.$_SYS['table']['stats_defense'].' AS s
                               WHERE    '.$where.'
                               GROUP BY name, season) AS s
                     ORDER BY points DESC, season DESC, name) AS t
              WHERE t.rank <= 3 AND t.points > 0';
  $queries['Safeties'][] = array('title' => 'Most Safeties, Season', 'query' => $_query);
}

$_query = 'SELECT points  AS value,
                  name    AS name,
                  game    AS game,
                  season  AS season,
                  week    AS week,
                  matchup AS matchup
           FROM   (SELECT   IF(@prev != s.`points`, @rank := @rank + 1, @rank) AS `rank`,
                            @prev := s.`points`                                AS `set_prev`,
                            s.`points`, s.`name`, s.season, s.game, s.week, s.matchup
                   FROM     (SELECT @rank := 0, @prev := 10000) AS r,
                            (SELECT   name, SUM(safeties) AS points, season, game, week, matchup
                             FROM     '.$_SYS['table']['stats_defense'].' AS s
                             WHERE    '.$where.'
                             GROUP BY name, game) AS s
                   ORDER BY points DESC, season DESC, week DESC, name) AS t
            WHERE t.rank <= 3 AND t.points > 0';
$queries['Safeties'][] = array('title' => 'Most Safeties, Game', 'query' => $_query);

?>