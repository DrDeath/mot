<?php
/**
*
* @package UserReminder v0.5.0
* @copyright (c) 2019, 2020 Mike-on-Tour
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
	'PLURAL_RULE'		=> 1,
	// Module
	'CONFIRM_USER_DELETE'						=> array(
		1	=> 'Are you really certain that you want to delete 1 user?<br><br><strong>This removes users permenantly from the data base and cannot be undone!</strong>',
		2	=> 'Are you really certain that you want to delete %d users?<br><br><strong>This removes users permenantly from the data base and cannot be undone!</strong>',
	),
	'NO_USER_SELECTED'							=> 'You have not selected any user for this action, please mark at least one user.',
	'USER_DELETED'								=> array(
		1	=> '1 users successfully deleted',
		2	=> '%d users successfully deleted',
	),
	'USER_REMINDED'								=> array(
		1	=> 'Reminding email sent to 1 user',
		2	=> 'Reminding email sent to %d users',
	),
	'USER_POSTS'								=> 'Posts',
	'DAYS_AGO'									=> 'No. of days ago',
	'AT_DATE'									=> 'On',
	'MARK_REMIND'								=> 'Remind',
	'MARK_DELETE'								=> 'Delete',
	'REMIND_MARKED'								=> 'Remind marked',
	'LOG_INACTIVE_REMIND_ONE'					=> '<strong>Sent first reminder email to inactive users</strong><br>» %s',
	'LOG_INACTIVE_REMIND_TWO'					=> '<strong>Sent second reminder email to inactive users</strong><br>» %s',
	//ACP
	'ACP_USERREMINDER'							=> 'User reminder',
	'ACP_USERREMINDER_SETTINGS'					=> 'Settings for the user reminder',
	'ACP_USERREMINDER_SETTINGS_EXPLAIN'			=> 'This is where you customize the user reminder.',
	'ACP_USERREMINDER_SETTING_SAVED'			=> 'Settings for the user reminder successfully saved.',
	'ACP_USERREMINDER_TIME_SETTINGS_TITLE'		=> 'Configure the reminder intervals',
	'ACP_USERREMINDER_TIME_SETTING_TEXT'		=> 'Configure the time in days until a user is viewed as inactive, the time in days between the first and second e-mail to remind this member that a login is necessary and the following period until this user is deleted.<br>You can also name users who are protected against any reminder emails and deletion.',
	'ACP_USERREMINDER_INACTIVE'					=> 'Number of days a user is offline until this user is viewed as inactive',
	'ACP_USERREMINDER_DAYS_REMINDED'			=> 'Number of days until a user counting as inactive is getting the second reminder mail;<br>
													sending a second mail is shut off if you input "0"',
	'ACP_USERREMINDER_AUTOREMIND'				=> 'Send reminder mails automatically?',
	'ACP_USERREMINDER_DAYS_UNTIL_DELETED'		=> 'Number of days after last reminder until a user can be deleted',
	'ACP_USERREMINDER_AUTODELETE'				=> 'Delete users automatically?',
	'ACP_USERREMINDER_PROTECTED_MEMBERS'		=> 'Enter the <i>user_id</i> of those users whom you wish to protect from getting reminded or deleted. Entries must be seperated by commas!',
	// ACP Mail settings
	'ACP_USERREMINDER_MAIL_SETTINGS_TITLE'		=> 'Email configuration',
	'ACP_USERREMINDER_EMAIL_BCC_TEXT'			=> 'Here you can set one email address each for sending a blind carbon copy or a carbon copy of the reminding emails to.',
	'ACP_USERREMINDER_EMAIL_BCC'				=> 'Send a blind carbon copy to',
	'ACP_USERREMINDER_EMAIL_CC'					=> 'Send a carbon copy to',
	// ACP Mail text edit
	'ACP_USERREMINDER_MAIL_EDIT_TITLE'			=> 'Edit the email texts',
	'ACP_USERREMINDER_MAIL_EDIT_TEXT'			=> 'Here you can edit the pre-set text of the first and second reminding email.',
	'ACP_USERREMINDER_MAIL_LANG'				=> 'Select language',
	'ACP_USERREMINDER_MAIL_FILE'				=> 'Select the file you want to edit',
	'ACP_USERREMINDER_MAIL_ONE'					=> '1st. reminder',
	'ACP_USERREMINDER_MAIL_TWO'					=> '2nd. reminder',
	'ACP_USERREMINDER_MAIL_PREVIEW'				=> 'In the window to the right you can edit the text of the choosen email. By clicking on the "Preview" button the text is shown below like
													it will be shown in the sent email. The tokens will be replaced with your respective dta. Together with the preview there will be shown a button
													to save the text as a file on your server.<br>
													You can use the following tokens as placeholders for the respective data of the addressed user:<br>
													- {USERNAME}: The members nickname<br>
													- {LAST_VISIT}: Date of the last login<br>
													- {LAST_REMIND}: Date of the first reminding mail<br>
													The following tokens can be used as placeholders for system data:<br>
													- {SITENAME}: Name of your forum<br>
													- {FORGOT_PASS}: Link to "I have forgotten my password"<br>
													- {ADMIN_MAIL}: The admins email address<br>
													- {EMAIL_SIG}: Salutation<br>
													- {DAYS_INACTIVE}: The above defined number of days of inactivity<br>
													- {DAYS_TIL_DELETE}: The above defined number of days until deletion<br>',
	'ACP_USERREMINDER_MAIL_LOAD_FILE'			=> 'Load file',
	'ACP_USERREMINDER_PREVIEW_TEXT'				=> 'Please note:<br>In the preview text the tokens for the data of the addressed user are replaced with your respective data, this means that the resulting text logically might not make any sense.<br>
													<strong>After saving the text it is mandatory to purge the cache!</strong>',
	'ACP_USERREMINDER_MAIL_SAVE_FILE'			=> 'Save file',
	'ACP_USERREMINDER_FILE_ERROR'				=> 'An error occurred while saving the file "%s"!<br>The File <strong>has not been saved</strong>!',
	'ACP_USERREMINDER_FILE_SAVED'				=> 'Successfully saved the file "%s".',
	// ACP Reminder
	'ACP_USERREMINDER_REMINDER'					=> 'Remind users',
	'ACP_USERREMINDER_REMINDER_EXPLAIN'			=> 'A list of those users who were online and posted something but have been offline for the number of days defined in the settings tab to qualify as inactive.
													You can manually select these users to send them the reminding emails or delete them after the set period of time after the second reminder has passed.
													The deletion is not selectable until the defined periods in the setting tab have passed without this user having at least once logged in.',
	'ACP_USERREMINDER_REMINDER_ONE'				=> 'First reminder',
	'ACP_USERREMINDER_REMINDER_TWO'				=> 'Second reminder',
	'ACP_USERREMINDER_NO_ENTRIES'				=> 'No data available',
	'ACP_USERREMINDER_SORT_DESC'				=> 'Ascending',
	'ACP_USERREMINDER_SORT_ASC'					=> 'Descending',
	'ACP_USERREMINDER_KEY_RD'					=> 'Registration date',
	'ACP_USERREMINDER_KEY_LV'					=> 'Last visit',
	'ACP_USERREMINDER_KEY_RO'					=> '1st reminder',
	'ACP_USERREMINDER_KEY_RT'					=> '2nd reminder',
	// ACP Registered Only
	'ACP_USERREMINDER_REGISTERED_ONLY'			=> 'Sleepers',
	'ACP_USERREMINDER_REGISTERED_ONLY_EXPLAIN'	=> 'A list of those users who have never been online after registration and activation.',
	// ACP Zeroposters
	'ACP_USERREMINDER_ZEROPOSTER'				=> 'Zeroposters',
	'ACP_USERREMINDER_ZEROPOSTER_EXPLAIN'		=> 'A list of those users who are online on a regular basis but have never posted anything.',
));
