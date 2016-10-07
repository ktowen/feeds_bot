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

	public function get_feed_object(\SimpleXMLElement $feed_xml) {
		$feed = null;

		foreach ($this->feed_types as $feed)
		{
			$type->load_xml($feed_xml);
			if ($type->is_valid())
			{
				$feed = $type;
				break;
			}
		}

		return $feed;
	}

	public function parse($entry, $feed, array $feed_config) {
		return array(
			'title'	=> $this->parse_tokens(
						$feed_config['subject_template'],
						$feed,
						$entry
					),
			'content'	=> $this->parse_tokens(
						$feed_config['body_template'],
						$feed,
						$entry
					),
		);
	}

	public function get_tokens() {
		$tokens = array(
			// TOKEN_NAME 			=> array( method, 		entry)
			'FEED_TITLE'			=> array('title',		false),
			'FEED_DESCRIPTION'		=> array('description',	false),
			'FEED_PUB_DATE'			=> array('pub_date',	false),
			'FEED_LINK'				=> array('link',		false),
			'ENTRY_TITLE'			=> array('title',		true),
			'ENTRY_CONTENT'			=> array('content',		true),
			'ENTRY_LINK'			=> array('link',		true),
			'ENTRY_ID'				=> array('id',			true),
			'ENTRY_PUB_DATE'		=> array('pub_date',	true),
		);

		return $tokens;
	}

	private function html_to_bbcode(string $text) {
		$html = array(
			'#<b(?:.*?)>(.*?)</b>#is',
			'#<strong(?:.*?)>(.*?)</strong>#is',
			'#<i(?:.*?)>(.*?)</i>#is',
			'#<em(?:.*?)>(.*?)</em>#is',
			'#<u(?:.*?)>(.*?)</u>#is',
			'#<ul(?:.*?)>(.*?)</ul>#is',
			'#<ol(?:.*?)>(.*?)</ol>#is',
			'#<li(?:.*?)>(.*?)</li>#is',
			'#<div(?:.*?)>(.*?)</div>#is',
			'#<p(?:.*?)>(.*?)</p>#is',
			'#<span(?:.*?)>(.*?)</span>#is',
			'#</?br>#is',
			'#<img(?:.*?)src="(.*?)"(?:.*?)/?>#is',
			'#<a(?:.*?)href="(.*?)"(?:.*?)>(.*?)</a>#is',
		);

		$bbcode = array(
			"[b]$1[/b]",
			"[b]$1[/b]",
			"[i]$1[/i]",
			"[i]$1[/i]",
			"[u]$1[/u]",
			"[list]$1[/list]",
			"[list=1]$1[/list]",
			"[*]$1",
			"\n$1\n",
			"\n$1\n",
			"$1",
			"\n",
			"[img]$1[/img]",
			"[url=$1]$2[/url]",
		);

		$text = preg_replace($html, $bbcode, $text);
		return $text;
	}

	private function prepare_text(string $text, bool $censor) {
		if ($censor)
		{
			if (!function_exists('censor_text'))
			{
				include($this->phpbb_root_path . 'includes/functions_content.' . $this->php_ext);
			}

			$text = censor_text($text);
		}
		$text = strip_tags($text);

		return $text;
	}

	private function parse_tokens(string $template, \SimpleXMLElement $feed, \SimpleXMLElement $entry) {
		$tokens = $this->get_tokens();

		$text = preg_replace_callback(
			array_keys($tokens),
			function($token) use ($entry, $feed, $tokens) {

				$token_data = $tokens[$token];

				if (!empty($data[1]))
				{
					$replace = $entry->${$token_data[0]}();
				}
				else
				{
					$replace = $feed->${$token_data[0]}();
				}
			},
			$template);

		$text = $this->html_to_bbcode($text);
		$text = $this->prepare_text($text);

		return $text;
	}
}