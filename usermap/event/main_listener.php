<?php
/**
*
* @package Usermap v0.5.x
* @copyright (c) 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\usermap\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{

	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_link',
			'core.user_active_flip_after'			=> 'user_active_flip_after',			// Perform additional actions after the users have been activated/deactivated
			'core.delete_user_after'				=> 'delete_user_after',					// Event after a user is deleted
			'core.ucp_profile_info_modify_sql_ary'	=> 'ucp_profile_info_modify_sql_ary',	// Modify profile data in UCP before submitting to the database
			'core.acp_users_profile_modify_sql_ary'	=> 'acp_modify_users_profile',			// Modify profile data in ACP before submitting to the database
			'core.group_add_user_after'				=> 'set_user_colour',					// Event after users are added to a group
			'core.user_set_default_group'			=> 'change_user_colour',				// Event when the default group is set for an array of users
			'core.ucp_register_register_after'		=> 'ucp_register_register_after',		// Event after registration, used to process user data for the Usermap if no activation after registration is needed
		);
	}

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/* @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;

	/** @var string PHP extension */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper $helper   Controller helper object
	 * @param \phpbb\template\template $template Template object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\controller\helper $helper,
								\phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\user $user,
								\phpbb\extension\manager $phpbb_extension_manager, $php_ext)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->helper = $helper;
		$this->template = $template;
		$this->db = $db;
		$this->user = $user;
		$this->phpbb_extension_manager 	= $phpbb_extension_manager;
		$this->php_ext = $php_ext;

		$this->ext_path = $this->phpbb_extension_manager->get_extension_path('mot/usermap', true);
		include_once($this->ext_path . 'includes/um_constants.' . $this->php_ext);
		$this->doubles_ary = array();
		$this->radius = 0.002;
		$this->u_action = '';
	}

	/**
	 * Load language files
	 *
	 * @param \phpbb\event\data $event
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'mot/usermap',
			'lang_set' => 'mot_usermap',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add a page header nav bar link
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function add_page_header_link()
	{
		$this->template->assign_vars(array(
			'U_USERMAP' => $this->helper->route('mot_usermap_route', array()),
		));
	}

	/*
	* Called after a user has finished registration. Three possible scenarios:
	* 1. w/o activation: Data for the Usermap must be obtained here
	* 2. and 3.: Activation by eMail confirmation or ba an administrator: Data for the Usermap will be processed within the function 'user_active_flip_after'
	* -> selection for registration w/o later activation must be done here!
	*
	* @param:	cp_data, data, message, server_url, user_actkey, user_id, user_row
	*/
	function ucp_register_register_after($event)
	{
		$cp_data = $event['cp_data'];
		$user_row = $event['user_row'];
		if ($user_row['user_actkey'] == '' && $user_row['user_inactive_reason'] == 0 && $user_row['user_inactive_time'] == 0)	// conditions if user registration w/o activation
		{
			$j = 0;		// start with first geonames user
			// get the country code array
			$land = json_decode($this->config_text->get('mot_usermap_countrycodes'), false);
			$land_size = sizeof($land);
			$this->gn_username = explode(",", $this->config['mot_usermap_geonamesuser']);	// get Geonames user(s) from config and make it an array
			$message = $this->user->lang['MOT_UCP_GEONAMES_ERROR'] . '<br /><br />' . sprintf($this->user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
			if ($this->gn_username[0] == '')
			{
				trigger_error($message, E_USER_ERROR);
				return;
			}
			// get some data not supplied by this functions event variable
			$query = 'SELECT user_id, username, user_colour
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . (int) $event['user_id'];
			$result = $this->db->sql_query($query);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$cp_data['user_id'] = $row['user_id'];
			$cp_data['username'] = $row['username'];
			$cp_data['user_colour'] = $row['user_colour'];
			// check if location was provided during registration
			if (!array_key_exists('pf_phpbb_location', $cp_data))
			{
				$cp_data['pf_phpbb_location'] = '';	// if not set it to empty string
			}
			if ($cp_data['pf_mot_zip'] <> '' and $cp_data['pf_mot_land'] > 1 and $cp_data['pf_mot_land'] < $land_size)	// check whether the profile fields data is correctly set
			{
				$this->add_user($cp_data, $j, $land);
			}
		}
	}

	/**
	* Called after a user got activated/deactivated; check for country and zip code profile fields to add them to usermap_users table and doubles array
	*
	* @params:	activated, deactivated, mode, reason, sql_statements, user_id_ary
	*/
	public function user_active_flip_after($event)
	{
		$user_id_ary = $event['user_id_ary'];
		// check if user(s) got activated
		if ($event['activated'] == 1)
		{
			$j = 0;		// start with first geonames user
			// get the country code array
			$land = json_decode($this->config_text->get('mot_usermap_countrycodes'), false);
			$land_size = sizeof($land);
			$this->gn_username = explode(",", $this->config['mot_usermap_geonamesuser']);	// get Geonames user(s) from config and make it an array
			// set error message according to activation mode
			if ($event['mode'] == 'activate')	// activation by email
			{
				$message = $this->user->lang['MOT_UCP_GEONAMES_ERROR'] . '<br /><br />' . sprintf($this->user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
			}
			if ($event['mode'] == 'flip')	// activation by administrator
			{
				$message = $this->user->lang['ACP_USERMAP_PROFILE_ERROR'] . adm_back_link($this->u_action);
			}
			if ($this->gn_username[0] == '')
			{
				trigger_error($message, E_USER_WARNING);
				return;
			}
			// check if user(s) filled in the profile fields land, plz and location
			foreach ($user_id_ary as &$value)		/* Ist die Parameterübergabe per Referenz so noch gültig??? (&$value) !!!!!!  */
			{
				$query = 'SELECT u.user_id, u.username, u.user_colour, pf.pf_phpbb_location, pf.pf_mot_zip, pf.pf_mot_land
						FROM ' . PROFILE_FIELDS_DATA_TABLE . ' AS pf
						INNER JOIN ' . USERS_TABLE . ' AS u
						ON pf.user_id = u.user_id
						WHERE u.user_id = ' . (int) $value;
				$result = $this->db->sql_query($query);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
				if ($row['pf_mot_zip'] <> '' and $row['pf_mot_land'] > 1 and $row['pf_mot_land'] < $land_size)	// check whether the profile fields data is correctly set
				{
					$this->add_user($row, $j, $land);
				}
			}
		}

		// check if user(s) got deactivated
		if ($event['deactivated'] == 1)
		{
			$cc = $zc = '';
			// if user(s) got deactivated we need to delete them from usermap_users table and from the doubles array
			$this->doubles_ary = json_decode($this->config_text->get('mot_usermap_doublesarray'), true);
			foreach ($user_id_ary as &$value)
			{
				if ($this->check_user_id ($this->doubles_ary, $value, $cc, $zc))	// prevent php warnings and errors
				{
					$this->delete_user($value);
				}
			}
		}
	}

	/**
	* Delete a user from usermap_users table after he was deleted from the users table
	*
	* @params:	mode, retain_username, user_ids, user_rows
	*/
	public function delete_user_after($event)
	{
		$cc = $zc = '';
		$this->doubles_ary = json_decode($this->config_text->get('mot_usermap_doublesarray'), true);
		// get the user_id's stored in an indexed array
		$user_id_ary = $event['user_ids'];
		// if user(s) got deleted we need to delete them from table _usermap_users and from the $doubles array
		foreach ($user_id_ary as &$value)
		{
			if ($this->check_user_id ($this->doubles_ary, $value, $cc, $zc))	// prevent php warnings and errors
			{
				$this->delete_user($value);
			}
		}
	}

	/**
	* Update data in usermap_users table and doubles array after user edited data in the UCP profile fields
	* cp_data is not yet submitted to the database, thus we can check against the database whether country, zip code or location have been edited
	*
	* @params:	cp_data, data, sql_ary
	*		'cp_data' holds the custom profile fields as associative array
	*			Array ( [pf_phpbb_location] => value ... pf_mot_zip] => value [pf_mot_land] => value ... )
	*		'data': Array ( [jabber] =>  [bday_year] => y [bday_month] => m [bday_day] => d [user_birthday] => dd-mm-yy [notify] => 0/1 )
	*		'sql_ary': Array ( [user_jabber] =>  [user_notify_type] => 0 [user_birthday] => d-m-y )
	*
	* The user is identified by $this->user
	*/
	public function ucp_profile_info_modify_sql_ary($event)
	{
		$message = $this->user->lang['MOT_UCP_GEONAMES_ERROR'] . '<br /><br />' . sprintf($this->user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
		$this->process_user_profile_data($this->user->data['user_id'], $event['cp_data'], $message, E_USER_ERROR);
	}


	/**
	* Update data in usermap_users table and doubles array after admin edited user profile data in the ACP (only called from 'Profile' tab when 'Submit' button is hit)
	* cp_data is not yet submitted to the database, thus we can check against the database whether country, zip code or location have been edited
	*
	* @params:	cp_data, data, sql_ary, user_id, user_row
	*		'cp_data' holds the custom profile fields as associative array (for details see function above)
	*		'data': Array ( [jabber] =>  [bday_year] => y [bday_month] => m [bday_day] => d [user_birthday] => dd-mm-yy)
	*		'sql_ary': Array ( [user_jabber] =>  [user_birthday] => d-m-y )
	*		'user_id': user_id of the user whose profile is currently edited
	*		'user_row': Array with user data from users table plus session data
	*
	*/
	public function acp_modify_users_profile($event)
	{
		$message = $this->user->lang['ACP_USERMAP_PROFILE_ERROR'] . adm_back_link($this->u_action);
		$this->process_user_profile_data($event['user_id'], $event['cp_data'], $message, E_USER_WARNING);
	}


	/**
	* Gets the group id of a group a user was manually added to, if the user is only in this group we assume that this user was dropped from the "newly registered members" group
	* as his only group and now we have to adjust the user colour in the usermap_users table
	*
	* @params:	group_id, group_name, pending, user_id_ary, username_ary
	*		'group_id' holds the group id, eg. '2' for 'registered users'
	*		'group_name' holds the group name, e. 'registered users'
	*		'pending' holds a 'Zero' value
	*		'user_id_ary' holds an array with the user id(s) of the selected user(s)
	*		'username_ary' holds an array with the user name(s) of the selected user(s)
	*
	*/
	public function set_user_colour($event)
	{
		$user_ary = $event['user_id_ary'];
		$group_id = $event['group_id'];
		foreach ($user_ary as $value)
		{
			$query = "SELECT * FROM " . USER_GROUP_TABLE . " WHERE user_id = " . (int) $value;
			$result = $this->db->sql_query($query);
			$groups = $this->db->sql_fetchrowset($result);
			$count_groups = sizeof($groups);
			$this->db->sql_freeresult($result);
			if ($count_groups == 1)
			{
				$query = "SELECT group_colour FROM " . GROUPS_TABLE . " WHERE group_id = " . (int) $group_id;
				$result = $this->db->sql_query($query);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
				$group_colour = $row['group_colour'];
				$query = "UPDATE " . USERMAP_USERS_TABLE . "
						 SET user_colour = '" . $group_colour . "'
						 WHERE user_id = " . (int) $value;
				/*$result = */$this->db->sql_query($query);
			}
		}
	}

	/**
	* Change the user colour in the usermap_users table when an admin changes the default (main) group of a user
	*
	* @params:	group_attributes, group_id, sql_ary, update_listing, user_id_ary
	*		'group_attributes' holds the attributes of the selected group
	*			Array ( group_colour ,group_rank ,group_avatar ,group_avatar_type ,group_avatar_width ,group_avatar_height )
	*		'group_id' holds the id of the selected group
	*		'sql_ary' holds the following data
	*			Array ( group_id ,user_colour )
	*		'update_listing' is empty
	*		'user_id_ary'
	*
	*/
	public function change_user_colour($event)
	{
		$user_ary = $event['user_id_ary'];
		$user_colour = $event['sql_ary']['user_colour'];
		foreach ($user_ary as $value)
		{
			if ($user_colour != '')
			{
				$query = "UPDATE " . USERMAP_USERS_TABLE . "
						 SET user_colour = '" . $user_colour . "'
						 WHERE user_id = " . (int) $value;
				/*$result = */$this->db->sql_query($query);
			}
		}
	}

/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

	/**
	* Function to process the data from a changed user profile. Since this task has to be done from the UCP (the user changed it)
	* as well as from the ACP (the admin changed it) and this are two different events, this task was put in a function to be called from both
	*
	* @params:	user_id: id of the user whose data has to be processed
	*		cp_data: array with the profile_fields_data prior to its submision to the database
	*
	* @return:	none
	*/
	function process_user_profile_data($user_id, $cp_data, $error_msg, $error_type)
	{
		$this->gn_username = explode(",", $this->config['mot_usermap_geonamesuser']);	// get Geonames user(s) from config and make it an array
		if ($this->gn_username[0] == '')
		{
			trigger_error($error_msg, $error_type);
			return;
		}
		$cc = $zc = '';
		$j = 0;	// start with first entry in the geonames user array

		$this->doubles_ary = json_decode($this->config_text->get('mot_usermap_doublesarray'), true);
		/* First we check whether this user is already in the doubles array (and thus in the usermap_users table as well) , if yes we use brute force and delete this user from both
		** and afterwards generate a new entry if we get a valid response from geonames.org (then we certainly don't have any corpse in both in case we don't get a valid coordinate from GeoNames
		*/
		if ($this->check_user_id ($this->doubles_ary, $user_id, $cc, $zc))
		{
			// user exists, delete from usermap_users table and doubles array
			$this->delete_user($user_id);
		}
		// user has been deleted from usermap_users table and doubles array, now we can check the new values (country, zip code, location) with geonames database
		// (and even if user wasn't listed in the usermap_users table and doubles array we have to assume the correct data was entered and must be processed here)
		$land = json_decode($this->config_text->get('mot_usermap_countrycodes'), false);
		$land_size = sizeof($land);

		// get the user's data from the users and profile_fields_data tables
		$query = 'SELECT u.user_id, u.username, u.user_colour, pf.pf_phpbb_location, pf.pf_mot_zip, pf.pf_mot_land
				FROM ' . PROFILE_FIELDS_DATA_TABLE . ' AS pf
				INNER JOIN ' . USERS_TABLE . ' AS u
				ON pf.user_id = u.user_id
				WHERE u.user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($query);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		// since at this time the changed values are not written in the users and profile_fields_data tables we have to set some values with those of the data from the event array
		$row['pf_mot_zip'] = $cp_data['pf_mot_zip'];
		$row['pf_mot_land'] = $cp_data['pf_mot_land'];
		$row['pf_phpbb_location'] = $cp_data['pf_phpbb_location'];
		// and now we can do the necessary checks
		if ($row['pf_mot_zip'] <> '' and $row['pf_mot_land'] > 1 and $row['pf_mot_land'] < $land_size)	// check whether the profile fields data is correctly set
		{
			$this->add_user($row, $j, $land);
		}
	}

	/**
	* Add a user to the doubles array and to the usermap_users table
	*
	* @param:	userrow: array with data from the users and profile_fields_data tables
	*		j: integer value which points to a user in the geonames users array, given as reference so in a later version we can use it for error handling (if necessary)
	*		land: array with the country codes
	*
	* @return: none
	*/
	function add_user($userrow, &$j, $land)
	{
		$lat = $lng = 0.0;
		// okay, for this user id we have a valid set of pf_phpbb_location, pf_mot_zip and pf_mot_land -> proceed with request to geonames.org
		if ($this->get_coords($this->gn_username, $j, $userrow['pf_mot_zip'], $land[$userrow['pf_mot_land']], $userrow['pf_phpbb_location'], $lat, $lng))
		{
			// get doubles array from config_text table, update it and save it back
			$doubles = json_decode($this->config_text->get('mot_usermap_doublesarray'), true);
			$factor = $this->build_doubles($doubles, $userrow['user_id'], $land[$userrow['pf_mot_land']], $userrow['pf_mot_zip']);
			$this->config_text->set('mot_usermap_doublesarray', json_encode($doubles));

			if ($factor > 0)
			{
				// calculate the offset angle, the number of the circle we are filling and - if appropriate - the additions to latitude and longitude to discriminate the new marker from any others with this zip code
				$angle = $factor * 30;
				$circle = (int) (($angle / 361) + 1);
				$lat = $lat + ($this->radius * $circle * cos(deg2rad($angle)));
				$lng = $lng + ($this->radius * $circle * sin(deg2rad($angle)));
			}

			// and now we can finally add this user to the usermap_users table
			$location = $this->db->sql_escape($userrow['pf_phpbb_location']);
			$userrow['user_colour'] = ($userrow['user_colour'] == '') ? '000000' : $userrow['user_colour'];
			$query = "INSERT INTO " . USERMAP_USERS_TABLE . " (user_id, username, user_colour, user_lat,
					user_lng, user_land, user_plz, user_location, user_change_plz, user_change_coord)
					VALUES (".$userrow['user_id'].",'".$userrow['username']."','".$userrow['user_colour']."','".$lat."','".
					$lng."','".$land[$userrow['pf_mot_land']]."','".$userrow['pf_mot_zip']."','".$location."',0,0)";
			$this->db->sql_query($query);
		}
	}

	/**
	* Delete a user from the doubles array and from the usermap_users table
	*
	* @param: user2delete: user_id of the user to be deleted
	*
	* @return: none
	*/
	function delete_user($user2delete)
	{
		// check if user_id is in usermap_users table and get country and zip code
		$query = "SELECT * FROM " . USERMAP_USERS_TABLE . " WHERE user_id = " . (int) $user2delete;
		$result = $this->db->sql_query($query);
		$row = $this->db->sql_fetchrow($result);	// $row['user_land'] and $row['user_plz'] hold the country code and respectively the zip code
		// remove this user_id from the doubles array (which holds all user_id's for a specific zipcode)
		$doubles = json_decode($this->config_text->get('mot_usermap_doublesarray'), true);
		$this->remove_doubles_value($doubles, $user2delete, $row['user_land'], $row['user_plz']);
		// save array again in config_text table
		$this->config_text->set('mot_usermap_doublesarray', json_encode($doubles));
		// and remove this user from usermap_users table
		$query = "DELETE FROM " . USERMAP_USERS_TABLE . " WHERE user_id = " . (int) $user2delete;
		$result = $this->db->sql_query($query);
	}

	/**
	* Check given location (city), zip code and country against the geonames data base and hopefully get back some coordinates to display on the map
	*
	* @params	gn_username: array of geonames user names
	*		j: an integer pointer into the gn_username array (as reference for a possible later error handling)
	*		postal_code: a string with the zip code
	*		country: a string with the an uppercase two letter denomination, e.g. DE for Germany
	*		city: a string with the location name
	*		gn_lat, gn_lng: a floating point value as reference, is set if return value is true
	*
	* @return  true if geonames.org gives a valid solution -> lat and lng are set, otherwise false, lat and lng do not contain a valid value
	*/
	function get_coords($gn_username, &$j, $postal_code, $country, $city, &$gn_lat, &$gn_lng)
	{
		if ($gn_username[$j] == '')
		{
			return false;
		}
		$xml = array();
		// retrieve only letters from the city name
		$city = preg_replace('/[^A-Za-zaäöüßÄÖÜôáàâé]+/', '', $city);
		// call to geonames.org
		$json_request ="http://api.geonames.org/postalCodeSearchJSON?username=".$gn_username[$j]."&style=short&postalcode=".$postal_code."&country=".$country;
		$json=file_get_contents($json_request);
		$xml = json_decode($json, true);

		// geonames api returns either a 'status' array with an error code or a 'postal Codes' array with at least one valid solution or an empty array if no match was found
		// if there is a status we've gotten an error and must deal with it
		if (array_key_exists('status', $xml))
		{
			$xml_array = $xml['status'];
			switch ($xml_array['value'])
			{
				case 18:								// daily limit of credits exceeded
				case 19:								// hourly limit of credits exceeded
				case 20:								// weekly limit of credits exceeded
					$j++;
					if ($j <= sizeof($gn_username))	// try with another username for geonames.org if one is available?
					{
						$json_request ="http://api.geonames.org/postalCodeSearchJSON?username=".$gn_username[$j]."&style=short&postalcode=".$postal_code."&country=".$country;
						$json=file_get_contents($json_request);
						$xml = json_decode($json, true);
					}
					else								// another username is not available, save variables for cron job or notify user
					{
						return false;
					}
				break;

				default:
					// log it for later investigation
					$handle = fopen ($this->ext_path . 'json_error.log', 'a');
					fwrite ($handle, $json);
					fclose ($handle);
				break;
			}
		}

		if (array_key_exists('postalCodes', $xml))
		{
			$xml_array = $xml['postalCodes'];
			$ary_size = sizeof($xml_array);
			switch ($ary_size)
			{
				case 0:
					// no solution for this country/zip code combination -> we return false
					$return = false;
				break;

				case 1:
					// there is only one solution, so thats it -> set lat and lng and a return value of true
					$solution = $xml_array[0];
					$gn_lat = $solution['lat'];			// We do have a solution so we can return the latitude
					$gn_lng = $solution['lng'];			// and longitude
					$return = true;
				break;

				default:
					// there is more than one solution so we have to check the placeName of each of them against our given city name
					for ($i = 0; $i < $ary_size; $i++)
					{
						$solution = $xml_array[$i];
						$temp_city = preg_replace('/[^A-Za-zaäöüßÄÖÜôáàâé]+/', '', $solution['placeName']);
						// if both names are equal we have found our solution -> set lat and lng
						if ($temp_city == $city)
						{
							$city_name = $solution['placeName'];
							$gn_lat = $solution['lat'];			// We do have a solution so we can return the latitude
							$gn_lng = $solution['lng'];			// and longitude
						}
					}
					// no match found so we fall back to the first solution -> set lat and lng
					if ($city_name == '' )	// no match found
					{
						$solution = $xml_array[0];
						$gn_lat = $solution['lat'];			// We do have a solution so we can return the latitude
						$gn_lng = $solution['lng'];			// and longitude
					}
					// we've found a valid solution so we can set the return value to true
					$return = true;
				break;
			}
		}
		return $return;
	}

	/**
	* Add a user id to the doubles array which holds the country codes and within the country code the zip codes and there all the users living there
	* The purpose of this array is to get an integer value to compute the offset for latitude and longitude to discriminate several users within the same location on the map
	*
	* @params:	doubles: multidimensional array with all country codes and within those a set of zip codes and within each zip code the user_ids of users with that zip code
	*			(given as reference because it gets altered)
	*		user:id: integer with the user's id
	*		country: two letter string with the international country code (e.g. DE for Germany)
	*		zip_code: string with user's zip code
	*
	* @return:	Return value is an integer between Zero and Infinite (well, not really, since not all mankind resides in the same town) to compute the offset from the original lat and lng
	*		of the location identified by country and zip code (0 means no offset, map marker will be displayed in the center)
	*/
	function build_doubles(&$doubles, $user_id, $country, $zip_code)
	{
		if (array_key_exists($country, $doubles))						// do we already have this country code?
		{
			if (array_key_exists($zip_code, $doubles[$country]))		// yes, country code already exists, now we check for existence of the zip code
			{
				// yes, country code and zip code already exist, now we have to check for empty entry (user_id equals 0)
				$i = 0;
				$size = sizeof($doubles[$country][$zip_code])-1;
				while ($i <= $size)										// for all stored user_ids:
				{
					if ($doubles[$country][$zip_code][$i] == 0)			// do we have user_id = 0 (earlier deleted user)?
					{
						$doubles[$country][$zip_code][$i] = $user_id;	// yes, overwrite it with this user
						return $i;										// and return the offset
					}
					$i++;
				}
				array_push($doubles[$country][$zip_code], $user_id);	// if we get here there was no empty slot and we have to add the user at the end
				return sizeof($doubles[$country][$zip_code])-1;			// and return the new offset
			}
			else
			{
				$doubles[$country][$zip_code] = array();				// no, zip code doesn't exist within in this country code, so we generate it . . .
				array_push($doubles[$country][$zip_code], $user_id);	// and save the user id
				return 0;												// first user with this zip code, so the offset will be zero
			}
		}
		else
		{
			$doubles[$country] = array();								// country code doesn't exist, generate it
			$doubles[$country][$zip_code] = array();					// generate the zip code as well
			array_push($doubles[$country][$zip_code], $user_id);		// and save the user id
			return 0;													// first user with this zip code, so the offset will be zero
		}
	}

	/**
	*	Remove a user_id from $doubles array (e.g. when user gets deleted or deactivated)
	*
	* @params: refer to function  build_doubles (above)
	*
	* @return: none
	*/
	function remove_doubles_value(&$doubles, $user_id, $country, $zip_code)
	{
		// does this zipcode array really exist?
		if (!array_key_exists($zip_code, $doubles[$country]))
		{
			return;		// no, it doesn't exist, there is no known user, so we leave the function
		}
		// first we check whether there is a single user for this country / zipcode pair
		if (sizeof($doubles[$country][$zip_code]) == 1)
		{
			if ($user_id <> $doubles[$country][$zip_code][0])	// is this really the user we want to remove?
			{
				return;											// no, leave the function
			}
			else
			{
				unset($doubles[$country][$zip_code]);			// YES, delete the user array for this zipcode
			}
		}
		else	// there is more than one user at this zipcode
		{
			// if all other users have been deleted earlier we can simply delete the user array for this zipcode
			$deletable = true;
			foreach ($doubles[$country][$zip_code] as $value)
			{
				if ($value <> 0 or $value <> $user_id)
				{
					$deletable = false;
				}
			}
			if ($deletable)	// $user_id is the last remaining user, delete the array
			{
				unset($doubles[$country][$zip_code]);	// delete the user array for this zipcode
			}
			else		// there are other users, so we have to set the entry to 0 which signales an empty value
			{
				$size = sizeof($doubles[$country][$zip_code]);
				$i = 0;
				foreach ($doubles[$country][$zip_code] as $value)
				{
					if ($value == $user_id)
					{
						if ($i == ($size - 1)) // if this is the last entry in the array, dump it
						{
							unset($doubles[$country][$zip_code][$i]);					// last entry, remove it ...
						$this->remove_doubles_value($doubles, 0, $country, $zip_code);	// and check, whether there are deleted entries before this one (user_id set to 0)
						}
						else
						{
							$doubles[$country][$zip_code][$i] = 0;		// not the last entry, set it to Zero (no valid user_id)
						}
					}
					$i++;
				}
			}
		}
	}

	/**
	* Checks whether a user identified by the user_id is part of the doubles array
	*
	* @params:	array2check: a doubles array to check for an instance of the -> user_id
	*		user_id: id of the user we are looking for
	*		key_cc: reference to a string which holds the country code in case of successful search
	*		key_zc: reference to a string which holds the zip code in case of successful search
	*
	* @return:	true if user is in the array (and therefore in the usermap_users table as well), false if not
	*		if true key_cc holds the country code and key_zc the zip code of this user (array2check is not changed)
	*/
	function check_user_id ($array2check, $user_id, &$key_cc, &$key_zc)
	{

/*		if (empty($array2check))
		{
			return false;
		}
*/		$val_cc = '';
		$val_zc = '';
		foreach ($array2check as $key_cc => $val_cc)
		{
			foreach ($val_cc as $key_zc => $val_zc)
			{

				$i = 0;
				foreach ((array) $array2check[$key_cc][$key_zc] as $val)
				{
					if ($val == $user_id)
					{
						return true;
					}
					$i++;
				}
			}
		}
		return false;
	}

}
