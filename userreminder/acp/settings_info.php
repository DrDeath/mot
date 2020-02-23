<?php

/**
*
* @package UserReminder v0.4.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\acp;

class settings_info
{
	public function module()
	{
		return array(
			'filename'	=> '\mot\userreminder\acp\settings_module',
			'title'		=> 'ACP_USERREMINDER',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_USERREMINDER_SETTINGS',
					'auth'	=> 'ext_mot/userreminder && acl_a_board',
					'cat'	=> array('ACP_USERREMINDER'),
				),
			),
		);
	}
}
