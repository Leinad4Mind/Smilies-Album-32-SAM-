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

if ($this->request->variable('submit', ''))
{	
	$sam_filename	= utf8_normalize_nfc($this->request->variable('sam_filename', '', true));
	$sam_title		= utf8_normalize_nfc($this->request->variable('sam_title', $sam_filename, true));
	$cat_id			= $this->request->variable('cat_id', 0);

	$user_id		= $this->user->data['user_id'];
	$username		= $this->user->data['username'];
	$cur_time		= time();

	$error_text		= '';
	$min_pic_dim	= 10;
	$max_pic_dim	= 1024;

	$factory = $this->phpbb_container->get('files.factory');

	$allowed_imagetypes = array('jpg', 'gif', 'png');

	$upload = $factory->get('upload')
		->set_error_prefix('')
		->set_allowed_extensions($allowed_imagetypes)
		->set_max_filesize($this->config['sam_file_size_max'])
		->set_allowed_dimensions(
			$min_pic_dim,
			$min_pic_dim,
			$max_pic_dim,
			$max_pic_dim)
		->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false));

	$this->language->add_lang('posting');

	$form_name = 'sam_filename';
	$upload_file = $this->request->file($form_name);
	unset($upload_file['local_mode']);

	$file = $upload->handle_upload('files.types.form', $form_name);

	$file_size = $upload_file['size'];
	$file_temp = $upload_file['tmp_name'];
	$file_name = $upload_file['name'];

	$error_count = sizeof($file->error);
	if ($error_count > 1 && $file_name)
	{
		$error_text .= implode('<br />', $file->error);
	}

	if ($file_name)
	{
		if (sprintf("%u", @filesize($file_temp) > $this->config['sam_file_size_max']))
		{
			$error_text .= '<br />' . $this->language->lang('SAM_FILE_TO_BIG');
		}
	}
	else
	{
		$error_text .= '<br />' . $this->language->lang('SAM_NO_FILENAME');
	}

	$extention = $file->get_extension($file_name);

	if (!in_array(strtolower($extention), array('jpg', 'gif', 'png')))
	{
		$error_text .= '<br />' . $this->language->lang('SAM_FORBIDDEN_EXTENTION');
	}

	if ($error_text)
	{
		$file->remove();
		trigger_error($error_text, E_USER_ERROR);
	}
		
	if ($this->config['sam_approve'])
	{
		$approve = true;
	}
	else
	{
		if ($this->auth->acl_get('a_') && $this->user->data['is_registered'] && !$this->user->data['user_perm_from'])
		{
			$approve = true;
		}
		else
		{
			$approve = false;
		}
	}

	if($cat_id)
	{
		$sam_pic_path = $ext_path . 'includes/uploads/';

		$result = $file->move_file($sam_pic_path, false, false, CHMOD_ALL);

		if ($result)
		{
			$sql_array = array(
					'filename'	=> $file_name,
					'title'		=> $sam_title,
					'user_id'	=> $user_id,
					'username'	=> $username,
					'time'		=> $cur_time,
					'cat_id'	=> $cat_id,
					'approve'	=> $approve,
			);
	
			$sql = 'INSERT INTO ' . SAM_DATA_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_array);
			$this->db->sql_query($sql);
	
			$approve_message = ($approve) ? '' : '<br />' . $this->language->lang('SAM_MUST_BE_APPROVED');
		
			$message = $this->language->lang('SAM_ADDED') . $approve_message . '<br /><br />' . $this->language->lang('CLICK_RETURN_SAM', '<a href="' . $this->helper->route('oxpus_sam_controller', array('cat_id' => $cat_id)) . '">', '</a>');
			meta_refresh(3, $this->helper->route('oxpus_sam_controller', array('cat_id' => $cat_id)));
		}
		else
		{
			$file->remove();
			$message = $this->language->lang('SAM_NOT_ADDED') . '<br /><br />' . $this->language->lang('CLICK_RETURN_SAM', '<a href="' . $this->helper->route('oxpus_sam_controller', array('cat_id' => $cat_id)) . '">', '</a>');
		}
	}
	else
	{
		$file->remove();
		$message = $this->language->lang('SAM_NOT_ADDED') . '<br /><br />' . $this->language->lang('CLICK_RETURN_SAM', '<a href="' . $this->helper->route('oxpus_sam_controller', array('cat_id' => $cat_id)) . '">', '</a>');
	}

	trigger_error($message);
}

$this->template->set_filenames(array(
	'body' => 'sam_edit.html')
);

$upload_max_size = get_formatted_filesize($this->config['sam_file_size_max']);

$this->template->assign_vars(array(
	'ENCTYPE'			=> 'enctype="multipart/form-data"',
	'MAX_UPLOAD_SIZE'	=> $this->language->lang('SAM_UPLOAD_MAX', $upload_max_size),
	'SAM_ADD_MODE'		=> true,
	'SAM_TITLE'			=> '',
	'SAM_CAT'			=> $sam->sam_cat_select(0, 0, $cat_id),
	'S_FORM_ACTION'		=> $this->helper->route('oxpus_sam_controller', array('mode' => 'add')),
));
