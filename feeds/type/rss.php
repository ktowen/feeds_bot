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
class rss extends \towen\feeds_bot\feeds\channel_base {

	/**
	 * @return string
	 */
	function description()
	{
		// TODO: Implement description() method.
	}

	/**
	 * @return string
	 */
	function title()
	{
		// TODO: Implement title() method.
	}

	/**
	 * @return string
	 */
	function pub_date()
	{
		// TODO: Implement pub_date() method.
	}

	/**
	 * @return string
	 */
	function link()
	{
		// TODO: Implement link() method.
	}

	/**
	 * @return array
	 */
	function entries()
	{
		// TODO: Implement entries() method.
	}

	/**
	 * @return bool
	 */
	function is_valid()
	{
		// TODO: Implement in_valid() method.
	}

	/**
	 * @param \SimpleXMLElement $xml
	 */
	function load_xml(\SimpleXMLElement $xml)
	{
		// TODO: Implement load_xml() method.
	}
}

/**
 * Class rss_entry
 * @package towen\feeds_bot\feeds\type
 */
class rss_entry extends \towen\feeds_bot\feeds\entry_base {

	/**
	 * @param \SimpleXMLElement $xml
	 */
	function __construct(\SimpleXMLElement $xml)
	{
		// TODO: Implement __construct() method.
	}

	/**
	 * @return string
	 */
	function title()
	{
		// TODO: Implement title() method.
	}

	/**
	 * @return string
	 */
	function content()
	{
		// TODO: Implement content() method.
	}

	/**
	 * @return string
	 */
	function id()
	{
		// TODO: Implement id() method.
	}

	/**
	 * @return string
	 */
	function link()
	{
		// TODO: Implement link() method.
	}

	/**
	 * @return string
	 */
	function pub_date()
	{
		// TODO: Implement pub_date() method.
	}
}