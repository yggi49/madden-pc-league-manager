<?php
/**
 * @(#) template.php
 */

class Template {

  function Template() {} // constructor

  function getHTML() {
    global $_SYS;

    $output = '';

    /* determine naked day */

    $naked = mktime(0, 0, 0, 04, 05, date('Y')) <= $_SYS['time']['now']
             && $_SYS['time']['now'] < mktime(0, 0, 0, 04, 06, date('Y'));

    /* determine user style */

    if (strlen($_SYS['user']['style']) == 0) {
      $_SYS['user']['style'] = $_SYS['site']['style'];
    }

    if (strpos($_SYS['request']['page'], 'static://') === 0) {

      $output .= $_SYS['html']->header(ucfirst(preg_replace('|^.*/(.*)\..*$|', '\1', $_SYS['request']['path'])), array(), '', '', $naked ? array() : array($_SYS['user']['style'].'.css'));
      $content = file_get_contents(preg_replace('|^static://|', '', $_SERVER['DOCUMENT_ROOT'].$_SYS['request']['path']));

    } else {
      $pageinfo = $_SYS['page'][$_SYS['request']['page']];

      /* include requested page */

      require($pageinfo['document']);
      $page = new Page();

      /* generate header */

      $meta = array();

      if (strlen($pageinfo['keywords'])) {
        $meta['keywords'] = $pageinfo['keywords'];
      }

      if (strlen($pageinfo['description'])) {
        $meta['description'] = $pageinfo['description'];
      }

      $output .= $_SYS['html']->header($pageinfo['title'], $meta, '', '', $naked ? array() : array($_SYS['user']['style'].'.css'), $page->getHeader());

      /* get page contents */

      $content = $page->getHTML();
    }

    $output .= '
<body>';

    if ($naked) {
      $output .= '
<h1>Wohin ist das Design verschwunden?</h1>
<p>
Heute ist der zweite allj&auml;hrliche nackte CSS Tag der von Dustin Diaz ins Leben
gerufen wurde um zu zeigen, dass eine Webseite auch ohne Layout lesbar bleiben
kann, wenn die Webstandards eingehalten wurden. Insbesondere soll hiermit
gezeigt werden, wie Inhalte im Netz auch f&uuml;r Menschen mit Handicaps wie
Blindheit usw. barrierefrei zugaenglich gemacht werden. (Es gibt in
Deutschland etwa 155.000 blinde Menschen, 1% der kompletten Bev&ouml;lkerung ist
Farbenblind)  <a href="http://naked.dustindiaz.com/">mehr Informationen...</a>
</p>
<hr />';
    }

    $output .= '
<div class="hide"><a href="#content">Skip Navigation</a></div>';

    /* create main navigation bar */

    $output .= '
<div id="navbar">
<ul>';

    foreach ($_SYS['site']['navbar'] as $_page) {
      if ($_page == 'STATIC') {
        $static_files = glob($_SERVER['DOCUMENT_ROOT'].$_SYS['dir']['hostdir'].'/static/*.htm*');
        if (!$static_files) $static_files = array();
        foreach ($static_files as $_filename) {
          $_filename = preg_replace('|^.*/|', '', $_filename);
          if (!preg_match('|\.no\.|', $_filename)) {
            $output .= '
  <li><a href="'.$_SYS['dir']['hostdir'].'/static/'.$_filename.'">'.ucfirst(preg_replace('|\.[^.]*$|', '', $_filename)).'</a></li>';
          }
        }
      } elseif ($_SYS['page'][$_page]['access']) {
        $output .= '
  <li><a href="'.$_SYS['page'][$_page]['url'].($_SYS['var']['season'] != $_SYS['request']['season'] ? '?season='.$_SYS['request']['season'] : '').'">'.$_SYS['page'][$_page]['linktext'].'</a></li>';
      }
    }

    unset($_page, $_filename);

    $output .= '
</ul>
</div>';

    /* insert page contents */

    $output .= '
<div id="content">';

    $output .= $content;

    /* template footer */

    $output .= '
  <br class="float" id="endcontent" />
</div>
<p id="switcher">';

    $_seasons = array();

    foreach (array_keys($_SYS['season']) as $_season) {
      $_seasons[] = "\n".'  '.($_season == $_SYS['request']['season'] ? '[ '.$_SYS['season'][$_season]['name'].' ]' : '<a href="'.$_SYS['page'][$_SYS['request']['page']]['url'].'?season='.$_season.'">'.$_SYS['season'][$_season]['name'].'</a>');
    }

    $output .= join(' &middot;', $_seasons).'
</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="paypal">
<p id="status">
';

    $output .= $_SYS['user']['id']
               ? 'You are logged in as <em>'.$_SYS['user']['nick'].'</em>'
               : 'You are not logged in';

    $output .= '
| Valid XHTML 1.0 Strict
| Valid CSS
  <br />
  <input type="hidden" name="cmd" value="_s-xclick" />
  <input type="submit" name="submit" value="Freiwillige Spende (PayPal)" class="submit" />
  <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
</p>
</form>
</body>
</html>';

    return $output;
  } // getHTML()

} // Template
?>