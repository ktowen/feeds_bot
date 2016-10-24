<?php
/**
 *
 * phpBB Feeds Bot
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	// Actions
	'ACP_FEEDS_BOT_LIST'		=> 'Lista de Feeds',
	'ACP_FEEDS_BOT_DELETE'		=> 'Eliminar Feed',
	'ACP_FEEDS_BOT_ADD'		=> 'Agregar Feed',
	'ACP_FEEDS_BOT_EDIT'		=> 'Editar Feed',
	'ACP_FEEDS_BOT_UPDATE'		=> 'Actualizar Feed',

	// Errors
	'FEEDS_BOT_NO_FEED_ID'		=> 'El id del Feed no existe',
	'FEEDS_BOT_INVALID_UPDATE_INTERVAL'		=> 'Intervalo de actualización incorrecto',
	'FEEDS_BOT_NO_FEED'		=> 'No existe el Feed',
	'FEEDS_BOT_NO_FORUM'		=> 'El id del foro seleccionado no existe',
	'FEEDS_BOT_INVALID_FORUM'		=> 'El tipo del foro seleccionado no es válido',
	'FEEDS_BOT_NO_TOPIC'		=> 'El id del tema no existe',

	'FEED_BOT_FEED_LOADER_ERROR'		=> 'Error al cargar el Feed desde la url seleccionada',
	'FEED_BOT_FEED_PARSER_ERROR'		=> 'No se pudo analizar el Feed',

	// Action success
	'FEEDS_BOT_FEED_EDITED'		=> 'Feed editado.',
	'FEEDS_BOT_FEED_ADDED'		=> 'Feed agregado.',
	'FEEDS_BOT_FEED_DELETED'		=> 'Feed eliminado.',
	'FEEDS_BOT_FEED_UPDATED'		=> 'Feed actualizado.',

	// TOKENS
	'TOKEN_FEED_TITLE_EXPLAIN'		=> 'TODO',
	'TOKEN_FEED_DESCRIPTION_EXPLAIN'	=> 'TODO',
	'TOKEN_FEED_PUB_DATE_EXPLAIN'		=> 'TODO',
	'TOKEN_FEED_LINK_EXPLAIN'			=> 'TODO',
	'TOKEN_ENTRY_TITLE_EXPLAIN'		=> 'TODO',
	'TOKEN_ENTRY_CONTENT_EXPLAIN'		=> 'TODO',
	'TOKEN_ENTRY_LINK_EXPLAIN'		=> 'TODO',
	'TOKEN_ENTRY_ID_EXPLAIN'			=> 'TODO',
	'TOKEN_ENTRY_PUB_DATE_EXPLAIN'	=> 'TODO',

	// Feed Bot config
	'FEEDS_BOT_ENABLED'	=> 'Activar Feed Bot',
	'FEEDS_BOT_USER_AGENT'	=> 'Agente de usuario',
	'FEEDS_BOT_USER_AGENT_EXPLAIN'	=> 'Puede especificar un agente de usuario para mandar en la cabecera de cada petición.',
	'FEEDS_BOT_GC'	=> 'Tiempo de actualización',
	'FEEDS_BOT_GC_EXPLAIN'	=> 'El tiempo de actualización general de la extensión, cuando pase este tiempo se comprobarán si hay Feeds pendientes por revisar. No puede ser menor de 20 minutos.',

	// Feed config
	'FEED_ENABLED'	=> 'Activar Feed',
	'FEED_URL'		=> 'URL del Feed',
	'FEED_URL_EXPLAIN'		=> 'La URL desde donde se descargará el Feed, debe ser una URL válida.',
	'FEED_UPDATE_INTERVAL'		=> 'Tiempo de actualización',
	'FEED_UPDATE_INTERVAL_EXPLAIN'		=> 'TODO',
	'FEED_POSTER_USERNAME'		=> 'Nombre de usuario',
	'FEED_POSTER_USERNAME_EXPLAIN'		=> 'El nombre de usuario que se mostrará en el mensaje. Siempre se usa la cuenta de anónimo.',
	'FEED_NEW_TOPIC'		=> 'Tema nuevo',
	'FEED_NEW_TOPIC_EXPLAIN'		=> 'Los mensajes del Feed se pueden crear como temas nuevos o como mensajes en un tema.',
	'FEED_FORUM_ID'		=> 'TODO',
	'FEED_FORUM_ID_EXPLAIN'		=> 'TODO',
	'FEED_TOPIC_ID'		=> 'TODO',
	'FEED_TOPIC_ID_EXPLAIN'		=> 'TODO',
	'FEED_MAX_MSG'		=> 'TODO',
	'FEED_MAX_MSG_EXPLAIN'		=> 'TODO',
	'FEED_ENQUEUE'		=> 'TODO',
	'FEED_ENQUEUE_EXPLAIN'		=> 'TODO',
	'FEED_CENSOR_TEXT'		=> 'TODO',
	'FEED_CENSOR_TEXT_EXPLAIN'		=> 'TODO',
	'FEED_PARSE_BBCODE'		=> 'TODO',
	'FEED_PARSE_BBCODE_EXPLAIN'		=> 'TODO',
	'FEED_STRIP_HTML'		=> 'TODO',
	'FEED_STRIP_HTML_EXPLAIN'		=> 'TODO',
	'FEED_SUBJECT_TEMPLATE'		=> 'TODO',
	'FEED_SUBJECT_TEMPLATE_EXPLAIN'		=> 'TODO',
	'FEED_BODY_TEMPLATE'		=> 'TODO',
	'FEED_BODY_TEMPLATE_EXPLAIN'		=> 'TODO',
	'FEED_LAST_UPDATE'	=> 'Última actualización',

	'TOKENS'	=> 'TODO',
	'TOKENS_EXPLAIN'	=> 'TODO',
	'TOKEN'	=> 'TODO',
	'TOKEN_DEFINITION'	=> 'TODO',

	'POSTING_SETTINGS'	=> 'Configuración de posteo',
));
