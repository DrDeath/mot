<?php

/**
*
* @package UserReminder v0.5.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\acp;

class registrated_only_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $db, $language, $template, $request, $config, $config_text, $phpbb_container, $user;

		$this->tpl_name = 'acp_ur_registratedonly';
		$this->page_title = $language->lang('ACP_USERREMINDER');

		add_form_key('acp_userreminder_registered_only');

		$secs_per_day = 86400;
		$now = time();
		$server_config = $config['server_protocol'].$config['server_name'].$config['script_path'];
		$common = $phpbb_container->get('mot.userreminder.common');

		// set parameters for pagination
		$start = 0;
		$limit = 25;	// max 25 lines per page

		// get sort variables from template (if we are in a loop of the pagination). At first call there are no variables from the (so far uncalled) template
		$sort_dir = $request->variable('sort_dir', '');

		// First call of this script, we don't get any variables back from the template -> we have to set initial parameters for sorting
		if (empty($sort_dir))
		{
			$sort_dir = 'ASC';
		}

		if ($request->is_set_post('delmarked'))
		{
			$marked = $request->variable('mark', array(0));
			if (sizeof($marked) > 0)
			{
				if (confirm_box(true))
				{
					$common->delete_users($marked);
					trigger_error($language->lang('USER_DELETED', sizeof($marked)) . adm_back_link($this->u_action), E_USER_NOTICE);
				}
				else
				{
					confirm_box(false, $language->lang('CONFIRM_USER_DELETE', sizeof($marked)), build_hidden_fields(array(
						'delmarked'	=> $deletemark,
						'mark'		=> $marked,
						'sd'		=> $sort_dir,
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $this->u_action,
					)));
				}
			}
			else
			{
				trigger_error($language->lang('NO_USER_SELECTED') . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		if ($request->is_set_post('sort'))
		{
			// sort direction has been changed in the template, so we set it here
			$sort_dir = $request->variable('sort_dir', '');
			// and start with the first page
			$start = 0;
		}
		else
		{
			$start = $request->variable('start', 0);
		}

		$query = 'SELECT user_id, username, user_colour, user_regdate
				FROM  ' . USERS_TABLE . '
				WHERE (user_type = ' . USER_NORMAL . ' OR user_type = ' . USER_FOUNDER . ') ' .		// ignore anonymous (=== guest), bots, inactive and deactivated users
				'AND mot_last_login = 0 ';															// select users who have never been online
		if ($config['mot_ur_protected_members'] <> '')										// prevent sql errors due to empty string
		{
			$query .= 'AND user_id NOT IN (' . $config['mot_ur_protected_members'] . ') ';
		}
		$query .= 'ORDER BY user_regdate ' . $sort_dir;

		$result = $db->sql_query($query);
		$registered_only = $db->sql_fetchrowset($result);
		$count_registered_only = sizeof($registered_only);//echo $count_registered_only;
		$db->sql_freeresult($result);

		$result = $db->sql_query_limit( $query, $limit, $start );
		$registered_only = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		//base url for pagination, filtering and sorting
		$base_url = $this->u_action
									. "&amp;sort_dir=" . $sort_dir;

		// Load pagination
		$pagination = $phpbb_container->get('pagination');
		$start = $pagination->validate_start($start, $limit, $count_registered_only);
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $count_registered_only, $limit, $start);

		// write data into zeroposter array (output by template)
		foreach ($registered_only as &$row)
		{
			$no_of_days = (int) (( $now - $row['user_regdate']) / $secs_per_day);
			$template->assign_block_vars('registered_only', array(
				'USERNAME'		=> $row['username'],
				'USER_COLOUR'	=> $row['user_colour'],
				'JOINED'		=> $user->format_date($row['user_regdate']),
				'OFFLINE_DAYS'	=> $no_of_days,
				'USER_ID'		=> $row['user_id'],
			));
		}

		$template->assign_vars(array(
			'SERVER_CONFIG'	=> $server_config,
			'SORT_DIR'		=> $sort_dir,
			)
		);

	}
}
