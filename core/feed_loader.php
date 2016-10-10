<?php
/**
 *
 * phpBB Feeds Bot
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\feeds_bot\core;

class feed_loader
{
	protected $config;

	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function getXML($url)
	{
		$old_user_agent = ini_get('user_agent');
		ini_set("user_agent", $this->config['feeds_bot_user_agent']);

		$xml = @simplexml_load_file($url, null, LIBXML_NOCDATA);

		ini_set("user_agent", $old_user_agent);

		return $xml;
	}
}