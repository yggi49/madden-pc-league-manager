<?php
/**
 * @(#) admin/index.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {

    global $_SYS;

    $output = '
<p>Description of various admin functions.</p>
<ul>
  <li><a href="'.$_SYS['page']['admin/season_list']['url'].'">'.$_SYS['page']['admin/season_list']['linktext'].'</a></li>
</ul>';

    if ($_SYS['site']['title'] == 'FFML' && in_array($_SYS['user']['nick'], array('igor', 'MoRe99'))) {
        $result = $_SYS['dbh']->query('select id, nick, rating, count(*) as anzahl
from
(
    select   if(g.away_sub = 0, g.away_hc, g.away_sub) as id,
             ua.nick as nick,
             g.away_rate as rating
    from     ffml_game g
             left join ffml_user ua on if(g.away_sub = 0, g.away_hc, g.away_sub) = ua.id
    where    g.away_rate != 0
    union all
    select   if(g.home_sub = 0, g.home_hc, g.home_sub) as id,
             uh.nick as nick,
             g.home_rate as rating
    from     ffml_game g
             left join ffml_user uh on if(g.home_sub = 0, g.home_hc, g.home_sub) = uh.id
    where    g.home_rate != 0
) as ratings
group by id, nick, rating
order by nick, rating desc') or die($_SYS['dbh']->error());

        $nicks = array();

        while ($row = $result->fetch_assoc()) {
            if (!array_key_exists($row['nick'], $nicks)) {
                $nicks[$row['nick']] = array(1 => 0, 0, 0);
            }

            $nicks[$row['nick']][$row['rating']] = $row['anzahl'];
        }

        $output .= '
<h2>Ratings</h2>
<table>
    <thead>
        <tr>
            <th scope="col">Nick</th>
            <th scope="col">Positiv</th>
            <th scope="col">Neutral</th>
            <th scope="col">Negativ</th>
            <!-- <th scope="col">%</th> -->
        </tr>
    </thead>
    <tbody>';

        $ratings = array(1 => 'negativ', 2 => 'neutral', 3 => 'positiv');

        foreach ($nicks as $nick => $rating) {
            $output .= '
        <tr>
            <td>'.$_SYS['html']->specialchars($nick).'</td>';

            $sum = 0;

            for ($i = 3; $i > 0; --$i) {
                $output .= '
            <td>'.$rating[$i].'</td>';
                $sum += $rating[$i] * ($i - 2);
            }

            $output .= '
            <!-- <td>'.sprintf('%.0f%%', 100 * $sum / array_sum($rating)).'</td> -->
        </tr>';
        }

        $output .= '
    </tbody>
</table>';
    }

    return $output;
  } // getHTML()

} // Page

?>