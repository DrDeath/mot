<?php

/**
*
* @package UserReminder v0.5.x
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
* Fixed a problem with the date format while automatic reminder mails are enabled in lines 127 and 184 (v0.3.0)
*/

namespace mot\userreminder;

use \phpbb\language\language;
use \phpbb\language\language_file_loader;

class common
{

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log $log */
	protected $log;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string PHP extension */
	protected $phpEx;

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db,
								\phpbb\user $user, \phpbb\log\log $log, $root_path, $phpEx)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->log = $log;
		$this->root_path = $root_path;
		$this->phpEx = $phpEx;
	}

	/**
	* Delete users
	* @param	array	$users_marked	Users selected for deletion identified by their user_id
	**/
	public function delete_users($users_marked)
	{
		// first include the user functions (one of which is "user_delete")
		include_once($this->root_path . 'includes/functions_user.' . $this->phpEx);

		if (sizeof($users_marked) > 0)					// lets check for an empty array; just to be certain that none of the called functions throws an error or an exception
		{
			// now we translate the given array of user_id's into an array of usernames for logging purposes
			$username_ary = array();
			user_get_id_name($users_marked, $username_ary);

			// now we have one array with the user_id's and another with the respective usernames: with the first one we delete the users and with the second we log this action in the admin log
			user_delete('retain', $users_marked);
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_USER_DELETED', false, array(implode(', ', $username_ary)));
		}

	}



	/**
	* Remind users
	* @param	array	$users_marked	Users selected for reminding identified by their user_id
	**/
	public function remind_users($users_marked)
	{
		global $phpbb_root_path;
		include_once($phpbb_root_path . 'includes/functions_messenger.' . $this->phpEx);
		$messenger = new \messenger(false);

		if (sizeof($users_marked) > 0)					// lets check for an empty array; just to be certain that none of the called functions throws an error or an exception
		{
			/**
			*	There is only one select box to select users for reminding so we have to discern here what users are supposed to get the first and the second reminder mail.
			*	This is done by firstly getting those users where the date of the first mail is greater than Zero (which means they have already received the first mail and are due for the second one)
			*	and secondly those users who have a value of Zero (which means they have not been reminded yet) .
			*	This sequence is necessary due to the fact that we set this date in the DB while sending the first mail and thus we would be sending both mails if we did it the other way round.
			*/
			$secs_per_day = 86400;
			$now = time();
			$reminder1 = $now - ($secs_per_day * $this->config['mot_ur_days_reminded']);
			// since we only have an array of user ids we need to get all the other user data from the DB and we start to select the users supposed to get the second reminder mail
			// get only users we have selected before
			// and who have been reminded once before
			$query = 'SELECT user_id, username, user_email, user_lang, user_timezone, user_dateformat, user_jabber, user_notify_type, mot_reminded_one
					FROM  ' . USERS_TABLE . '
					WHERE user_id IN (' . implode(', ', $users_marked) . ')
					AND (mot_reminded_one > 0 AND mot_reminded_one <= ' .	$reminder1 . ')
					AND mot_reminded_two = 0
					ORDER BY user_id';

			$result = $this->db->sql_query($query);
			$second_reminders = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			if (sizeof($second_reminders) > 0)				// to prevent error messages if there are no results (in auto_reminder mode)
			{
				$second_reminders_ary = array();
				$username_ary = array();

				foreach ($second_reminders as $row)
				{
					$second_reminders_ary[] = $row['user_id'];
					$username_ary[] = $row['username'];
					$mail_template_path = $phpbb_root_path . 'ext/mot/userreminder/language/' . $row['user_lang'] . '/email/';
					$messenger->template('reminder_two', $row['user_lang'], $mail_template_path);
					$messenger->set_addresses($row);

					if ($this->config['mot_ur_email_bcc'] != '')
					{
						$messenger->bcc($this->config['mot_ur_email_bcc']);
					}

					if ($this->config['mot_ur_email_cc'] != '')
					{
						$messenger->bcc($this->config['mot_ur_email_cc']);
					}

					$messenger->anti_abuse_headers($this->config, $this->user);

					$messenger->assign_vars(array(
						'USERNAME'			=> htmlspecialchars_decode($row['username']),
						'LAST_REMIND'		=> $this->format_date_time($row['user_lang'], $row['user_timezone'], $row['user_dateformat'], $row['mot_reminded_one']),
						'DAYS_INACTIVE'		=> $this->config['mot_ur_inactive_days'],
						'FORGOT_PASS'		=> $this->config['server_protocol'].$this->config['server_name']."/ucp.".$this->phpEx."?mode=sendpassword",
						'ADMIN_MAIL'		=> $this->config['board_contact'],
						'DAYS_TIL_DELETE'	=> $this->config['mot_ur_days_until_deleted'],
					));

					$messenger->send($row['user_notify_type']);
				}

				// all mails have been sent, let's set the reminder time
				$sql_ary = array(
					'mot_reminded_two'	=>	$now,
				);

				$query = 'UPDATE ' . USERS_TABLE . '
								SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) .'
								WHERE user_id IN (' . implode(", ", $second_reminders_ary) . ')';

				$result = $this->db->sql_query($query);

				// emails are sent, time is set in the DB, so we can log this action in the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_INACTIVE_REMIND_TWO', false, array(implode(', ', $username_ary)));
				//$this->config_text->set('mot_ur_mail2', implode(', ', $username_ary));
			}

			//--------------------------------------------------------------------------------------
			// and now we start to select the users supposed to get the first reminder mail
			$day_limit = $now - ($secs_per_day * $this->config['mot_ur_inactive_days']);
			$query = 'SELECT user_id, username, user_email, mot_last_login, user_lang, user_timezone, user_dateformat, user_jabber, user_notify_type
					FROM  ' . USERS_TABLE . '
					WHERE user_id IN (' . implode(', ', $users_marked) . ')
					AND mot_last_login <= ' . $day_limit . '
					AND mot_reminded_one = 0
					ORDER BY user_id';

			$result = $this->db->sql_query($query);
			$first_reminders = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			if (sizeof($first_reminders) > 0)				// to prevent error messages if there are no results (in auto_reminder mode)
			{
				$first_reminders_ary = array();
				$first_username_ary = array();
				foreach ($first_reminders as $row)
				{
					$first_reminders_ary[] = $row['user_id'];
					$first_username_ary[] = $row['username'];
					$mail_template_path = $phpbb_root_path . 'ext/mot/userreminder/language/' . $row['user_lang'] . '/email/';
					$messenger->template('reminder_one', $row['user_lang'], $mail_template_path);
					$messenger->set_addresses($row);
					if ($this->config['mot_ur_email_bcc'] != '')
					{
						$messenger->bcc($this->config['mot_ur_email_bcc']);
					}
					if ($this->config['mot_ur_email_cc'] != '')
					{
						$messenger->bcc($this->config['mot_ur_email_cc']);
					}
					$messenger->anti_abuse_headers($this->config, $this->user);

					$messenger->assign_vars(array(
						'USERNAME'		=> htmlspecialchars_decode($row['username']),
						'LAST_VISIT'	=> $this->format_date_time($row['user_lang'], $row['user_timezone'], $row['user_dateformat'], $row['mot_last_login']),
						'FORGOT_PASS'	=> $this->config['server_protocol'].$this->config['server_name']."/ucp.".$this->phpEx."?mode=sendpassword",
						'ADMIN_MAIL'	=> $this->config['board_contact'],
					));

					$messenger->send($row['user_notify_type']);
				}

				// all mails have been sent, let's set the reminder time(s)
				$query = 'UPDATE ' . USERS_TABLE . ' SET mot_reminded_one = ' . $now;

				if ($this->config['mot_ur_days_reminded'] == 0)		// if the admin selected to have only one reminder by setting this time frame to Zero ...
				{
					$query .= ', mot_reminded_two = ' . $now;		// ... we have to set this column too to enable deletion
				}

				$query .= ' WHERE user_id IN (' . implode(', ', $first_reminders_ary) . ')';
				$result = $this->db->sql_query($query);

				// emails are sent, time is set in the DB, so we can log this action in the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_INACTIVE_REMIND_ONE', false, array(implode(', ', $first_username_ary)));
			}
		}
	}

/* ------------------------------------------------------------------------------------------------------------------------------------------------ */

	/*
	* @param string	$user_lang			addressed user's language
	* @param string	$user_timezone		addressed user's time zone
	* @param string	$user_dateformat		addressed user's date/time format
	* @param int	$user_timestamp		addressed user's php timestamp (registration date, last login, reminder mails as UNIX timestamp from users table)
	*
	* @return string	the timestamp in user's choosen date/time format and time zone as DateTime string
	*/
	private function format_date_time($user_lang, $user_timezone, $user_dateformat, $user_timestamp)
	{
		$default_tz = date_default_timezone_get();
		$date = new \DateTime('now', new \DateTimeZone($default_tz));
		$date->setTimestamp($user_timestamp);
		$date->setTimezone(new \DateTimeZone($user_timezone));
		$time = $date->format($user_dateformat);

		// Instantiate a new language class (with its own loader), set the user's chosen language and translate the date/time string
		$lang = new language(new language_file_loader($this->root_path, $this->phpEx));
		$lang->set_user_language($user_lang);

		// Find all words in date/time string and replace them with the translations from user's language
		preg_match_all("/[a-zA-Z]+/", $time, $matches, PREG_PATTERN_ORDER);
		if (sizeof ($matches[0]) > 0)
		{
			foreach ($matches[0] as $value)
			{
				$time = preg_replace("/".$value."/", $lang->lang(array('datetime', $value)), $time);
			}
		}

		// return the formatted and translated time in users timezone
		return $time;
	}

}
