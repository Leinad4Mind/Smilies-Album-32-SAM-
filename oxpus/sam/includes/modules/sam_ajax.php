<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

$this->db->return_on_error = true;

$sql = 'UPDATE ' . SAM_RATE_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', array(
	'points'	=> $points)) . '
	WHERE user_id = ' . (int) $this->user->data['user_id'] . '
		AND pic_id = ' . (int) $sam_id;
$result = $this->db->sql_query($sql);

if (!$this->db->sql_affectedrows($result))
{
	$sql = 'INSERT INTO ' . SAM_RATE_TABLE . ' ' . $this->db->sql_build_array('INSERT', array(
		'points'	=> $points,
		'user_id'	=> $this->user->data['user_id'],
		'pic_id'	=> $sam_id));
	$this->db->sql_query($sql);
}

$this->db->return_on_error = false;

$json_out = json_encode(array('rating_img' => $sam->rating_img($ext_path_web, $points, $perm_rate, $sam_id)));

$http_headers = array(
	'Content-type' => 'text/html; charset=UTF-8',
	'Cache-Control' => 'private, no-cache="set-cookie"',
	'Expires' => gmdate('D, d M Y H:i:s', time()) . ' GMT',
);

foreach ($http_headers as $hname => $hval)
{
	header((string) $hname . ': ' . (string) $hval);
}

$this->template->set_filenames(array(
	'body' => 'sam_json.html')
);
$this->template->assign_var('JSON_OUTPUT', $json_out);
$this->template->display('body');
