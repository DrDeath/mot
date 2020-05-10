<?php
/**
*
* @package Usermap v0.5.x
* @copyright (c) 2020 Mike-on-Tour
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
$lang = array_merge($lang, array(
	'PLURAL_RULE'					=> 1,
	// Module
	'USERMAP'						=> 'User Map',
	'USERMAP_NOT_AUTHORIZED'		=> 'You are not authorized to see the user map.',
	'USERMAP_SEARCHFORM'			=> 'Search Form',
	'USERMAP_LEGEND'				=> 'Legend',
	'USERMAP_CREDENTIALS'			=> 'Geo references used by Usermap courtesy of ',
	'USERMAP_LEGEND_TEXT'			=> 'Toggle mousewheel zoom by clicking on the map',
	'MAP_USERS'						=> array(
		1	=> 'There is currently %1$s member shown on the user map.',
		2	=> 'There are currently %1$s members shown on the user map.',
	),
	'MAP_SEARCH'					=> 'Search for members at postal (zip) code %1$s within a range of ',
	'MAP_RESULT'					=> 'shows the following result:',
	'MAP_NORESULT'					=> 'found no members within the range of ',
	// ACP
	'ACP_USERMAP'					=> 'User Map',
	'ACP_USERMAP_SETTINGS'			=> 'Settings',
	'ACP_USERMAP_SETTINGS_EXPLAIN'	=> 'This is where you customize your user map.',
	'ACP_USERMAP_SETTING_SAVED'		=> 'Settings for the user map successfully saved.',
	'ACP_USERMAP_MAPSETTING_TITLE'	=> 'Map Settings',
	'ACP_USERMAP_MAPSETTING_TEXT'	=> 'Map center and zoom at start of the user map.',
	'ACP_USERMAP_LAT'				=> 'Latitude of the map center',
	'ACP_USERMAP_LAT_EXP'			=> 'Values between 90.0 (North Pole) and -90.0 (South Pole)',
	'ACP_USERMAP_LON'				=> 'Longitude of the map center',
	'ACP_USERMAP_LON_EXP'			=> 'Values between 180.0 (East) and -180.0 (West)',
	'ACP_USERMAP_ZOOM'				=> 'Initial zoom of the user map',
	'ACP_USERMAP_GEONAMES_TITLE'	=> 'Username for geonames.org',
	'ACP_USERMAP_GEONAMES_TEXT'		=> 'User Map relies on the services of geonames.org to get the geographical coordinates
										of the member location identified by the postal code (zip code) and country and additionally
										the provided location in the member\'s profile.
										Therefore a registration at
										<a href="http://www.geonames.org/login" target="_blank">
										<span style="text-decoration: underline;">geonames.org/login</span></a>
										is mandatory. This registered username has to be entered here.<br>
										Each request costs 1 credit, with the free webservice you are limited to a maximum of
										1,000 credits per hour; if you operate a forum with more than 1,000 members it is recommended to
										register one username per 1,000 - 1,500 members. Otherwise your users may experience an
										error message while entering their profile data (postal code and country).<br>
										Multiple usernames need to be separated by commas.<br>
										ATTENTION: You have to enable (activate) your desired service after the first login
										on geonames.org using the link with your "username"!!',
	'ACP_USERMAP_GEONAMESUSER'		=> 'username(s) for geonames.org',
	'ACP_USERMAP_GEONAMESUSER_ERR'	=> 'It is mandatory to provide at least one valid username for geonames.org!',
	'ACP_USERMAP_PROFILE_ERROR'		=> 'This action could not be concluded successfully since you neglected to provide a Geonames.org user in the Usermap settings tab. Please do so immediately!',
	'ACP_USERMAP_LANGS'				=> 'Language packs',
	'ACP_USERMAP_LANGS_EXPLAIN'		=> 'This is where you can install additional language packs for the User Map. This might be necessary after adding
										language packs to the User Map after its first activation because their data have not been
										incorporated in the dropdown list to select the country; this you can do here after uploading the language pack
										with a ftp program in the subdirectory <italic>language</italic>  of this extnsion.',
	'ACP_USERMAP_INSTALLABLE_LANG'	=> 'Language packs ready for installation',
	'ACP_USERMAP_INSTALL_LANG_EXP'	=> 'Usermap language packs waiting for installation.',
	'ACP_USERMAP_MISSING_LANG'		=> 'Missing language packs',
	'ACP_USERMAP_MISSING_LANG_EXP'	=> 'Languages installed within the board but missing in the Usermap extension.',
	'ACP_USERMAP_ADDITIONAL_LANG'	=> 'Additional language packs of Usermap',
	'ACP_USERMAP_ADD_LANG_EXP'		=> 'The extension\'s language packs for which no language exists within this board.',
	'ACP_USERMAP_LANGPACK_NAME'		=> 'Name',
	'ACP_USERMAP_LANGPACK_LOCAL'	=> 'Local Name',
	'ACP_USERMAP_LANGPACK_ISO'		=> 'ISO',
	'ACP_USERMAP_NO_ENTRIES'		=> 'No language packs found',
	// UCP
	'MOT_ZIP'						=> 'Postal code / Zip code',
	'MOT_ZIP_EXP'					=> 'Please enter the postal code / zip code of your location in order to be listet on the usermap.<br>(Uppercase letters, numbers and dashes/hyphens only)',
	'MOT_LAND'						=> 'Country',
	'MOT_LAND_EXP'					=> 'Please select the country where you live in order to be listet on the usermap.',
	'MOT_UCP_GEONAMES_ERROR'		=> 'The administrator didn\'t provide a Geonames.org user, therefore the data for the usermap could not be retrieved!',
));
