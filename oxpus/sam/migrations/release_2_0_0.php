<?php

/**
*
* @package phpBB Extension - Smilie Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\migrations;

class release_2_0_0 extends \phpbb\db\migration\migration
{
	var $ext_version = '2.0.0';

	public function effectively_installed()
	{
		return isset($this->config['sam_version']) && version_compare($this->config['sam_version'], $this->ext_version, '>=');
	}

	static public function depends_on()
	{
		return array('\oxpus\sam\migrations\release_1_0_4');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.update', array('sam_version', $this->ext_version)),

			array('module.add', array(
 				'acp',
 				'ACP_CAT_DOT_MODS',
 				'ACP_SAM'
 			)),
			array('module.add', array(
				'acp',
				'ACP_SAM',
				array(
					'module_basename'	=> '\oxpus\sam\acp\main_module',
					'modes'				=> array('overview','config','cats'),
				),
			)),

			array('permission.add', array('a_sam_overview')),
			array('permission.add', array('a_sam_config')),
			array('permission.add', array('a_sam_cats')),

			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_sam_overview')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_sam_config')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_sam_cats')),

			array('custom', array(array($this, 'convert_phpbb2'))),
			array('custom', array(array($this, 'add_sam_bbcode'))),
		);
	}

	public function convert_phpbb2()
	{
		// First check if smilies still exists, after just updating the phpBB 3.0.x MOD
		$this->db->sql_return_on_error(true);
		$sql_check = 'SELECT * FROM ' . $this->table_prefix . 'sam_data';
		$result = $this->db->sql_query($sql_check);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$this->db->sql_return_on_error(false);

		// No smilies found? So let's try to update from the phpBB 2.0.x MOD if exists
		if ($row)
		{
			// First search for the smilies themselves
			$this->db->sql_return_on_error(true);
			$result = $this->db->sql_query('SELECT * FROM ' . $this->table_prefix . 'smilies_page');
			$this->db->sql_return_on_error(false);
	
			$smilies = false;

			// Old table found? Okay, now fetch the smilies...
			if ($result)
			{
				while ($row = $this->db->sql_fetchrow($result))
				{
					$sql_insert = 'INSERT INTO ' . $this->table_prefix . 'sam_data' . ' ' . array('INSERT', array(
						'id'		=> $row['pic_id'],
						'filename'	=> $row['pic_filename'],
						'title'		=> $row['pic_title'],
						'user_id'	=> $row['pic_user_id'],
						'username'	=> $row['pic_username'],
						'time'		=> $row['pic_time'],
						'cat_id'	=> 0,
						'approve'	=> 1,
					));
					$this->db->sql_query($sql_insert);
				}

				// At least one smiley was found to be transferd. Yeah!!! 
				$smilies = true;

				$this->db->sql_freeresult($result);
			}

			// Fetch the old configuration to update the default new one...
			$this->db->sql_return_on_error(true);
			$result = $this->db->sql_query('SELECT * FROM ' . $this->table_prefix . 'smilies_config');
			$this->db->sql_return_on_error(false);
	
			if ($result)
			{
				while ($row = $this->db->sql_fetchrow($result))
				{
					// Translate old config names into new ones
					switch($row['config_name'])
					{
						case 'max_file_size':
							$config_name = 'sam_file_size_max';
						break;
						case 'cols_per_page':
							$config_name = 'sam_cols';
						break;
						case 'rows_per_page':
							$config_name = 'sam_rows';
						break;
						default:
							$config_name = '';
					}

					// Update the new config
					if ($config_name)
					{
						set_config($config_name, $row['config_value']);
					}
				}

				$this->db->sql_freeresult($result);
			}

			// Okay, smilies found in the old phpBB 2.0.x tables? Then go on...
			if ($smilies)
			{
				// Check existing rate points
				$this->db->sql_return_on_error(true);
				$result = $this->db->sql_query('SELECT * FROM ' . $this->table_prefix . 'smilies_rate');
				$this->db->sql_return_on_error(false);
		
				if ($result)
				{
					while ($row = $this->db->sql_fetchrow($result))
					{
						// We have some points... And now? Right! Save them:
						$sql_insert = 'INSERT INTO ' . $this->table_prefix . 'sam_rate' . ' ' . array('INSERT', array(
							'pic_id'	=> $row['rate_pic_id'],
							'user_id'	=> $row['rate_user_id'],
							'points'	=> $row['rate_point'],
						));
						$this->db->sql_query($sql_insert);
					}
	
					$this->db->sql_freeresult($result);
				}
	
				// Okay, all smilies data are transfered. So now we have to insert a category to be able to display them.
				$sql_insert = 'INSERT INTO ' . $this->table_prefix . 'sam_cats' . ' ' . array('INSERT', array(
					'cat_title'		=> 'Transfer from Smilies Page',
					'cat_parent'	=> 0,
					'cat_order'		=> 0,
				));
				$this->db->sql_query($sql_insert);
	
				// So we have a cat id... 
				$this->db->sql_query($sql_insert);
				$cat_id = $this->db->sql_nextid();

				// ... to update the imported smilies
				$sql_update = 'UPDATE ' . $this->table_prefix . 'sam_data SET ' . array('UPDATE', array(
					'cat_id'	=> $cat_id));
				$this->db->sql_query($sql_update);

				// Finished *puh*
			}
		}
	}

	public function add_sam_bbcode()
	{
		$sql = 'SELECT bbcode_id FROM ' . $this->table_prefix . "bbcodes WHERE LOWER(bbcode_tag) = 'sam'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			// Create new BBCode
			$sql = 'SELECT MAX(bbcode_id) AS max_bbcode_id FROM ' . $this->table_prefix . 'bbcodes';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				$bbcode_id = $row['max_bbcode_id'] + 1;
				// Make sure it is greater than the core BBCode ids...
				if ($bbcode_id <= NUM_CORE_BBCODES)
				{
					$bbcode_id = NUM_CORE_BBCODES + 1;
				}
			}
			else
			{
				$bbcode_id = NUM_CORE_BBCODES + 1;
			}

			$url = generate_board_url() . '/';
			if ($this->config['enable_mod_rewrite'])
			{
				$url .= 'sam/';
			}
			else
			{
				$url .= 'app.php/sam/';
			}
			if ($bbcode_id <= BBCODE_LIMIT)
			{
				$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'bbcodes ' . $this->db->sql_build_array('INSERT', array(
					'bbcode_tag'			=> 'sam',
					'bbcode_id'				=> (int) $bbcode_id,
					'bbcode_helpline'		=> '',
					'display_on_posting'	=> 1,
					'bbcode_match'			=> '[sam]{NUMBER}[/sam]',
					'bbcode_tpl'			=> '<a href="' . $url . '"><img src="' . $url . '?mode=smilie&amp;sam_id={NUMBER}" alt="{NUMBER}" /></a>',
					'first_pass_match'		=> '!\[sam\]([0-9]+)\[/sam\]!i',
					'first_pass_replace'	=> '[sam:$uid]${1}[/sam:$uid]',
					'second_pass_match'		=> '!\[sam:$uid\]([0-9]+)\[/sam:$uid\]!s',
					'second_pass_replace'	=> '<a href="' . $url . '"><img src="' . $url . '?mode=smilie&amp;sam_id=${1}" alt="${1}" /></a>',
				)));
			}
		}
	}

	public function revert_schema()
	{
		$sql = 'DELETE FROM ' . BBCODES_TABLE . " WHERE bbcode_tag = 'sam'";
		$this->db->sql_query($sql);

		return array();
	}
}
