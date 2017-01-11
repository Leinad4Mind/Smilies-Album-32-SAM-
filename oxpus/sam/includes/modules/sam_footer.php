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

if ($mode == 'modcp' || $mode == 'main')
{
	$sql = 'SELECT sam_text, sam_uid, sam_bitfield, sam_flags FROM ' . SAM_TEXT_TABLE . "
		WHERE sam_key = 'GENERAL_USE'";
	$result = $this->db->sql_query($sql);
	$row = $this->db->sql_fetchrow($result);
	$this->db->sql_freeresult($result);
	
	$general_use = censor_text($row['sam_text']);
	$general_use = generate_text_for_display($general_use, $row['sam_uid'], $row['sam_bitfield'], $row['sam_flags']);
}
else
{
	$general_use = '';
}

$this->template->assign_vars(array(
	'GENERAL_USE'	=> $general_use,
	'SAM_VERSION'	=> '2011 - ' . date('Y', time()),
));
