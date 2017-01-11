<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*
* [ english ] language file
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
	'ACP_SAM_CATS'						=> 'Categories',
	'ACP_SAM_CATS_EXPLAIN'				=> 'Create from here categories for your smilies.<br />You can create sub-categories, too.',
	'ACP_SAM_CONFIG'					=> 'Settings',
	'ACP_SAM_CONFIG_EXPLAIN'			=> 'From here you can manage all settings of your Smilies Album.',
	'ACP_SAM_OVERVIEW'					=> 'Overview',
	'ACP_SAM_OVERVIEW_EXPLAIN'			=> 'Welcome to your Smilies Album.<br />From here you can create a page to manage smilies.',
	'CLICK_RETURN_SAM'					=> '%sClick here to return to the smilies album%s',
	'CLICK_RETURN_SAM_CONFIG'			=> '%sClick here to return to the settings%s',
	'CLICK_RETURN_SAM_CATADMIN'			=> '%sClick here to return to the categories%s',
	'SAM_ADD'							=> 'Add new smilie',
	'SAM_ADDED'							=> 'Smilie successfull added',
	'SAM_ALL'							=> 'All',
	'SAM_APPROVE'						=> 'Approved',
	'SAM_BBCODE'						=> 'BBcode',
	'SAM_CAT'							=> 'Category',
	'SAM_CAT_ADDED'						=> 'Category added successfull',
	'SAM_CAT_PARENT'					=> 'Parent Category',
	'SAM_CAT_REMOVED'					=> 'Category removed successfull',
	'SAM_CAT_ROOT'						=> 'Root index',
	'SAM_CAT_TEXT'						=> 'Text for the category.<br />This text will be displayed over the smilies.',
	'SAM_CAT_UPDATED'					=> 'Category updated successfull',
	'SAM_CONFIG_ACTIVE'					=> 'Enable Smilies Album',
	'SAM_CONFIG_APPROVE'				=> 'New smilies will be approved automatically<br />(otherside administrators must approve)',
	'SAM_CONFIG_COLS'					=> 'Smilies per row',
	'SAM_CONFIG_FILE_SIZE_MAX'			=> 'Maximum filesize for smilies',
	'SAM_CONFIG_PERM_RATE'				=> 'Who can rate smilies',
	'SAM_CONFIG_PERM_UPLOAD'			=> 'Who can upload smilies',
	'SAM_CONFIG_RATE_MAX'				=> 'Maximum rating points',
	'SAM_CONFIG_ROWS'					=> 'Rows per album page',
	'SAM_CONFIG_UPDATED'				=> 'Settings updated successfull',
	'SAM_CURRENT_VERSION'				=> 'Your release',
	'SAM_EMPTY_PAGE'					=> 'This view have no contents',
	'SAM_FILE'							=> 'Image file',
	'SAM_FILE_TITLE'					=> 'Smilie name',
	'SAM_FILE_TO_BIG'					=> 'The image file is too big!<br />Please go back and select a smaller one.',
	'SAM_FORBIDDEN_EXTENTION'			=> 'Forbidden file extention!<br />Allowed are only JPEG, GIF and PNG.',
	'SAM_GENERAL_USE'					=> 'Hints about using the album.<br />The user will see this under the smilies.',
	'SAM_LATEST_VERSION'				=> 'Latest release',
	'SAM_LOCK'							=> 'Lock',
	'SAM_MARK_ALL'						=> 'Mark all',
	'SAM_MODCP'							=> 'Smilies Album - Moderator Panel',
	'SAM_MOVE'							=> 'Move to category',
	'SAM_MUST_BE_APPROVED'				=> 'Thank you for this smilie.<br />We will now check this and approve it.<br />Please stay tuned.',
	'SAM_NO_CATS'						=> 'This smilies album do not have any categories',
	'SAM_NO_FILENAME'					=> 'Filename missing!',
	'SAM_NO_INFO'						=> 'No informations found',
   	'SAM_NO_PERMISSION'					=> 'You have no permissions to use this module!',
	'SAM_NOT_ADDED'						=> 'The smilie could not be added!<br />Please go back and retry.',
	'SAM_NOT_UP_TO_DATE'				=> '%s is not up to date',
	'SAM_PERM_GUEST'					=> 'Everybody',
	'SAM_PERM_USERS'					=> 'Registered users',
	'SAM_PERM_ADMIN'					=> 'Only administrators',
	'SAM_POINT'							=> ' Point',
	'SAM_POINTS'						=> ' Points',
	'SAM_POPUP'							=> 'Smilies Album',
	'SAM_SELECTED'						=> 'Selected Smilies',
	'SAM_SMILIE'						=> 'Smilie',
	'SAM_STATUS'						=> 'Approval',
	'SAM_SIZE_B'						=> 'Bytes',
	'SAM_SIZE_KB'						=> 'KB',
	'SAM_SIZE_MB'						=> 'MB',
	'SAM_SIZE_GB'						=> 'GB',
	'SAM_TITLE'							=> 'Smilies Album',
	'SAM_TOTAL'							=> 'Total smilies',
	'SAM_UNMARK'						=> 'Unmark all',
	'SAM_UP_TO_DATE'					=> '%s is up to date',
	'SAM_UPLOAD_MAX'					=> 'The file must be smaller than %s',
	'SAM_UPLOAD_TIME'					=> 'Upload at',
	'SAM_UPLOAD_USER'					=> 'Upload by',
	'SAM_USER_RATING'					=> 'Your rating',
	'SAM_VERSION'						=> 'Smilies Album MOD (SAM) &copy; 2011 by <a href="http://phpbb3.oxpus.net">OXPUS</a>',
	'SAM_VERSION_CHECK'					=> 'Release check',
	'SAM_VIEWONLINE'					=> 'Walk through the Smilies Album',
	'SAM_WELCOME_MSG'					=> 'Welcome message for the Smilies Album.<br />This text will be displayed over the smilies on the album start page.',

));
