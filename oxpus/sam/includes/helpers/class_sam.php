<?php

/**
*
* @package phpBB Extension - Smilies Album
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\sam\includes\helpers;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class sam
{
	var $sam_index = array();

	public function __construct()
	{
		global $db, $user, $config, $cache, $phpbb_root_path, $phpEx;

		$this->sam_index = $this->obtain_sam_cats();

		$cat_counts = $this->obtain_sam_counts();

		if (is_array($cat_counts) && sizeof($cat_counts))
		{
			foreach($cat_counts as $key => $value)
			{
				$this->sam_index[$key]['total'] = $value;
			}
		}

		if (sizeof($this->sam_index) && !$config['sam_active'])
		{
			$config->set('sam_active', '1', true);
			$config['sam_active'] = '1';
		}
		else if (!sizeof($this->sam_index) && $config['sam_active'])
		{
			$config->set('sam_active', '0', true);
			$config['sam_active'] = '0';
		}
	}

	private function obtain_sam_cats()
	{
		global $db;

		$sql = "SELECT * FROM " . SAM_CATS_TABLE . '
			ORDER BY cat_parent, cat_order';
		$result = $db->sql_query($sql);

		if ($db->sql_affectedrows($result))
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$sam_index[$row['cat_id']] = $row;
				$sam_index[$row['cat_id']]['total'] = 0;
			}
		}
		else
		{
			$sam_index = array();
		}

		$db->sql_freeresult($result);

		return $sam_index;
	}

	private function obtain_sam_counts()
	{
		global $db;

		$sql = 'SELECT COUNT(id) AS total, cat_id FROM ' . SAM_DATA_TABLE . '
			GROUP BY cat_id';
		$result = $db->sql_query($sql);

		if ($db->sql_affectedrows($result))
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$sam_counts[$row['cat_id']] = $row['total'];
			}
		}
		else
		{
			$sam_counts = array();
		}

		$db->sql_freeresult($result);

		return $sam_counts;
	}

	public function index($parent = 0, $level = 0, $only_count = 0, $cat_count = 0)
	{
		global $tree_dl;

		if (!is_array($this->sam_index) || !sizeof($this->sam_index))
		{
			return array();
		}

		foreach($this->sam_index as $cat_id => $value)
		{
			if (isset($value['cat_parent']) && $value['cat_parent'] == $parent)
			{
				if ($only_count)
				{
					$cat_count++;
				}
				else
				{
					$seperator = '';
					for ($i = 0; $i < $level; $i++)
					{
						$seperator .= ($value['cat_parent'] != 0) ? '&nbsp;|__&nbsp;' : '';
					}

					$tree_dl[$cat_id] = $value;
					if ($seperator)
					{
						$tree_dl[$cat_id]['cat_title'] = $seperator . $value['cat_title'];
					}
				}

				$level++;
				$this->index($cat_id, $level, $only_count, $cat_count);
				$level--;
			}
		}

		return ($only_count) ? $cat_count : $tree_dl;
	}

	public function sam_nav($parent)
	{
		if (!is_array($this->sam_index) || !sizeof($this->sam_index))
		{
			return array();
		}

		global $path_dl_array, $tmp_nav;

		if (!is_array($tmp_nav))
		{
			$tmp_nav = array();
		}

		$cat_id = (isset($this->sam_index[$parent]['cat_id'])) ? $this->sam_index[$parent]['cat_id'] : 0;

		if (!$cat_id)
		{
			return array();
		}

		$tmp_nav[] = array('name' => str_replace('&nbsp;|__&nbsp;', '', $this->sam_index[$parent]['cat_title']), 'link' => array('cat_id' => $cat_id));

		if (isset($this->sam_index[$parent]['cat_parent']) && $this->sam_index[$parent]['cat_parent'] != 0)
		{
			$this->sam_nav($this->sam_index[$parent]['cat_parent']);
		}

		return $tmp_nav;
	}

	public function rating_img($ext_path, $rating_points, $rate = false, $sam_id = 0)
	{
		global $user, $config, $phpbb_container;
		$language = $phpbb_container->get('language');

		$rate_points = ceil($rating_points);
		$rate_image = '';

		$style_path = $ext_path . 'includes/images';

		for ($i = 0; $i < $config['sam_rate_max']; $i++)
		{
			$j = $i + 1;
			$points_text = ($j == 1) ? $language->lang('SAM_POINT') : $language->lang('SAM_POINTS');

			if ($rate)
			{
				$ajax = 'onclick="AJAXSAMVote(' . $sam_id . ', ' . $j . '); return false;"';
				$rate_image .= ($j <= $rate_points ) ? '<a href="#" ' . $ajax . '><img src="' . $style_path . '/sam_yes.png" alt="' . $j . $points_text . '" title="' . $j . $points_text . '" /></a>' : '<a href="#" ' . $ajax . '><img src="' . $style_path . '/sam_no.png" alt="' . $j . $points_text . '" title="' . $j . $points_text . '" /></a>';

			}
			else
			{
				$rate_image .= ($j <= $rate_points ) ? '<img src="' . $style_path . '/sam_yes.png" alt="' . $j . $points_text . '" title="' . $j . $points_text . '" />' : '<img src="' . $style_path . '/sam_no.png" alt="' . $j . $points_text . '" title="' . $j . $points_text . '" />';
			}
		}

		return $rate_image;
	}

	public function sam_cat_select($parent = 0, $level = 0, $select_cat = 0, $no_cat = 0)
	{
		if (!isset($catlist))
		{
			$catlist = '';
		}

		if (!isset($this->sam_index) || !sizeof($this->sam_index))
		{
			return array();
		}

		foreach($this->sam_index as $cat_id => $value)
		{
			if (isset($this->sam_index[$cat_id]['cat_parent']) && $this->sam_index[$cat_id]['cat_parent'] == $parent)
			{
				if ($cat_id != $no_cat)
				{
					$cat_title = $this->sam_index[$cat_id]['cat_title'];

					$seperator = '';

					if ($this->sam_index[$cat_id]['cat_parent'] != 0)
					{
						for ($i = 0; $i < $level; $i++)
						{
							$seperator .= '&nbsp;|';
						}
						$seperator .= '__&nbsp;';
					}

					$status = ($cat_id == $select_cat) ? 'selected="selected"' : '';

					$catlist .= '<option value="' . $cat_id . '" ' . $status . '>' . $seperator . $cat_title . '</option>';
				}

				$level++;
				$catlist .= $this->sam_cat_select($cat_id, $level, $select_cat, $no_cat);
				$level--;
			}
		}

		return $catlist;
	}
}

$sam = new sam();
