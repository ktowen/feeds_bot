<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\core;

class feeds_bot {

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

	public function get_feeds_data(array $feeds_id) {
		$sql = "SELECT feed_id, enabled, url, update_interval, last_update, last_entry_date, poster_username,
					new_topic, forum_id, topic_id, max_msg, enqueue, censor_text, subject_template, body_template
				FROM {$this->table}
				WHERE " . $this->db->sql_in_set('feed_id', $feeds_id);
		$result = $this->db->sql_query($sql);
		$feeds_data = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $feeds_data;
	}

	public function get_pending_feeds() {
		$now = time();
		$sql = "SELECT feed_id, enabled, url, update_interval, last_update, last_entry_date, poster_username,
					new_topic, forum_id, topic_id, max_msg, enqueue, censor_text, subject_template, body_template
				FROM {$this->table}
				WHERE update_interval + last_update <= {$now} AND enabled = 1";
		$result = $this->db->sql_query($sql);
		$feeds_data = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $feeds_data;
	}

	public function handle_feed($feed_config) {
		$return = false;
		$feed_xml = $this->feed_loader->getXML($feed_config['url']);

		if (!$feed_xml)
		{
			$this->feed_error('FEED_BOT_FEED_LOADER_ERROR', $feed_config);
			return false;
		}

		$last_entry_date = 0;

		$feed_object = $this->feed_parser->get_feed_object($feed_xml);

		if (!$feed_object)
		{
			$this->feed_error('FEED_BOT_FEED_PARSER_ERROR', $feed_config);
			return false;
		}

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

		$update_array = array(
			'last_update'	=> time(),
		);

		if ($last_entry_date)
		{
			$return = true;
			$update_array = array_merge($update_array, array(
				'last_entry_date'	=> $last_entry_date,
			));
		}
		$sql = "UPDATE {$this->table} SET ". $this->db->sql_build_array('UPDATE', $update_array) ."
					WHERE feed_id = {$feed_config['feed_id']}";
		$this->db->sql_query($sql);

		return $return;
	}

	public function update_pending_feeds() {
		$pending_feeds = $this->get_pending_feeds();

		foreach ($pending_feeds as $feed) {
			$this->handle_feed($feed);
		}
	}

	private function post(array $post_data, array $feed_config) {
		if (!function_exists('submit_post'))
		{
			include($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}

//		$entry = array_merge($entry, array(
//			'post_time'	=> '',
//			'force_approved_state'	=> '', //ITEM_APPROVED, ITEM_UNAPPROVED
//			'forum_id'	=> '',
//			'topic_id'	=> '',
//			'bbcode_bitfield'	=> '',
//			'bbcode_uid'	=> '',
//			'message'	=> '',
//			'message_md5'	=> '',
//
//		));

//		$data = array(
//			'topic_title'			=> (empty($post_data['topic_title'])) ? $post_data['post_subject'] : $post_data['topic_title'],
//			'topic_first_post_id'	=> (isset($post_data['topic_first_post_id'])) ? (int) $post_data['topic_first_post_id'] : 0,
//			'topic_last_post_id'	=> (isset($post_data['topic_last_post_id'])) ? (int) $post_data['topic_last_post_id'] : 0,
//			'topic_time_limit'		=> (int) $post_data['topic_time_limit'],
//			'topic_attachment'		=> (isset($post_data['topic_attachment'])) ? (int) $post_data['topic_attachment'] : 0,
//			'post_id'				=> (int) $post_id,
//			'topic_id'				=> (int) $topic_id,
//			'forum_id'				=> (int) $forum_id,
//			'icon_id'				=> 0,
//			'poster_id'				=> ANONYMOUS,
//			'enable_sig'			=> false,
//			'enable_bbcode'			=> (bool) $post_data['enable_bbcode'],
//			'enable_smilies'		=> (bool) $post_data['enable_smilies'],
//			'enable_urls'			=> (bool) $post_data['enable_urls'],
//			'enable_indexing'		=> (bool) $post_data['enable_indexing'],
//			'message_md5'			=> (string) $message_md5,
//			'post_checksum'			=> (isset($post_data['post_checksum'])) ? (string) $post_data['post_checksum'] : '',
//			'post_edit_reason'		=> $post_data['post_edit_reason'],
//			'post_edit_user'		=> ($mode == 'edit') ? $user->data['user_id'] : ((isset($post_data['post_edit_user'])) ? (int) $post_data['post_edit_user'] : 0),
//			'forum_parents'			=> $post_data['forum_parents'],
//			'forum_name'			=> $post_data['forum_name'],
//			'notify'				=> $notify,
//			'notify_set'			=> $post_data['notify_set'],
//			'poster_ip'				=> (isset($post_data['poster_ip'])) ? $post_data['poster_ip'] : $user->ip,
//			'post_edit_locked'		=> (int) $post_data['post_edit_locked'],
//			'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
//			'bbcode_uid'			=> $message_parser->bbcode_uid,
//			'message'				=> $message_parser->message,
//			'attachment_data'		=> $message_parser->attachment_data,
//			'filename_data'			=> $message_parser->filename_data,
//			'topic_status'			=> $post_data['topic_status'],
//
//			'topic_visibility'			=> (isset($post_data['topic_visibility'])) ? $post_data['topic_visibility'] : false,
//			'post_visibility'			=> (isset($post_data['post_visibility'])) ? $post_data['post_visibility'] : false,
//		);

//		submit_post('post'||'reply', $entry['subject'], $entry['username'], POST_NORMAL, null, &$entry);
	}

	private function feed_error(string $error, array $feed_config) {
		$sql = "UPDATE {$this->table} SET enabled = 0 WHERE feed_id = {$feed_config['feed_id']}";
		$this->db->sql_query($sql);

		$this->log->add('critical', $error);
	}
}