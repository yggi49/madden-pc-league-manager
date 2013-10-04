<?php
/**
 * @(#) html.php
 */

class HTML {

  var $bbcode;

  function HTML() {
    require('system/bbcode.php');

    // Zeilenumbrüche verschiedener Betriebsysteme vereinheitlichen
    function convertlinebreaks ($text) {
        return preg_replace ("/\015\012|\015|\012/", "\n", $text);
    }

    // Alles bis auf Neuezeile-Zeichen entfernen
    function bbcode_stripcontents ($text) {
        return preg_replace ("/[^\n]/", '', $text);
    }

    function do_bbcode_url ($action, $attributes, $content, $params, $node_object) {
        if (!isset ($attributes['default'])) {
            $url = $content;
            $text = htmlspecialchars ($content);
        } else {
            $url = $attributes['default'];
            $text = $content;
        }
        if ($action == 'validate') {
            if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
                || substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
                return false;
            }
            return true;
        }
        return '<a href="'.htmlspecialchars ($url).'">'.$text.'</a>';
    }

    // Funktion zum Einbinden von Bildern
    function do_bbcode_img ($action, $attributes, $content, $params, $node_object) {
        if ($action == 'validate') {
            if (substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:'
                || substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:') {
                return false;
            }
            return true;
        }
        return '<img src="'.htmlspecialchars($content).'" alt="">';
    }

    $this->bbcode = new StringParser_BBCode ();
    $this->bbcode->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');

    $this->bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
    $this->bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'nl2br');
    $this->bbcode->addParser ('list', 'bbcode_stripcontents');

    $this->bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array ());
    $this->bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array ());
    $this->bbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
                      'link', array ('listitem', 'block', 'inline'), array ('link'));
    $this->bbcode->addCode ('link', 'callback_replace_single', 'do_bbcode_url', array (),
                      'link', array ('listitem', 'block', 'inline'), array ('link'));
    $this->bbcode->addCode ('img', 'usecontent', 'do_bbcode_img', array (),
                      'image', array ('listitem', 'block', 'inline', 'link'), array ());
    $this->bbcode->addCode ('bild', 'usecontent', 'do_bbcode_img', array (),
                      'image', array ('listitem', 'block', 'inline', 'link'), array ());
    $this->bbcode->setOccurrenceType ('img', 'image');
    $this->bbcode->setOccurrenceType ('bild', 'image');
    $this->bbcode->setMaxOccurrences ('image', 2);
    $this->bbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
                      'list', array ('block', 'listitem'), array ());
    $this->bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
                      'listitem', array ('list'), array ());
    $this->bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
    $this->bbcode->setCodeFlag ('*', 'paragraphs', true);
    $this->bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
    $this->bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
    $this->bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
    $this->bbcode->setRootParagraphHandling (false);

    // Zeilenumbrüche verschiedener Betriebsysteme vereinheitlichen
    /* function convertlinebreaks ($text) { */
    /*   return preg_replace ("/\015\012|\015|\012/", "<br />\n", $text); */
    /* } */

    /* function do_bbcode_url ($action, $attributes, $content, $params, $node_object) { */
    /*   if ($action == 'validate') { */
    /*     return true; */
    /*   } */
    /*   if (!isset ($attributes['default'])) { */
    /*     return '<a href="'.$content.'">'.$content.'</a>'; */
    /*   } */
    /*   return '<a href="'.htmlspecialchars($attributes['default']).'">'.$content.'</a>'; */
    /* } */

    /* function do_bbcode_anchor ($action, $attributes, $content, $params, $node_object) { */
    /*   if ($action == 'validate') { */
    /*     return true; */
    /*   } */
    /*   if (!isset ($attributes['default'])) { */
    /*     return '<a name="'.$content.'"></a>'; */
    /*   } */
    /*   return '<a name="'.htmlspecialchars($attributes['default']).'">'.$content.'</a>'; */
    /* } */

    /* $this->bbcode = new StringParser_BBCode(); */
    /* $this->bbcode->addFilter(STRINGPARSER_FILTER_PRE, 'convertlinebreaks'); */

    /* $this->bbcode->addCode('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'), */
    /*                  'inline', array ('listitem', 'block', 'inline', 'link'), array ()); */
    /* $this->bbcode->addCode('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'), */
    /*                  'inline', array ('listitem', 'block', 'inline', 'link'), array ()); */
    /* $this->bbcode->addCode('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'), */
    /*                  'link', array ('listitem', 'block', 'inline'), array ('link')); */
    /* $this->bbcode->addCode('anchor', 'usecontent?', 'do_bbcode_anchor', array ('usecontent_param' => 'default'), */
    /*                  'link', array ('listitem', 'block', 'inline'), array ('link')); */
    /* $this->bbcode->setRootParagraphHandling(false); */
  } // constructor()

  function bbcode($string) {
    return $this->bbcode->parse($string);
  }

  /* --------------------------------------------------------------------
   * -------------------------- ALLGEMEINES -----------------------------
   * -------------------------------------------------------------------- */

  /**
   * Ersetzt HTML Special Characters
   *
   * @param $text       Text dessen special characters ersetzt werden sollen
   *
   * optionale Parameter:
   *
   * @param $javascript wenn true werden single quote, double quote und
   *                    backslash escaped
   *
   * @return Text dessen special characters ersetzt wurden
   */
  function specialchars($text, $javascript=false) {

    return mb_convert_encoding(htmlspecialchars($text, ENT_QUOTES), 'HTML-ENTITIES', 'auto');

    $text = htmlentities(html_entity_decode($text, ENT_QUOTES), ENT_QUOTES);

//     $text = preg_replace('/&(amp;)?#0*(\d+);/', '&#$2;', $text); // ersetzt z.B. &amp;#77; wieder durch &#77;
//     if ($javascript) {
//       /* ;) addslashes
//        * ' -> \' und " -> \" und \ -> \\
//        */
//       $text = preg_replace('/(&#39;|&quot;|\\\\)/', '\\\\\\1', $text);
//     }
//     $text = preg_replace('|\[([a-zA-Z]+)\]([^\[]*)\[/\1\]|', '<$1>$2</$1>', $text);
    return $text;
  } // specialchars($text)

  /**
   * Gibt den Inhalt der Variablen aus
   *
   * @param var Variable, die ausgegeben werden soll
   *
   * @return HTML-Code des Variablen-Inhalts
   */
  function dump($var) {
    if (function_exists('var_export')) {
      $output = '<pre class="dump">'.var_export($var, 1).'</pre>';
    } else {
      ob_start();
      print_r($var);
      $output = '<pre class="dump">'.ob_get_contents().'</pre>';
      ob_end_clean();
    }

    return $output;
  } // dump($var)

  /**
   * Generiere einen HTML-Kopf
   *
   * @param $titel       Dokument Titel
   *
   * optionale Parameter:
   * @param $meta        Zusatzinformationen für diese Seite
   *
   *        $meta['author']   = 'Iggy Fedorow';
   *        $meta['keywords'] = 'iggy fedorow webpage';
   *
   * @param $javascript  Javascript für diese Seite
   * @param $styles      Style-Definitionen für diese Seite
   * @param $extern      array mit einzubindende Dateien (javascript, css)
   * @param $other       zusaetzliche Header
   * @param $lang        Sprache des Dokuments
   * @param $dtd         'strict', 'transitional' oder 'frameset'
   *
   * @return HTML-Code des HTML-Kopfes
   */
  function header($titel, $meta=array(), $javascript='', $styles='', $extern=array(), $other='', $lang='de', $dtd='strict') {

    global $_SYS;

    /* generate xml header */

    $output = '';
    /*$output = '<?xml version="1.0" encoding="utf-8"?>'."\n"; */

    /* append dtd */

    switch ($dtd) {
    case 'strict':
      $output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">';
      break;
    case 'frameset':
      $output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "DTD/xhtml1-frameset.dtd">';
      break;
    default:
      $output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">';
    }

    /* general header information */

    $output .= '
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang.'" lang="'.$lang.'">
<head>
  <title>'.$titel.'</title>
  <meta http-equiv="Content-Type" content="'.$_SYS['var']['content_type'].'; charset=utf-8" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="Content-Style-Type" content="text/css" />';

    /* additional meta data */

    foreach ($meta as $key => $value) {
      $output .= '
  <meta name="'.$key.'" content="'.$value.'" />';
    }

    /* fixed meta information */

    $output .= '
  <meta name="author" content="Igor Fedorow" />
  <meta name="copyright" content="&copy; '.date('Y', $_SYS['time']['now']).' Igor Fedorow" />
  <meta name="date" content="'.gmdate('Y-m-d\TH:i:s\Z', $_SYS['time']['now']).'" />';

    /* additional headers */

    $output .= $other;

    /* default style sheet */

    $output .= '
  <link rel="stylesheet" href="'.$_SYS['dir']['hostdir'].'/styles/default.css" type="text/css" />';

    /* external style sheet and javascript files */

    foreach ($extern as $datei) {
      switch (strrchr($datei, '.')) {
      case '.css':
        $output .= '
  <link rel="stylesheet" href="'.$_SYS['dir']['hostdir'].'/styles/'.$datei.'" type="text/css" />';
        break;
      case '.js':
        $output .= '
  <script type="text/javascript" src="'.$_SYS['dir']['hostdir'].'/scripts/'.$datei.'" />';
        break;
      }
    }

    /* additional style sheets */

    if (strlen($styles)) {
      $output .= '
  <style type="text/css">
  <!--'.$styles.'
  //-->
  </style>';
    }

    /* additional javascript */

    if (strlen($javascript)) {
      $output .= '
  <script type="text/javascript">
  <!--'.$javascript.'
  //-->
  </script>';
    }

    $output .= '
</head>';

    return $output;
  } // header($titel, $meta, $javascript, $styles, $extern, $other, $lang, $dtd)

  /**
   * Generiert eine Seitennavigation
   *
   * @param pages       array(1 => '1', 2 => '2', 3 => 'WB');
   * @param name        name des seiten-parameters
   * @param current     aktuelle seite
   * @param url         seite welche gelinkt werden soll
   * @param params      zusaetzliche get-parameter fuer die links
   * @param links       anzahl der seiten die vor bzw nach der aktuellen
   *                    gelinkt werden
   * @param indent      einzug
   */
  function pagebar($pages, $name, $current='', $url='', $params='', $links=4, $indent=0) {

    $output = '';
    $temp   = array();
    $indent = abs(intval($indent));

    $keys   = array_keys($pages);
    ksort($keys);
    $min = intval(array_shift($keys));
    $max = intval(array_pop($keys));

    if (count($pages) == 0) {
      return $output;
    }

    if ($current - $links > $min) {
      $output .= "\n".str_repeat(' ', $indent).'<a href="'.$url.'?'.$name.'=1'.$params.'">&laquo;Erste</a> ...';
    }

    if ($current > $min) {
      $temp[] = "\n".str_repeat(' ', $indent).'<a href="'.$url.'?'.$name.'='.($current - 1).$params.'">&laquo;</a>';
    }

    for ($i = max($current - $links, $min); $i <= min($current + $links, $max); ++$i) {
      if ($i == $current) {
        $temp[] = "\n".str_repeat(' ', $indent).'[ '.$pages[$i].' ]';
      } else {
        $temp[] = "\n".str_repeat(' ', $indent).'<a href="'.$url.'?'.$name.'='.$i.$params.'">'.$pages[$i].'</a>';
      }
    }

    if ($current < $max) {
      $temp[] = "\n".str_repeat(' ', $indent).'<a href="'.$url.'?'.$name.'='.($current + 1).$params.'">&raquo;</a>';
    }

    $output .= join(' &middot;', $temp);

    if ($current + $links < $max) {
      $output .= "\n".str_repeat(' ', $indent).'... <a href="'.$url.'?'.$name.'='.$max.$params.'">Letzte&raquo;</a>';
    }

    return $output;
  } // pagebar(pages, name, current, url, params, links)



  /**
   * Generiere eine Fehler-Seite
   *
   * @param $id       Fehlernummer
   * @param $text     Text der Fehlerseite
   * @param $title    Title der Fehlerseite
   *
   * optionale Parameter:
   * @param $backlink wenn true wird ein ``Zurueck''-Link eingefuegt
   *
   * @return HTML-Code der Fehlerseite
   */
  function fehler($id, $text, $backlink=true, $title='') {
    $output = '
<div class="error">
  <h1>Error #'.$id.(strlen($title) > 0 ? ' ('.$this->specialchars($title).')' : '').'</h1>
  <p>'.$this->specialchars($text).'</p>';

    if ($backlink === true || (is_string($backlink))) {
      $backurl = $backlink === true || $backlink === '' ? 'javascript:history.back();' : $backlink;
      $output .= '
  <a href="'.$backurl.'">Back</a>';
    }

    $output .= '
</div>';

    return $output;
  } // fehler($id, $text, $title, $backlink)

  /* --------------------------------------------------------------------
   * ----------------------- FORMULAR ELEMENTE --------------------------
   * -------------------------------------------------------------------- */

  /**
   * Generiere ein einzeiliges Formular-Textfeld.
   *
   * @param $name       Name-Attribut des Textfeldes
   *
   * optionale Parameter:
   * @param $value      Vorbelegung des Textfeldes
   * @param $size       Anzeigelaenge (Anz. Zeichen) des Textfeldes
   * @param $maxlength  interne Feldlaenge (Anz. Zeichen) des Textfeldes
   * @param $class      Style des Textfeldes
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Textfeldes
   */
  function textfield($name, $value='', $size=0, $maxlength=0, $class='', $other='') {
    $value     = strlen($value)     > 0 ? ' value="'.$this->specialchars($value).'"' : '';
    $size      = intval($size)      > 0 ? ' size="'.intval($size).'"'                : '';
    $maxlength = intval($maxlength) > 0 ? ' maxlength="'.intval($maxlength).'"'      : '';
    $class     = strlen($class)     > 0 ? ' class="'.$class.'"'                      : '';
    $other     = strlen($other)     > 0 ? ' '.$other                                 : '';

    return '<input type="text" name="'.$name.'"'.$value.$size.$maxlength.$class.$other.' />';
  } // textfield($name, $value, $size, $maxlength, $class, $other)

  /**
   * Generiere ein Formular-Passwortfeld.
   *
   * @param $name       Name-Attribut des Passwortfeldes
   *
   * optionale Parameter:
   * @param $value      Vorbelegung des Passwortfeldes
   * @param $size       Anzeigelaenge (Anz. Zeichen) des Passwortfeldes
   * @param $maxlength  interne Feldlaenge (Anz. Zeichen) des Passwortfeldes
   * @param $class      Style des Passwortfeldes
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Passwortfeldes
   */
  function password($name, $value='', $size=0, $maxlength=0, $class='', $other='') {
    $value     = strlen($value)     > 0 ? ' value="'.$this->specialchars($value).'"' : '';
    $size      = intval($size)      > 0 ? ' size="'.intval($size).'"'                : '';
    $maxlength = intval($maxlength) > 0 ? ' maxlength="'.intval($maxlength).'"'      : '';
    $class     = strlen($class)     > 0 ? ' class="'.$class.'"'                      : '';
    $other     = strlen($other)     > 0 ? ' '.$other                                 : '';

    return '<input type="password" name="'.$name.'"'.$value.$size.$maxlength.$class.$other.' />';
  } // password($name, $value, $size, $maxlength, $class, $other)


  /**
   * Generiere ein Dateiauswahlfeld
   *
   * @param $name       Name-Attribut des Dateifeldes
   * @param $size       Anzeigelaenge (Anz. Zeichen) des Feldes
   *
   * optionale Parameter:
   * @param $class      Style des Feldes
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Dateiauswahlfeldes
   */
  function file($name, $size=0, $class='', $other='') {
    $size      = intval($size)  > 0 ? ' size="'.intval($size).'"' : '';
    $class     = strlen($class) > 0 ? ' class="'.$class.'"'       : '';
    $other     = strlen($other) > 0 ? ' '.$other                  : '';

    return '<input type="file" name="'.$name.'"'.$size.$class.$other.' />';
  } // file($name, $size, $class, $other)

  /**
   * Generiere ein mehrzeiliges Textfeld.
   *
   * @param $name       Name-Attribut des Textfeldes
   * @param $rows       Anzahl der Zeilen des Textfeldes
   * @param $cols       Anzeigelaenge (Anz. Zeichen) pro Zeile des Textfeldes
   *
   * optionale Parameter:
   * @param $value      Vorbelegung des Textfeldes
   * @param $class      Style des Textfeldes
   * @param $other      weitere Attribute (JavaScript, ...)
   * @param $indent     Einrueckung des schliessenden Tags
   *
   * @return HTML-Code des Textfeldes
   */
  function textarea($name, $value, $rows, $cols, $class='', $other='', $indent=0) {
    $class = strlen($class) > 0 ? ' class="'.$class.'"' : '';
    $other = strlen($other) > 0 ? ' '.$other            : '';

    return '<textarea name="'.$name.'" rows="'.intval($rows).'" cols="'.intval($cols).'"'.$class.$other.'>'.$this->specialchars($value).'</textarea>';
  } // textarea($name, $rows, $cols, $value, $class, $other, $indent)

  /**
   * Generiere einen Submit-Button
   *
   * @param $name       Name-Attribut des Buttons
   * @param $value      Text auf dem Button
   *
   * optionale Parameter:
   * @param $class      Style des Buttons
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Submit-Buttons
   */
  function submit($name, $value, $class='', $other='') {
    $other = strlen($other) > 0 ? ' '.$other            : '';

    return '<input type="submit" name="'.$name.'" value="'.$this->specialchars($value).'" class="submit'.(strlen($class) > 0 ? " $class" : '').'"'.$other.' />';
  } // submit($name, $value, $class, $other)

  /**
   * Generiere einen Reset-Button
   *
   * optionale Parameter:
   * @param $value      Vorbelegung des Buttons
   * @param $class      Style des Buttons
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Reset-Buttons
   */
  function reset($value='', $class='', $other='') {
    $value = strlen($value) > 0 ? ' value="'.$this->specialchars($value).'"' : '';
    $class = strlen($class) > 0 ? ' class="'.$class.'"'                      : '';
    $other = strlen($other) > 0 ? ' '.$other                                 : '';

    return '<input type="reset"'.$value.$class.$other.' />';
  } // reset($value, $class, $other)

  /**
   * Generiere einen Button
   *
   * @param $name       Name-Attribut des Buttons
   * @param $value      Vorbelegung des Buttons
   *
   * optionale Parameter:
   * @param $class      Class des Buttons
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Buttons
   */
  function button($name, $value, $class='', $other='') {
    $value     = strlen($value)     > 0 ? ' value="'.$this->specialchars($value).'"' : '';
    $size      = intval($size)      > 0 ? ' size="'.intval($size).'"'                : '';
    $maxlength = intval($maxlength) > 0 ? ' maxlength="'.intval($maxlength).'"'      : '';
    $class     = strlen($class)     > 0 ? ' class="'.$class.'"'                      : '';
    $other     = strlen($other)     > 0 ? ' '.$other                                 : '';

    return '<input type="button" name="'.$name.'"'.$value.$class.$other.' />';
  } // button($name, $value, $class, $other)

  /**
   * Generiere ein verstecktes Formularfeld.
   *
   * @param $name  Name-Attribut des versteckten Feldes
   * @param $value Value des versteckten Feldes
   *
   * optionale Parameter:
   * @param $other weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des versteckten Formularfeldes
   */
  function hidden($name, $value, $other='') {
    $other = strlen($other) > 0 ? ' '.$other : '';
    return '<input type="hidden" name="'.$name.'" value="'.$this->specialchars($value).'"'.$other.' />';
  } // hidden($name, $value, $other)

  /**
   * Generiere ein Label.
   *
   * @param $id    ID-Tag des Formular-Elements zu dem das Label gehört
   * @param $value Name-Attribut des versteckten Feldes
   *
   * @return HTML-Code des Labels
   */
  function label($id, $value) {
    return '<label for="'.$id.'">'.$value.'</label>';
  } // label($id, $value)

  /**
   * Generiere eine Checkbox.
   *
   * @param $name       Name-Attribut der Checkbox
   * @param $value      Value der Checkbox
   *
   * optionale Parameter:
   * @param $checked    die Checkbox ist checkediert, wenn $value == $checked
   * @param $id         ID-Tag der Checkbox (z.B. für HTML-Labels)
   * @param $class      Class der Checkbox
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code der Checkbox
   */
  function checkbox($name, $value, $checked='', $id='', $class='', $other='') {
    $checked = $checked === $value    ? ' checked="checked"'  : '';
    $id      = strlen($id)        > 0 ? ' id="'.$id.'"'       : '';
    $other   = strlen($other)     > 0 ? ' '.$other            : '';

    return '<input type="checkbox" name="'.$name.'" value="'.$this->specialchars($value).'" class="checkbox'.(strlen($class) > 0 ? " $class" : '').'"'.$checked.$id.$other.' />';
  } // checkbox($name, $value, $checked, $id, $class, $other)

  /**
   * Generiere einen Radiobutton.
   *
   * @param $name       Name-Attribut des Radiobutton
   * @param $value      Value des Radiobutton
   *
   * optionale Parameter:
   * @param $checked    der Radiobutton ist checkediert, wenn $value == $checked
   * @param $id         ID-Tag der Checkbox (z.B. für HTML-Labels)
   * @param $class      Class des Radiobutton
   * @param $other      weitere Attribute (JavaScript, ...)
   *
   * @return HTML-Code des Radiobutton
   */
  function radio($name, $value, $checked='', $id='', $class='', $other='') {
    $checked = $checked === $value    ? ' checked="checked"'  : '';
    $id      = strlen($id)        > 0 ? ' id="'.$id.'"'       : '';
    $other   = strlen($other)     > 0 ? ' '.$other            : '';

    return '<input type="radio" name="'.$name.'" value="'.$this->specialchars($value).'" class="radio'.(strlen($class) > 0 ? " $class" : '').'"'.$checked.$id.$other.' />';
  } // radiobox($name, $value, $checked, $id, $class, $other)

  /**
   * Generiere eine Dropdown-Liste.
   *
   * @param $name       Name-Attribut der Dropdown-Liste
   * @param $options    ein assoziatives Array mit den auswaehlbaren Optionen
   *
   *        $optionen[] = array('display' => 'Apfel', 'value' => 1, 'tooltip' => 'Gruenes Zeugs');
   *        $optionen[] = array('display' => 'Birne', 'value' => 2, 'tooltip' => 'auch sowas');
   *
   * optionale Parameter:
   * @param $selected   Dropdown-Eintrag ist ausgewählt, wenn sein wert == $aktiv
   * @param $default    Tooltip der Leer-Auswahl
   * @param $class      Style des Radiobutton
   * @param $other      weitere Attribute (JavaScript, ...)
   * @param $indent     Einrueckung
   *
   * @return HTML-Code der Dropdown-Liste
   */
  function dropdown($name, $options, $selected='', $default='', $class='', $other='', $indent=0) {
    $length = 0;
    $indent = abs(intval($indent));
    $opts   = array();

    foreach ($options as $option) {
      $title       = strlen($option['tooltip']) > 0 ? ' title="'.$this->specialchars($option['tooltip']).'"' : '';
      $is_selected = $selected === $option['value'] ? ' selected="selected"'                                 : '';

      $opts[] = "\n".str_repeat(' ', $indent + 2).'<option value="'.$this->specialchars($option['value']).'"'.$title.$is_selected.'>'.$this->specialchars($option['display']).'</option>';
      $length = max(strlen($option['display']), $length);
    }

    $class   = strlen($class)   > 0 ? ' class="'.$class.'"'                        : '';
    $other   = strlen($other)   > 0 ? ' '.$other                                   : '';
    $default = strlen($default) > 0 ? ' title="'.$this->specialchars($default).'"' : '';

    array_unshift($opts, "\n".str_repeat(' ', $indent + 2).'<option value=""'.$default.'>'.str_repeat('-', max(3, $length * 1.4)).'</option>');

    $return = '<select name="'.$name.'"'.$class.$other.'>';
    $return .= join('', $opts);
    $return .= "\n".str_repeat(' ', $indent).'</select>';

    return $return;
  } // dropdown($name, $options, $selected, $default, $class, $other, $indent)

} // HTML

?>
