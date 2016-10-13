<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\core;

class feeds_bot
{

	protected $config;
	protected $phpbb_dispatcher;
	protected $db;
	protected $log;
	protected $feed_parser;
	protected $feed_loader;
	protected $table;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(\phpbb\config\config $config, \phpbb\event\dispatcher_interface $phpbb_dispatcher,
		\phpbb\db\driver\driver_interface $db, \phpbb\log\log $log, \towen\feeds_bot\core\feed_parser $feed_parser,
		\towen\feeds_bot\core\feed_loader $feed_loader, $feed_bot_table, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->db = $db;
		$this->log = $log;
		$this->feed_parser = $feed_parser;
		$this->feed_loader = $feed_loader;
		$this->table = $feed_bot_table;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function get_feeds_data(array $feeds_id)
	{
		$sql = "SELECT feed_id, enabled, url, update_interval, last_update, last_entry_date, poster_username,
					new_topic, forum_id, topic_id, max_msg, enqueue, censor_text, parse_bbcode, strip_html,
					subject_template, body_template
				FROM {$this->table}
				WHERE " . $this->db->sql_in_set('feed_id', $feeds_id);
		$result = $this->db->sql_query($sql);
		$feeds_data = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $feeds_data;
	}

	public function get_pending_feeds()
	{
		$now = time();
		$sql = "SELECT feed_id, enabled, url, update_interval, last_update, last_entry_date, poster_username,
					new_topic, forum_id, topic_id, max_msg, enqueue, censor_text, parse_bbcode, strip_html,
					subject_template, body_template
				FROM {$this->table}
				WHERE update_interval + last_update <= {$now} AND enabled = 1";
		$result = $this->db->sql_query($sql);
		$feeds_data = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $feeds_data;
	}

	public function load_feed($url)
	{
		$feed_xml = $this->feed_loader->getXML($url);

		if (!$feed_xml)
		{
			throw new \Exception('FEED_BOT_FEED_LOADER_ERROR');
		}

		$feed_object = $this->feed_parser->get_feed_object($feed_xml);

		if (!$feed_object)
		{
			throw new \Exception('FEED_BOT_FEED_PARSER_ERROR');
		}

		return $feed_object;
	}

	public function handle_feed($feed_config)
	{
		try
		{
			$feed_object = $this->load_feed($feed_config['url']);
		}
		catch (\Exception $e)
		{
			return $this->feed_error($e->getMessage(), $feed_config);
		}

		$last_entry_date = $feed_config['last_entry_date'];

		$this->user_hack();
		foreach ($feed_object->entries() as $entry)
		{
			if ($entry->pub_date() > $feed_config['last_entry_date'])
			{
				if ($entry->pub_date() > $last_entry_date)
				{
					$last_entry_date = $entry->pub_date();
				}
				$post_data = $this->feed_parser->parse($entry, $feed_object, $feed_config);
				$this->post($post_data, $feed_config);
			}
		}
		$this->user_hack(true);

		$update_array = array(
			'last_update'	=> time(),
			'last_entry_date'	=> $last_entry_date,
		);

		$sql = "UPDATE {$this->table} SET ". $this->db->sql_build_array('UPDATE', $update_array) ."
					WHERE feed_id = {$feed_config['feed_id']}";
		$this->db->sql_query($sql);

		return false;
	}

	public function update_pending_feeds()
	{
		$pending_feeds = $this->get_pending_feeds();

		foreach ($pending_feeds as $feed)
		{
			$this->handle_feed($feed);
		}
	}

	private function post(array $post_data, array $feed_config)
	{
		if (!function_exists('submit_post'))
		{
			include($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}

		$poll = $uid = $bitfield = '';
		generate_text_for_storage($post_data['content'], $uid, $bitfield, $flags, $feed_config['parse_bbcode'], true, true);

		$data = array(
			'forum_id'      	=> $feed_config['forum_id'],
			'icon_id'			=> false,
			'poster_id'			=> ANONYMOUS,

			'enable_bbcode'     => $feed_config['parse_bbcode'],
			'enable_smilies'    => true,
			'enable_urls'       => true,
			'enable_sig'        => true,

			'message'       	=> $post_data['content'],
			'message_md5'   	=> md5($post_data['content']),

			'bbcode_bitfield'   => $bitfield,
			'bbcode_uid'        => $uid,

			'post_edit_locked'  => 0,
			'topic_title'       => $post_data['title'],
			'notify_set'        => false,
			'notify'            => false,
			'post_time'         => 0,
			'forum_name'        => '',
			'enable_indexing'   => true,

			'force_approved_state'	=> $feed_config['enqueue'] ? ITEM_UNAPPROVED : ITEM_APPROVED,
		);

		if ($feed_config['new_topic'])
		{
			$mode = 'post';
		}
		else
		{
			$mode = 'reply';
			$data['topic_id']= $feed_config['topic_id'];
		}

		submit_post($mode, $post_data['title'], $feed_config['poster_username'], POST_NORMAL, $poll, $data);
	}

	private function feed_error($error, array $feed_config)
	{
		$sql = "UPDATE {$this->table} SET enabled = 0 WHERE feed_id = {$feed_config['feed_id']}";
		$this->db->sql_query($sql);

//		$this->log->add('critical', $error);
		return $error;
	}

	private function user_hack($end = false)
	{
		global $user, $db;
		static $user_data;

		if (!$end)
		{
			$user_data = $user->data;

			$sql = 'SELECT *, 0 as is_registered FROM ' . USERS_TABLE . '
				WHERE user_id = ' . ANONYMOUS;
			$result = $db->sql_query($sql);
			$user->data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		else
		{
			$user->data = $user_data;
		}

	}
}