<?php
/**
 * @(#) team/news_save.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $output = '';

    /* error if this is not a POST request */

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      return $_SYS['html']->fehler('1', 'Bad request.');
    }

    /* determine news and team id */

    $id = intval($_POST['id']);
    $team = intval($_POST['team']);

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
      return $_SYS['html']->fehler('2', 'Team does not exist or you are not owner.');
    }

    $row = $result->fetch_assoc();

    $_SYS['request']['season'] = $row['season'];

    /* image size */

    if (is_uploaded_file($_FILES['image']['tmp_name'])
        && $_FILES['image']['size'] > 1024 * 100) {
      return $_SYS['html']->fehler('3', 'Image is too large.');
    }

    /* delete image(s)? */

    if ($_POST['rmimg'] || is_uploaded_file($_FILES['image']['tmp_name']) || $_POST['submit'] == 'Delete') {
      $image_files = glob($_SYS['dir']['imgdir'].'/news/'.$id.'.*');
      if (!$image_files) $image_files = array();
      foreach ($image_files as $_filename) {
        unlink($_filename);
      }
    }

    /* assemble query */

    if ($_POST['submit'] == 'Delete') {
      if ($id == 0) {
        return $_SYS['html']->fehler('3', 'Schwindler!!!!!');
      }

      $query = 'DELETE
                FROM   '.$_SYS['table']['news'].'
                WHERE  id = '.$id.' AND team = '.$team;
    } else {
      $query = ($id != 0 ? 'UPDATE ' : 'INSERT INTO ').$_SYS['table']['news'].'
               SET team  = '.$team.',
                   title = '.$_SYS['dbh']->escape_string(trim($_POST['title'])).',
                   news  = '.$_SYS['dbh']->escape_string(trim($_POST['news'])).',
                   user  = '.$_SYS['user']['id'].',
                   date  = '.($id == 0 ? $_SYS['time']['now'] : 'date').'
               '.($id != 0 ? 'WHERE id = '.$id.' AND team = '.$team : '');
    }

    $_SYS['dbh']->query($query) or die($_SYS['dbh']->error());

    /* save image */

    if ($_POST['submit'] != 'Delete' && is_uploaded_file($_FILES['image']['tmp_name'])) {
      if ($id == 0) {
        $id = $_SYS['dbh']->insert_id();
      }

      move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $_SYS['dir']['imgdir'].'/news/'.$id.strtolower(strstr($_FILES['image']['name'], '.'))
      );
    }

    header('Location: '.$_SYS['page']['team/news']['url'].'?id='.$team);
    exit;
  } // getHTML()

} // Page

?>