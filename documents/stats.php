<?php
/**
 * @(#) stats.php
 *
 * $_GET['period'] => gibt an ueber welchen zeitraum sich die stats erstrecken
 *                    moegliche werte:
 *                    - eine zahl != 0 => zeigt die stats der angegebenen woche
 *                    - einer der keys aus dem array $this->periods
 *
 * $_GET['cat']    => gibt an welche kategorie angezeigt werden soll.
 *                    moegliche werte: siehe array keys in $this->categories
 *
 * $_GET['avg']    => 1 oder 0 (true oder false): gibt an ob durchschnittswerte
 *                    (true) oder gesamtsummen (false) angezeigt werden sollen
 *
 * $_GET['order1'] => gibt jene spalte an nach der primaer sortiert werden soll
 * $_GET['type1']  => ASC oder DESC: gibt an ob die primaere sortierreihenfolge
 *                    aufsteigend oder absteigend ist
 *
 * analog: $_GET['order2'], $_GET['type2'] und $_GET['order3'], $_GET['type3']
 */

class Page {

  var $periods = array('all'  => 'Overall',
                       'ex'   => 'Exhibitions',
                       'pre'  => 'Preseason',
                       'reg'  => 'Regular Season',
                       'post' => 'Postseason');

  var $categories = array('ind'  => array('name'       => 'Individual',
                                          'categories' => array('fantasy'     => array('name'  => 'Fantasy',
                                                                                       'order' => array('column' => 'points', 'type' => 'DESC')),
                                                                'passing'     => array('name'  => 'Passing',
                                                                                       'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'rushing'     => array('name'  => 'Rushing',
                                                                                       'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'receiving'   => array('name'  => 'Receiving',
                                                                                       'order' => array('column' => 'yds', 'type' => 'DESC'),
                                                                                       'break' => 1),
                                                                'kicking'     => array('name'  => 'Kicking',
                                                                                       'order' => array('column' => 'points', 'type' => 'DESC')),
                                                                'punting'     => array('name'  => 'Punting',
                                                                                       'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'kickreturns' => array('name'  => 'Kick Returns',
                                                                                       'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'puntreturns' => array('name'  => 'Punt Returns',
                                                                                       'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'defense'     => array('name'  => 'Defense',
                                                                                       'order' => array('column' => 'tack', 'type' => 'DESC')),
                                                                'blocking'    => array('name'  => 'Blocking',
                                                                                       'order' => array('column' => 'pancakes', 'type' => 'DESC'),
                                                                                       'end'   => 1))),
                          'team' => array('name'       => 'Team',
                                          'categories' => array('offtotal'   => array('name'  => 'Total Offense',
                                                                                      'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'offpass'    => array('name'  => 'Passing Offense',
                                                                                      'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'offrush'    => array('name'  => 'Rushing Offense',
                                                                                      'order' => array('column' => 'yds', 'type' => 'DESC')),
                                                                'offeff'     => array('name'  => 'O# Efficiency',
                                                                                      'order' => array('column' => 'rz%', 'type' => 'DESC')),
                                                                'offscoring' => array('name'  => 'Scoring Offense',
                                                                                      'order' => array('column' => 'pts', 'type' => 'DESC'),
                                                                                      'break' => 1),
                                                                'deftotal'   => array('name'  => 'Total Defense',
                                                                                      'order' => array('column' => 'yds', 'type' => 'ASC')),
                                                                'defpass'    => array('name'  => 'Passing Defense',
                                                                                      'order' => array('column' => 'yds', 'type' => 'ASC')),
                                                                'defrush'    => array('name'  => 'Rushing Defense',
                                                                                      'order' => array('column' => 'yds', 'type' => 'ASC')),
                                                                'defeff'     => array('name'  => 'D# Efficiency',
                                                                                      'order' => array('column' => 'rz%', 'type' => 'ASC')),
                                                                'defscoring' => array('name'  => 'Scoring Defense',
                                                                                      'order' => array('column' => 'pts', 'type' => 'ASC')),
                                                                'turnover'   => array('name'  => 'Turnover',
                                                                                      'order' => array('column' => 'ratio', 'type' => 'DESC'),
                                                                                      'end'   => 1))));


  /** ------------------------------------------------------------------------
   * constructor
   * -------------------------------------------------------------------------
   */
  function Page() {} // constructor


  /** ------------------------------------------------------------------------
   * returns header information for this page
   * -------------------------------------------------------------------------
   */
  function getHeader() {
    return '';
  } // getHeader()


  /** ------------------------------------------------------------------------
   * returns a query for the specified stats category
   * -------------------------------------------------------------------------
   */
  function _getQuery($category, $averages, $period, $order, $filter) {
    global $_SYS;

    if ($period == 'ex') {
      $weeks = '= 0';
    } elseif ($period == 'pre') {
      $weeks = '< 0';
    } elseif ($period == 'reg') {
      $weeks = 'IN ('.join(', ', $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg']).')';
    } elseif ($period == 'post') {
      $weeks = '> '.$_SYS['season'][$_SYS['request']['season']]['reg_weeks'];
    } elseif ($period == 'all') {
      $_pre_weeks = $_SYS['season'][$_SYS['request']['season']]['pre_weeks'] > 0 ? range(-1, -$_SYS['season'][$_SYS['request']['season']]['pre_weeks']) : array();
      $_reg_weeks = $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg'];
      $_post_weeks = $_SYS['season'][$_SYS['request']['season']]['post_weeks'] > 0 ? range($_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + 1, $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] + $_SYS['season'][$_SYS['request']['season']]['post_weeks']) : array();

      $weeks = 'IN (0, '.join(', ', array_merge($_pre_weeks, $_reg_weeks, $_post_weeks)).')';
      unset($_pre_weeks, $_reg_weeks, $_post_weeks);
    } elseif (intval($period) != 0) {
      $period = intval($period);

      if ($period > 0 && $period <= $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] && !in_array($period, $_SYS['season'][$_SYS['request']['season']]['visible_weeks']['reg'])) {
        $weeks = 'IS NULL';
      } else {
        $weeks = '= '.$period;
      }
    } else {
      return false;
    }

    $_order = array();

    foreach ($order as $_item) {
      $_order[] = '`'.$_item['column'].'` '.$_item['type'];
    }

    $order = join(', ', $_order);

    if ($order == '') {
      return false;
    }

    unset($_order, $_item);

    $_filter = $filter;

    $filter = array();

    foreach (array_keys($_filter) as $_type) {
      foreach ($_filter[$_type] as $_col => $_val) {
        switch ($_type) {
        case 'min':
          $filter[] = '`'.$_col.'` >= "'.$_val.'"';
          break;
        case 'max':
          $filter[] = '`'.$_col.'` <= "'.$_val.'"';
          break;
        case 'filter':
          $_search = explode('|', $_val);
          $_items = array();

          foreach ($_search as $_item) {
            if ($_col == 'team') {
              if (array_key_exists($category, $this->categories['ind']['categories'])) {
                $_item = strtoupper($_item);
              } else {
                preg_match('/^([^A-Za-z0-9]*)([A-Za-z0-9]?)(.*)$/', $_item, $_matches);
                $_item = strtolower($_matches[1]).strtoupper($_matches[2]).strtolower($_matches[3]);
                unset($_matches);
              }
            }

            $_re = str_replace(array('%', '_', '*'), array('\%', '\_', '%'), $_item);

            $_items[] = '`'.$_col.'` LIKE "'.($_col == 'team' ? '%:' : '').$_re.'"';
          }

          $filter[] = '('.join(' OR ', $_items).')';
          break;
        }
      }
    }

    $filter = count($filter) ? 'HAVING   '.join(' AND ', $filter) : '';

    unset($_filter, $_minmax, $_col, $_val, $_search, $_items, $_item);

    if ($category == 'fantasy' && !$averages) {
      return 'SELECT   `name`                 AS `name`,
                       `team`                 AS `team`,
                       `user`                 AS `user`,
                       COUNT(DISTINCT `game`) AS `g`,
                       SUM(`yds`)             AS `yds`,
                       SUM(`td`)              AS `td`,
                       SUM(`int`)             AS `int`,
                       SUM(`points`)          AS `points`
              FROM     (SELECT   s.`name` AS `name`,
                                 CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                                 t.`user` AS `user`,
                                 s.`game` AS `game`,
                                 s.`yds`  AS `yds`,
                                 s.`td`   AS `td`,
                                 s.`int`  AS `int`,
                                 ROUND(FLOOR(CAST(s.`yds` AS SIGNED) / 2) / 10 + CAST(s.`td` AS SIGNED) * 6 - CAST(s.`int` AS SIGNED) * 3, 1) AS `points`
                        FROM     '.$_SYS['table']['stats_passing'].'  AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.`name` AS `name`,
                                 CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                                 t.`user` AS `user`,
                                 s.`game` AS `game`,
                                 s.`yds`  AS `yds`,
                                 s.`td`   AS `td`,
                                 0        AS `int`,
                                 ROUND(s.`yds` / 10 + s.`td` * 6, 1) AS `points`
                        FROM     '.$_SYS['table']['stats_receiving'].'   AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.`name` AS `name`,
                                 CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                                 t.`user` AS `user`,
                                 s.`game` AS `game`,
                                 s.`yds`  AS `yds`,
                                 s.`td`   AS `td`,
                                 0        AS `int`,
                                 ROUND(s.`yds` / 10 + s.`td` * 6, 1) AS `points`
                        FROM     '.$_SYS['table']['stats_rushing'].'   AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].') AS tmp
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'fantasy' && $averages) {
      return 'SELECT   `name`                                           AS `name`,
                       `team`                                           AS `team`,
                       `user`                                           AS `user`,
                       COUNT(DISTINCT `game`)                           AS `g`,
                       ROUND(SUM(`yds`) / COUNT(DISTINCT `game`), 1)    AS `yds`,
                       ROUND(SUM(`td`) / COUNT(DISTINCT `game`), 1)     AS `td`,
                       ROUND(SUM(`int`) / COUNT(DISTINCT `game`), 1)    AS `int`,
                       ROUND(SUM(`points`) / COUNT(DISTINCT `game`), 1) AS `points`
              FROM     (SELECT   s.`name` AS `name`,
                                 CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                                 t.`user` AS `user`,
                                 s.`game` AS `game`,
                                 s.`yds`  AS `yds`,
                                 s.`td`   AS `td`,
                                 s.`int`  AS `int`,
                                 ROUND(FLOOR(CAST(s.`yds` AS SIGNED) / 2) / 10 + CAST(s.`td` AS SIGNED) * 6 - CAST(s.`int` AS SIGNED) * 3, 1) AS `points`
                        FROM     '.$_SYS['table']['stats_passing'].'  AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.`name` AS `name`,
                                 CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                                 t.`user` AS `user`,
                                 s.`game` AS `game`,
                                 s.`yds`  AS `yds`,
                                 s.`td`   AS `td`,
                                 0        AS `int`,
                                 ROUND(s.`yds` / 10 + s.`td` * 6, 1) AS `points`
                        FROM     '.$_SYS['table']['stats_receiving'].'   AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
                        UNION ALL
                        SELECT   s.`name` AS `name`,
                                 CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                                 t.`user` AS `user`,
                                 s.`game` AS `game`,
                                 s.`yds`  AS `yds`,
                                 s.`td`   AS `td`,
                                 0        AS `int`,
                                 ROUND(s.`yds` / 10 + s.`td` * 6, 1) AS `points`
                        FROM     '.$_SYS['table']['stats_rushing'].'   AS s
                                 LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                                 LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
                        WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].') AS tmp
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'passing' && !$averages) {
      return 'SELECT   s.`name`                                                                 AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                                            AS `team`,
                       t.`user`                                                                 AS `user`,
                       COUNT(*)                                                                 AS `g`,
                       SUM(s.`cmp`)                                                             AS `cmp`,
                       SUM(s.`att`)                                                             AS `att`,
                       ROUND(IFNULL(100 * SUM(s.`cmp`) / SUM(s.`att`), 0), 1)                   AS `pct`,
                       SUM(s.`yds`)                                                             AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1)                         AS `ypa`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`cmp`), 0), 1)                         AS `ypc`,
                       SUM(s.`td`)                                                              AS `td`,
                       SUM(s.`int`)                                                             AS `int`,
                       MAX(s.`long`)                                                            AS `long`,
                       SUM(s.`sack`)                                                            AS `sack`,
                       ROUND((LEAST(GREATEST(0, 5 * SUM(s.`cmp`) / SUM(s.`att`) - 3/2), 19/8)
                              + LEAST(GREATEST(0, SUM(s.`yds`) / 4 / SUM(s.`att`) - 3/4), 19/8)
                              + LEAST(20 * SUM(s.`td`) / SUM(s.`att`), 19/8)
                              + GREATEST(19/8 - 25 * SUM(s.`int`) / SUM(s.`att`), 0))
                             / 6 * 100, 1)                                                      AS `rat`
              FROM     '.$_SYS['table']['stats_passing'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'passing' && $averages) {
      return 'SELECT   s.`name`                                                                 AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                                            AS `team`,
                       t.`user`                                                                 AS `user`,
                       COUNT(*)                                                                 AS `g`,
                       ROUND(AVG(s.`cmp`), 1)                                                   AS `cmp`,
                       ROUND(AVG(s.`att`), 1)                                                   AS `att`,
                       ROUND(IFNULL(100 * SUM(s.`cmp`) / SUM(s.`att`), 0), 1)                   AS `pct`,
                       ROUND(AVG(s.`yds`), 1)                                                   AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1)                         AS `ypa`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`cmp`), 0), 1)                         AS `ypc`,
                       ROUND(AVG(s.`td`), 1)                                                    AS `td`,
                       ROUND(AVG(s.`int`), 1)                                                   AS `int`,
                       MAX(s.`long`)                                                            AS `long`,
                       ROUND(AVG(s.`sack`), 1)                                                  AS `sack`,
                       ROUND((LEAST(GREATEST(0, 5 * SUM(s.`cmp`) / SUM(s.`att`) - 3/2), 19/8)
                              + LEAST(GREATEST(0, SUM(s.`yds`) / 4 / SUM(s.`att`) - 3/4), 19/8)
                              + LEAST(20 * SUM(s.`td`) / SUM(s.`att`), 19/8)
                              + GREATEST(19/8 - 25 * SUM(s.`int`) / SUM(s.`att`), 0))
                             / 6 * 100, 1)                                                      AS `rat`
              FROM     '.$_SYS['table']['stats_passing'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'rushing' && !$averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       SUM(s.`att`)                                     AS `att`,
                       SUM(s.`yds`)                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `ypa`,
                       MAX(s.`long`)                                    AS `long`,
                       SUM(s.`td`)                                      AS `td`,
                       SUM(s.`fum`)                                     AS `fum`
              FROM     '.$_SYS['table']['stats_rushing'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'rushing' && $averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       ROUND(AVG(s.`att`), 1)                           AS `att`,
                       ROUND(AVG(s.`yds`), 1)                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `ypa`,
                       MAX(s.`long`)                                    AS `long`,
                       ROUND(AVG(s.`td`), 1)                            AS `td`,
                       ROUND(AVG(s.`fum`), 1)                           AS `fum`
              FROM     '.$_SYS['table']['stats_rushing'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'receiving' && !$averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       SUM(s.`rec`)                                     AS `rec`,
                       SUM(s.`yds`)                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`rec`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       SUM(s.`td`)                                      AS `td`,
                       SUM(s.`drop`)                                    AS `drop`,
                       SUM(s.`yac`)                                     AS `yac`
              FROM     '.$_SYS['table']['stats_receiving'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'  AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'   AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'receiving' && $averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       ROUND(AVG(s.`rec`), 1)                           AS `rec`,
                       ROUND(AVG(s.`yds`), 1)                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`rec`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       ROUND(AVG(s.`td`), 1)                            AS `td`,
                       ROUND(AVG(s.`drop`), 1)                          AS `drop`,
                       ROUND(AVG(s.`yac`), 1)                           AS `yac`
              FROM     '.$_SYS['table']['stats_receiving'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'  AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'   AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'kicking' && !$averages) {
      return 'SELECT   s.`name`                                               AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                          AS `team`,
                       t.`user`                                               AS `user`,
                       COUNT(*)                                               AS `g`,
                       SUM(s.`fgm`)                                           AS `fgm`,
                       SUM(s.`fga`)                                           AS `fga`,
                       ROUND(IFNULL(100 * SUM(s.`fgm`) / SUM(s.`fga`), 0), 1) AS `fg%`,
                       SUM(s.`fgsblocked`)                                    AS `fgblk`,
                       SUM(s.`xpm`)                                           AS `xpm`,
                       SUM(s.`xpa`)                                           AS `xpa`,
                       ROUND(IFNULL(100 * SUM(s.`xpm`) / SUM(s.`xpa`), 0), 1) AS `xp%`,
                       SUM(s.`xpsblocked`)                                    AS `xpblk`,
                       SUM(s.`kickoffs`)                                      AS `ko`,
                       SUM(s.`touchbacks`)                                    AS `tb`,
                       SUM(s.`fgm`) * 3 + SUM(s.`xpm`)                        AS `points`
              FROM     '.$_SYS['table']['stats_kicking'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'kicking' && $averages) {
      return 'SELECT   s.`name`                                               AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                          AS `team`,
                       t.`user`                                               AS `user`,
                       COUNT(*)                                               AS `g`,
                       ROUND(AVG(s.`fgm`), 1)                                 AS `fgm`,
                       ROUND(AVG(s.`fga`), 1)                                 AS `fga`,
                       ROUND(IFNULL(100 * SUM(s.`fgm`) / SUM(s.`fga`), 0), 1) AS `fg%`,
                       ROUND(AVG(s.`fgsblocked`), 1)                          AS `fgblk`,
                       ROUND(AVG(s.`xpm`), 1)                                 AS `xpm`,
                       ROUND(AVG(s.`xpa`), 1)                                 AS `xpa`,
                       ROUND(IFNULL(100 * SUM(s.`xpm`) / SUM(s.`xpa`), 0), 1) AS `xp%`,
                       ROUND(AVG(s.`xpsblocked`), 1)                          AS `xpblk`,
                       ROUND(AVG(s.`kickoffs`), 1)                            AS `ko`,
                       ROUND(AVG(s.`touchbacks`), 1)                          AS `tb`,
                       ROUND(AVG(s.`fgm`) * 3 + AVG(s.`xpm`), 1)              AS `points`
              FROM     '.$_SYS['table']['stats_kicking'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'punting' && !$averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       SUM(s.`att`)                                     AS `att`,
                       SUM(s.`yds`)                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       SUM(s.`blocks`)                                  AS `blk`,
                       SUM(s.`in20`)                                    AS `in20`,
                       SUM(s.`touchbacks`)                              AS `tb`
              FROM     '.$_SYS['table']['stats_punting'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'punting' && $averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       ROUND(AVG(s.`att`), 1)                           AS `att`,
                       ROUND(AVG(s.`yds`), 1)                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       ROUND(AVG(s.`blocks`), 1)                        AS `blk`,
                       ROUND(AVG(s.`in20`), 1)                          AS `in20`,
                       ROUND(AVG(s.`touchbacks`), 1)                    AS `tb`
              FROM     '.$_SYS['table']['stats_punting'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'kickreturns' && !$averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       SUM(s.`att`)                                     AS `att`,
                       SUM(s.`yds`)                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       SUM(s.`td`)                                      AS `td`
              FROM     '.$_SYS['table']['stats_kick_returns'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'kickreturns' && $averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       ROUND(AVG(s.`att`), 1)                           AS `att`,
                       ROUND(AVG(s.`yds`), 1)                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       ROUND(AVG(s.`td`), 1)                            AS `td`
              FROM     '.$_SYS['table']['stats_kick_returns'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'puntreturns' && !$averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       SUM(s.`att`)                                     AS `att`,
                       SUM(s.`yds`)                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       SUM(s.`td`)                                      AS `td`
              FROM     '.$_SYS['table']['stats_punt_returns'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'puntreturns' && $averages) {
      return 'SELECT   s.`name`                                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)                    AS `team`,
                       t.`user`                                         AS `user`,
                       COUNT(*)                                         AS `g`,
                       ROUND(AVG(s.`att`), 1)                           AS `att`,
                       ROUND(AVG(s.`yds`), 1)                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`yds`) / SUM(s.`att`), 0), 1) AS `avg`,
                       MAX(s.`long`)                                    AS `long`,
                       ROUND(AVG(s.`td`), 1)                            AS `td`
              FROM     '.$_SYS['table']['stats_punt_returns'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defense' && !$averages) {
      return 'SELECT   s.`name`                      AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                       t.`user`                      AS `user`,
                       COUNT(*)                      AS `g`,
                       SUM(s.`tot`)                  AS `tack`,
                       SUM(s.`loss`)                 AS `loss`,
                       SUM(s.`sack`)                 AS `sack`,
                       SUM(s.`ff`)                   AS `ff`,
                       SUM(s.`frec`)                 AS `frec`,
                       SUM(s.`yds`)                  AS `yds`,
                       SUM(s.`td`)                   AS `td`,
                       SUM(s.`int`)                  AS `int`,
                       SUM(s.`ret`)                  AS `ret`,
                       SUM(s.`deflections`)          AS `def`,
                       SUM(s.`safeties`)             AS `saf`,
                       SUM(s.`cth_allow`)            AS `cta`,
                       SUM(s.`big_hits`)             AS `bh`,
                       ROUND(SUM(s.`tot`) + SUM(s.`loss`) / 2 + SUM(s.`sack`) * 2 + SUM(s.`ff`) * 2 + SUM(s.`frec`) * 1 + SUM(s.`td`) * 6 + SUM(s.`int`) * 3 + SUM(s.`safeties`) * 2, 1) AS `fan`
              FROM     '.$_SYS['table']['stats_defense'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defense' && $averages) {
      return 'SELECT   s.`name`                       AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)  AS `team`,
                       t.`user`                       AS `user`,
                       COUNT(*)                       AS `g`,
                       ROUND(AVG(s.`tot`), 1)         AS `tack`,
                       ROUND(AVG(s.`loss`), 1)        AS `loss`,
                       ROUND(AVG(s.`sack`), 1)        AS `sack`,
                       ROUND(AVG(s.`ff`), 1)          AS `ff`,
                       ROUND(AVG(s.`frec`), 1)        AS `frec`,
                       ROUND(AVG(s.`yds`), 1)         AS `yds`,
                       ROUND(AVG(s.`td`), 1)          AS `td`,
                       ROUND(AVG(s.`int`), 1)         AS `int`,
                       ROUND(AVG(s.`ret`), 1)         AS `ret`,
                       ROUND(AVG(s.`deflections`), 1) AS `def`,
                       ROUND(AVG(s.`safeties`), 1)    AS `saf`,
                       ROUND(AVG(s.`cth_allow`), 1)   AS `cta`,
                       ROUND(AVG(s.`big_hits`), 1)    AS `bh`,
                       ROUND((SUM(s.`tot`) + SUM(s.`loss`) / 2 + SUM(s.`sack`) * 2 + SUM(s.`ff`) * 2 + SUM(s.`frec`) * 1 + SUM(s.`td`) * 6 + SUM(s.`int`) * 3 + SUM(s.`safeties`) * 2) / COUNT(*), 1) AS `fan`
              FROM     '.$_SYS['table']['stats_defense'].'  AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'blocking' && !$averages) {
      return 'SELECT   s.`name`                      AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`) AS `team`,
                       t.`user`                      AS `user`,
                       COUNT(*)                      AS `g`,
                       SUM(s.`pancakes`)             AS `pancakes`,
                       SUM(s.`sacks_allowed`)        AS `sacks allowed`
              FROM     '.$_SYS['table']['stats_blocking'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'blocking' && $averages) {
      return 'SELECT   s.`name`                         AS `name`,
                       CONCAT(t.`id`, ":", n.`acro`)    AS `team`,
                       t.`user`                         AS `user`,
                       COUNT(*)                         AS `g`,
                       ROUND(AVG(s.`pancakes`), 1)      AS `pancakes`,
                       ROUND(AVG(s.`sacks_allowed`), 1) AS `sacks allowed`
              FROM     '.$_SYS['table']['stats_blocking'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].' AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'  AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `name`, `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offtotal' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                         AS `team`,
                       t.`user`                                                                                            AS `user`,
                       COUNT(*)                                                                                            AS `g`,
                       SUM(s.`first_downs`)                                                                                AS `1st`,
                       SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)                                        AS `play`,
                       ROUND(IFNULL(100 * (SUM(s.`passing_att`) + SUM(s.`sacks`))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1)               AS `%pa`,
                       SUM(s.`passing_yds`)                                                                                AS `pass`,
                       SUM(s.`rushing_yds`)                                                                                AS `rush`,
                       SUM(s.`passing_yds`) + SUM(s.`rushing_yds`)                                                         AS `yds`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) + SUM(rushing_yds))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1)               AS `ypp`,
                       SUM(s.`passing_td`) + SUM(s.`rushing_td`)                                                           AS `td`,
                       SUM(s.`fumbles`)                                                                                    AS `fum`,
                       SUM(s.`fumbles_lost`)                                                                               AS `lost`,
                       SUM(s.`penalties`)                                                                                  AS `pen`,
                       SUM(s.`penalty_yds`)                                                                                AS `pyd`,
                       TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(s.`top`))), "%k:%i:%s")                                     AS `top`
--                       ROUND(FLOOR((SUM(CAST(s.`passing_yds` AS SIGNED)) + SUM(CAST(s.`rushing_yds` AS SIGNED))) / 2) / 10
--                             + (SUM(CAST(s.`passing_td` AS SIGNED)) + SUM(CAST(s.`rushing_td` AS SIGNED))) * 6
--                             - SUM(CAST(s.`interceptions` AS SIGNED)) * 3, 1)                                              AS `fan`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offtotal' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                          AS `team`,
                       t.`user`                                                                                             AS `user`,
                       COUNT(*)                                                                                             AS `g`,
                       ROUND(AVG(s.`first_downs`), 1)                                                                       AS `1st`,
                       ROUND(AVG(s.`rushing_att`) + AVG(s.`passing_att`) + AVG(s.`sacks`), 1)                               AS `play`,
                       ROUND(IFNULL(100 * (SUM(s.`passing_att`) + SUM(s.`sacks`))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1)                AS `%pa`,
                       ROUND(AVG(s.`passing_yds`), 1)                                                                       AS `pass`,
                       ROUND(AVG(s.`rushing_yds`), 1)                                                                       AS `rush`,
                       ROUND(AVG(s.`passing_yds`) + AVG(s.`rushing_yds`), 1)                                                AS `yds`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) + SUM(rushing_yds))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1)                AS `ypp`,
                       ROUND(AVG(s.`passing_td`) + AVG(s.`rushing_td`), 1)                                                  AS `td`,
                       ROUND(AVG(s.`fumbles`), 1)                                                                           AS `fum`,
                       ROUND(AVG(s.`fumbles_lost`), 1)                                                                      AS `lost`,
                       ROUND(AVG(s.`penalties`), 1)                                                                         AS `pen`,
                       ROUND(AVG(s.`penalty_yds`), 1)                                                                       AS `pyds`,
                       TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(s.`top`))), "%i:%s")                                         AS `top`
--                       ROUND((FLOOR((SUM(CAST(s.`passing_yds` AS SIGNED)) + SUM(CAST(s.`rushing_yds` AS SIGNED))) / 2) / 10
--                              + (SUM(CAST(s.`passing_td` AS SIGNED)) + SUM(CAST(s.`rushing_td` AS SIGNED))) * 6
--                              - SUM(CAST(s.`interceptions` AS SIGNED)) * 3) / COUNT(*), 1)                                  AS `fan`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offpass' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                              AS `team`,
                       t.`user`                                                                                 AS `user`,
                       COUNT(*)                                                                                 AS `g`,
                       SUM(s.`passing_cmp`)                                                                     AS `cmp`,
                       SUM(s.`passing_att`)                                                                     AS `att`,
                       ROUND(IFNULL(100 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`), 0), 1)                   AS `pct`,
                       SUM(s.`passing_yds`)                                                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_att`), 0), 1)                         AS `ypa`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_cmp`), 0), 1)                         AS `ypc`,
                       SUM(s.`passing_yds`) - (SELECT SUM(yac) FROM '.$_SYS['table']['stats_receiving'].' AS r WHERE r.team = s.team AND r.week '.$weeks.') AS `net`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) - (SELECT SUM(yac) FROM '.$_SYS['table']['stats_receiving'].' AS r WHERE r.team = s.team AND r.week '.$weeks.')) / SUM(s.`passing_cmp`), 0), 2) AS `npc`,
                       ROUND(IFNULL(100 * (SELECT SUM(yac) FROM '.$_SYS['table']['stats_receiving'].' AS r WHERE r.team = s.team AND r.week '.$weeks.') / SUM(s.`passing_yds`), 0), 1) AS `%yc`,
                       SUM(s.`passing_td`)                                                                      AS `td`,
                       SUM(s.`interceptions`)                                                                   AS `int`,
                       SUM(s.`sacks`)                                                                           AS `sack`,
                       SUM(s.`sack_yds`)                                                                        AS `syds`,
                       ROUND((LEAST(GREATEST(0, 5 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`) - 3/2), 19/8)
                              + LEAST(GREATEST(0, SUM(s.`passing_yds`) / 4 / SUM(s.`passing_att`) - 3/4), 19/8)
                              + LEAST(20 * SUM(s.`passing_td`) / SUM(s.`passing_att`), 19/8)
                              + GREATEST(19/8 - 25 * SUM(s.`interceptions`) / SUM(s.`passing_att`), 0))
                             / 6 * 100, 1)                                                                      AS `rat`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offpass' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                              AS `team`,
                       t.`user`                                                                                 AS `user`,
                       COUNT(*)                                                                                 AS `g`,
                       ROUND(AVG(s.`passing_cmp`), 1)                                                           AS `cmp`,
                       ROUND(AVG(s.`passing_att`), 1)                                                           AS `att`,
                       ROUND(IFNULL(100 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`), 0), 1)                   AS `pct`,
                       ROUND(AVG(s.`passing_yds`), 1)                                                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_att`), 0), 1)                         AS `ypa`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_cmp`), 0), 1)                         AS `ypc`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) - (SELECT SUM(yac) FROM '.$_SYS['table']['stats_receiving'].' AS r WHERE r.team = s.team AND r.week '.$weeks.')) / COUNT(*), 0), 1) AS `net`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) - (SELECT SUM(yac) FROM '.$_SYS['table']['stats_receiving'].' AS r WHERE r.team = s.team AND r.week '.$weeks.')) / SUM(s.`passing_cmp`), 0), 2) AS `npc`,
                       ROUND(IFNULL(100 * (SELECT SUM(yac) FROM '.$_SYS['table']['stats_receiving'].' AS r WHERE r.team = s.team AND r.week '.$weeks.') / SUM(s.`passing_yds`), 0), 1) AS `%yc`,
                       ROUND(AVG(s.`passing_td`), 1)                                                            AS `td`,
                       ROUND(AVG(s.`interceptions`), 1)                                                         AS `int`,
                       ROUND(AVG(s.`sacks`), 1)                                                                 AS `sack`,
                       ROUND(AVG(s.`sack_yds`), 1)                                                              AS `syds`,
                       ROUND((LEAST(GREATEST(0, 5 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`) - 3/2), 19/8)
                              + LEAST(GREATEST(0, SUM(s.`passing_yds`) / 4 / SUM(s.`passing_att`) - 3/4), 19/8)
                              + LEAST(20 * SUM(s.`passing_td`) / SUM(s.`passing_att`), 19/8)
                              + GREATEST(19/8 - 25 * SUM(s.`interceptions`) / SUM(s.`passing_att`), 0))
                             / 6 * 100, 1)                                                                      AS `rat`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offrush' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                      AS `team`,
                       t.`user`                                                         AS `user`,
                       COUNT(*)                                                         AS `g`,
                       SUM(s.`rushing_att`)                                             AS `att`,
                       SUM(s.`rushing_yds`)                                             AS `yds`,
                       ROUND(IFNULL(SUM(s.`rushing_yds`) / SUM(s.`rushing_att`), 0), 1) AS `ypa`,
                       SUM(s.`rushing_td`)                                              AS `td`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offrush' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                      AS `team`,
                       t.`user`                                                         AS `user`,
                       COUNT(*)                                                         AS `g`,
                       ROUND(AVG(s.`rushing_att`), 1)                                   AS `att`,
                       ROUND(AVG(s.`rushing_yds`), 1)                                   AS `yds`,
                       ROUND(IFNULL(SUM(s.`rushing_yds`) / SUM(s.`rushing_att`), 0), 1) AS `ypa`,
                       ROUND(AVG(s.`rushing_td`), 1)                                    AS `td`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offeff' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                   AS `team`,
                       t.`user`                                                                                      AS `user`,
                       COUNT(*)                                                                                      AS `g`,
                       SUM(s.`third_down_conv`)                                                                      AS `3-md`,
                       SUM(s.`third_downs`)                                                                          AS `3-at`,
                       ROUND(IFNULL(100 * SUM(s.`third_down_conv`) / SUM(s.`third_downs`), 0), 1)                    AS `3rd%`,
                       SUM(s.`fourth_down_conv`)                                                                     AS `4-md`,
                       SUM(s.`fourth_downs`)                                                                         AS `4-at`,
                       ROUND(IFNULL(100 * SUM(s.`fourth_down_conv`) / SUM(s.`fourth_downs`), 0), 1)                  AS `4th%`,
                       SUM(s.`two_pt_conv_made`)                                                                     AS `2-md`,
                       SUM(s.`two_pt_conv_att`)                                                                      AS `2-at`,
                       ROUND(IFNULL(100 * SUM(s.`two_pt_conv_made`) / SUM(s.`two_pt_conv_att`), 0), 1)               AS `2pt%`,
                       SUM(s.`redzone_num`)                                                                          AS `rz#`,
                       SUM(s.`redzone_fg`)                                                                           AS `rz-fg`,
                       SUM(s.`redzone_td`)                                                                           AS `rz-td`,
                       ROUND(IFNULL(100 * (SUM(s.`redzone_fg`) + SUM(s.`redzone_td`)) / SUM(s.`redzone_num`), 0), 1) AS `rz%`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offeff' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                   AS `team`,
                       t.`user`                                                                                      AS `user`,
                       COUNT(*)                                                                                      AS `g`,
                       ROUND(AVG(s.`third_down_conv`), 1)                                                            AS `3-md`,
                       ROUND(AVG(s.`third_downs`), 1)                                                                AS `3-at`,
                       ROUND(IFNULL(100 * SUM(s.`third_down_conv`) / SUM(s.`third_downs`), 0), 1)                    AS `3rd%`,
                       ROUND(AVG(s.`fourth_down_conv`), 1)                                                           AS `4-md`,
                       ROUND(AVG(s.`fourth_downs`), 1)                                                               AS `4-at`,
                       ROUND(IFNULL(100 * SUM(s.`fourth_down_conv`) / SUM(s.`fourth_downs`), 0), 1)                  AS `4th%`,
                       ROUND(AVG(s.`two_pt_conv_made`), 1)                                                           AS `2-md`,
                       ROUND(AVG(s.`two_pt_conv_att`), 1)                                                            AS `2-at`,
                       ROUND(IFNULL(100 * SUM(s.`two_pt_conv_made`) / SUM(s.`two_pt_conv_att`), 0), 1)               AS `2pt%`,
                       ROUND(AVG(s.`redzone_num`), 1)                                                                AS `rz#`,
                       ROUND(AVG(s.`redzone_fg`), 1)                                                                 AS `rz-fg`,
                       ROUND(AVG(s.`redzone_td`), 1)                                                                 AS `rz-td`,
                       ROUND(IFNULL(100 * (SUM(s.`redzone_fg`) + SUM(s.`redzone_td`)) / SUM(s.`redzone_num`), 0), 1) AS `rz%`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offscoring' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                         AS `team`,
                       t.`user`                                                            AS `user`,
                       COUNT(*)                                                            AS `g`,
                       SUM(s.`q1`)                                                         AS `q1`,
                       SUM(s.`q2`)                                                         AS `q2`,
                       SUM(s.`q3`)                                                         AS `q3`,
                       SUM(s.`q4`)                                                         AS `q4`,
                       SUM(s.`ot`)                                                         AS `ot`,
                       SUM(s.`td`)                                                         AS `td`,
                       SUM(s.`xpm`)                                                        AS `xpm`,
                       SUM(s.`xpa`)                                                        AS `xpa`,
                       SUM(s.`2pm`)                                                        AS `2pm`,
                       SUM(s.`2pa`)                                                        AS `2pa`,
                       SUM(s.`fgm`)                                                        AS `fgm`,
                       SUM(s.`fga`)                                                        AS `fga`,
                       SUM(s.`safeties`)                                                   AS `saf`,
                       SUM(s.`q1`) + SUM(s.`q2`) + SUM(s.`q3`) + SUM(s.`q4`) + SUM(s.`ot`) AS `pts`
              FROM     '.$_SYS['table']['stats_scoring_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'offscoring' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                   AS `team`,
                       t.`user`                                                                      AS `user`,
                       COUNT(*)                                                                      AS `g`,
                       ROUND(AVG(s.`q1`), 1)                                                         AS `q1`,
                       ROUND(AVG(s.`q2`), 1)                                                         AS `q2`,
                       ROUND(AVG(s.`q3`), 1)                                                         AS `q3`,
                       ROUND(AVG(s.`q4`), 1)                                                         AS `q4`,
                       ROUND(AVG(s.`ot`), 1)                                                         AS `ot`,
                       ROUND(AVG(s.`td`), 1)                                                         AS `td`,
                       ROUND(AVG(s.`xpm`), 1)                                                        AS `xpm`,
                       ROUND(AVG(s.`xpa`), 1)                                                        AS `xpa`,
                       ROUND(AVG(s.`2pm`), 1)                                                        AS `2pm`,
                       ROUND(AVG(s.`2pa`), 1)                                                        AS `2pa`,
                       ROUND(AVG(s.`fgm`), 1)                                                        AS `fgm`,
                       ROUND(AVG(s.`fga`), 1)                                                        AS `fga`,
                       ROUND(AVG(s.`safeties`), 1)                                                   AS `saf`,
                       ROUND(AVG(s.`q1`) + AVG(s.`q2`) + AVG(s.`q3`) + AVG(s.`q4`) + AVG(s.`ot`), 1) AS `pts`
              FROM     '.$_SYS['table']['stats_scoring_offense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'deftotal' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                           AS `team`,
                       t.`user`                                                                              AS `user`,
                       COUNT(*)                                                                              AS `g`,
                       SUM(s.`first_downs`)                                                                  AS `1st`,
                       SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)                          AS `play`,
                       ROUND(IFNULL(100 * (SUM(s.`passing_att`) + SUM(s.`sacks`))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1) AS `%pa`,
                       SUM(s.`passing_yds`)                                                                  AS `pass`,
                       SUM(s.`rushing_yds`)                                                                  AS `rush`,
                       SUM(s.`passing_yds`) + SUM(s.`rushing_yds`)                                           AS `yds`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) + SUM(rushing_yds))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1) AS `ypp`,
                       SUM(s.`passing_td`) + SUM(s.`rushing_td`)                                             AS `td`,
                       SUM(s.`fumbles_forced`)                                                               AS `ff`,
                       SUM(s.`fumbles_recovered`)                                                            AS `frec`,
                       SUM(s.`penalties`)                                                                    AS `pen`,
                       SUM(s.`penalty_yds`)                                                                  AS `pyd`,
                       TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(s.`top`))), "%k:%i:%s")                       AS `top`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'deftotal' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                           AS `team`,
                       t.`user`                                                                              AS `user`,
                       COUNT(*)                                                                              AS `g`,
                       ROUND(AVG(s.`first_downs`), 1)                                                        AS `1st`,
                       ROUND(AVG(s.`rushing_att`) + AVG(s.`passing_att`) + AVG(s.`sacks`), 1)                AS `play`,
                       ROUND(IFNULL(100 * (SUM(s.`passing_att`) + SUM(s.`sacks`))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1) AS `%pa`,
                       ROUND(AVG(s.`passing_yds`), 1)                                                        AS `pass`,
                       ROUND(AVG(s.`rushing_yds`), 1)                                                        AS `rush`,
                       ROUND(AVG(s.`passing_yds`) + AVG(s.`rushing_yds`), 1)                                 AS `yds`,
                       ROUND(IFNULL((SUM(s.`passing_yds`) + SUM(rushing_yds))
                                    / (SUM(s.`rushing_att`) + SUM(s.`passing_att`) + SUM(s.`sacks`)), 0), 1) AS `ypp`,
                       ROUND(AVG(s.`passing_td`) + AVG(s.`rushing_td`), 1)                                   AS `td`,
                       ROUND(AVG(s.`fumbles_forced`), 1)                                                     AS `ff`,
                       ROUND(AVG(s.`fumbles_recovered`), 1)                                                  AS `frec`,
                       ROUND(AVG(s.`penalties`), 1)                                                          AS `pen`,
                       ROUND(AVG(s.`penalty_yds`), 1)                                                        AS `pyds`,
                       TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(s.`top`))), "%i:%s")                          AS `top`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defpass' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                              AS `team`,
                       t.`user`                                                                                 AS `user`,
                       COUNT(*)                                                                                 AS `g`,
                       SUM(s.`passing_cmp`)                                                                     AS `cmp`,
                       SUM(s.`passing_att`)                                                                     AS `att`,
                       ROUND(IFNULL(100 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`), 0), 1)                   AS `pct`,
                       SUM(s.`passing_yds`)                                                                     AS `yds`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_att`), 0), 1)                         AS `ypa`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_cmp`), 0), 1)                         AS `ypc`,
                       SUM(s.`passing_td`)                                                                      AS `td`,
                       SUM(s.`interceptions`)                                                                   AS `int`,
                       SUM(s.`sacks`)                                                                           AS `sack`,
                       SUM(s.`sack_yds`)                                                                        AS `syds`,
                       ROUND((LEAST(GREATEST(0, 5 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`) - 3/2), 19/8)
                              + LEAST(GREATEST(0, SUM(s.`passing_yds`) / 4 / SUM(s.`passing_att`) - 3/4), 19/8)
                              + LEAST(20 * SUM(s.`passing_td`) / SUM(s.`passing_att`), 19/8)
                              + GREATEST(19/8 - 25 * SUM(s.`interceptions`) / SUM(s.`passing_att`), 0))
                             / 6 * 100, 1)                                                                      AS `rat`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defpass' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                              AS `team`,
                       t.`user`                                                                                 AS `user`,
                       COUNT(*)                                                                                 AS `g`,
                       ROUND(AVG(s.`passing_cmp`), 1)                                                           AS `cmp`,
                       ROUND(AVG(s.`passing_att`), 1)                                                           AS `att`,
                       ROUND(IFNULL(100 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`), 0), 1)                   AS `pct`,
                       ROUND(AVG(s.`passing_yds`), 1)                                                           AS `yds`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_att`), 0), 1)                         AS `ypa`,
                       ROUND(IFNULL(SUM(s.`passing_yds`) / SUM(s.`passing_cmp`), 0), 1)                         AS `ypc`,
                       ROUND(AVG(s.`passing_td`), 1)                                                            AS `td`,
                       ROUND(AVG(s.`interceptions`), 1)                                                         AS `int`,
                       ROUND(AVG(s.`sacks`), 1)                                                                 AS `sack`,
                       ROUND(AVG(s.`sack_yds`), 1)                                                              AS `syds`,
                       ROUND((LEAST(GREATEST(0, 5 * SUM(s.`passing_cmp`) / SUM(s.`passing_att`) - 3/2), 19/8)
                              + LEAST(GREATEST(0, SUM(s.`passing_yds`) / 4 / SUM(s.`passing_att`) - 3/4), 19/8)
                              + LEAST(20 * SUM(s.`passing_td`) / SUM(s.`passing_att`), 19/8)
                              + GREATEST(19/8 - 25 * SUM(s.`interceptions`) / SUM(s.`passing_att`), 0))
                             / 6 * 100, 1)                                                                      AS `rat`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defrush' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                      AS `team`,
                       t.`user`                                                         AS `user`,
                       COUNT(*)                                                         AS `g`,
                       SUM(s.`rushing_att`)                                             AS `att`,
                       SUM(s.`rushing_yds`)                                             AS `yds`,
                       ROUND(IFNULL(SUM(s.`rushing_yds`) / SUM(s.`rushing_att`), 0), 1) AS `ypa`,
                       SUM(s.`rushing_td`)                                              AS `td`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defrush' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                      AS `team`,
                       t.`user`                                                         AS `user`,
                       COUNT(*)                                                         AS `g`,
                       ROUND(AVG(s.`rushing_att`), 1)                                   AS `att`,
                       ROUND(AVG(s.`rushing_yds`), 1)                                   AS `yds`,
                       ROUND(IFNULL(SUM(s.`rushing_yds`) / SUM(s.`rushing_att`), 0), 1) AS `ypa`,
                       ROUND(AVG(s.`rushing_td`), 1)                                    AS `td`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defeff' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                   AS `team`,
                       t.`user`                                                                                      AS `user`,
                       COUNT(*)                                                                                      AS `g`,
                       SUM(s.`third_down_conv`)                                                                      AS `3-md`,
                       SUM(s.`third_downs`)                                                                          AS `3-at`,
                       ROUND(IFNULL(100 * SUM(s.`third_down_conv`) / SUM(s.`third_downs`), 0), 1)                    AS `3rd%`,
                       SUM(s.`fourth_down_conv`)                                                                     AS `4-md`,
                       SUM(s.`fourth_downs`)                                                                         AS `4-at`,
                       ROUND(IFNULL(100 * SUM(s.`fourth_down_conv`) / SUM(s.`fourth_downs`), 0), 1)                  AS `4th%`,
                       SUM(s.`two_pt_conv_made`)                                                                     AS `2-md`,
                       SUM(s.`two_pt_conv_att`)                                                                      AS `2-at`,
                       ROUND(IFNULL(100 * SUM(s.`two_pt_conv_made`) / SUM(s.`two_pt_conv_att`), 0), 1)               AS `2pt%`,
                       SUM(s.`redzone_num`)                                                                          AS `rz#`,
                       SUM(s.`redzone_fg`)                                                                           AS `rz-fg`,
                       SUM(s.`redzone_td`)                                                                           AS `rz-td`,
                       ROUND(IFNULL(100 * (SUM(s.`redzone_fg`) + SUM(s.`redzone_td`)) / SUM(s.`redzone_num`), 0), 1) AS `rz%`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defeff' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                   AS `team`,
                       t.`user`                                                                                      AS `user`,
                       COUNT(*)                                                                                      AS `g`,
                       ROUND(AVG(s.`third_down_conv`), 1)                                                            AS `3-md`,
                       ROUND(AVG(s.`third_downs`), 1)                                                                AS `3-at`,
                       ROUND(IFNULL(100 * SUM(s.`third_down_conv`) / SUM(s.`third_downs`), 0), 1)                    AS `3rd%`,
                       ROUND(AVG(s.`fourth_down_conv`), 1)                                                           AS `4-md`,
                       ROUND(AVG(s.`fourth_downs`), 1)                                                               AS `4-at`,
                       ROUND(IFNULL(100 * SUM(s.`fourth_down_conv`) / SUM(s.`fourth_downs`), 0), 1)                  AS `4th%`,
                       ROUND(AVG(s.`two_pt_conv_made`), 1)                                                           AS `2-md`,
                       ROUND(AVG(s.`two_pt_conv_att`), 1)                                                            AS `2-at`,
                       ROUND(IFNULL(100 * SUM(s.`two_pt_conv_made`) / SUM(s.`two_pt_conv_att`), 0), 1)               AS `2pt%`,
                       ROUND(AVG(s.`redzone_num`), 1)                                                                AS `rz#`,
                       ROUND(AVG(s.`redzone_fg`), 1)                                                                 AS `rz-fg`,
                       ROUND(AVG(s.`redzone_td`), 1)                                                                 AS `rz-td`,
                       ROUND(IFNULL(100 * (SUM(s.`redzone_fg`) + SUM(s.`redzone_td`)) / SUM(s.`redzone_num`), 0), 1) AS `rz%`
              FROM     '.$_SYS['table']['stats_team_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defscoring' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                         AS `team`,
                       t.`user`                                                            AS `user`,
                       COUNT(*)                                                            AS `g`,
                       SUM(s.`q1`)                                                         AS `q1`,
                       SUM(s.`q2`)                                                         AS `q2`,
                       SUM(s.`q3`)                                                         AS `q3`,
                       SUM(s.`q4`)                                                         AS `q4`,
                       SUM(s.`ot`)                                                         AS `ot`,
                       SUM(s.`td`)                                                         AS `td`,
                       SUM(s.`xpm`)                                                        AS `xpm`,
                       SUM(s.`xpa`)                                                        AS `xpa`,
                       SUM(s.`2pm`)                                                        AS `2pm`,
                       SUM(s.`2pa`)                                                        AS `2pa`,
                       SUM(s.`fgm`)                                                        AS `fgm`,
                       SUM(s.`fga`)                                                        AS `fga`,
                       SUM(s.`safeties`)                                                   AS `saf`,
                       SUM(s.`q1`) + SUM(s.`q2`) + SUM(s.`q3`) + SUM(s.`q4`) + SUM(s.`ot`) AS `pts`
              FROM     '.$_SYS['table']['stats_scoring_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'defscoring' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                   AS `team`,
                       t.`user`                                                                      AS `user`,
                       COUNT(*)                                                                      AS `g`,
                       ROUND(AVG(s.`q1`), 1)                                                         AS `q1`,
                       ROUND(AVG(s.`q2`), 1)                                                         AS `q2`,
                       ROUND(AVG(s.`q3`), 1)                                                         AS `q3`,
                       ROUND(AVG(s.`q4`), 1)                                                         AS `q4`,
                       ROUND(AVG(s.`ot`), 1)                                                         AS `ot`,
                       ROUND(AVG(s.`td`), 1)                                                         AS `td`,
                       ROUND(AVG(s.`xpm`), 1)                                                        AS `xpm`,
                       ROUND(AVG(s.`xpa`), 1)                                                        AS `xpa`,
                       ROUND(AVG(s.`2pm`), 1)                                                        AS `2pm`,
                       ROUND(AVG(s.`2pa`), 1)                                                        AS `2pa`,
                       ROUND(AVG(s.`fgm`), 1)                                                        AS `fgm`,
                       ROUND(AVG(s.`fga`), 1)                                                        AS `fga`,
                       ROUND(AVG(s.`safeties`), 1)                                                   AS `saf`,
                       ROUND(AVG(s.`q1`) + AVG(s.`q2`) + AVG(s.`q3`) + AVG(s.`q4`) + AVG(s.`ot`), 1) AS `pts`
              FROM     '.$_SYS['table']['stats_scoring_defense'].' AS s
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON s.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    s.`week` '.$weeks.' AND s.`season` = '.$_SYS['request']['season'].'
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'turnover' && !$averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                          AS `team`,
                       t.`user`                                                                                             AS `user`,
                       COUNT(*)                                                                                             AS `g`,
                       SUM(d.`fumbles_forced`)                                                                              AS `ff`,
                       SUM(d.`fumbles_recovered`)                                                                           AS `frec`,
                       SUM(d.`interceptions`)                                                                               AS `d.int`,
                       SUM(d.`interceptions`) + SUM(d.`fumbles_recovered`)                                                  AS `takeaway`,
                       SUM(o.`fumbles`)                                                                                     AS `fum`,
                       SUM(o.`fumbles_lost`)                                                                                AS `lost`,
                       SUM(o.`interceptions`)                                                                               AS `o.int`,
                       SUM(o.`interceptions`) + SUM(o.`fumbles_lost`)                                                       AS `giveaway`,
                       SUM(d.`interceptions`) + SUM(d.`fumbles_recovered`) - SUM(o.`interceptions`) - SUM(o.`fumbles_lost`) AS `ratio`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS o,
                       '.$_SYS['table']['stats_team_defense'].' AS d
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON d.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    d.`week` '.$weeks.' AND d.`season` = '.$_SYS['request']['season'].'
                       AND d.team = o.team
                       AND d.game = o.game
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    } elseif ($category == 'turnover' && $averages) {
      return 'SELECT   CONCAT(t.`id`, ":", n.nick)                                                                                    AS `team`,
                       t.`user`                                                                                                       AS `user`,
                       COUNT(*)                                                                                                       AS `g`,
                       ROUND(AVG(d.`fumbles_forced`), 1)                                                                              AS `ff`,
                       ROUND(AVG(d.`fumbles_recovered`), 1)                                                                           AS `frec`,
                       ROUND(AVG(d.`interceptions`), 1)                                                                               AS `d.int`,
                       ROUND(AVG(d.`interceptions`) + AVG(d.`fumbles_recovered`), 1)                                                  AS `takeaway`,
                       ROUND(AVG(o.`fumbles`), 1)                                                                                     AS `fum`,
                       ROUND(AVG(o.`fumbles_lost`), 1)                                                                                AS `lost`,
                       ROUND(AVG(o.`interceptions`), 1)                                                                               AS `o.int`,
                       ROUND(AVG(o.`interceptions`) + AVG(o.`fumbles_lost`), 1)                                                       AS `giveaway`,
                       ROUND(AVG(d.`interceptions`) + AVG(d.`fumbles_recovered`) - AVG(o.`interceptions`) - AVG(o.`fumbles_lost`), 1) AS `ratio`
              FROM     '.$_SYS['table']['stats_team_offense'].' AS o,
                       '.$_SYS['table']['stats_team_defense'].' AS d
                       LEFT JOIN '.$_SYS['table']['team'].'     AS t ON d.team = t.id
                       LEFT JOIN '.$_SYS['table']['nfl'].'      AS n ON t.team = n.id
              WHERE    d.`week` '.$weeks.' AND d.`season` = '.$_SYS['request']['season'].'
                       AND d.team = o.team
                       AND d.game = o.game
              GROUP BY `team`
              '.$filter.'
              ORDER BY '.$order;

    }

    return false;
  } // _getQuery()


  /** ------------------------------------------------------------------------
   * handles a request to this page
   * -------------------------------------------------------------------------
   */
  function getHTML() {
    global $_SYS;

    $output = '';

    if (in_array($_SERVER['HTTP_USER_AGENT'], array('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Mozilla/5.0 (Twiceler-0.9 http://www.cuil.com/twiceler/robot.html)'))) {
        return $output;
    }

    /* determine period */

    $period = $_GET['period'];

    if (!array_key_exists($period, $this->periods) && intval($period) == 0) {
      if ($_SYS['request']['week'] < 0) {
        $period = 'pre';
      } elseif ($_SYS['request']['week'] > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
        $period = 'post';
      } else {
        $period = 'reg';
      }
    }

    /* determine category */

    $cat = $_GET['cat'];

    if (!in_array($cat, array_merge(array_keys($this->categories['ind']['categories']), array_keys($this->categories['team']['categories'])))) {
      $cat = 'fantasy';
    }

    /* determine if averages should be shown */

    $avg = intval($_GET['avg']);

    /* determine sort order */

    $order = array();
    $order_query = '';

    for ($i = 1; $i <= 3; ++$i) {
      if ($_GET['order'.$i] && in_array(strtoupper($_GET['type'.$i]), array('ASC', 'DESC'))) {
        $order[] = array('column' => strtolower($_GET['order'.$i]), 'type' => strtoupper($_GET['type'.$i]));
        $order_query .= '&amp;order'.$i.'='.urlencode($_GET['order'.$i]).'&amp;type'.$i.'='.urlencode($_GET['type'.$i]);
      }
    }

    if (count($order) == 0) {
      $categories = array_merge($this->categories['ind']['categories'], $this->categories['team']['categories']);
      $order[] = $categories[$cat]['order'];
      unset($categories);
    }

    /* determine min/max-filters */

    $filter = array('min' => array(), 'max' => array());
    $filter_query = '';

    foreach ($_GET as $_key => $_val) {
      $_val = trim($_val);

      if (strlen($_val) == 0) {
        continue;
      }

      if (preg_match('/^min_/', $_key)) {
        $filter['min'][substr($_key, 4)] = $_val;
        $filter_query .= '&amp;'.urlencode($_key).'='.urlencode($_val);
      } elseif (preg_match('/^max_/', $_key)) {
        $filter['max'][substr($_key, 4)] = $_val;
        $filter_query .= '&amp;'.urlencode($_key).'='.urlencode($_val);
      } elseif ($_key == 'filter_name') {
        $filter['filter']['name'] = $_val;
        $filter_query .= '&amp;'.urlencode($_key).'='.urlencode($_val);
      } elseif ($_key == 'filter_team' && !(array_key_exists('filter_team_pre', $_GET) && trim($_GET['filter_team_pre']) != '')) {
        $filter['filter']['team'] = $_val;
        $filter_query .= '&amp;'.urlencode($_key).'='.urlencode($_val);
      } elseif ($_key == 'filter_team_pre' && $_val != '') {
        $filter['filter']['team'] = $_val;
        $filter_query .= '&amp;'.urlencode($_key).'='.urlencode($_val);
      }
    }

    unset($_key, $_val);

    $season = $_SYS['request']['season'];

    /* season & period header */

    $output .= '
<p>';

    $_period = array();

    foreach ($_SYS['season'] as $_key => $_val) {
      if ($season == $_key) {
        $_period[] = '
  [ '.$_val['name'].' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg='.$avg.'&amp;period='.$period.$order_query.$filter_query.'&amp;season='.$_key.'">'.$_val['name'].'</a>';
      }
    }

    $output .= join(' &middot;', $_period).'
</p>
<p>';

    $_period = array();

    foreach ($this->periods as $_key => $_val) {
      if ($period == $_key) {
        $_period[] = '
  [ '.$_val.' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg='.$avg.'&amp;period='.$_key.$order_query.$filter_query.'&amp;season='.$_SYS['request']['season'].'">'.$_val.'</a>';
      }
    }

    $output .= join(' &middot;', $_period).'<br />';

    $_period = array();

    foreach ($_SYS['season'][$_SYS['request']['season']]['weeks'] as $_key => $_val) {
      if ($_key < 0) {
        $_val = 'P'.(-$_key);
      } elseif ($_key > $_SYS['season'][$_SYS['request']['season']]['reg_weeks']) {
        $_val = $_SYS['season'][$_SYS['request']['season']]['post_names'][$_key - $_SYS['season'][$_SYS['request']['season']]['reg_weeks'] - 1]['acro'];
      } else {
        $_val = $_key;
      }

      if ($period == $_key) {
        $_period[] = '
  [ '.$_val.' ]';
      } else {
        $_period[] = '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg='.$avg.'&amp;period='.$_key.$order_query.$filter_query.'&amp;season='.$_SYS['request']['season'].'">'.$_val.'</a>';
      }
    }

    $output .= join(' &middot;', $_period);
    $output .= '
</p>';

    unset($_period, $_key, $_val);

    /* category header */

    foreach ($this->categories as $_type) {
      $output .= '
<p>'.$_type['name'].':';

      foreach ($_type['categories'] as $_cat_key => $_cat) {
        if ($cat == $_cat_key) {
          $output .= '
  [ '.$_cat['name'].' ]';
        } else {
          $output .= '
  <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$_cat_key.'&amp;avg='.$avg.'&amp;period='.$period.'&amp;season='.$_SYS['request']['season'].'">'.$_cat['name'].'</a>';
        }

        if ($_cat['break']) {
          $output .= '<br />';
        } elseif ($_cat['end']) {
          $output .= '';
        } else {
          $output .= ' &middot;';
        }
      }

      $output .= '
</p>';
    }

    unset($_type, $_cat_key, $_cat);

    /* averages header */

    $output .= '
<p>
  '.($avg  ? '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg=0&amp;period='.$period.$order_query.$filter_query.'&amp;season='.$_SYS['request']['season'].'">Totals</a>'   : '[ Totals ]').' &middot;
  '.(!$avg ? '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg=1&amp;period='.$period.$order_query.$filter_query.'&amp;season='.$_SYS['request']['season'].'">Averages</a>' : '[ Averages ]').'
</p>';

    /* get list of conferences and divisions for dropdown */

    $query = 'SELECT   t.conference                                      AS conference,
                       CONCAT(t.conference, SUBSTRING(t.division, 1, 1)) AS division,
                       '.(array_key_exists($cat, $this->categories['ind']['categories']) ? 'LCASE(n.acro)' : 'LCASE(n.nick)').' AS team
              FROM     '.$_SYS['table']['team'].' AS t
                       LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
              WHERE    t.season = '.$_SYS['request']['season'].'
              ORDER BY conference, division, acro';
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $_conference = array();
    $_division = array();

    while ($row = $result->fetch_assoc()) {
      $_conference[$row['conference']][] = $row['team'];
      $_division[$row['division']][] = $row['team'];
    }

    $team_dropdown = array();

    foreach ($_conference as $_conf => $_teams) {
      $team_dropdown[] = array('display' => $_conf, 'value' => join('|', $_teams), 'tooltip' => $_conf);
    }

    foreach ($_division as $_div => $_teams) {
      $team_dropdown[] = array('display' => $_div, 'value' => join('|', $_teams), 'tooltip' => $_div);
    }

    unset($query, $result, $_conference, $_division, $_conf, $_div, $_teams);

    /* query */

    if (!$query = $this->_getQuery($cat, $avg, $period, $order, $filter)) {
      return $_SYS['html']->fehler('1', 'Query does not (yet?) exist.');
    }

    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    $info = $result->info();

    /* sort order */

    $_categories = array();

    foreach ($info['name'] as $_info) {
      if ($_info != 'user') {
        $_categories[] = array('display' => strtoupper($_info), 'value' => $_info, 'tooltip' => strtoupper($_info));
      }
    }

    $_types = array(array('display' => 'ASC', 'value' => 'ASC', 'tooltip' => 'ASC'),
                    array('display' => 'DESC', 'value' => 'DESC', 'tooltip' => 'DESC'));

    $output .= '
<form action="'.$_SYS['page'][$_SYS['request']['page']]['url'].'" method="get">
<p>
  Sort Order:
  1st:
'.$_SYS['html']->dropdown('order1', $_categories, $order[0]['column'], '', '', 'tabindex="10"').'
'.$_SYS['html']->dropdown('type1', $_types, $order[0]['type'], '', '', 'tabindex="20"').'
  2nd:
'.$_SYS['html']->dropdown('order2', $_categories, $order[1]['column'], '', '', 'tabindex="30"').'
'.$_SYS['html']->dropdown('type2', $_types, $order[1]['type'], '', '', 'tabindex="40"').'
  3rd:
'.$_SYS['html']->dropdown('order3', $_categories, $order[2]['column'], '', '', 'tabindex="50"').'
'.$_SYS['html']->dropdown('type3', $_types, $order[2]['type'], '', '', 'tabindex="60"').'
'.$_SYS['html']->hidden('avg', $avg).'
'.$_SYS['html']->hidden('cat', $cat).'
'.$_SYS['html']->hidden('period', $period).'
'.$_SYS['html']->hidden('season', $_SYS['request']['season']).'
'.$_SYS['html']->submit('submit', 'Sort & Filter', '', 'tabindex="99"').'
</p>';

    unset($_categories, $_types);

    /* output */

    $output .= '
<table class="stats">
  <thead>';

    $thead = '
    <tr>
      <th scope="col">#</th>';

    /* header information */

    foreach ($info['name'] as $_info) {
      if ($_info != 'user') {
        $thead .= '
      <th scope="col">'.strtoupper($_info).'</th>';
      }
    }

    unset($_info);

    $thead .= '
    </tr>';

    $output .= $thead.'
  </thead>
  <tbody class="filter">
    <tr>
      <td>&nbsp;</td>';

    /* sorting buttons */

    $info = $result->info();

    foreach ($info['name'] as $_info) {
      if ($_info != 'user') {
        $output .= '
      <td>
        <a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg='.$avg.'&amp;period='.$period.'&amp;order1='.urlencode($_info).'&amp;type1=asc'.$filter_query.'&amp;season='.$_SYS['request']['season'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/up.gif" alt="&uarr;" /></a><a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?cat='.$cat.'&amp;avg='.$avg.'&amp;period='.$period.'&amp;order1='.urlencode($_info).'&amp;type1=desc'.$filter_query.'&amp;season='.$_SYS['request']['season'].'"><img src="'.$_SYS['dir']['hostdir'].'/styles/'.$_SYS['user']['style'].'/down.gif" alt="&darr;" /></a>
      </td>';
      }
    }

    $output .= '
    </tr>';

    /* print filter rows */

    foreach (array('min', 'max') as $_minmax) {
      $output .= '
    <tr>
      <th scope="row">'.$_minmax.'</th>';

      if ($_minmax == 'min') {
        if (in_array('name', $info['name'])) {
          $output .= '
      <td rowspan="2">'.$_SYS['html']->textfield('filter_name', $filter['filter']['name'], 15, 0, '', 'tabindex="100"').'</td>';
        }

        $output .= '
      <td>'.$_SYS['html']->textfield('filter_team', $filter['filter']['team'], 3, 0, '', 'tabindex="110"').'</td>';
      } else {
        $output .= '
      <td>
        '.$_SYS['html']->dropdown('filter_team_pre', $team_dropdown, '', '', '', 'tabindex="115"', 8).'
      </td>';
      }

      $_tabindex = $_minmax == 'min' ? 110 : 115;

      foreach ($info['name'] as $_info) {
        if ($_info == 'name' || $_info == 'team' || $_info == 'user') {
          continue;
        }

        $_tabindex += 10;

        $_output = $_SYS['html']->textfield($_minmax.'_'.$_SYS['html']->specialchars($_info), $filter[$_minmax][$_info], 3, 0, '', 'tabindex="'.$_tabindex.'"');

        $output .= '
      <td>'.$_output.'</td>';
      }

      $output .= '
    </tr>';
    }

    unset($_minmax, $_info, $_output, $_tabindex);

    $output .= '
  </tbody>
  <tbody>';

    /* print each row */

    if ($result->rows() == 0) {
      $output .= '
    <tr>
      <td colspan="'.(count($info['name'])).'">No stats available.</td>
    </tr>';
    }

    $_previous = null;

    for ($i = 1; $i <= $result->rows(); ++$i) {
      if ($i % 32 == 1 && $i != 1) {
        $output .= '
  </tbody>
  <tbody class="header">'.$thead.'
  </tbody>
  <tbody>';
      }

      $row = $result->fetch_assoc();

      $_current = $row[$order[0]['column']].'::'.$row[$order[1]['column']].'::'.$row[$order[2]['column']];
      $_rank = $_current == $_previous ? '&nbsp;' : $i;
      $_previous = $_current;

      $output .= '
    <tr'.($row['user'] == $_SYS['user']['id'] ? ' class="user"' : '').'>
      <td class="pos">'.$_rank.'</td>';

      foreach ($info['name'] as $_info) {
        if ($_info == 'user') {
          continue;
        }

        $_is_team_category = $_info == 'team' && array_key_exists($cat, $this->categories['team']['categories']);

        $_class = array();

        if ($_info == $order[0]['column']) {
          $_class[] = 'order';
        }

        if ($_info == 'name' || ($_info == 'team' && $_is_team_category)) {
          $_class[] = $_info;
        }

        if ($_info == 'team') {
          $_team_id = substr($row[$_info], 0, strpos($row[$_info], ':'));
          $row[$_info] = substr($row[$_info], strpos($row[$_info], ':') + 1);
        }

        if ($_info == 'ratio' && $row[$_info] > 0) {
          $row[$_info] = '+'.$row[$_info];
        }

        $output .= '
      <td'.(count($_class) > 0 ? ' class="'.join(' ', $_class).'"' : '').'>'.($_info == 'team' ? '<a href="'.$_SYS['page']['team/home']['url'].'?id='.$_team_id.'">' : '').($_info == 'team' && !$_is_team_category && $_SYS['user']['logos'] ? '<img src="'.$_SYS['dir']['hostdir'].'/images/logos/'.$_SYS['user']['logos'].'/'.strtolower($row[$_info]).'.gif" alt="'.$row[$_info].'" class="logo" />' : $row[$_info]).($_info == 'team' ? '</a>' : '').'</td>';

        unset($_team_id);
      }

      $output .= '
    </tr>';
    }

    unset($_previous, $_current, $_rank, $i, $_info, $_class);

    $output .= '
  </tbody>
</table>
</form>';

    return $output;
  } // getHTML()
}