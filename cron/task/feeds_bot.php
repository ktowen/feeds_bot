<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\cron\task;


/**
 * Class feeds_bot
 * @package towen\feeds_bot\cron\task
 */
class feeds_bot extends \phpbb\cron\task\base
{
	/** @var \phpbb\config\config */
	protected $config;

    /**
     * @param \phpbb\config\config $config
     */
    public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function run()
	{
        $this->config->set('feeds_bot_last_gc', time(), true);
		return;
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