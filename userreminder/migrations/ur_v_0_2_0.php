<?php

/**
*
* @package UserReminder v0.4.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\migrations;

class ur_v_0_2_0 extends \phpbb\db\migration\migration
{

	/**
	* Check for migration v_0_1_0 to be installed
	*/
	static public function depends_on()
	{
		return array('\mot\userreminder\migrations\ur_v_0_1_0');
	}

	public function update_data()
	{
		return array(
			// Add the config variable we want to be able to set
			array('config.add', array('mot_ur_email_cc', '')),
			array('config.add', array('mot_ur_email_bcc', '')),
			array('config.add', array('mot_ur_consec_run', 0)),
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'mot_last_login'	=> array('UINT:11', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'mot_last_login',
				),
			),
		);
	}

}
