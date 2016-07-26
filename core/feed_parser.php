<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\core;

class feed_parser {

	protected $config;
	protected $user;
	protected $phpbb_dispatcher;
	protected $feed_types;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(\phpbb\config\config $config, \phpbb\user $user,
		\phpbb\event\dispatcher_interface $phpbb_dispatcher, \phpbb\di\service_collection $feed_types,
		$phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->user = $user;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->feed_types = $feed_types;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function parse(\SimpleXMLElement $xml, array $feed_config) {
		return [];
	}
}