<?php

/**
*
* @package Usermap v0.4.x
* @copyright (c) 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\usermap\acp;

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $language, $template, $request, $config;

		$this->tpl_name = 'acp_usermap_body';
		$this->page_title = $language->lang('ACP_USERMAP');

		add_form_key('mot_usermap_settings');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('mot_usermap_settings'))
			{
				trigger_error($language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

			// save the settings to the phpbb_config table
			$config->set('mot_usermap_lat', substr($request->variable('mot_usermap_lat', ''), 0, 7));
			$config->set('mot_usermap_lon', substr($request->variable('mot_usermap_lon', ''), 0, 6));
			$config->set('mot_usermap_zoom', $request->variable('mot_usermap_zoom', 0));
			$geonames_user = substr($request->variable('mot_usermap_geonamesuser', ''), 0, 255);
			$geonames_user = preg_replace('/[ ]/', '', $geonames_user); // get rid of any spaces
			$config->set('mot_usermap_geonamesuser', $geonames_user);
			trigger_error($language->lang('ACP_USERMAP_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'ACP_USERMAP_LAT'			=> $config['mot_usermap_lat'],
			'ACP_USERMAP_LON'			=> $config['mot_usermap_lon'],
			'ACP_USERMAP_ZOOM'			=> $config['mot_usermap_zoom'],
			'ACP_USERMAP_GEONAMESUSER'	=> $config['mot_usermap_geonamesuser'],
			'U_ACTION'					=> $this->u_action,
		));
	}
}
