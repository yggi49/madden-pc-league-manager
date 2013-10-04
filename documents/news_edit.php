<?php
/**
 * @(#) news_edit.php
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

    /* read news from db */

    if ($id != 0) {
      $query = 'SELECT id, team, title, news, user, date, top
                FROM   '.$_SYS['table']['news'].'
                WHERE  id = '.$id.' AND team = 0';
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
<form action="'.$_SYS['page']['news_save']['url'].'" method="post" enctype="multipart/form-data">
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
  <dt>'.$_SYS['html']->label('ftop', 'Top-News').'</dt>
  <dd>'.$_SYS['html']->checkbox('top', '1', $row['top'], 'ftop').'</dd>
</dl>

<dl>
  <dt>
    '.$_SYS['html']->hidden('id', $id).'
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