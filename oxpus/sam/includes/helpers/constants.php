<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*
* connect to phpBB
*/
if ( !defined('IN_PHPBB') )
{
	exit;
}

define('SAM_CATS_TABLE',	$table_prefix . 'sam_cats');
define('SAM_DATA_TABLE',	$table_prefix . 'sam_data');
define('SAM_RATE_TABLE',	$table_prefix . 'sam_rate');
define('SAM_TEXT_TABLE',	$table_prefix . 'sam_text');
