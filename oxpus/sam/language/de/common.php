<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*
* [ german ] language file
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

$lang = array_merge($lang, array(
	'ACP_SAM'							=> 'Smilies Album MOD (SAM)',
	'ACP_SAM_CATS'						=> 'Kategorien',
	'ACP_SAM_CATS_EXPLAIN'				=> 'Richte dir hier Kategorien für die Smilies ein.<br />Unterkategorien sind ebenfalls möglich.',
	'ACP_SAM_CONFIG'					=> 'Einstellungen',
	'ACP_SAM_CONFIG_EXPLAIN'			=> 'Hier kannst du alle Einstellungen deines Smilies Album vornehmen.',
	'ACP_SAM_OVERVIEW'					=> 'Übersicht',
	'ACP_SAM_OVERVIEW_EXPLAIN'			=> 'Willkommen im Smilies Album.<br />Hier kannst du dir einen Bereich einrichten, mit dem Smilies verwaltet werden können.',
	'CLICK_RETURN_SAM'					=> '%sKlick hier, um zum Smilies Album zurückzukehren%s',
	'CLICK_RETURN_SAM_CONFIG'			=> '%sKlick hier, um zu den Einstellungen zurückzukehren%s',
	'CLICK_RETURN_SAM_CATADMIN'			=> '%sKlick hier, um zu den Kategorien zurückzukehren%s',
	'SAM_ADD'							=> 'Neues Smilie',
	'SAM_ADDED'							=> 'Smilie erfolgreich hinzugefügt',
	'SAM_ALL'							=> 'Alle',
	'SAM_APPROVE'						=> 'Freigegeben',
	'SAM_BBCODE'						=> 'BBCode',
	'SAM_CAT'							=> 'Kategorie',
	'SAM_CAT_ADDED'						=> 'Kategorie erfolgreich hinzugefügt',
	'SAM_CAT_PARENT'					=> 'Übergeordnete Kategorie',
	'SAM_CAT_REMOVED'					=> 'Kategorie erfolgreich entfernt',
	'SAM_CAT_ROOT'						=> 'Oberste Ebene',
	'SAM_CAT_TEXT'						=> 'Text für die Kategorie.<br />Wird oberhalb der Smilies angezeigt.',
	'SAM_CAT_UPDATED'					=> 'Kategorie erfolgreich aktualisiert',
	'SAM_CONFIG_ACTIVE'					=> 'Smilies Album aktivieren',
	'SAM_CONFIG_APPROVE'				=> 'Neue Smilies sofort freigeben<br />(ansonsten nur durch Administratoren)',
	'SAM_CONFIG_COLS'					=> 'Smilies je Zeile',
	'SAM_CONFIG_FILE_SIZE_MAX'			=> 'Maximale Dateigrösse der Smilies',
	'SAM_CONFIG_PERM_RATE'				=> 'Wer darf Smilies bewerten',
	'SAM_CONFIG_PERM_UPLOAD'			=> 'Wer darf Smilies hinzufügen',
	'SAM_CONFIG_RATE_MAX'				=> 'Maximale Bewertungspunkte',
	'SAM_CONFIG_ROWS'					=> 'Zeilen je Album-Seite',
	'SAM_CONFIG_UPDATED'				=> 'Einstellungen erfolgreich gespeichert',
	'SAM_CURRENT_VERSION'				=> 'Deine Version',
	'SAM_EMPTY_PAGE'					=> 'Diese Ansicht hat keine Inhalte',
	'SAM_FILE'							=> 'Bilddatei',
	'SAM_FILE_TITLE'					=> 'Smilie Name',
	'SAM_FILE_TO_BIG'					=> 'Die Bilddatei ist zu groß!<br />Gehe bitte zurück und wähle eine kleine Datei aus.',
	'SAM_FORBIDDEN_EXTENTION'			=> 'Verbotene Dateiendung!<br />Erlaubt sind nur JPEG, GIF und PNG.',
	'SAM_GENERAL_USE'					=> 'Hinweis zur Benutzung des Albums.<br />Der Benutzer sieht diesen Text unter den Smilies.',
	'SAM_LATEST_VERSION'				=> 'Aktuelle Version',
	'SAM_LOCK'							=> 'Sperren',
	'SAM_MARK_ALL'						=> 'Alle markieren',
	'SAM_MODCP'							=> 'Smilies Album - Moderations-Bereich',
	'SAM_MOVE'							=> 'Verschieben nach Kategorie',
	'SAM_MUST_BE_APPROVED'				=> 'Vielen Dank für das Smilie.<br />Wir werden dieses nun prüfen und anschließend freischalten.<br />Bitte gedulde dich bis dahin.',
	'SAM_NO_CATS'						=> 'Dieses Smilies Album besitzt keine Kategorien',
	'SAM_NO_FILENAME'					=> 'Dateiname fehlt!',
	'SAM_NO_INFO'						=> 'Keine Information verfügbar',
   	'SAM_NO_PERMISSION'					=> 'Du hast keine Berechtigungen, dieses Modul zu verwenden!',
	'SAM_NOT_ADDED'						=> 'Das Smilie wurde nicht hinzugefügt!<br />Gehe bitte zurück und versuche es erneut.',
	'SAM_NOT_UP_TO_DATE'				=> '%s ist nicht aktuell',
	'SAM_PERM_GUEST'					=> 'Jeder',
	'SAM_PERM_USERS'					=> 'Registrierte Benutzer',
	'SAM_PERM_ADMIN'					=> 'Nur Administratoren',
	'SAM_POINT'							=> ' Punkt',
	'SAM_POINTS'						=> ' Punkte',
	'SAM_POPUP'							=> 'Smilies Album',
	'SAM_SELECTED'						=> 'Ausgewählte Smilies',
	'SAM_SMILIE'						=> 'Smilie',
	'SAM_STATUS'						=> 'Freigabe',
	'SAM_SIZE_B'						=> 'Bytes',
	'SAM_SIZE_KB'						=> 'KB',
	'SAM_SIZE_MB'						=> 'MB',
	'SAM_SIZE_GB'						=> 'GB',
	'SAM_TITLE'							=> 'Smilies Album',
	'SAM_TOTAL'							=> 'Smilies gesamt',
	'SAM_UP_TO_DATE'					=> '%s ist aktuell',
	'SAM_UNMARK'						=> 'Keine markieren',
	'SAM_UPLOAD_MAX'					=> 'Die Datei muss kleiner als %s sein',
	'SAM_UPLOAD_TIME'					=> 'Upload am',
	'SAM_UPLOAD_USER'					=> 'Upload von',
	'SAM_USER_RATING'					=> 'Deine Bewertung',
	'SAM_VERSION'						=> 'Smilies Album MOD (SAM) &copy; by <a href="http://phpbb3.oxpus.net">OXPUS</a>',
	'SAM_VERSION_CHECK'					=> 'Versionsprüfung',
	'SAM_VIEWONLINE'					=> 'Wandert im Smilies Album herum',
	'SAM_WELCOME_MSG'					=> 'Willkommenstext für das Smilies Album.<br />Wird über den Smilies auf der Album-Startseite angezeigt.',

));
