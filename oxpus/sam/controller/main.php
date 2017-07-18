<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\controller;

use Symfony\Component\DependencyInjection\Container;

class main
{
	/* @var string phpBB root path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/* @var string table_prefix */
	protected $table_prefix;

	/* @var Container */
	protected $phpbb_container;

	/* @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;

	/* @var \phpbb\path_helper */
	protected $phpbb_path_helper;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\log\log_interface */
	protected $log;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language $language Language object */
	protected $language;

	/**
	* Constructor
	*
	* @param string									$root_path
	* @param string									$php_ext
	* @param string									$table_prefix
	* @param Container 								$phpbb_container
	* @param \phpbb\extension\manager				$phpbb_extension_manager
	* @param \phpbb\path_helper						$phpbb_path_helper
	* @param \phpbb\db\driver\driver_interfacer		$db
	* @param \phpbb\config\config					$config
	* @param \phpbb\log\log_interface 				$log
	* @param \phpbb\controller\helper				$helper
	* @param \phpbb\auth\auth						$auth
	* @param \phpbb\request\request_interface 		$request
	* @param \phpbb\template\template				$template
	* @param \phpbb\user							$user
	*/
	public function __construct($root_path, $php_ext, $table_prefix, Container $phpbb_container, \phpbb\extension\manager $phpbb_extension_manager, \phpbb\path_helper $phpbb_path_helper, \phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\log\log_interface $log, \phpbb\controller\helper $helper, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\language\language $language)
	{
		$this->root_path				= $root_path;
		$this->php_ext 					= $php_ext;
		$this->table_prefix 			= $table_prefix;
		$this->phpbb_container 			= $phpbb_container;
		$this->phpbb_extension_manager 	= $phpbb_extension_manager;
		$this->phpbb_path_helper		= $phpbb_path_helper;
		$this->db 						= $db;
		$this->config 					= $config;
		$this->phpbb_log 				= $log;
		$this->helper 					= $helper;
		$this->auth						= $auth;
		$this->request					= $request;
		$this->template 				= $template;
		$this->user 					= $user;
		$this->language					= $language;
	}

	public function handle($mode = '')
	{
		// Define the ext path
		$ext_path					= $this->phpbb_extension_manager->get_extension_path('oxpus/sam', true);
		$this->phpbb_path_helper	= $this->phpbb_container->get('path_helper');
		$ext_path_web				= $this->phpbb_path_helper->update_web_root_path($ext_path);

		$table_prefix = $this->table_prefix;
		include_once($ext_path . '/includes/helpers/constants.' . $this->php_ext);

		$this->template->assign_vars(array(
			'SAM_EXT_PATH'		=> $ext_path_web,
		));

		$action		= $this->request->variable('action', 'manage');
		$mode		= $this->request->variable('mode', 'main');

		$start		= $this->request->variable('start', 0);
		$cat_id		= $this->request->variable('cat_id', 0);
		$sam_id		= $this->request->variable('sam_id', 0);
		$points		= $this->request->variable('points', 0);

		include($ext_path . '/includes/helpers/class_sam.' . $this->php_ext);
		$index		= $sam->index();

		$smilies_per_page = $this->config['sam_rows'] * $this->config['sam_cols'];
		$total_smilies = 0;

		$page_start = max($start - 1, 0) * $smilies_per_page;
		$start = $page_start;

		$perm_upload = false;

		switch ($this->config['sam_perm_upload'])
		{
			case '0':
				$perm_upload = true;
			break;
			case '1':
				$perm_upload = ($this->user->data['is_registered']) ? true : false;
			break;
			default:
				$perm_upload = ($this->auth->acl_get('a_') && $this->user->data['is_registered'] && !$this->user->data['user_perm_from']) ? true : false;
			break;
		}

		$perm_rate = false;

		switch ($this->config['sam_perm_rate'])
		{
			case '0':
				$perm_rate = true;
			break;
			case '1':
				$perm_rate = ($this->user->data['is_registered']) ? true : false;
			break;
			default:
				$perm_rate = ($this->auth->acl_get('a_') && $this->user->data['is_registered'] && !$this->user->data['user_perm_from']) ? true : false;
			break;
		}

		if (($mode == 'detail' || $mode == 'smilie') && !$sam_id)
		{
			$mode = 'main';
		}

		if ($mode == 'modcp' && !$this->auth->acl_get('a_'))
		{
			$mode = 'main';
		}

		if ($mode == 'ajax' && !$perm_rate)
		{
			$mode = 'main';
		}

		$nav_string['link'][] = array('mode' => 'main');
		$nav_string['name'][] = $this->language->lang('SAM_TITLE');

		if ($mode == 'modcp')
		{
			$nav_string['link'][] = array('mode' => 'modcp', 'cat_id' => $cat_id);
			$nav_string['name'][] = $this->language->lang('MCP');
		}
		else if ($cat_id)
		{
			$nav_tmp = $sam->sam_nav($cat_id);

			for ($i = sizeof($nav_tmp) - 1; $i >= 0; $i--)
			{
				$nav_string['link'][] = $nav_tmp[$i]['link'];
				$nav_string['name'][] = $nav_tmp[$i]['name'];
			}
		}

		for ($i = 0; $i < sizeof($nav_string['link']); $i++)
		{
			$this->template->assign_block_vars('navlinks', array(
				'U_VIEW_FORUM'	=> $this->helper->route('oxpus_sam_controller', $nav_string['link'][$i]),
				'FORUM_NAME'	=> $nav_string['name'][$i],
			));
		}

		$s_cat_select = '';

		switch($mode)
		{
			case 'ajax':

				include($ext_path . 'includes/modules/sam_ajax.' . $this->php_ext);

			break;

			case 'smilie':
				$sql = 'SELECT filename FROM ' . SAM_DATA_TABLE . '
					WHERE id = ' . (int) $sam_id;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$file_path = $ext_path . 'includes/uploads/' . $row['filename'];
				$browser = $this->user->data['session_browser'];

				if ((@file_exists($file_path) && @is_readable($file_path)) && !headers_sent())
				{
					$image_data = @getimagesize($file_path);
					$image_type = image_type_to_mime_type($image_data[2]);

					header('Pragma: public');
					header('Content-Type: ' . $image_type);

					if (strpos(strtolower($browser), 'msie') !== false && strpos(strtolower($browser), 'msie 8.0') === false)

					{
						header('Content-Disposition: attachment; ' . $row['filename']);

						if (strpos(strtolower($browser), 'msie 6.0') !== false)
						{
							header('Expires: -1');
						}
						else
						{
							header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
						}
					}
					else
					{
						header('Content-Disposition: inline; ' . $row['filename']);
						header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
					}

					readfile($file_path);
					flush();
				}
				else
				{
					send_status_line(404, 'Not Found');
				}

				garbage_collection();
				exit_handler();

			break;

			case 'add':

				include($ext_path . 'includes/modules/sam_upload.' . $this->php_ext);

			break;

			case 'modcp':

				if (!$this->auth->acl_get('a_'))
				{
					redirect($this->helper->route('oxpus_sam_controller', array('cat_id' => $cat_id)));
				}

				$new_cat_id = $this->request->variable('new_cat_id', 0);

				include($ext_path . 'includes/modules/sam_modcp.' . $this->php_ext);

			break;

			case 'detail':

				$sql = 'SELECT d.*, u.username AS user_name, u.user_colour, AVG(r.points) AS rating
					FROM ' . SAM_DATA_TABLE . ' d
					LEFT JOIN ' . USERS_TABLE . ' u ON u.user_id = d.user_id
					LEFT JOIN ' . SAM_RATE_TABLE . ' r ON (r.pic_id = d.id AND r.user_id = ' . (int) $this->user->data['user_id'] . ')
					WHERE d.id = ' . (int) $sam_id . '
					GROUP BY user_name, u.user_colour, d.username, d.filename, d.title, d.id, d.user_id';
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$user_sam		= $row['username'];
				$user_forum		= $row['user_name'];

				if ($user_forum)
				{
					$sam_user = $user_forum;
				}
				else if ($user_sam)
				{
					$sam_user = $user_sam;
				}
				else
				{
					$sam_user = $this->language->lang('GUEST');
				}

				$rating = $sam->rating_img($ext_path_web, $row['rating'], (($perm_rate) ? true : false), $row['id']);

				$this->template->set_filenames(array('body' => 'sam_detail.html'));

				$this->template->assign_vars(array(
					'FILENAME'		=> $ext_path_web . 'includes/uploads/' . $row['filename'],
					'TITLE'			=> censor_text($row['title']),
					'RATING'		=> $rating,
					'USER'			=> get_username_string('no_profile', $row['user_id'], $sam_user, $row['user_colour']),
					'TIME'			=> $this->user->format_date($row['time']),
					'BBCODE'		=> '[sam]' . $row['id'] . '[/sam]',
				));

			break;

			case 'popup':
			default:

				if ($mode == 'popup')
				{
					$this->template->set_filenames(array('body' => 'sam_popup.html'));
				}
				else
				{
					$this->template->set_filenames(array('body' => 'sam_main.html'));
				}

				$index_parent = ($cat_id) ? $index[$cat_id]['cat_parent'] : 0;
				$index2 = $index;

				foreach ($index as $key => $value)
				{
					if ($index[$key]['cat_parent'] == $index_parent)
					{
						$cat_title = str_replace('&nbsp;|__&nbsp;', '', $index[$key]['cat_title']);

						$this->template->assign_block_vars('sam_cats', array(
							'CAT_SEL'		=> ($index[$key]['cat_id'] == $cat_id) ? true : false,
							'CAT_SIGN'		=> '&bull;&nbsp;',
							'CAT_TITLE'		=> $cat_title,
							'CAT_COUNT'		=> $index[$key]['total'],
							'CAT_ID'		=> $index[$key]['cat_id'],
							'U_CAT'			=> $this->helper->route('oxpus_sam_controller', array('cat_id' => $index[$key]['cat_id'])),
							'U_CAT_POPUP'	=> $this->helper->route('oxpus_sam_controller', array('cat_id' => $index[$key]['cat_id'], 'mode' => 'popup')),
						));

						if ($index[$key]['cat_id'] == $cat_id)
						{
							foreach ($index2 as $key2 => $value2)
							{
								if ($index2[$key2]['cat_parent'] == $index[$key]['cat_id'])
								{
									$cat_title = str_replace('&nbsp;|__&nbsp;', '', $index2[$key2]['cat_title']);

									$this->template->assign_block_vars('sam_cats', array(
										'CAT_SEL'	=> ($index2[$key2]['cat_id'] == $cat_id) ? true : false,
										'CAT_SIGN'	=> '&nbsp;&nbsp;&nbsp;&bull;&nbsp;',
										'CAT_TITLE'	=> $cat_title,
										'CAT_COUNT'	=> $index2[$key2]['total'],
										'U_CAT'		=> $this->helper->route('oxpus_sam_controller', array('cat_id' => $index2[$key2]['cat_id'])),
									));
								}
							}
						}
					}
				}

				if (sizeof($index))
				{
					foreach ($index as $key => $value)
					{
						if ($cat_id)
						{
							if ($cat_id == $key)
							{
								$total_smilies += $index[$key]['total'];
							}
						}
						else
						{
							$total_smilies += $index[$key]['total'];
						}
					}
				}

				if ($cat_id && $mode <> 'popup')
				{
					$sam_key = 'SAM_CAT_' . (int) $cat_id;
				}
				else
				{
					$sam_key = 'WELCOME_MSG';
				}

				$sql = 'SELECT * FROM ' . SAM_TEXT_TABLE . "
					WHERE sam_key = '" . $this->db->sql_escape($sam_key) . "'";
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$display_text = censor_text($row['sam_text']);
				$display_text = generate_text_for_display($display_text, $row['sam_uid'], $row['sam_bitfield'], $row['sam_flags']);

				if (!$total_smilies)
				{
					$this->template->assign_var('S_NO_SMILIES', true);
				}
				else
				{
					$sql_where = '';
					$sql_order = '';

					if ($cat_id)
					{
						$sql_where = ' AND c.cat_id = ' . (int) $cat_id . ' ';
					}
					else
					{
						$sql_order = ' ORDER BY rand() ';
					}

					$s_cat_select = '<select name="cat_id" onchange="submit(this);"><option value="0">' . $this->language->lang('SAM_ALL') . '</option>';
					$s_cat_select .= $sam->sam_cat_select(0, 0, $cat_id);
					$s_cat_select .= '</select>';

					$sam_id_ary = array();

					$sql = 'SELECT d.*, c.cat_title, AVG(r.points) AS rating
						FROM ' . SAM_CATS_TABLE . ' c,  ' . SAM_DATA_TABLE . ' d
						LEFT JOIN ' . SAM_RATE_TABLE . ' r ON r.pic_id = d.id
						WHERE c.cat_id = d.cat_id
							AND d.approve = ' . true . $sql_where . '
						GROUP BY c.cat_title, d.cat_id, d.filename, d.title, d.id ' . $sql_order . '
						LIMIT ' . (int) $start . ', ' . (int) $smilies_per_page;
					$result = $this->db->sql_query($sql);

					$column = 1;
					$row = 1;

					while ($row = $this->db->sql_fetchrow($result))
					{
						$rating = $sam->rating_img($ext_path_web, $row['rating']);

						$this->template->assign_block_vars('smilies_data', array(
							'COLUMN'	=> $column,
							'FILENAME'	=> $ext_path_web . '/includes/uploads/' . $row['filename'],
							'TITLE'		=> censor_text($row['title']),
							'CAT_TITLE'	=> ($cat_id) ? '' : $row['cat_title'],
							'RATING'	=> $rating,
							'U_SMILIE'	=> $this->helper->route('oxpus_sam_controller', array('mode' => 'detail', 'sam_id' => $row['id'])),
							'U_CAT'		=> ($cat_id) ? '' : $this->helper->route('oxpus_sam_controller', array('cat_id' => $row['cat_id'])),
							'U_SAM_POST'	=> '[sam]' . $row['id'] . '[/sam]',
						));

						$column++;

						if ($column > $this->config['sam_cols'])
						{
							$column = 1;
							$row++;
						}
					}

					$this->db->sql_freeresult($result);
				}

			break;
		}

		page_header($this->language->lang('SAM_TITLE'));

		if ($total_smilies > $smilies_per_page)
		{
			$pagination = $this->phpbb_container->get('pagination');
			$pagination->generate_template_pagination(
				array(
					'routes' => array(
						'oxpus_sam_controller',
						'oxpus_sam_controller',
					),
					'params' => array('mode' => 'modcp', 'cat_id' => $cat_id),
				), 'pagination', 'start', $total_smilies, $smilies_per_page, $page_start);

			$this->template->assign_vars(array(
				'PAGE_NUMBER'      => $pagination->on_page($total_smilies, $smilies_per_page, $page_start),
				'TOTAL_SMILIES'	   => $this->language->lang('SMILIES_TOTAL', $total_smilies),
			));
		}

		$this->template->assign_vars(array(
			'DISPLAY_TEXT'	=> (isset($display_text)) ? $display_text : '',
			'MAX_COLUMNS'	=> $this->config['sam_cols'],
			'S_SAM_MODCP'	=> ($this->auth->acl_get('a_') && $this->user->data['is_registered'] && !$this->user->data['user_perm_from'] && $cat_id && $index[$cat_id]['total']) ? true : false,
			'S_SAM_MODE'	=> $mode,
			'S_CAT_SELECT'	=> $s_cat_select,
			'U_SAM_ADD'		=> ($perm_upload) ? $this->helper->route('oxpus_sam_controller', array('mode' => 'add')) : '',
			'U_SAM_BASIC'	=> $this->helper->route('oxpus_sam_controller', array('mode' => 'main')),
			'U_SAM_MODCP'	=> $this->helper->route('oxpus_sam_controller', array('mode' => 'modcp', 'cat_id' => $cat_id)),
			'U_SAM_AJAX'	=> $this->helper->route('oxpus_sam_controller', array('mode' => 'ajax')),
		));

		include($ext_path . 'includes/modules/sam_footer.' . $this->php_ext);

		page_footer();
	}
}
