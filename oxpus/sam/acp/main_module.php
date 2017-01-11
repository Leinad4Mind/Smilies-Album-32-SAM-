<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\acp;

/**
* @package acp
*/
class main_module
{
	var $u_action;

	function main($mode)
	{
		global $db, $user, $auth, $cache, $phpbb_container;
		global $phpbb_extension_manager, $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix;

		$config			= $phpbb_container->get('config');
		$language		= $phpbb_container->get('language');
		$request		= $phpbb_container->get('request');
		$template		= $phpbb_container->get('template');

		$mode			= $request->variable('mode', 'overview');
		$action			= $request->variable('action', '');
		$submit			= $request->variable('submit', '');
		$cancel			= $request->variable('cancel', '');
		$confirm		= $request->variable('confirm', '');

		$cat_id			= $request->variable('cat_id', 0);

		$ext_path		= $phpbb_extension_manager->get_extension_path('oxpus/sam', true);

		include_once($ext_path . 'includes/helpers/constants.' . $phpEx);

		$auth->acl($user->data);
		$module_denied = false;
		
		switch ($mode)
		{
			case 'overview':
				if (!$auth->acl_get('a_sam_overview'))
				{
					$module_denied = true;
				}
			break;
			case 'config':
				if (!$auth->acl_get('a_sam_config'))
				{
					$module_denied = true;
				}
			break;
			case 'cats':
				if (!$auth->acl_get('a_sam_cats'))
				{
					$module_denied = true;
				}
			break;
		}
							
		if ($module_denied)
		{
			trigger_error('NO_PERMISSION', E_USER_WARNING);
		}

		if ($cancel)
		{
			$action = '';
		}

		$this->tpl_name = 'acp_sam';

		$template->assign_vars(array(
			'SAM_ACP_PAGE'	=> $mode,
			'SAM_RELEASE'	=> $config['sam_version'],
			'U_BACK'		=> $this->u_action,
		));

		$basic_link = $this->u_action . '&amp;mode=' . $mode;

		add_form_key('sam_adm');

		if ($submit && !check_form_key('sam_adm'))
		{
			trigger_error('FORM_INVALID', E_USER_WARNING);
		}

		/*
		* include the choosen module
		*/
		switch($mode)
		{
			case 'config':
				$this->page_title = 'ACP_SAM_CONFIG';

				foreach ($config as $key => $value)
				{
					if (substr($key, 0, 4) == 'sam_')
					{	
						$new[$key] = utf8_normalize_nfc($request->variable($key, $value, true));
					
						if ($submit)
						{
							if ($key == 'sam_file_size_max')
							{
								$x = $request->variable('sam_file_size_ms', '');
								switch($x)
								{
									case 'kb':
										$new[$key] = floor(intval($new[$key]) * 1024);
										break;
									case 'mb':
										$new[$key] = floor(intval($new[$key]) * 1048576);
										break;
									case 'gb':
										$new[$key] = floor(intval($new[$key]) * 1073741824);
										break;
								}
							}

							$config->set($key, $new[$key], true);
						}
					}
				}

				if($submit)
				{
					// Save the general texts
					$welcome_msg = utf8_normalize_nfc($request->variable('welcome_msg', '', true));
					$general_use = utf8_normalize_nfc($request->variable('general_use', '', true));

					$allow_bbcode		= ($config['allow_bbcode']) ? true : false;
					$allow_urls			= true;
					$allow_smilies		= ($config['allow_smilies']) ? true : false;
					$sam_uid			= '';
					$sam_bitfield		= '';
					$sam_flags			= 0;

					generate_text_for_storage($welcome_msg, $sam_uid, $sam_bitfield, $sam_flags, $allow_bbcode, $allow_urls, $allow_smilies);

					$sql = 'UPDATE ' . SAM_TEXT_TABLE . ' SET ' . $db->sql_build_array('UPDATE', array(
						'sam_text'		=> $welcome_msg,
						'sam_uid'		=> $sam_uid,
						'sam_bitfield'	=> $sam_bitfield,
						'sam_flags'		=> $sam_flags,
					)) . " WHERE sam_key = 'WELCOME_MSG'";
					$result = $db->sql_query($sql);

					if (!$db->sql_affectedrows($result))
					{
						$sql = 'INSERT INTO ' . SAM_TEXT_TABLE . ' ' . $db->sql_build_array('INSERT', array(
							'sam_key'		=> 'WELCOME_MSG',
							'sam_text'		=> $welcome_msg,
							'sam_uid'		=> $sam_uid,
							'sam_bitfield'	=> $sam_bitfield,
							'sam_flags'		=> $sam_flags,
						));
						$db->sql_query($sql);
					}

					$allow_bbcode		= ($config['allow_bbcode']) ? true : false;
					$allow_urls			= true;
					$allow_smilies		= ($config['allow_smilies']) ? true : false;
					$sam_uid			= '';
					$sam_bitfield		= '';
					$sam_flags			= 0;

					generate_text_for_storage($general_use, $sam_uid, $sam_bitfield, $sam_flags, $allow_bbcode, $allow_urls, $allow_smilies);

					$sql = 'UPDATE ' . SAM_TEXT_TABLE . ' SET ' . $db->sql_build_array('UPDATE', array(
						'sam_text'		=> $general_use,
						'sam_uid'		=> $sam_uid,
						'sam_bitfield'	=> $sam_bitfield,
						'sam_flags'		=> $sam_flags,
					)) . " WHERE sam_key = 'GENERAL_USE'";
					$result = $db->sql_query($sql);

					if (!$db->sql_affectedrows($result))
					{
						$sql = 'INSERT INTO ' . SAM_TEXT_TABLE . ' ' . $db->sql_build_array('INSERT', array(
							'sam_key'		=> 'GENERAL_USE',
							'sam_text'		=> $general_use,
							'sam_uid'		=> $sam_uid,
							'sam_bitfield'	=> $sam_bitfield,
							'sam_flags'		=> $sam_flags,
						));
						$db->sql_query($sql);
					}

					// Purge the config cache
					$cache->destroy('config');
				
					$message = $language->lang('SAM_CONFIG_UPDATED') . "<br /><br />" . $language->lang('CLICK_RETURN_SAM_CONFIG', '<a href="' . $basic_link . '">', '</a>');
				
					trigger_error($message);
				}

				$welcome_msg = '';
				$general_use = '';

				$sql = 'SELECT sam_key, sam_text, sam_uid, sam_flags FROM ' . SAM_TEXT_TABLE . '
					WHERE ' . $db->sql_in_set('sam_key', array('WELCOME_MSG', 'GENERAL_USE'));
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$sam_key	= $row['sam_key'];
					$sam_text	= $row['sam_text'];
					$sam_uid	= $row['sam_uid'];
					$sam_flags	= $row['sam_flags'];

					$text_ary	= generate_text_for_edit($sam_text, $sam_uid, $sam_flags);
					$$sam_key	= $text_ary['text'];
				}

				$db->sql_freeresult($result);

				$s_sam_perm_upload = '<select name="sam_perm_upload">';
				$s_sam_perm_upload .= '<option value="0">' . $language->lang('SAM_PERM_GUEST') . '</option>';
				$s_sam_perm_upload .= '<option value="1">' . $language->lang('SAM_PERM_USERS') . '</option>';
				$s_sam_perm_upload .= '<option value="2">' . $language->lang('SAM_PERM_ADMIN') . '</option>';
				$s_sam_perm_upload .= '</select>';
				$s_sam_perm_upload = str_replace('value="' . $new['sam_perm_upload'] . '">', 'value="' . $new['sam_perm_upload'] . '" selected="selected">', $s_sam_perm_upload);

				$s_sam_perm_rate = '<select name="sam_perm_rate">';
				$s_sam_perm_rate .= '<option value="0">' . $language->lang('SAM_PERM_GUEST') . '</option>';
				$s_sam_perm_rate .= '<option value="1">' . $language->lang('SAM_PERM_USERS') . '</option>';
				$s_sam_perm_rate .= '<option value="2">' . $language->lang('SAM_PERM_ADMIN') . '</option>';
				$s_sam_perm_rate .= '</select>';
				$s_sam_perm_rate = str_replace('value="' . $new['sam_perm_rate'] . '">', 'value="' . $new['sam_perm_rate'] . '" selected="selected">', $s_sam_perm_rate);

				$sam_file_size_max = $new['sam_file_size_max'];
				if ($sam_file_size_max < 1024)
				{
					$sam_file_size_max_out = $sam_file_size_max;
					$data_range_select = 'b';
				}
				else if ($sam_file_size_max < 1048576)
				{
					$sam_file_size_max_out = number_format($sam_file_size_max / 1024, 0);
					$data_range_select = 'kb';
				}
				else if ($sam_file_size_max < 1073741824)
				{
					$sam_file_size_max_out = number_format($sam_file_size_max / 1048576, 0);
					$data_range_select = 'mb';}
				else
				{
					$sam_file_size_max_out = number_format($sam_file_size_max / 1073741824, 0);
					$data_range_select = 'gb';
				}
				
				$s_select_datasize = '<option value="b">' . $language->lang('SAM_SIZE_B') . '</option>';
				$s_select_datasize .= '<option value="kb">' . $language->lang('SAM_SIZE_KB') . '</option>';
				$s_select_datasize .= '<option value="mb">' . $language->lang('SAM_SIZE_MB') . '</option>';
				$s_select_datasize .= '<option value="gb">' . $language->lang('SAM_SIZE_GB') . '</option>';

				$s_sam_file_size_ms = str_replace('value="' . $data_range_select . '">', 'value="' . $data_range_select . '" selected="selected">', $s_select_datasize);
				$s_sam_file_size_ms = '<select name="sam_file_size_ms">' . $s_sam_file_size_ms . '</select>';
				
				$template->assign_vars(array(
					'SAM_ROWS'			=> $new['sam_rows'],
					'SAM_COLS'			=> $new['sam_cols'],
					'SAM_PERM_UPLOAD'	=> $s_sam_perm_upload,
					'SAM_PERM_RATE'		=> $s_sam_perm_rate,
					'SAM_RATE_MAX'		=> $new['sam_rate_max'],
					'SAM_FILE_SIZE_MAX'	=> $sam_file_size_max_out,
					'SAM_FILE_SIZE_MS'	=> $s_sam_file_size_ms,
					'SAM_APPROVE_YES'	=> ($new['sam_approve']) ? 'checked="checked"' : '',
					'SAM_APPROVE_NO'	=> (!$new['sam_approve']) ? 'checked="checked"' : '',
					'SAM_ACTIVE_YES'	=> ($new['sam_active']) ? 'checked="checked"' : '',
					'SAM_ACTIVE_NO'		=> (!$new['sam_active']) ? 'checked="checked"' : '',
					'SAM_WELCOME_MSG'	=> $WELCOME_MSG,
					'SAM_GENERAL_USE'	=> $GENERAL_USE,

					'S_CONFIG_ACTION'	=> $basic_link,
				));					

			break;
			case 'cats':
				$this->page_title = 'ACP_SAM_CATS';

				$cat_parent	= $request->variable('cat_parent', 0);
				$cat_title	= utf8_normalize_nfc($request->variable('cat_title', '', true));

				global $table_prefix;

				include($ext_path . 'includes/helpers/class_sam.' . $phpEx);
				
				$index = array();
				$index = $sam->index();

				if (!isset($index) || !sizeof($index))
				{
					$action = 'add';
				}

				switch ($action)
				{
					case 'add':
					case 'edit':

						$cat_text = '';

						$template->assign_var('S_CAT_EDIT', true);
					
						$s_hidden_fields = array('action' => $action);
					
						if ($submit)
						{
							if ($cat_id && isset($index[$cat_id]['cat_title']))
							{
								$sql = 'UPDATE ' . SAM_CATS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', array(
									'CAT_TITLE'		=> $cat_title,
									'CAT_PARENT'	=> $cat_parent)) . ' WHERE cat_id = ' . (int) $cat_id;
					
								$message = $language->lang('SAM_CAT_UPDATED');
							}
							else
							{		
								$sql = 'INSERT INTO ' . SAM_CATS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
									'CAT_TITLE'		=> $cat_title,
									'CAT_PARENT'	=> $cat_parent,
									'CAT_ORDER'		=> 0));
					
								$message = $language->lang('SAM_CAT_ADDED');
							}
					
							$db->sql_query($sql);

							if (!$cat_id)
							{
								$cat_id = $db->sql_nextid();
							}

							// Save the general texts
							$cat_text = utf8_normalize_nfc($request->variable('cat_text', '', true));
		
							$allow_bbcode		= ($config['allow_bbcode']) ? true : false;
							$allow_urls			= true;
							$allow_smilies		= ($config['allow_smilies']) ? true : false;
							$sam_uid			= '';
							$sam_bitfield		= '';
							$sam_flags			= 0;
		
							generate_text_for_storage($cat_text, $sam_uid, $sam_bitfield, $sam_flags, $allow_bbcode, $allow_urls, $allow_smilies);
		
							$sql = 'UPDATE ' . SAM_TEXT_TABLE . ' SET ' . $db->sql_build_array('UPDATE', array(
								'sam_text'		=> $cat_text,
								'sam_uid'		=> $sam_uid,
								'sam_bitfield'	=> $sam_bitfield,
								'sam_flags'		=> $sam_flags,
							)) . " WHERE sam_key = 'SAM_CAT_" . (int) $cat_id . "'";
							$result = $db->sql_query($sql);
		
							if (!$db->sql_affectedrows($result))
							{
								$sql = 'INSERT INTO ' . SAM_TEXT_TABLE . ' ' . $db->sql_build_array('INSERT', array(
									'sam_key'		=> 'SAM_CAT_' . (int) $cat_id,
									'sam_text'		=> $cat_text,
									'sam_uid'		=> $sam_uid,
									'sam_bitfield'	=> $sam_bitfield,
									'sam_flags'		=> $sam_flags,
								));
								$db->sql_query($sql);
							}
				
							$message .= "<br /><br />" . $language->lang('CLICK_RETURN_SAM_CATADMIN', '<a href="' . $basic_link . '">', '</a>');

							trigger_error($message);
						}
					
						if ($action == 'edit' && $cat_id)
						{
							$cat_title			= $index[$cat_id]['cat_title'];
							$cat_title			= str_replace('&nbsp;|__&nbsp;', '', $cat_title);
							$cat_parent			= $sam->sam_cat_select(0, 0, $index[$cat_id]['cat_parent'], $cat_id);
					
							$s_hidden_fields	= array_merge($s_hidden_fields, array('cat_id' => $cat_id));

							$sql = 'SELECT sam_key, sam_text, sam_uid, sam_flags FROM ' . SAM_TEXT_TABLE . '
								WHERE ' . $db->sql_in_set('sam_key', array('SAM_CAT_' . (int) $cat_id));
							$result = $db->sql_query($sql);
							while ($row = $db->sql_fetchrow($result))
							{
								$sam_key	= $row['sam_key'];
								$sam_text	= $row['sam_text'];
								$sam_uid	= $row['sam_uid'];
								$sam_flags	= $row['sam_flags'];
			
								$text_ary	= generate_text_for_edit($sam_text, $sam_uid, $sam_flags);
								$cat_text	= $text_ary['text'];
							}
			
							$db->sql_freeresult($result);
						
						}
						else
						{
							$cat_parent			= $sam->sam_cat_select(0);
						}

						$template->assign_vars(array(
							'SAM_CAT_MODE'			=> ($action == 'edit') ? $language->lang('EDIT') : $language->lang('ADD'),
							'CAT_TITLE'				=> $cat_title,
							'CAT_PARENT'			=> $cat_parent,
							'CAT_TEXT'				=> $cat_text,
					
							'S_FORM_ACTION'			=> $basic_link,
							'S_HIDDEN_FIELDS'		=> build_hidden_fields($s_hidden_fields),
						));

					break;

					case 'delete':

						if ($cat_id && ($sam->index($cat_id, 0, true) || (isset($sam->sam_index[$cat_id]['total']) && $sam->sam_index[$cat_id]['total'])))
						{
							redirect($basic_link);
						}
	
						if (confirm_box(true))
						{
							$sql = 'DELETE FROM ' . SAM_CATS_TABLE . '
								WHERE cat_id = ' . (int) $cat_id;
							$db->sql_query($sql);

							$cache->destroy('_sam_cats');
							$cache->destroy('_sam_counts');
				
							$message = $language->lang('SAM_CAT_REMOVED') . "<br /><br />" . $language->lang('CLICK_RETURN_SAM_CATADMIN', '<a href="' . $basic_link . '">', '</a>');
					
							trigger_error($message);
						}
						else
						{
							confirm_box(false, $language->lang('CONFIRM_OPERATION'), build_hidden_fields(array(
								'cat_id'	=> $cat_id,
								'action'	=> 'delete',
								'mode'		=> $mode,
							)));
						}

					break;

					case 'cat_order':

						$sql = 'SELECT cat_order FROM ' . SAM_CATS_TABLE . '
							WHERE cat_id = ' . (int) $cat_id;
						$result = $db->sql_query($sql);
						$sql_move = $db->sql_fetchfield('cat_order');
						$db->sql_freeresult($result);

						if ($move)
						{
							$sql_move += 15;
						}
						else
						{
							$sql_move -= 15;
						}
					
						$sql = 'UPDATE ' . SAM_CATS_TABLE . '
							SET cat_order = ' . (int) $sql_move . '
							WHERE cat_id = ' . (int) $cat_id;
						$db->sql_query($sql);
					
						$parent_cat = $index[$cat_id]['cat_parent']; 
					
						$sql = 'SELECT cat_id FROM ' . SAM_CATS_TABLE . '
							WHERE cat_parent = ' .(int) $parent_cat . '
							ORDER BY cat_order';
						$result = $db->sql_query($sql);
					
						$i = 10;
					
						while($row = $db->sql_fetchrow($result))
						{
							$sql_move = 'UPDATE ' . SAM_CATS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', array(
								'cat_order' => $i)) . ' WHERE cat_id = ' . (int) $row['cat_id'];
							$db->sql_query($sql_move);
					
							$i += 10;
						}
					
						$db->sql_freeresult($result);

						$cache->destroy('_sam_cats');
					
						redirect($basic_link);

					break;

					default:
				
						foreach (array_keys($index) as $key)
						{
							$cat_id		= $key;
							$cat_title	= $index[$cat_id]['cat_title'];
							$cat_edit	= $basic_link . '&amp;action=edit&amp;cat_id=' . $cat_id;
							$cat_sub	= $sam->index($cat_id, 0, true);

							if ($cat_sub)
							{
								$cat_delete = '';
							}
							else
							{
								$cat_delete = $basic_link . '&amp;action=delete&amp;cat_id=' . $cat_id;
							}
						
							$sam_move_up = $basic_link . ' &amp;action=cat_order&amp;move=0&amp;cat_id=' . $cat_id;
							$sam_move_down = $basic_link . '&amp;action=cat_order&amp;move=1&amp;cat_id=' . $cat_id;
						
							$template->assign_block_vars('categories', array(
								'CAT_TITLE'				=> $cat_title,
						
								'U_CAT_EDIT'			=> $cat_edit,
								'U_CAT_DELETE'			=> $cat_delete,
								'U_CATEGORY_MOVE_UP'	=> $sam_move_up,
								'U_CATEGORY_MOVE_DOWN'	=> $sam_move_down,

								'S_FORM_ACTION'			=> $basic_link,
							));
						}
		
					break;
				}

			break;
			default:
				$this->page_title = 'ACP_SAM_OVERVIEW';
		}
	}
}
