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

if ($this->request->variable('move', '', true))
{
	$action = 'move';
}
if ($this->request->variable('lock', '', true))
{
	$action = 'lock';
}
if ($this->request->variable('approve', '', true))
{
	$action = 'approve';
}

/*
* And now the different work from here
*/
if ($action == 'delete' && $sam_id)
{
	if (confirm_box(true))
	{
		$sql = 'SELECT filename FROM ' . SAM_DATA_TABLE . '
			WHERE id = ' . (int) $sam_id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			@unlink($ext_path . '/includes/uploads/' . $row['filename']);
		}

		$this->db->sql_freeresult($result);

		$sql = 'DELETE FROM ' . SAM_DATA_TABLE . '
			WHERE id = ' . (int) $sam_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . SAM_RATE_TABLE . '
			WHERE pic_id = ' . (int) $sam_id;
		$this->db->sql_query($sql);

		redirect($this->helper->route('sam_controller', array('mode' => $mode, 'cat_id' => $cat_id)));
	}
	else
	{
		confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields(array(
			'cat_id'	=> $cat_id,
			'action'	=> 'delete',
			'mode'		=> $mode,
			'sam_id'	=> $sam_id,
		)));
	}

	$action = 'manage';
}

if ($action == 'move' && $cat_id && $new_cat_id)
{
	$sam_id_ary = $this->request->variable('sam_id', array(0));

	if (sizeof($sam_id_ary))
	{
		$sam_id_ary = array_map('intval', $sam_id_ary);

		$sql = 'UPDATE ' . SAM_DATA_TABLE. ' SET ' . $this->db->sql_build_array('UPDATE', array(
			'cat_id' => $new_cat_id)) . ' WHERE ' . $this->db->sql_in_set('id', $sam_id_ary);
		$this->db->sql_query($sql);
	}

	$action = 'manage';
}

if ($action == 'lock' || $action == 'approve')
{
	$sam_id_ary = $this->request->variable('sam_id', array(0));
	$approve = ($action == 'lock') ? 0 : true;

	if (sizeof($sam_id_ary))
	{
		$sam_id_ary = array_map('intval', $sam_id_ary);

		$sql = 'UPDATE ' . SAM_DATA_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', array(
			'approve' => $approve)) . ' WHERE ' . $this->db->sql_in_set('id', $sam_id_ary);
		$this->db->sql_query($sql);
	}

	$action = 'manage';
}

if ($action == 'manage' && $cat_id)
{
	$total_smilies = $index[$cat_id]['total'];

	if (!$total_smilies)
	{
		redirect($this->helper->route('sam_controller'));
	}

	page_header($this->language->lang('SAM_TITLE'));

	$this->template->set_filenames(array(
		'body' => 'sam_modcp.html')
	);

	$sql = 'SELECT * FROM ' . SAM_DATA_TABLE . '
		WHERE cat_id = ' . (int) $cat_id . '
		ORDER BY time DESC';
	$result = $this->db->sql_query_limit($sql, $this->config['posts_per_page'], $start);

	while ($row = $this->db->sql_fetchrow($result))
	{
		$sam_title		= censor_text($row['title']);
		$sam_id			= $row['id'];
		$sam_approve	= $row['approve'];

		$this->template->assign_block_vars('smilie_row', array(
			'SAM_ID'		=> $sam_id,
			'SAM_TITLE'		=> $sam_title,
			'SAM_APPROVE'	=> ($sam_approve) ? true : false,

			'I_SAM_FILE'	=> $this->helper->route('sam_controller', array('mode' => 'smilie', 'sam_id' => $sam_id)),

			'U_SMILIE'		=> $this->helper->route('sam_controller', array('mode' => 'detail', 'sam_id' => $row['id'])),
			'U_DELETE'		=> $this->helper->route('sam_controller', array('mode' => 'modcp', 'action' => 'delete', 'cat_id' => $cat_id, 'sam_id' => $sam_id)),
		));
	}

	$this->db->sql_freeresult($result);

	$s_cat_select = $sam->sam_cat_select(0, 0, 0, $cat_id);

	$s_hidden_fields = array(
		'mode'		=> 'modcp',
		'action'	=> 'manage',
		'cat_id'	=> $cat_id,
	);

	$cat_title = $index[$cat_id]['cat_title'];
	$cat_title = str_replace('&nbsp;|__&nbsp;', '', $cat_title);

	$cur_number_smilies = $this->config['sam_rows'] * $this->config['sam_cols'];

	if ($index[$cat_id]['total'] > $cur_number_smilies)
	{
		$pagination = $this->phpbb_container->get('pagination');
		$pagination->generate_template_pagination(
			array(
				'routes' => array(
					'sam_controller',
					'sam_controller',
				),
				'params' => array('mode' => 'modcp', 'cat_id' => $cat_id),
			), 'pagination', 'start', $index[$cat_id]['total'], $cur_number_smilies, $page_start);
			
		$this->template->assign_vars(array(
			'PAGE_NUMBER'      => $pagination->on_page($index[$cat_id]['total'], $cur_number_smilies, $page_start),
			'TOTAL_SMILIES'	   => $index[$cat_id]['total'] . ' ' . $this->language->lang('SAM_TOTAL'),
		));
	}

	if ((floor($start / $cur_number_smilies) + 1) == max(ceil($total_smilies / $cur_number_smilies), 1))
	{
		$cur_number_smilies = $total_smilies - ($cur_number_smilies * (floor($start / $cur_number_smilies)));
	}

	$this->template->assign_vars(array(
		'CAT_TITLE'			=> $cat_title,
		'SMILIES_PER_PAGE'	=> $cur_number_smilies,

		'S_CAT_SELECT'		=> $s_cat_select,
		'S_FORM_ACTION'		=> $this->helper->route('sam_controller', array('mode' => 'modcp')),
		'S_HIDDEN_FIELDS'	=> build_hidden_fields($s_hidden_fields))
	);
}
else
{
	redirect($this->helper->route('sam_controller'));
}
