<?php

/**
*
* @package phpBB Extension - Smilie Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	var $ext_version = '1.0.0';

	public function effectively_installed()
	{
		return isset($this->config['sam_version']) && version_compare($this->config['sam_version'], $this->ext_version, '>=');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.add', array('sam_version', $this->ext_version)),

			array('config.add', array('sam_rows', '10')),
			array('config.add', array('sam_cols', '4')),
			array('config.add', array('sam_perm_upload', '1')),
			array('config.add', array('sam_perm_rate', '1')),
			array('config.add', array('sam_rate_max', '5')),
			array('config.add', array('sam_file_size_max', '307200')),
			array('config.add', array('sam_approve', '1')),
			array('config.add', array('sam_active', '0')),

			array('custom', array(array($this, 'add_default_text'))),
		);
	}
			
	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'sam_cats' => array(
					'COLUMNS'		=> array(
						'cat_id'				=> array('UINT:11', NULL, 'auto_increment'),
						'cat_parent'			=> array('UINT:11', 0),
						'cat_title'				=> array('VCHAR:255', ''),
						'cat_order'				=> array('INT:11', 0),
					),
					'PRIMARY_KEY'	=> 'cat_id'
				),
				$this->table_prefix . 'sam_data' => array(
					'COLUMNS'		=> array(
						'id'				=> array('UINT:11', NULL, 'auto_increment'),
						'filename'			=> array('VCHAR:255', ''),
						'title'				=> array('VCHAR:255', ''),
						'user_id'			=> array('INT:11', 0),
						'username'			=> array('VCHAR:50', ''),
						'time'				=> array('TIMESTAMP', 0),
						'cat_id'			=> array('UINT:11', 0),
						'approve'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'id'
				),
				$this->table_prefix . 'sam_rate' => array(
					'COLUMNS'		=> array(
						'pic_id'	=> array('INT:11', 0),
						'user_id'	=> array('INT:11', 0),
						'points'	=> array('INT:3', 0),
					),
				),
				$this->table_prefix . 'sam_text' => array(
					'COLUMNS'		=> array(
						'sam_key'		=> array('VCHAR:50', ''),
						'sam_text'		=> array('MTEXT_UNI', ''),
						'sam_uid'		=> array('CHAR:8', ''),
						'sam_bitfield'	=> array('VCHAR', ''),
						'sam_flags'		=> array('UINT:11', 0),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'sam_cats',
				$this->table_prefix . 'sam_data',
				$this->table_prefix . 'sam_rate',
				$this->table_prefix . 'sam_text',
			),
		);
	}

	public function add_default_text()
	{
		$sql_insert = array(
			array('sam_key'	=> 'WELCOME_MSG', 'sam_text' => 'Willkommen beim Smilies Album.'),
			array('sam_key'	=> 'GENERAL_USE', 'sam_text' => 'Um die Details eines Smilies anzuschauen oder es in einem Beitrag zu verwenden, klicke es bitte an.'),
		);

		$this->db->sql_multi_insert($this->table_prefix . 'sam_text', $sql_insert);
	}
}
