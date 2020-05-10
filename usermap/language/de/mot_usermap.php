<?php
/**
*
* @package Usermap v0.5.x
* @copyright (c) 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
$lang = array_merge($lang, array(
	'PLURAL_RULE'					=> 1,
	// Module
	'USERMAP'						=> 'Mitgliederkarte',
	'USERMAP_NOT_AUTHORIZED'		=> 'Du bist nicht befugt, die Mitgliederkarte zu sehen.',
	'USERMAP_SEARCHFORM'			=> 'Sucheingabe',
	'USERMAP_LEGEND'				=> 'Legende',
	'USERMAP_CREDENTIALS'			=> 'Die GeoDaten für die Mitgliederkarte wurden bereitgestellt von ',
	'USERMAP_LEGEND_TEXT'			=> 'Zoomen der Karte mit dem Mausrad mit einem Klick in die Karte ein- und ausschalten.',
	'MAP_USERS'						=> array(
		1	=> 'Es ist aktuell %1$s Mitglied in der Mitgliederkarte erfasst.',
		2	=> 'Es sind aktuell %1$s Mitglieder in der Mitgliederkarte erfasst.',
	),
	'MAP_SEARCH'					=> 'Mitgliedersuche um die PLZ %1$s mit dem Radius ',
	'MAP_RESULT'					=> 'ergab folgendes Ergebnis:',
	'MAP_NORESULT'					=> 'fand keine Mitglieder innerhalb des Radius von ',
	// ACP
	'ACP_USERMAP'					=> 'Mitgliederkarte',
	'ACP_USERMAP_SETTINGS'			=> 'Einstellungen',
	'ACP_USERMAP_SETTINGS_EXPLAIN'	=> 'Hier kannst du die Einstellungen für die Mitgliederkarte ändern.',
	'ACP_USERMAP_SETTING_SAVED'		=> 'Die Einstellungen für die Mitgliederkarte wurden erfolgreich gesichert.',
	'ACP_USERMAP_MAPSETTING_TITLE'	=> 'Karteneinstellungen',
	'ACP_USERMAP_MAPSETTING_TEXT'	=> 'Einstellungen für das Kartenzentrum und die Vergrößerung beim Start.',
	'ACP_USERMAP_LAT'				=> 'Geogr. Breite des Kartenzentrums',
	'ACP_USERMAP_LAT_EXP'			=> 'Werte zwischen 90.0 (Nordpol) und -90.0 (Südpol)',
	'ACP_USERMAP_LON'				=> 'Geogr. Länge des Kartenzentrums',
	'ACP_USERMAP_LON_EXP'			=> 'Werte zwischen 180.0 (Osten) und -180.0 (Westen)',
	'ACP_USERMAP_ZOOM'				=> 'Zoom-Faktor der Mitgliederkarte beim Aufruf',
	'ACP_USERMAP_GEONAMES_TITLE'	=> 'Benutzername für geonames.org',
	'ACP_USERMAP_GEONAMES_TEXT'		=> 'Die Mitgliederkarte verwendet den Service von geonames.org zum Ermitteln der geogr.
										Koordinaten des über Postleitzahl und Land angegebenen Ortes sowie zur Verfeinerung den
										angegebenen Wohnort.
										Dafür wird eine Registrierung auf
										<a href="http://www.geonames.org/login" target="_blank">
										<span style="text-decoration: underline;">geonames.org/login</span></a>
										benötigt. Der dort registrierte Benutzername wird hier eingegeben.<br>
										Pro Abfrage wird ein Kredit-Punkt angerechnet, im kostenlosen Service sind pro Stunde
										maximal 1.000 Kredit-Punkte verfügbar; bei Foren mit mehr als 1.000 Benutzern wird empfohlen,
										pro 1.000 - 1.500 Mitgliedern je einen Benutzernamen anzumelden. Ansonsten könnte den
										Benutzern eine Fehlermeldung bei Eingabe von Postleitzahl und Land im Profil angezeigt
										werden, wenn beim Absenden die Koordinate ermittelt wird.<br>
										Mehrere Benutzernamen sind durch Kommata zu trennen.<br>
										ACHTUNG: Benutzer müssen nach dem ersten Login auf geonames.org über den Link "Benutzername"
										gesondert für den gewünschten Service freigeschaltet werden!!',
	'ACP_USERMAP_GEONAMESUSER'		=> 'Benutzername(n) für geonames.org',
	'ACP_USERMAP_GEONAMESUSER_ERR'	=> 'Du musst mindestens einen gültigen Benutzernamen für geonames.org eingeben!',
	'ACP_USERMAP_PROFILE_ERROR'		=> 'Diese Aktion konnte nicht abgeschlossen werden, da du noch keinen Geonames.org Nutzer in den Einstellungen der Mitgliederkarte angegeben hast. Tue dies bitte jetzt!',
	'ACP_USERMAP_LANGS'				=> 'Sprachpakete',
	'ACP_USERMAP_LANGS_EXPLAIN'		=> 'Hier kannst du nachträglich weitere Sprachpakete für die Mitgliederkarte installieren. Dies kann notwendig werden,
										wenn Sprachpakete für die Mitgliederkarte nach der ersten Aktivierung hinzugefügt werden, weil deren Daten noch nicht
										in die Auswahlliste für die Länderauswahl aufgenommen wurden; das kannst du hier erledigen, nachdem das Sprachpaket
										per ftp-Transfer in das Unterverzeichnis <italic>language</italic> dieser Erweiterung kopiert wurde.',
	'ACP_USERMAP_INSTALLABLE_LANG'	=> 'Zur Installation verfügbare Sprachpakete',
	'ACP_USERMAP_INSTALL_LANG_EXP'	=> 'Hier sind alle Sprachpakete der Mitgliederkarte aufgelistet, die noch installiert werden müssen.',
	'ACP_USERMAP_MISSING_LANG'		=> 'Fehlende Sprachpakete',
	'ACP_USERMAP_MISSING_LANG_EXP'	=> 'Hier sind die Sprachpakete aufgelistet, die im Board installiert sind, aber in der Mitgliederkarte fehlen.',
	'ACP_USERMAP_ADDITIONAL_LANG'	=> 'Zusätzliche Sprachpakete der Mitgliederkarte',
	'ACP_USERMAP_ADD_LANG_EXP'		=> 'Hier sind die Sprachpakete der Erweiterung aufgelistet, für die in diesem Board keine Sprache installiert ist.',
	'ACP_USERMAP_LANGPACK_NAME'		=> 'Name',
	'ACP_USERMAP_LANGPACK_LOCAL'	=> 'Lokaler Name',
	'ACP_USERMAP_LANGPACK_ISO'		=> 'ISO',
	'ACP_USERMAP_NO_ENTRIES'		=> 'Keine Sprachpakete gefunden',
	// UCP
	'MOT_ZIP'						=> 'Postleitzahl',
	'MOT_ZIP_EXP'					=> 'Gib hier die Postleitzahl deines Wohnortes ein, damit du auf der Mitgliederkarte erscheinst.<br>(Nur Großbuchstaben, Ziffern und Bindestrich erlaubt)',
	'MOT_LAND'						=> 'Land',
	'MOT_LAND_EXP'					=> 'Wähle hier das Land aus, in dem du wohnst, damit du auf der Mitgliederkarte erscheinst.',
	'MOT_UCP_GEONAMES_ERROR'		=> 'Es wurde durch den Administrator kein Geonames.org Nutzer angegeben, die Daten für die Mitgliederkarte konnten nicht ermittelt werden!',
));
