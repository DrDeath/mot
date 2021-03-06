<?php

/**
*
* @package Usermap v0.5.x
* @copyright (c) 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\usermap\acp;

class lang_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $language, $template, $request, $db, $phpbb_root_path, $phpEx;

		$this->tpl_name = 'acp_usermap_lang';
		$this->page_title = $language->lang('ACP_USERMAP');
		$this->lang_path = $phpbb_root_path . 'ext/mot/usermap/language/';

		add_form_key('acp_usermap_langs');

//		$action = (empty($action)) ? $request->variable('action', '') : $action;
		$action = $request->variable('action', '');
		$iso = $request->variable('iso', '');

		// Set some variables first
		$langs_2_install = array();
		$missing_langs = array();
		// Get the field_id of the 'mot_land' field from the profile_fields table
		$query = "SELECT field_id FROM " . PROFILE_FIELDS_TABLE . " WHERE field_name = 'mot_land'";
		$result = $db->sql_query($query);
		$row = $db->sql_fetchrow($result);
		$mot_land_id = $row['field_id'];
		$db->sql_freeresult($result);

		// then we load the 'lang' table
		$query = 'SELECT * FROM ' . LANG_TABLE;
		$result = $db->sql_query($query);
		$langs = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		// then we get the names of the subdirectories in the 'language' directory
		$lang_dirs = $this->dir_counter($this->lang_path);

		switch ($action)
		{
			case 'install':
				// at this point we do know: field_id of mot_land ($mot_land_id), iso code of the language to install and therefore it's language id and the subdirectory name
				foreach ($langs as $row)
				{
					if ($row['lang_iso'] == $iso)
					{
						$lang_id = $row['lang_id'];
					}
				}
				// we have to delete the current lines for this field_id and lang_id in the profile_fields_lang table first since they are either from the default or the en language (which shouldn't need to be installed)
				$query = 'DELETE FROM ' . PROFILE_FIELDS_LANG_TABLE . '
						WHERE field_id = ' . (int) $mot_land_id . '
						AND lang_id = ' . (int) $lang_id;
				$result = $db->sql_query($query);

				// now we read the content of the approbriate countrycode file
				$countrycodes = array();
				$handle = fopen($this->lang_path . $iso . '/countrycode.' . $phpEx, "rb");
				while (!feof($handle))
				{
					$countrycodes[] = fgets($handle);
				}
				fclose($handle);

				// and insert it into the profile_fields_lang table
				$max_i = sizeof($countrycodes) - 1;
				$insert_buffer = new \phpbb\db\sql_insert_buffer($db, PROFILE_FIELDS_LANG_TABLE);
				for ($i = 0; $i < $max_i; $i++)
				{
					$insert_buffer->insert(array(
						'field_id'		=> (int) $mot_land_id,
						'lang_id'		=> (int) $lang_id,
						'option_id'		=> (int) $i,
						'field_type'	=> 'profilefields.type.dropdown',
						'lang_value'	=> $countrycodes[$i],
					));
				}
				$insert_buffer->flush();
			break;
		}

		// we start by iterating through the 'lang' table content to check for missing language packs
		foreach ($langs as $row)
		{
			$nr = array_search($row['lang_dir'], $lang_dirs);
			if ($nr !== false)
			{			// at least there is a directory with this language iso code, now we check whether this language pack is successfully installed with usermap
				$handle = fopen($this->lang_path . $row['lang_dir'] . '/countrycode.' . $phpEx, "rb");
				$line_file = fgets($handle);	// get the first line from the file (reads 'xx-Select your country' in the English version)
				fclose($handle);

				$query = 'SELECT lang_value FROM ' . PROFILE_FIELDS_LANG_TABLE . '
							WHERE field_id = ' . (int) $mot_land_id . '
							AND lang_id = ' . (int) $row['lang_id'] . ' AND option_id = 0';
				$result = $db->sql_query($query);
				$entry = $db->sql_fetchrow($result);
				$line_db = $entry['lang_value'];	// get the first line from the database
				$db->sql_freeresult($result);

				// compare the 2 lines, if they differ, this language wasn't installed, e.g. it was installed with the en version during activation of the usermap or with the boards default language during installation of this language
				if ($line_file != $line_db)
				{
					$langs_2_install[] = $row;
				}

				array_splice($lang_dirs, $nr, 1);	// delete this language from the directory list
			}
			else
			{			// no directory with this language iso code found -> assume it is a missing language pack
				$missing_langs[] = $row;
			}
		}

		foreach ($langs_2_install as $row)
		{
			$template->assign_block_vars('notinst', array(
				'NAME'			=> $row['lang_english_name'],
				'LOCAL_NAME'	=> $row['lang_local_name'],
				'ISO'			=> $row['lang_iso'],
				'U_INSTALL'		=> $this->u_action . '&amp;action=install&amp;iso=' . urlencode($row['lang_iso']),
			));
		}

		foreach ($missing_langs as $row)
		{
			$template->assign_block_vars('missing', array(
				'NAME'			=> $row['lang_english_name'],
				'LOCAL_NAME'	=> $row['lang_local_name'],
				'ISO'			=> $row['lang_iso'],
			));
		}

		// if there still is some content in the lang_dirs array we've got languages in the extension without a corresponding language installed in the board
		// possible future improvement: Delete those subdirectories from the mot/usermap/language directory (only reason so far would be that they no longer 'disturb')
		foreach ($lang_dirs as $row)
		{
			$template->assign_block_vars('additional', array(
				'ISO'			=> $row,
			));
		}

		$template->assign_vars(array(
			'U_ACTION'						=> $this->u_action,
		));
	}

	function dir_counter($dir)
	{
		$rtn = array();
		$path = scandir($dir);

		foreach ($path as $element)
		{
			if ($element != '.' && $element != '..' && is_dir ($dir.'/'.$element))
			{
				$rtn[] = $element;
			}
		}

		return $rtn;
	}
}
