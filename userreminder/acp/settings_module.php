<?php

/**
*
* @package UserReminder v0.5.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\acp;

class settings_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
	global $user, $language, $template, $request, $config, $phpbb_root_path, $phpEx;

		$this->tpl_name = 'acp_ur_settings';
		$this->page_title = $language->lang('ACP_USERREMINDER');

		add_form_key('acp_userreminder_settings');

		$lang_dir = $phpbb_root_path . 'ext/mot/userreminder/language';
		$ur_lang = $ur_file = $ur_email_text = $preview_text = '';
		$show_preview = $show_filecontent = false;

		/*
		* this IF clause gets activated when the 'submit' button is pressed, writes all settings to $config
		*/
		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('acp_userreminder_settings'))
			{
				trigger_error($language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// save the settings to the phpbb_config table
			$config->set('mot_ur_inactive_days', substr($request->variable('mot_ur_inactive_days', ''), 0, 3));
			$config->set('mot_ur_days_reminded', substr($request->variable('mot_ur_days_reminded', ''), 0, 3));
			$config->set('mot_ur_autoremind', ($request->variable('mot_ur_autoremind', '')) ? '1' : '0');
			$config->set('mot_ur_days_until_deleted', $request->variable('mot_ur_days_until_deleted', 0, 3));
			$config->set('mot_ur_autodelete', ($request->variable('mot_ur_autodelete', '')) ? '1' : '0');
			$protected_members = substr($request->variable('mot_ur_protected_members', ''), 0, 255);
			$protected_members = preg_replace('/[ ]/', '', $protected_members); // get rid of any spaces
			$config->set('mot_ur_protected_members', $protected_members);
			$config->set('mot_ur_email_bcc', substr($request->variable('mot_ur_email_bcc', ''), 0, 255));
			$config->set('mot_ur_email_cc', substr($request->variable('mot_ur_email_cc', ''), 0, 255));

			trigger_error($language->lang('ACP_USERREMINDER_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		/*
		* This IF clause gets activated when the 'load file' button is pressed and loads the respective file defined by $ur_lang and $ur_file from the drive
		*/
		if ($request->is_set_post('load_file'))
		{
			$show_filecontent = true;
			$ur_lang = $request->variable('choose_lang', '');
			$ur_file = $request->variable('choose_file', '');
			$ur_email_text = file_get_contents($lang_dir . '/' . $ur_lang . '/email/' . $ur_file . '.txt');
		}

		/*
		* This IF clause gets activated when the 'preview' button is pressed and shows how the email will look with all the tokens replaced
		*/
		if ($request->is_set_post('preview'))
		{
			$show_preview = true;
			$show_filecontent = true;
			$ur_lang = $request->variable('choose_lang', '');
			$ur_file = $request->variable('choose_file', '');
			$ur_email_text = $request->variable('mot_ur_mail_text', '', true);
			$preview_text = $ur_email_text;

			$token = array('{SITENAME}', '{USERNAME}', '{LAST_VISIT}', '{LAST_REMIND}', '{DAYS_INACTIVE}', '{FORGOT_PASS}',
							'{ADMIN_MAIL}', '{DAYS_TIL_DELETE}', '{EMAIL_SIG}');
			$real_text = array($config['sitename'], $user->data['username'], $user->format_date($user->data['user_lastvisit']),
							$user->format_date($user->data['mot_reminded_one']), $config['mot_ur_inactive_days'],
							$config['server_protocol'].$config['server_name']."/ucp.".$phpEx."?mode=sendpassword",
							$config['board_contact'], $config['mot_ur_days_until_deleted'], $config['board_email_sig']);
			$preview_text = str_replace($token, $real_text, $preview_text);

			$flags = 0;
			$uid = $bitfield = '';
			$preview_text = generate_text_for_display($preview_text, $uid, $bitfield, $flags);
		}

		/*
		* This IF clause gets activated when the 'save file' button is pressed and saves the respective file defined by $ur_lang and $ur_file to the drive
		*/
		if ($request->is_set_post('save_file'))
		{
			$ur_lang = $request->variable('choose_lang', '');
			$ur_file = $request->variable('choose_file', '');
			$ur_email_text = $request->variable('mot_ur_mail_text', '', true);

//			$ur_email_text = mb_convert_encoding($ur_email_text, "ASCII");


			$file = $lang_dir . '/' . $ur_lang . '/email/' . $ur_file . '.txt';

			if (file_put_contents($file, $ur_email_text) === false)
			{
				trigger_error($language->lang('ACP_USERREMINDER_FILE_ERROR', $file) . adm_back_link($this->u_action), E_USER_WARNING);
			}
			else
			{
				trigger_error($language->lang('ACP_USERREMINDER_FILE_SAVED', $file) . adm_back_link($this->u_action), E_USER_NOTICE);
			}
		}

		//base url for pagination, filtering and sorting
		$base_url = $this->u_action
									. "&amp;choose_lang=" . $ur_lang
									. "&amp;choose_file=" . $ur_file
									. "&amp;=show_filecontent" . $show_filecontent;

		$dirs = $this->load_dirs($lang_dir);
		foreach ($dirs as $value)
		{
			$template->assign_block_vars('langs', array(
				'VALUE'		=> $value,
			));
		}
		$template->assign_vars(array(
			'ACP_USERREMINDER_INACTIVE_DAYS'		=> $config['mot_ur_inactive_days'],
			'ACP_USERREMINDER_DAYS_REMINDED'		=> $config['mot_ur_days_reminded'],
			'ACP_USERREMINDER_AUTOREMIND'			=> $config['mot_ur_autoremind'] ? true : false,
			'ACP_USERREMINDER_DAYS_UNTIL_DELETED'	=> $config['mot_ur_days_until_deleted'],
			'ACP_USERREMINDER_AUTODELETE'			=> $config['mot_ur_autodelete'] ? true : false,
			'ACP_USERREMINDER_PROTECTED_MEMBERS'	=> $config['mot_ur_protected_members'],
			'ACP_USERREMINDER_EMAIL_BCC'			=> $config['mot_ur_email_bcc'],
			'ACP_USERREMINDER_EMAIL_CC'				=> $config['mot_ur_email_cc'],
			'ACP_USERREMINDER_EMAIL_TEXT'			=> $ur_email_text,
			'U_ACTION'								=> $this->u_action,
			'CHOOSE_LANG'							=> $ur_lang,
			'CHOOSE_FILE'							=> $ur_file,
			'SHOW_FILECONTENT'						=> $show_filecontent,
			'PREVIEW_TEXT'							=> $preview_text,
			'SHOW_PREVIEW'							=> $show_preview,
		));
	}


// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	/*
	* Loads all language directories of ext/mot/userreminder/language
	* Returns an array with all found directories
	*/
	protected function load_dirs($dir)
	{
		$result = array();
		$dir_ary = scandir($dir);
		foreach ($dir_ary as $key => $value)
		{
			if (!in_array($value,array(".","..")))
			{
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
				{
					$result[] = $value;
				}
			}
		}
		return $result;
	}
}
