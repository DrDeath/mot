<?php
/**
*
* @package Usermap v0.5.x
* @copyright (c) 2019 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\usermap\migrations;

class v_0_5_0 extends \phpbb\db\migration\migration
{

	/**
	* Check for migration v_0_4_0_1 to be installed
	*/
	static public function depends_on()
	{
		return array('\mot\usermap\migrations\v_0_4_0_1');
	}

	public function update_data()
	{
		return array(

			// Add the lang_module to the parent module (ACP_USERMAP)
			array('module.add', array(
				'acp',
				'ACP_USERMAP',
				array(
					'module_basename'		=> '\mot\usermap\acp\lang_module',
					'modes'					=> array('lang'),
				),
			)),
		);
	}

}
