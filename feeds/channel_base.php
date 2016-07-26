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
 * Class channel_base
 * @package towen\feeds_bot\feeds
 */
abstract class channel_base {
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var string
	 */
	protected $link;
	/**
	 * @var string
	 */
	protected $pub_date;

	/**
	 * @return string
	 */
	abstract function title();

	/**
	 * @return string
	 */
	abstract function pub_date();

	/**
	 * @return string
	 */
	abstract function link();

	/**
	 * @return string
	 */
	abstract function description();

	/**
	 * @return array
	 */
	abstract function entries();

	/**
	 * @return bool
	 */
	abstract function in_valid();
}
