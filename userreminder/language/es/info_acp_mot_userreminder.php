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
		1	=> '¿Está completamente seguro de querer eliminar 1 usuario?<br><br><strong>¡Esto eliminará al usuario de forma permanente de la base de datos y no se podrá deshacer el cambio!</strong>',
		2	=> '¿Está completamente seguro de querer eliminar %d usuarios?<br><br><strong>¡Esto eliminará a los usuarios de forma permanente de la base de datos y no se podrán deshacer los cambios!</strong>',
	),
	'NO_USER_SELECTED'							=> 'No ha seleccionado a ningún usuario para esta acción, por favor seleccione por lo menos un usuario.',
	'USER_DELETED'								=> array(
		1	=> '1 usuario eliminado satisfactoriamente',
		2	=> '%d usuarios eliminados satisfactoriamente',
	),
	'USER_REMINDED'								=> array(
		1	=> 'Correo de recordatorio enviado a 1 usuario',
		2	=> 'Correo de recordatorio enviado a %d usuarios',
	),
	'USER_POSTS'								=> 'Mensajes',
	'DAYS_AGO'									=> 'Número de días',
	'AT_DATE'									=> 'En',
	'MARK_REMIND'								=> 'Recordar',
	'MARK_DELETE'								=> 'Eliminar',
	'REMIND_MARKED'								=> 'Recordar a usuarios seleccionados',
	'LOG_INACTIVE_REMIND_ONE'					=> '<strong>Enviar primer correo de recordatorio a usuarios inactivos</strong><br>» %s',
	'LOG_INACTIVE_REMIND_TWO'					=> '<strong>Enviar segundo correo de recordatorio a usuarios inactivos</strong><br>» %s',
	//ACP
	'ACP_USERREMINDER'							=> 'Recordatorio de Usuario',
	'ACP_USERREMINDER_SETTINGS'					=> 'Opciones para recordatorio de usuario',
	'ACP_USERREMINDER_SETTINGS_EXPLAIN'			=> 'Aquí es donde se configura el recordatorio de usuario.',
	'ACP_USERREMINDER_SETTING_SAVED'			=> 'Opciones de recordatorio de usuario han sido guardadas satisfactoriamente.',
	'ACP_USERREMINDER_TIME_SETTINGS_TITLE'		=> 'Configurar los intervalos de recordatorios',
	'ACP_USERREMINDER_TIME_SETTING_TEXT'		=> 'Configure el tiempo en días para que un usuario sea visto como inactivo, el tiempo en días entre el primer y el segundo correo electrónico para recordarle a este miembro que es necesario iniciar sesión y el siguiente período hasta que se elimine a este usuario. También puede nombrar a los usuarios que no se les enviará ningún correo de recordatorio ni tampoco serán eliminados.',
	'ACP_USERREMINDER_INACTIVE'					=> 'Número de días desconectado para que un usuario se considere inactivo',
	'ACP_USERREMINDER_DAYS_REMINDED'			=> 'Número de días desconectado para que un usuario que es considerado inactivo reciba el segundo correo de recordatorio;<br>
													esta opción está deshabilitada si usted ingresa "0"',
	'ACP_USERREMINDER_AUTOREMIND'				=> '¿Enviar correos de recordatorio automáticamente?',
	'ACP_USERREMINDER_DAYS_UNTIL_DELETED'		=> 'Número de días después del último recordatorio para que se pueda eliminar a un usuario:',
	'ACP_USERREMINDER_AUTODELETE'				=> '¿Eliminar usuarios automáticamente?',
	'ACP_USERREMINDER_PROTECTED_MEMBERS'		=> 'Ingrese el <i>user_id</i> de aquellos usuarios a los que desea proteger para que no se les recuerde o elimine. ¡Las entradas deben de estar separadas por comas!',
	// ACP Mail settings
	'ACP_USERREMINDER_MAIL_SETTINGS_TITLE'		=> 'Configuración del Correo',
	'ACP_USERREMINDER_EMAIL_BCC_TEXT'			=> 'Aquí puede configurar una dirección de correo electrónico para enviar una copia oculta o una copia de los correos electrónicos de recordatorios.',
	'ACP_USERREMINDER_EMAIL_BCC'				=> 'Enviar una copia oculta a',
	'ACP_USERREMINDER_EMAIL_CC'					=> 'Enviar una copia a',
	// ACP Mail text edit
	'ACP_USERREMINDER_MAIL_EDIT_TITLE'			=> 'Editar el texto del correo',
	'ACP_USERREMINDER_MAIL_EDIT_TEXT'			=> 'Aquí puede editar el texto predefinido del primer y segundo correo de recordatorio.',
	'ACP_USERREMINDER_MAIL_LANG'				=> 'Seleccionar lenguaje',
	'ACP_USERREMINDER_MAIL_FILE'				=> 'Seleccionar el archivo que desea editar',
	'ACP_USERREMINDER_MAIL_ONE'					=> '1er. recordatorio',
	'ACP_USERREMINDER_MAIL_TWO'					=> '2do. recordatorio',
	'ACP_USERREMINDER_MAIL_PREVIEW'				=> 'En la ventana de la derecha, puede editar el texto del correo electrónico elegido. Al hacer click en "Vista Previa"  el texto se visualiza
													tal como se mostrará en el correo enviado. Los tokens serán reemplazdos con sus respectivos datos. Junto a la vista previa se mostrará un botón
													para guardar el mensaje como un archivo en su servidor.<br>
													Puede usar los siguientes tokens como marcadores de posición para los datos respectivos del usuario dirigido:<br>
													- {USERNAME}: Los nombre de los usuarios<br>
													- {LAST_VISIT}: Fecha de la última conexión<br>
													- {LAST_REMIND}: Fecha del primer correo de recordatorio<br>
													Los siguientes tokens se pueden usar como marcadores de posición para los datos del sistema:<br>
													- {SITENAME}: Nombre de su foro<br>
													- {FORGOT_PASS}: Enlace a "He olvidado mi clave"<br>
													- {ADMIN_MAIL}: La dirección de correo electrónico del administrador<br>
													- {EMAIL_SIG}: Saludo<br>
													- {DAYS_INACTIVE}: El número de días de inactividad definido anteriormente<br>
													- {DAYS_TIL_DELETE}: El número de días definido anteriormente hasta la eliminación<br>',
	'ACP_USERREMINDER_MAIL_LOAD_FILE'			=> 'Cargar archivo',
	'ACP_USERREMINDER_PREVIEW_TEXT'				=> 'Por favor tome nota:<br>En el texto de la vista previa, los tokens para los datos del usuario a quien se dirige el mensaje se reemplazan con sus datos respectivos, esto significa que el texto resultante lógicamente podría no tener ningún sentido.<br>
													<strong>¡Después de guardar el texto, es obligatorio purgar la caché!</strong>',
	'ACP_USERREMINDER_MAIL_SAVE_FILE'			=> 'Guardar archivo',
	'ACP_USERREMINDER_FILE_ERROR'				=> 'Un error ha ocurrido mientras se guardaba el archivo "%s"!<br>El archivo <strong>no ha sido guardado.</strong>!',
	'ACP_USERREMINDER_FILE_SAVED'				=> 'Se ha guardado el archivo satisfactoriamente "%s".',
	// ACP Reminder
	'ACP_USERREMINDER_REMINDER'					=> 'Usuarios recordados',
	'ACP_USERREMINDER_REMINDER_EXPLAIN'			=> 'Una lista de aquellos usuarios que estuvieron en línea y publicaron algo pero han estado desconectados durante la cantidad de días definidos en la pestaña de configuración para ser tomados como inactivos.
													Puede seleccionar manualmente estos usuarios para enviarles los correos electrónicos de recordatorio o eliminarlos después del período de tiempo establecido después de que haya pasado el segundo recordatorio.
													La eliminación no se puede seleccionar hasta que hayan transcurrido los períodos definidos en la pestaña de configuración sin que este usuario haya iniciado sesión al menos una vez.',
	'ACP_USERREMINDER_REMINDER_ONE'				=> 'Primer recordatorio',
	'ACP_USERREMINDER_REMINDER_TWO'				=> 'Segundo recordatorio',
	'ACP_USERREMINDER_NO_ENTRIES'				=> 'No hay datos disponibles',
	'ACP_USERREMINDER_SORT_DESC'				=> 'Ascendente',
	'ACP_USERREMINDER_SORT_ASC'					=> 'Descendente',
	'ACP_USERREMINDER_KEY_RD'					=> 'Fecha de registro',
	'ACP_USERREMINDER_KEY_LV'					=> 'Última visita',
	'ACP_USERREMINDER_KEY_RO'					=> '1er. recordatorio',
	'ACP_USERREMINDER_KEY_RT'					=> '2do. recordatorio',
	// ACP Registered Only
	'ACP_USERREMINDER_REGISTERED_ONLY'			=> 'Inactivos',
	'ACP_USERREMINDER_REGISTERED_ONLY_EXPLAIN'	=> 'Una lista de aquellos usuarios que nunca se conectaron después del registro y activación.',
	// ACP Zeroposters
	'ACP_USERREMINDER_ZEROPOSTER'				=> 'Nunca activos',
	'ACP_USERREMINDER_ZEROPOSTER_EXPLAIN'		=> 'Una lista de aquellos usuarios que se han conectado regularmente pero que nunca han publicado nada.',
));
