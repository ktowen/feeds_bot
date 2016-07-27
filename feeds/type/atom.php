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
 * Class atom
 * @package towen\feeds_bot\feeds\type
 */
class atom extends \towen\feeds_bot\feeds\channel_base {

	/**
	 * @return string
	 */
	function description()
	{
		return (string) $this->xml->subtitle;
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
	function pub_date()
	{
		return strtotime($this->xml->updated);
	}

	/**
	 * @return string
	 */
	function link()
	{
		return (string) $this->xml->link['href'];
	}

	/**
	 * @return array
	 */
	function entries()
	{
		$entries = array();
		foreach($this->xml->entry as $entry)
		{
			$entries[] = new atom_entry($entry);
		}
		return $entries;
	}

	/**
	 * @return bool
	 */
	function is_valid()
	{
		return !empty($this->xml->entry);
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
 * Class atom_entry
 * @package towen\feeds_bot\feeds\type
 */
class atom_entry extends \towen\feeds_bot\feeds\entry_base {

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
		return (string) $this->xml->content;
	}

	/**
	 * @return string
	 */
	function id()
	{
		return (string) $this->xml->id;
	}

	/**
	 * @return string
	 */
	function link()
	{
		return (string) $this->xml->link['href'];
	}

	/**
	 * @return string
	 */
	function pub_date()
	{
		$date = $this->xml->updated ? 'updated' : 'published';
		return strtotime($this->xml->$date);
	}
}