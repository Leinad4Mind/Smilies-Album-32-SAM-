<?php

/**
*
* @package phpBB Extension - Smilie Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\migrations;

class release_2_0_2 extends \phpbb\db\migration\migration
{
	var $ext_version = '2.0.2';

	public function effectively_installed()
	{
		return isset($this->config['sam_version']) && version_compare($this->config['sam_version'], $this->ext_version, '>=');
	}

	static public function depends_on()
	{
		return array('\oxpus\sam\migrations\release_2_0_1');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.update', array('sam_version', $this->ext_version)),
		);
	}
}
