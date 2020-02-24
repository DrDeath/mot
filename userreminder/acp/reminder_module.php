<?php

/**
*
* @package UserReminder v0.5.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\acp;

class reminder_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $db, $language, $template, $request, $config, $config_text, $phpbb_container, $user;

		$secs_per_day = 86400;
		$now = time();
		$day_limit = $now - ($secs_per_day * $config['mot_ur_inactive_days']);
		$server_config = $config['server_protocol'].$config['server_name'].$config['script_path'];
		$common = $phpbb_container->get('mot.userreminder.common');

		// set parameters for pagination
		$start = 0;
		$limit = 25;	// max 25 lines per page

		// get sort variables from template (if we are in a loop of the pagination). At first call there are no variables from the (so far uncalled) template
		$sort_key = $request->variable('sort_key', '');
		$sort_dir = $request->variable('sort_dir', '');

		// First call of this script, we don't get any variables back from the template -> we have to set initial parameters for sorting
		if (empty($sort_key) && empty($sort_dir))
		{
			$sort_key = 'mot_last_login';
			$sort_dir = 'ASC';
		}

		$enable_sort_one = $enable_sort_two = 0;

		$this->tpl_name = 'acp_ur_reminder';
		$this->page_title = $language->lang('ACP_USERREMINDER');

		add_form_key('acp_userreminder_reminder');

		if ($request->is_set_post('sort'))
		{
			// sort key and/or direction have been changed in the template, so we set them here
			$sort_key = $request->variable('sort_key', '');
			$sort_dir = $request->variable('sort_dir', '');
			// and start with the first page
			$start = 0;
		}
		else
		{
			$start = $request->variable('start', 0);
		}

		if ($request->is_set_post('rem_marked'))
		{
			$marked = $request->variable('mark_remind', array(0));
			if (sizeof($marked) > 0)
			{
				$common->remind_users($marked);
				trigger_error($language->lang('USER_REMINDED', sizeof($marked)) . adm_back_link($this->u_action), E_USER_NOTICE);
			}
			else
			{
				trigger_error($language->lang('NO_USER_SELECTED') . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		if ($request->is_set_post('delmarked'))
		{
			$marked = $request->variable('mark_delete', array(0));
			if (sizeof($marked) > 0)
			{
				if (confirm_box(true))
				{
					$common->delete_users($marked);
					trigger_error($language->lang('USER_DELETED', sizeof($marked)) . adm_back_link($this->u_action), E_USER_NOTICE);
				}
				else
				{
					confirm_box(false, '<p>'.$language->lang('CONFIRM_USER_DELETE', sizeof($marked)).'</p>', build_hidden_fields(array(
						'delmarked'		=> $deletemark,
						'mark_delete'	=> $marked,
						'sk'			=> $sort_key,
						'sd'			=> $sort_dir,
						'i'				=> $id,
						'mode'			=> $mode,
						'action'		=> $this->u_action,
					)));
				}
			}
			else
			{
				trigger_error($language->lang('NO_USER_SELECTED') . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		// ignore anonymous (=== guest), bots, inactive and deactivated users
		// ignore users who have never posted anything (they are dealt with in the "zeroposter" tab)
		$query = 'SELECT user_id, user_regdate, username, user_posts, mot_last_login, user_colour, mot_reminded_one, mot_reminded_two ' .
				'FROM  ' . USERS_TABLE . '
				WHERE (user_type = ' . USER_NORMAL . ' OR user_type = ' . USER_FOUNDER . ')
				AND user_posts > 0
				AND mot_last_login <= ' . (int) $day_limit;					// get all users who have been inactive for at least the number of days specified in settings

		if ($config['mot_ur_protected_members'] <> '')						// prevent sql errors due to empty string
		{
			$query .= ' AND user_id NOT IN (' . $config['mot_ur_protected_members'] . ')';
		}
		$query .= ' ORDER BY ' . $sort_key . ' ' . $sort_dir;

		$result = $db->sql_query($query);
		$reminders = $db->sql_fetchrowset($result);
		$count_reminders = sizeof($reminders);
		$db->sql_freeresult($result);
		foreach ($reminders as &$row)			// those variables need to be set here because otherwise it would depend on the values of users shown on the current pagination page
		{
			if ($row['mot_reminded_one'] > 0)
			{
				$enable_sort_one = 1;
			}
			if ($row['mot_reminded_two'] > 0)
			{
				$enable_sort_two = 1;
			}
		}

		$result = $db->sql_query_limit( $query, $limit, $start );
		$reminders = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		//base url for pagination, filtering and sorting
		$base_url = $this->u_action
									. "&amp;sort_key=" . $sort_key
									. "&amp;sort_dir=" . $sort_dir;

		// Load pagination
		$pagination = $phpbb_container->get('pagination');
		$start = $pagination->validate_start($start, $limit, $count_reminders);
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $count_reminders, $limit, $start);

		// write data into zeroposter array (output by template)
		$enable_remind = $delete_enabled = 0;
		foreach ($reminders as &$row)
		{
			$no_offline_days = (int) (($now - $row['mot_last_login']) / $secs_per_day);
			$date_reminder_one = ($row['mot_reminded_one'] > 0) ? $user->format_date($row['mot_reminded_one']) : '-';
			$reminder_one_ago = ($row['mot_reminded_one'] > 0) ? (int) (($now - $row['mot_reminded_one']) / $secs_per_day) : '-';
			$reminder_enabled = (($row['mot_reminded_one'] == 0) || (($row['mot_reminded_two'] == 0) && ($reminder_one_ago >= $config['mot_ur_days_reminded']))) ? '1' : '0';
			$date_reminder_two = ($row['mot_reminded_two'] > 0) ? $user->format_date($row['mot_reminded_two']) : '-';
			$reminder_two_ago = ($row['mot_reminded_two'] > 0) ? (int) (($now - $row['mot_reminded_two']) / $secs_per_day) : '-';
			$enable_delete = ($reminder_two_ago >= $config['mot_ur_days_until_deleted']) ? '1' : '0';
			if ($reminder_enabled > 0)
			{
				$enable_remind = 1;
			}
			if ($enable_delete > 0)
			{
				$delete_enabled = 1;
			}

			$template->assign_block_vars('reminders', array(
				'USERNAME'			=> $row['username'],
				'USER_COLOUR'		=> $row['user_colour'],
				'JOINED'			=> $user->format_date($row['user_regdate']),
				'USER_POSTS'		=> $row['user_posts'],
				'LAST_VISIT'		=> $user->format_date($row['mot_last_login']),
				'OFFLINE_DAYS'		=> $no_offline_days,
				'REMINDER_ONE'		=> $date_reminder_one,
				'ONE_AGO'			=> $reminder_one_ago,
				'REMINDER_ENABLED'	=> $reminder_enabled,
				'REMINDER_TWO'		=> $date_reminder_two,
				'TWO_AGO'			=> $reminder_two_ago,
				'DEL_ENABLED'		=> $enable_delete,
				'USER_ID'			=> $row['user_id'],
			));
		}

		$template->assign_vars(array(
			'SERVER_CONFIG'	=> $server_config,
			'SORT_KEY'		=> $sort_key,
			'SORT_DIR'		=> $sort_dir,
			'SORT_ONE_ABLE'	=> $enable_sort_one,
			'SORT_TWO_ABLE'	=> $enable_sort_two,
			'ENABLE_REMIND'	=> $enable_remind,
			'ENABLE_DELETE'	=> $delete_enabled,
			)
		);

	}
}
