<?php
/**
*
* @package Usermap v0.4.x
* @copyright (c) 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace mot\usermap\controller;

//use Symfony\Component\HttpFoundation\Response;

class main
{

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language $language Language object */
	protected $language;

	/* @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string PHP extension */
	protected $php_ext;

	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template,
		\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\language\language $language, \phpbb\extension\manager $phpbb_extension_manager,
		$root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->db = $db;
		$this->user = $user;
		$this->language = $language;
		$this->phpbb_extension_manager 	= $phpbb_extension_manager;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->ext_path = $this->phpbb_extension_manager->get_extension_path('mot/usermap', true);
		include_once($this->ext_path . 'includes/um_constants.' . $this->php_ext);
	}


	/**
	* {@inheritdoc}
	*/
	public function handle()
	{
		/*
		*	include the user functions file, because these functions are not accessible from $this->user
		*/
		include($this->root_path . 'includes/functions_user.' . $this->php_ext);

		page_header($this->language->lang('USERMAP'));

		/*
		*	get configuration values and send them to the javascript for initialising the map
		*/
		$usermap_config = $this->config['mot_usermap_lat']."|".$this->config['mot_usermap_lon']."|".$this->config['mot_usermap_zoom'];
		$server_config = $this->config['server_protocol'].$this->config['server_name'].$this->config['script_path'];

		// Get data of current user
		$query = 'SELECT * FROM ' . USERMAP_USERS_TABLE . ' WHERE user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query($query);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if (isset($row))
		{
			$current_user = $row['user_id']."|".$row['username']."|".$row['user_plz']."|".$row['user_lat']."|".$row['user_lng'];
			$zip_code = '"'.$row['user_land'].'-'.$row['user_plz'].'"';
			$valid_user = true;			// the current user is listed in the usermap_users table and therefore authorized to use the map search
		}
		else
		{
			$current_user = "0|''|0|0|0";
			$zip_code = 0;
			$valid_user = false;			// the current user is NOT listed in the usermap_users table and therefore NOT authorized to use the map search
		}

		$query = 'SELECT * FROM ' . USERMAP_USERS_TABLE . ' ORDER BY user_id DESC';
		$result = $this->db->sql_query($query);
		$user_data = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$map_users = sizeof($user_data);

		/*
		*	Get user groups for the map legend
		*/
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';
		$sql = 'SELECT group_id, group_name, group_colour, group_type
			FROM ' . GROUPS_TABLE . '
			WHERE group_legend > 0
			ORDER BY ' . $order_legend . ' ASC';
		$result = $this->db->sql_query($sql);

		$usergroup_legend = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang['G_' . $row['group_name']] : $row['group_name'];

			if ($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile')))
			{
				$usergroup_legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$usergroup_legend[] = '<a' . $colour_text . ' href="' . append_sid("{$this->root_path}memberlist.{$this->php_ext}", 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
			}
		}
		$this->db->sql_freeresult($result);

		$usergroup_legend = implode(', ', $usergroup_legend);

		$this->template->assign_vars(array(
			'USER'			=> json_encode($current_user),
			'AUTH_USER'		=> $valid_user,
			'MAPCONFIG'		=> json_encode($usermap_config),
			'SERVERCONFIG'	=> json_encode($server_config),
			'MAPDATA'		=> json_encode($user_data),
			'MAP_USERS'		=> $this->user->lang('MAP_USERS', (int) $map_users),
			'MAP_LEGEND'	=> $usergroup_legend,
			'MAP_SEARCH'	=> $this->user->lang('MAP_SEARCH', $zip_code),
			)
		);
		return $this->helper->render('usermap_main.html');
	}

}
