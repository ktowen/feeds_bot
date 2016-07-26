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
	protected $feeds_bot;

    public function __construct(\phpbb\config\config $config, \towen\feeds_bot\core\feeds_bot $feeds_bot)
	{
		$this->config = $config;
		$this->feeds_bot = $feeds_bot;
	}

	public function run()
	{
		$this->feeds_bot->update_pending_feeds();
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