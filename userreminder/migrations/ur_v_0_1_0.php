<?php

/**
*
* @package UserReminder v0.4.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\migrations;

class ur_v_0_1_0 extends \phpbb\db\migration\migration
{

	/**
	* If our config variable already exists in the db
	* skip this migration.
	*/
	public function effectively_installed()
	{
		return isset($this->config['mot_ur_inactive_days']);
	}
/*
	// Für die nächste Version:
	static public function depends_on()
	{
		return array('\mot\usermap\migrations\v_0_3_0');
	}
*/
	public function update_data()
	{
		return array(
			// Add the config variable we want to be able to set
			array('config.add', array('mot_ur_inactive_days', 70)),
			array('config.add', array('mot_ur_days_reminded', 10)),
			array('config.add', array('mot_ur_autoremind', 0)),				// 0 == false, 1 == true
			array('config.add', array('mot_ur_days_until_deleted', 10)),
			array('config.add', array('mot_ur_autodelete', 0)),				// 0 == false, 1 == true
			array('config.add', array('mot_ur_protected_members', '')),

			// Add a parent module (ACP_USERREMINDER) to the Extensions tab (ACP_CAT_DOT_MODS)
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_USERREMINDER'
			)),

			// Add our settings_module to the parent module (ACP_USERREMINDER)
			array('module.add', array(
				'acp',
				'ACP_USERREMINDER',
				array(
					'module_basename'		=> '\mot\userreminder\acp\settings_module',
					'modes'					=> array('settings'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_USERREMINDER',
				array(
					'module_basename'		=> '\mot\userreminder\acp\reminder_module',
					'modes'					=> array('reminder'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_USERREMINDER',
				array(
					'module_basename'		=> '\mot\userreminder\acp\registrated_only_module',
					'modes'					=> array('registrated_only'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_USERREMINDER',
				array(
					'module_basename'		=> '\mot\userreminder\acp\zeroposter_module',
					'modes'					=> array('zeroposter'),
				),
			)),
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'mot_reminded_one'	=> array('UINT:11', 0),
					'mot_reminded_two'	=> array('UINT:11', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'mot_reminded_one',
					'mot_reminded_two',
				),
			),
		);
	}
}
