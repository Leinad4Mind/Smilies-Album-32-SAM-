<?php

/**
*
* @package phpBB Extension - Smilies ALbum
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\acp;

class main_info
{
	function module()
	{
		global $config;

		return array(
			'filename'	=> '\oxpus\sam\acp\main_info',
			'title'		=> 'ACP_SAM',
			'version'	=> $config['sam_version'],
			'modes'		=> array(
				'overview'		=> array('title' => 'ACP_SAM_OVERVIEW',	'auth' => 'ext_oxpus/sam && acl_a_sam_ovewview',	'cat' => array('ACP_SAM')),
				'config'		=> array('title' => 'ACP_SAM_CONFIG',	'auth' => 'ext_oxpus/sam && acl_a_sam_config',		'cat' => array('ACP_SAM')),
				'cats'			=> array('title' => 'ACP_SAM_CATS',		'auth' => 'ext_oxpus/sam && acl_a_sam_cats',		'cat' => array('ACP_SAM')),
			),
		);
	}
}
