<?php
/**
*
* @package UserReminder v0.4.0
* @copyright (c) 2019, 2020 Mike-on-Tour
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
	'PLURAL_RULE'		=> 1,
	// Module
	'CONFIRM_USER_DELETE'						=> array(
		1	=> 'Sind Sie sicher, dass Sie 1 Mitglied löschen möchten?<br><br>Damit werden Mitglieder endgültig aus der Datenbank entfernt, <strong>dieser Vorgang kann nicht rückgängig gemacht werden!</strong>',
		2	=> 'Sind Sie sicher, dass Sie %d Mitglieder löschen möchten?<br><br>Damit werden Mitglieder endgültig aus der Datenbank entfernt, <strong>dieser Vorgang kann nicht rückgängig gemacht werden!</strong>',
	),
	'NO_USER_SELECTED'							=> 'Es wurde kein Mitglied für diese Aktion markiert. Bitte mindestens ein Mitglied markieren.',
	'USER_DELETED'								=> array(
		1	=> '1 Mitglied erfolgreich gelöscht',
		2	=> '%d Mitglieder erfolgreich gelöscht',
	),
	'USER_REMINDED'								=> array(
		1	=> '1 Mitglied per eMail erinnert',
		2	=> '%d Mitglieder per eMail erinnert',
	),
	'USER_POSTS'								=> 'Beiträge',
	'DAYS_AGO'									=> 'vor Anzahl Tagen',
	'AT_DATE'									=> 'Am',
	'MARK_REMIND'								=> 'Erinnern',
	'MARK_DELETE'								=> 'Löschen',
	'REMIND_MARKED'								=> 'Markierte erinnern',
	'LOG_INACTIVE_REMIND_ONE'					=> '<strong>Erste Erinnerungs-E-Mail an inaktive Benutzer verschickt</strong><br>» %s',
	'LOG_INACTIVE_REMIND_TWO'					=> '<strong>Zweite Erinnerungs-E-Mail an inaktive Benutzer verschickt</strong><br>» %s',
	//ACP Settings
	'ACP_USERREMINDER'							=> 'User Reminder',
	'ACP_USERREMINDER_SETTINGS'					=> 'Einstellungen',
	'ACP_USERREMINDER_SETTINGS_EXPLAIN'			=> 'Hier können Sie die Einstellungen für den User Reminder ändern.',
	'ACP_USERREMINDER_SETTING_SAVED'			=> 'Die Einstellungen für den User Reminder wurden erfolgreich gesichert.',
	'ACP_USERREMINDER_TIME_SETTINGS_TITLE'		=> 'Konfiguration der Erinnerungsintervalle',
	'ACP_USERREMINDER_TIME_SETTING_TEXT'		=> 'Einstellungen für die Zeitdauer, bis ein Mitglied als inaktiv gilt, Dauer zwischen der ersten und zweiten Erinnerung sowie die darauf folgende Dauer bis zur Löschung. Außerdem kann hier ausgewählt werden, ob Erinnerungs-Mails automatisch verschickt und Löschungen automatisch erfolgen sollen.<br>Zusätzlich können Mitglieder benannt werden, die vor Erinnerungen und Löschung geschützt werden sollen.',
	'ACP_USERREMINDER_INACTIVE'					=> 'Anzahl der Tage, die ein Mitglied offline sein muss, um als inaktiv zu gelten',
	'ACP_USERREMINDER_DAYS_REMINDED'			=> 'Anzahl der Tage bis ein als inaktiv eingestuftes Mitglied die zweite Erinnerungsmail bekommen soll;<br>
													die Eingabe von "0" schaltet die zweite Erinnerungsmail ab',
	'ACP_USERREMINDER_AUTOREMIND'				=> 'Erinnerungs-Mails automatisch versenden?',
	'ACP_USERREMINDER_DAYS_UNTIL_DELETED'		=> 'Anzahl der Tage zwischen letzter Erinnerung und Löschen des Mitgliedes',
	'ACP_USERREMINDER_AUTODELETE'				=> 'Mitglieder nach Ablauf aller Wartezeiten automatisch löschen?',
	'ACP_USERREMINDER_PROTECTED_MEMBERS'		=> 'Eingabe der <i>user_id</i> von Mitgliedern, die von Erinnerungen und Löschung ausgenommen werden sollen. Eingaben müssen durch Kommata getrennt sein!',
	// ACP Mail settings
	'ACP_USERREMINDER_MAIL_SETTINGS_TITLE'		=> 'Konfiguration der E-Mails',
	'ACP_USERREMINDER_EMAIL_BCC_TEXT'			=> 'Hier können Sie jeweils eine E-Mail-Adresse angeben, die in Blindkopie oder in Kopie an den Erinnerungs-Mails beteiligt wird.',
	'ACP_USERREMINDER_EMAIL_BCC'				=> 'Blindkopie der Erinnerungs-Mail an',
	'ACP_USERREMINDER_EMAIL_CC'					=> 'Kopie der Erinnerungs-Mail an',
	// ACP Mail text edit
	'ACP_USERREMINDER_MAIL_EDIT_TITLE'			=> 'Bearbeiten der E-Mail Texte',
	'ACP_USERREMINDER_MAIL_EDIT_TEXT'			=> 'Bearbeitung des voreingestellten Textes für die erste und zweite Erinnerungsmail.',
	'ACP_USERREMINDER_MAIL_LANG'				=> 'Sprache auswählen',
	'ACP_USERREMINDER_MAIL_FILE'				=> 'Zu bearbeitende Datei wählen',
	'ACP_USERREMINDER_MAIL_ONE'					=> '1. Erinnerung',
	'ACP_USERREMINDER_MAIL_TWO'					=> '2. Erinnerung',
	'ACP_USERREMINDER_MAIL_PREVIEW'				=> 'Im Fenster rechts kann der Text der ausgewählten E-Mail bearbeitet werden. Durch Anklicken des "Vorschau"-Buttons wird der Text dargestellt,
													wie er später in der E-Mail versandt wird, die Tokens werden dabei durch Ihre Daten ersetzt. Zusätzlich wird mit der Vorschau auch ein Button
													zum Speichern des Textes auf dem Server dargestellt.<br>
													Im Text können folgende Tokens verwendet werden, die als Platzhalter für die entsprechenden Daten des angeschriebenen Mitgliedes stehen:<br>
													- {USERNAME}: Nickname des Mitgliedes<br>
													- {LAST_VISIT}: Datum des letzten Einloggens<br>
													- {LAST_REMIND}: Datum der ersten Erinnerungs-Mail<br>
													Die folgenden Tokens können als Platzhalter für systembezogene Daten verwendet werden:<br>
													- {SITENAME}: Name des Forums<br>
													- {FORGOT_PASS}: Link zu "Ich habe mein Passwort vergessen"<br>
													- {ADMIN_MAIL}: Email-Adresse des Foren-Admins<br>
													- {EMAIL_SIG}: Absender-Grußformel<br>
													- {DAYS_INACTIVE}: Oben angegebene Anzahl Tage der Inaktivität<br>
													- {DAYS_TIL_DELETE}: Oben angegebene Anzahl an Tagen bis zur Löschung<br>',
	'ACP_USERREMINDER_MAIL_LOAD_FILE'			=> 'Datei laden',
	'ACP_USERREMINDER_PREVIEW_TEXT'				=> 'Bitte beachten:<br>Im Vorschautext werden die Tokens für die Daten des angeschriebenen Mitgliedes mit Ihren Daten ersetzt, dies muss nicht unbedingt einen logisch sinnvollen Text ergeben.<br>
													<strong>Nach dem Speichern MUSS der Cache geleert werden!</strong>',
	'ACP_USERREMINDER_MAIL_SAVE_FILE'			=> 'Datei speichern',
	'ACP_USERREMINDER_FILE_ERROR'				=> 'Beim Speichern der Datei "%s" trat ein Fehler auf!<br>Die Datei wurde <strong>nicht gespeichert</strong>.',
	'ACP_USERREMINDER_FILE_SAVED'				=> 'Die Datei "%s" wurde erfolgreich gespeichert.',
	// ACP Reminder
	'ACP_USERREMINDER_REMINDER'					=> 'Mitglieder erinnern',
	'ACP_USERREMINDER_REMINDER_EXPLAIN'			=> 'Hier werden die Mitglieder aufgelistet, die nach Registrierung und Aktivierung bereits online waren und Beiträge gepostet haben, aber seit der in den Einstellungen vorgegebenen Anzahl von Tagen nicht mehr online waren.
													Diese Mitglieder können hier manuell zur Erinnerung ausgewählt bzw. nach Verstreichen der beiden eingestellten Zeiträume nach den Erinnerungen gelöscht werden.
													Erst wenn ein Mitglied beide Erinnerungen hat verstreichen lassen, ohne sich einzuloggen, wird es zur Löschung freigegeben.',
	'ACP_USERREMINDER_REMINDER_ONE'				=> 'Erste Erinnerung',
	'ACP_USERREMINDER_REMINDER_TWO'				=> 'Zweite Erinnerung',
	'ACP_USERREMINDER_NO_ENTRIES'				=> 'Keine Daten gefunden',
	'ACP_USERREMINDER_SORT_DESC'				=> 'Absteigend',
	'ACP_USERREMINDER_SORT_ASC'					=> 'Aufsteigend',
	'ACP_USERREMINDER_KEY_RD'					=> 'Registrierungsdatum',
	'ACP_USERREMINDER_KEY_LV'					=> 'Letzter Besuch',
	'ACP_USERREMINDER_KEY_RO'					=> '1. Erinnerung',
	'ACP_USERREMINDER_KEY_RT'					=> '2. Erinnerung',
	// ACP Registered Only
	'ACP_USERREMINDER_REGISTERED_ONLY'			=> 'Schläfer',
	'ACP_USERREMINDER_REGISTERED_ONLY_EXPLAIN'	=> 'Hier werden die Mitglieder aufgelistet, die nach Registrierung und Aktivierung noch nie online waren.',
	// ACP Zeroposters
	'ACP_USERREMINDER_ZEROPOSTER'				=> 'Null-Poster',
	'ACP_USERREMINDER_ZEROPOSTER_EXPLAIN'		=> 'Hier werden die Mitglieder aufgelistet, die zwar regelmäßig online sind, aber bisher noch keine Beiträge gepostet haben.',
));
