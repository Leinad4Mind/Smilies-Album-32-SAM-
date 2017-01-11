<?php

/**
*
* @package phpBB Extension - Smilies ALbum
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* Language pack for Extension permissions [German]
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

// Permissions
$lang = array_merge($lang, array(
	'ACP_SAM'				=> 'Smilies Album',

	'A_SAM_CATS'		=> 'Kann Kategorien verwalten',
	'A_SAM_CONFIG'		=> 'Kann die Einstellungen ändern',
	'A_SAM_OVERVIEW'	=> 'Kann die Übersicht einsehen',
));
