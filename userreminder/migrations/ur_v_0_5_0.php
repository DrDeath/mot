<?php

/**
*
* @package UserReminder v0.5.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\migrations;

class ur_v_0_5_0 extends \phpbb\db\migration\migration
{

	/**
	* Check for migration v_0_2_0 to be installed
	*/
	static public function depends_on()
	{
		return array('\mot\userreminder\migrations\ur_v_0_2_0');
	}

	public function update_data()
	{
		return array(
			// set the initial values for column 'mot_last_login' from column 'user_lastvisit' in users table
			array('custom', array(array($this, 'init_ur'))),
		);
	}

	public function init_ur()
	{
		if ($this->config['mot_ur_consec_run'] == 0)
		{
			$query = 'SELECT user_id, user_lastvisit
					FROM  ' . USERS_TABLE . '
					ORDER BY user_id';

			$result = $this->db->sql_query($query);
			$all_users = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($all_users as $row)
			{
				$sql_ary = array(
					'mot_last_login'	=> $row['user_lastvisit'],
				);

				$query = 'UPDATE ' . USERS_TABLE . '
							SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
							WHERE user_id = ' . (int) $row['user_id'];
				$this->db->sql_query($query);
			}
			$this->config->set('mot_ur_consec_run', 1);
		}
	}

}
