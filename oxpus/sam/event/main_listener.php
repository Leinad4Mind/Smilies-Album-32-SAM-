<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\event;

/**
* @ignore
*/
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_links',
			'core.viewonline_overwrite_location'	=> 'add_viewonline',
			'core.update_username'					=> 'change_username',
			'core.delete_user_after'				=> 'delete_user',
			'core.permissions'						=> 'add_permission_cat',
			'core.posting_modify_template_vars'		=> 'post_template_data',
		);
	}

	/* @var string phpbb_root_path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/* @var string table_prefix */
	protected $table_prefix;

	/* @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;
	
	/* @var \phpbb\path_helper */
	protected $phpbb_path_helper;

	/* @var Container */
	protected $phpbb_container;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\auth\auth */
	protected $auth;

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
	* @param \phpbb\extension\manager				$phpbb_extension_manager
	* @param \phpbb\path_helper						$phpbb_path_helper
	* @param Container								$phpbb_container
	* @param \phpbb\db\driver\driver_interfacer		$db
	* @param \phpbb\config\config					$config
	* @param \phpbb\controller\helper				$helper
	* @param \phpbb\auth\auth						$auth
	* @param \phpbb\template\template				$template
	* @param \phpbb\user							$user
	*/
	public function __construct($root_path, $php_ext, $table_prefix, \phpbb\extension\manager $phpbb_extension_manager, \phpbb\path_helper $phpbb_path_helper, Container $phpbb_container, \phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\language\language $language)
	{
		$this->root_path				= $root_path;
		$this->php_ext 					= $php_ext;
		$this->table_prefix 			= $table_prefix;
		$this->phpbb_extension_manager	= $phpbb_extension_manager;
		$this->phpbb_path_helper		= $phpbb_path_helper;
		$this->phpbb_container 			= $phpbb_container;
		$this->db 						= $db;
		$this->config 					= $config;
		$this->helper 					= $helper;
		$this->auth						= $auth;
		$this->template 				= $template;
		$this->user 					= $user;
		$this->language					= $language;
	}

	public function load_language_on_setup($event)
	{	
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'oxpus/sam',
			'lang_set' => 'common',
		);

		if (defined('ADMIN_START'))
		{
			$lang_set_ext[] = array(
				'ext_name' => 'oxpus/sam',
				'lang_set' => 'permissions_sam',
			);
		}

		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_page_header_links($event)
	{
		$ext_path = $this->phpbb_extension_manager->get_extension_path('oxpus/sam', true);

		if ($this->config['sam_active'])
		{
			$table_prefix = $this->table_prefix;
			include_once($ext_path . 'includes/helpers/constants.' . $this->php_ext);

			$this->template->assign_vars(array(
				'U_SAM_NAVI' => $this->helper->route('oxpus_sam_controller'),
			));
		}
	}

	public function add_viewonline($event)
	{
		if (strpos($event['row']['session_page'], '/sam') !== false)
		{
			$event['location'] = $this->language->lang('SAM_TITLE');
			$event['location_url'] = $this->helper->route('oxpus_sam_controller');
		}
	}

	public function change_username($event)
	{
		$sql = 'UPDATE ' . $this->table_prefix . "sam_data 
			SET username = '" . $this->db->sql_escape($event['new_name']) . "'
			WHERE username = '" . $this->db->sql_escape($event['old_name']) . "'";
		$this->db->sql_query($sql);
	}

	public function delete_user($event)
	{
		$sql = 'DELETE FROM ' . $this->table_prefix . 'sam_data 
			WHERE ' . $this->db->sql_in_set('user_id', $event['user_ids']);
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->table_prefix . 'sam_rate 
			WHERE ' . $this->db->sql_in_set('user_id', $event['user_ids']);
		$this->db->sql_query($sql);
	}

	public function add_permission_cat($event)
	{
		$perm_cat = $event['categories'];
		$perm_cat['sam'] = 'ACP_SAM';
		$event['categories'] = $perm_cat;

		$permission = $event['permissions'];
		$permission['a_sam_overview']	= array('lang' => 'ACP_SAM_OVERVIEW',	'cat' => 'sam');
		$permission['a_sam_config']		= array('lang' => 'ACP_SAM_CONFIG',		'cat' => 'sam');
		$permission['a_sam_cats']		= array('lang' => 'ACP_SAM_CATS',		'cat' => 'sam');
		$event['permissions'] = $permission;
	}

	public function post_template_data($event)
	{
		$this->template->assign_vars(array(
			'U_SAM_POPUP' => $this->helper->route('oxpus_sam_controller', array('mode' => 'popup')),
		));
	}
}
