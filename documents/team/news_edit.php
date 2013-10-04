<?php
/**
 * @(#) team/news_edit.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';
    $id = intval($_GET['id']);
    $team = intval($_GET['team']);

    /* read team info */

    $query = 'SELECT u.id                        AS uid,
                     u.nick                      AS user,
                     CONCAT(n.team, " ", n.nick) AS team,
                     t.season                    AS season
              FROM   '.$_SYS['table']['team'].' AS t
                     LEFT JOIN '.$_SYS['table']['nfl'].' AS n ON t.team = n.id
                     LEFT JOIN '.$_SYS['table']['user'].' AS u ON t.user = u.id
              WHERE  t.id = '.$team.'
                     AND u.id = '.$_SYS['user']['id'];
    $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    if ($result->rows() == 0) {
      return $_SYS['html']->fehler('1', 'Team does not exist or you are not owner.');
    }

    $row = $result->fetch_assoc();

    $_SYS['request']['season'] = $row['season'];

    $output .= '
<p>
  '.$row['team'].'
  '.(strlen($row['user']) > 0 ? '(HC '.($_SYS['page']['profile']['access'] ? '<a href="'.$_SYS['page']['profile']['url'].'?id='.$row['uid'].'">'.$_SYS['html']->specialchars($row['user']).'</a>' : $_SYS['html']->specialchars($row['user'])).')' : '').'
</p>
<p>
  <a href="'.$_SYS['page']['team/home']['url'].'?id='.$team.'">Home</a>
  &middot; <a href="'.$_SYS['page']['team/news']['url'].'?id='.$team.'">News</a>
  &middot; <a href="'.$_SYS['page']['team/roster']['url'].'?id='.$team.'">Roster</a>
  &middot; <a href="'.$_SYS['page']['team/schedule']['url'].'?id='.$team.'">Schedule</a>
  &middot; <a href="'.$_SYS['page']['team/stats']['url'].'?id='.$team.'">Stats</a>
  &middot; <a href="'.$_SYS['page']['team/scouts']['url'].'?id='.$team.'">Scouts</a>
</p>';

    /* read news from db */

    if ($id != 0) {
      $query = 'SELECT id, team, title, news, user, date
                FROM   '.$_SYS['table']['news'].'
                WHERE  id = '.$id.' AND team = '.$team;
      $result = $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

      if ($result->rows() == 0) {
        return $_SYS['html']->fehler('3', 'Message not found.');
      }

      $row = $result->fetch_assoc();

      /* image available? */

      $image_files = glob($_SYS['dir']['imgdir'].'/news/'.$id.'.*');
      if (!$image_files) $image_files = array();
      foreach ($image_files as $_filename) {
        $row['image'] = $_SYS['dir']['hostdir'].'/images/news/'.basename($_filename);
        break;
      }

      unset($_filename);
    }

    /* assemble form */

    $output .= '
<form action="'.$_SYS['page']['team/news_save']['url'].'" method="post" enctype="multipart/form-data">
<dl>
  <dt>Allowed BB-Code</dt>
  <dd>[b]bold[/b], [i]italics[/i], [url=http://www.xyz.com]hyperlink[/url]</dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('ftitle', 'Title').'</dt>
  <dd>
    '.$_SYS['html']->textfield('title', $row['title'], 0, 0, '', 'id="ftitle" tabindex="10"').'
  </dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('fnews', 'News').'</dt>
  <dd>
    '.$_SYS['html']->textarea('news', $row['news'], 10, 50, '', 'id="fnews" tabindex="20"', 4).'
  </dd>
</dl>

<dl>
  <dt>'.$_SYS['html']->label('fimage', 'Image').' (100&nbsp;kB)</dt>';

    if ($row['image']) {
      $output .= '
  <dd>
    <img src="'.$row['image'].'" alt="" /><br />
    '.$_SYS['html']->checkbox('rmimg', 1, 0, 'frmimg').' '.$_SYS['html']->label('frmimg', 'Delete current image').'
  </dd>';
    }

      $output .= '
  <dd>
    '.$_SYS['html']->file('image', 0, '', 'id="fimage"').'
    (jpg, gif, png)
  </dd>
</dl>

<dl>
  <dt>
    '.$_SYS['html']->hidden('id', $id).'
    '.$_SYS['html']->hidden('team', $team).'
  </dt>
  <dd>
    '.$_SYS['html']->submit('submit', 'Save').'
    '.($id != 0 ? $_SYS['html']->submit('submit', 'Delete') : '').'
  </dd>
</dl>
</form>';

    return $output;
  } // getHTML()

} // Page

?>