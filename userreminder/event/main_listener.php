<?php
/**
*
* @package UserReminder v0.5.0
* @copyright (c) 2019, 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\userreminder\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{

	public static function getSubscribedEvents()
	{
		return array(
			'core.session_create_after'		=> 'check_user_login',
		);
	}

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config   Config object
	 * @param \phpbb\db\driver\driver_interface $db	Database object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db)
	{
		global $phpbb_container;

		$this->config = $config;
		$this->db = $db;

		$this->common = $phpbb_container->get('mot.userreminder.common');
		$this->secs_per_day = 86400;
	}


	/**
	* Set the reminding times to Zero every time a user logs into the forum in order to delete any reminders in case this user has been reminded and logs on again.
	* In addition we set the value of "mot_last_login" to the time stamp the user logged in to make sure that there is no gap in which this "newborn" user gets reminded again.
	* If in automatic mode check for users in need of being reminded or deleted
	*
	* @param session_data
	* 	Array[session_user_id, session_start, session_last_visit, session_time, session_browser, session_forwarded_for, session_ip, session_autologin, session_admin, session_viewonline,
	*		session_page, session_forum_id, session_id]
	*
	*/
	public function check_user_login($event)
	{
		/*
		* First we set the times of first and second reminder to Zero to flag this user as active again in order to delete any reminders this user might have
		*/

		$session_data = $event['session_data'];
		if ($session_data['session_user_id'] > 1)		// avoid updating the guest account (user_id == 1 when logging off)
		{
			// we set the current time variable first
			$now = time();

			$sql_ary = array(
				'mot_reminded_one'	=> 0,
				'mot_reminded_two'	=> 0,
				'mot_last_login'	=> $now,
			);

			$query = 'UPDATE ' . USERS_TABLE . '
						SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
						WHERE user_id = ' . (int) $session_data['session_user_id'];
			$this->db->sql_query($query);

			/*
			* Now we check whether reminder mails should be sent automatically and if yes we check what users are supposed to get a reminding email
			*/
			if ($this->config['mot_ur_autoremind'] == 1)
			{
				$day_limit = $now - ($this->secs_per_day * $this->config['mot_ur_inactive_days']);

				// ignore inactive users, anonymous (=== guest) and bots
				// ignore users who have never posted anything
				$query = 'SELECT user_id ' .
						'FROM  ' . USERS_TABLE . '
						WHERE (user_type = ' . USER_NORMAL . ' OR user_type = ' . USER_FOUNDER . ')
						AND user_posts > 0
						AND (
						(mot_last_login <= ' . (int) $day_limit . ' AND mot_reminded_one = 0) ' .	// get all inactive users who have not been reminded yet
						'OR (mot_reminded_one > 0 AND mot_reminded_two = 0)) ';						// get all inactive users due for the second reminder

				if ($this->config['mot_ur_protected_members'] <> '')			// prevent sql errors due to empty string
				{
					$query .= ' AND user_id NOT IN (' . $this->config['mot_ur_protected_members'] . ') ';
				}
				$query .= 'ORDER BY user_id';

				$result = $this->db->sql_query($query);
				$reminders = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);

				$marked = array();
				foreach ($reminders as $value)
				{
					$marked[] = $value['user_id'];
				}
				$this->common->remind_users($marked);
			}

			/*
			* Now we check whether users should be deleted automatically and if yes we check what users are supposed to get deleted and do it
			*/
			if ($this->config['mot_ur_autodelete'] == 1)
			{
				$day_limit = $now - ($this->secs_per_day * $this->config['mot_ur_days_until_deleted']);

				$marked_users = array();

				// ignore users who have never posted anything (they are dealt with in the "zeroposter" tab)
				// ignore users who have never posted anything
				// get only users who have been reminded twice
				$query = 'SELECT user_id
						FROM  ' . USERS_TABLE . '
						WHERE (user_type = ' . USER_NORMAL . ' OR user_type = ' . USER_FOUNDER . ')
						AND user_posts > 0
						AND mot_reminded_two > 0
						AND mot_reminded_two <= ' . (int) $day_limit;			// get all users who have been inactive since the 2nd reminder for at least the number of days specified in settings

				if ($this->config['mot_ur_protected_members'] <> '')	// prevent sql errors due to empty string
				{
					$query .= ' AND user_id NOT IN (' . $this->config['mot_ur_protected_members'] . ') ';
				}
				$query .= 'ORDER BY user_id';

				$result = $this->db->sql_query($query);
				$user_result = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);
				foreach ($user_result as $value)
				{
					$marked_users[] = $value['user_id'];
				}
				$this->common->delete_users($marked_users);
			}
		}

	}

}
