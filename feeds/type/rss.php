<?php
/**
 *
 * phpBB Feeds Bot
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\feeds_bot\feeds\type;

/**
 * Class rss
 * @package towen\feeds_bot\feeds\type
 */
class rss extends \towen\feeds_bot\feeds\feed_base
{

	/**
	 * @return string
	 */
	function description()
	{
		return (string) $this->xml->channel->description;
	}

	/**
	 * @return string
	 */
	function title()
	{
		return (string) $this->xml->channel->title;
	}

	/**
	 * @return string
	 */
	function pub_date()
	{
		$date = $this->xml->channel->lastBuildDate ? 'lastBuildDate' : 'pubDate';
		return strtotime($this->xml->channel->$date);
	}

	/**
	 * @return string
	 */
	function link()
	{
		return (string) $this->xml->channel->title;
	}

	/**
	 * @return array
	 */
	function entries()
	{
		$entries = array();
		foreach($this->xml->channel->item as $entry)
		{
			$entries[] = new rss_entry($entry);
		}
		return $entries;
	}

	/**
	 * @return bool
	 */
	function is_valid()
	{
		return !empty($this->xml->channel->item);
	}

	/**
	 * @param \SimpleXMLElement $xml
	 */
	function load_xml(\SimpleXMLElement $xml)
	{
		$this->xml = $xml;
	}
}

/**
 * Class rss_entry
 * @package towen\feeds_bot\feeds\type
 */
class rss_entry extends \towen\feeds_bot\feeds\entry_base
{

	/**
	 * @param \SimpleXMLElement $xml
	 */
	function __construct(\SimpleXMLElement $xml)
	{
		$this->xml = $xml;
	}

	/**
	 * @return string
	 */
	function title()
	{
		return (string) $this->xml->title;
	}

	/**
	 * @return string
	 */
	function content()
	{
		return (string) $this->xml->description;
	}

	/**
	 * @return string
	 */
	function id()
	{
		return (string) $this->xml->guid;
	}

	/**
	 * @return string
	 */
	function link()
	{
		return (string) $this->xml->link;
	}

	/**
	 * @return string
	 */
	function pub_date()
	{
		return (string) $this->xml->pubDate;
	}
}