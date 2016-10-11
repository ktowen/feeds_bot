<?php
/**
 *
 * phpBB Feeds Bot
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\feeds_bot\feeds;

/**
 * Class entry_base
 * @package towen\feeds_bot\feeds
 */
abstract class entry_base
{
	/**
	 * @var \SimpleXMLElement
	 */
	protected $xml;

	/**
	 * @param \SimpleXMLElement $xml
	 */
	abstract function __construct(\SimpleXMLElement $xml);

	/**
	 * @return string
	 */
	abstract function title();

	/**
	 * @return string
	 */
	abstract function content();

	/**
	 * @return string
	 */
	abstract function id();

	/**
	 * @return string
	 */
	abstract function link();

	/**
	 * @return string
	 */
	abstract function pub_date();
}
