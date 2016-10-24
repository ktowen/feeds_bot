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
    'ACP_FEEDS_BOT'			=> 'Feeds Bot',
    'ACP_FEEDS_BOT_MANAGE'			=> 'Administrar feeds',
    'ACP_FEEDS_BOT_CONFIG'			=> 'Configuración',

	'LOG_FEEDS_BOT_FEED_DELETED'	=> 'TODO',
	'LOG_FEEDS_BOT_FEED_ADDED'	=> 'TODO',
	'LOG_FEEDS_BOT_FEED_EDITED'	=> 'TODO',
	'LOG_FEEDS_BOT_CONFIG_UPDATED'	=> '<strong>TODO</strong><br />» %s',
	'LOG_FEED_BOT_FEED_LOADER_ERROR'		=> 'TODO',
	'LOG_FEED_BOT_FEED_PARSER_ERROR'		=> 'TODO',
));
