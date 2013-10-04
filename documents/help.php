<?php
/**
 * @(#) help.php
 */

class Page {

  function Page() {} // constructor


  function getHeader() {
    global $_SYS;

    return '';
  } // getHeader()


  function getHTML() {
    global $_SYS;

    $text = '[b]CONTROL PANEL[/b]

im "control panel" kannst du deine persoenlichen einstellungen verwalten.

* [b]nick:[/b] dein name hier auf der liga-hp; du kannst ihn auch aendern wenn du willst

* [b]new pass:[/b] falls du dein passwort aendern willst dann kannst du hier ein neues angeben

* [b]confirm pass:[/b] falls du oben ein neues passwort angegeben hast dann musst du es hier nochmal eingeben um tippfehler zu vermeiden

* [b]email:[/b] deine email-adresse; musst du nicht angeben wenn du nicht willst

* [b]show email:[/b] gibt an ob andere eingeloggte benutzer deine email-adresse in deinem profil sehen koennen.  nicht eingeloggte benutzer koennen die email-adresse so oder so nicht sehen.

* [b]notify:[/b] falls diese checkbox aktiviert ist dann erhaeltst du sobald eine neue partie eingetragen wurde eine email mit dem ergebnis samt link zum box score.  du musst dazu oben latuernich eine gueltige email-adresse eingetragen haben damit das funktioniert.

* [b]phone:[/b] hier kannst du deine telefonnummer angeben; kann von eingeloggten benutzern in deinem profil gesehen werden

* [b]icq:[/b] rate mal... ;-)

* [b]show ip:[/b] falls aktiviert dann koennen eingeloggte benutzer in deinem profil sehen unter welcher ip-adresse du zuletzt auf der liga-homepage warst.  ist auch nuetzlich um die eigene ip herauszufinden.

* [b]style:[/b] hier kannst du deinen persoenlichen style fuer die liga-homepage auswaehlen.

wenn du fertig bist klickst du auf "save" und deine einstellungen werden gespeichert.  dein oeffentliches profil kannst du dir ansehen wenn du zb im hauptmenue auf die seite "teams" gehst und in der uebersicht dann auf deinen namen klickst.  wichtig: nicht eingeloggte benutzer (gaeste) koennen keine profile ansehen.


[b]GAMELOGS UPLOADEN[/b]

sobald der upload fuer eine spielwoche freigegeben ist findest du auf der seite "schedule" in der score box neben dem namen deines teams in grosses "U".  wenn du darauf klickst gelangst du zur log-upload seite.  dort kannst du von deiner festplatte das gamelog zum entsprechenden spiel auswaehlen und hinaufladen.

falls du zwei oder mehrere gamelogs vorliegen hast -- zb weil das spiel unterbrochen wurde -- so kannst du diese dort ebenfalls auswaehlen; sie werden beim upload automatisch zu einem einzelnen neuen log zusammengefuegt.  wichtig dabei: das erste log in der liste bestimmt die gesamtspieldauer.  wenn im ersten log zb quarter length "8 minutes" notiert ist dann wird am ende geprueft ob die time of possession der beiden teams in summe auch wirklich 32 minuten ausmacht.  falls in einem oder mehreren logfiles (bzw ggf auch im zusammengefuegten log) inkonsistenzen auftreten so wird eine entsprechende fehlermeldung ausgegeben und der upload abgebrochen.

sobald von beiden spielern ein gamelog fuer das entsprechende spiel am server ist werden die beiden logs miteinander verglichen.  wenn sie uebereinstimmen so werden die ergebnis und stats ausgelesen und direkt in die datenbank uebernommen.  die ergebnisse sind dann auch sofort bei stats und standings beruecksichtigt.  sollten die beiden logs sich unterscheiden so wird eine fehlermeldung ausgespuckt und auch eine benachrichtigungs-email an die liga-admins verschickt (soferne diese eine email-adresse in ihrem profil angegeben haben ;-)).  oft sind es nur kleinigkeiten wie zb nicht uebereinstimmende slider und der fehler ist schnell auszubessern.

wenn ein spiel erfolgreich eingetragen wurde sieht man auf der seite "schedule" die voll ausgefuellte score box samt den statistical leaders.  ueber den link "box score" gelangt man zur seite mit den box score stats fuer dieses spiel; ueber den link "game log" kann man sich das rohe logfile herunterladen.  ueber den link "recaps" gelangt man zu ...


[b]RECAPS[/b]

hier kann man spielberichte und interviews sowie scouting reports und kommentare zum spiel lesen.  ueber den link "post comment" im abschnitt "comments" kann jeder eingeloggte benutzer kommentare zum spiel abgeben.  eigene kommentare kann man spaeter bei bedarf auch editieren bzw ggf auch wieder loeschen.

recaps/interviews/scouting reports koennen nur von den beiden usern verfasst werden die auch die logfiles hinaufgeladen haben.  dazu klickt man auf den link "add/edit" in einem der drei bereiche.  auch hier gilt dass man alles spaeter noch editieren kann.


[b]VERTRETUNGEN[/b]

ab und zu kann man ein spiel nicht selbst bestreiten und muss eine vertretung darum bitten fuer einen einzuspringen.  wenn du im hauptmenue auf die seite "teams" gehst und dann auf dein eigenes team klickst so gelangst du auf die seite mit der schedule fuer dein team.  mittels der dropdown-listen in der spalte ganz rechts hast du dort fuer jeds spiel die moeglichkeit dir eine vertretung zu bestimmen; einfach einen spieler aus der liste auswaehlen und am ende der tabelle auf "save" klicken.  der hier ausgewaehlte spieler darf dann ebenso fuer das entsprechende spiel gamelogs hinaufladen.

das betreffende spiel zaehlt in puncto head coach career stats (die auch mal kommen werden) fuer jenen spieler der schlussendlich auch das gamelog hinaufgeladen hat.  recaps/interviews/scouting reports duerfen ebenso nur von dem spieler erstellt werden der das log hinaufgeladen hat.  bei absolvierten spielen kann man bei der team schedule in der spalte "coach" schliesslich ablesen wer das spiel gespielt = das gamelog hinaufgeladen hat.  wenn der betreffende spieler mit einem * versehen ist dann bedeutet das dass er als vertretung fuer den eigentlichen coach eingesprungen ist.

hinweis: liga-admins koennen nicht nur fuer das eigene team vertretungen einstellen sondern fuer jedes team.  wenn ein liga-admin ueber die funktion "admin upload" ein gamelog hinauflaedt dann zaehlt das spiel aber jedenfalls fuer den zum zeitpunkt des uploads aktuellen teambesitzer.


[b]STATS[/b]

ueber den hauptmenuepunkt "stats" kommt man auf die statistik-seite.  dort kann man diverse individual stats und team stats einsehen; wahlweise nur fuer einzelne spielwochen oder aber auch ueber einen bestimmten zeitraum.  mit dem toggle "totals/averages" kann man einstellen ob die stats aufsummiert werden oder ob die durchschnittswerte pro spiel angezeigt werden sollen.

wenn du die tabelle schnell nach einer bestimmten spalte sortieren willst dann klicke auf einen der beiden pfeile im entsprechenden spaltenkopf.  alternativ dazu kann man auch die sortierleiste oberhalb der tabelle verwenden welche auch eine feinere (bis zu dreistufige) sortierung nach den diversen spalten ermoeglicht.  klicke auf "sort &amp; filter" wenn du mit hilfe der dropdowns deine sortierreihenfolge gewaehlt hast um diese anzuwenden.

zusaetzlich zum sortieren kann man die tabellen auch nach diversen kriterien filtern.  fuer spalten mit numerischen werden kann man minimum- und maximum-werte angeben; wenn man also zb in der kategorie "individual/passing" in der spalte "cmp" in der zeile "min" 100 eingibt so werden nach einem klick auf "sort &amp; filter" nur mehr jene passer angezeigt die mindestens 100 completions geworfen haben.

in der spalte name (bei individual stats) kann man konkret nach spielern filtern. der asterisk (*) dient hierbei als wildcard und die pipe (|) als trenner von mehreren optionen.  wenn man zb "alex smith" eingibt dann werden alle spieler angezeigt die exakt "alex smith" heissen (gross-/kleinschreibung egal).  eine eingabe "john*|jake*" filter auf alle spielernamen die mit "john" oder "jake" beginnen.

fuer die spalte team gilt selbiges wie fuer die spalte name.  wenn man bei den team stats als filter (in der zeile "min") zum beispiel eingibt: "sf|sea|oak|sd" so wird auf alle west coast teams gefiltert.  wenn man auf alle teams einer conference oder division filtern will so muss man aber nicht umstaendlich alle betreffenden teams in das kleine feld einhaemmern sondern kann in der dropdown-liste unterhalb direkt die division oder conference auswaehlen.  danach noch auf "sort &amp; filter" klicken et voila.
';

    $output = '
<h2 class="boxed">Schnellanleitung</h2>
<p class="boxed">
'.$_SYS['html']->bbcode($text).'
</p>';

    return $output;
  } // getHTML()

} // Page

?>