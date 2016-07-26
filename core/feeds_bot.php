<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\core;

class posting_bot {

	protected $config;
	protected $phpbb_dispatcher;
	protected $db;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(\phpbb\config\config $config, \phpbb\event\dispatcher_interface $phpbb_dispatcher,
		\phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function post(array $post_data, array $feed_config) {

	}
}