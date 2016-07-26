<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\cron\task;


class feeds_bot extends \phpbb\cron\task\base
{
	protected $config;
	protected $log;
	protected $posting_bot;
	protected $feed_parser;
	protected $feed_loader;
	protected $table;

    public function __construct(\phpbb\config\config $config, \phpbb\log\log $log,
		\towen\feeds_bot\core\posting_bot $posting_bot, \towen\feeds_bot\core\feed_parser $feed_parser,
		\towen\feeds_bot\core\feed_loader $feed_loader, $feed_bot_table)
	{
		$this->config = $config;
		$this->log = $log;
		$this->posting_bot = $posting_bot;
		$this->feed_parser = $feed_parser;
		$this->feed_loader = $feed_loader;
		$this->table = $feed_bot_table;
	}

	public function run()
	{
		//todo
		/*
		 * cargar configuracion de los feed
		 * si hay alguno pendiente, para cada uno:
		 * 		llamar al loader
		 * 		si abre:
		 * 			si hay mensajes nuevos:
		 * 				llamar al parser
		 * 				llamar al posting bot
		 * 			configurar ultima entrada al feed
		 * 		si no abre:
		 * 			agregar un log
		 * 			desactivarlo
		 * 		actualizar ultima entrada al cron
		 */

        $this->config->set('feeds_bot_last_gc', time(), true);
	}

	public function is_runnable()
	{
		return !empty($this->config['feeds_bot_enabled']);
	}

	public function should_run()
	{
//		return $this->config['feeds_bot_last_gc'] < time() - $this->config['feeds_bot_gc'];
		return $this->config['feeds_bot_last_gc'] < time() - 10; //$this->config['feeds_bot_gc'];
	}
}