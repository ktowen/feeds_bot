<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\alpha2');
	}

	public function update_data()
	{
		return array(
            array('config.add', array('feeds_bot_version', '1.0.0')),

            array('config.add', array('feeds_bot_last_gc', 0, true)),
            array('config.add', array('feeds_bot_gc', 20*60)),

            array('config.add', array('feeds_bot_enabled', true)),
            array('config.add', array('feeds_bot_user_agent', '')),

            array('permission.add', array('a_towen_feeds_bot_manage')),

            array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_FEEDS_BOT')),
            array('module.add', array(
                'acp', 'ACP_FEEDS_BOT', array(
                    'module_basename'	=> '\towen\feeds_bot\acp\feeds_bot_module',
                    'modes'				=> array('list', 'config'),
                ),
            )),

        );
	}

    public function update_schema()
    {
        return array(
            'add_tables'	=> array(
                $this->table_prefix . 'feeds_bot' => array(
                    'COLUMNS' => array(
                        'feed_id'           => array('UINT', NULL, 'auto_increment'),
                        'enabled'           	=> array('BOOL', 0),
                        'url'               => array('VCHAR:255', ''),
                        'update_interval'   => array('USINT', 0),
                        'last_update'       => array('TIMESTAMP', 0),
                        'last_entry_date'       => array('TIMESTAMP', 0),
                        'poster_username'   => array('VCHAR:100', ''),
                        'new_topic'         => array('BOOL', 0),
                        'forum_id'          => array('USINT', 0),
                        'topic_id'          => array('USINT', 0),
                        'enqueue'           => array('BOOL', 0),
						'censor_text'		=> array('BOOL', 0),
                        'max_msg'           => array('UINT', 0),
                        'subject_template'  => array('VCHAR:255', ''),
                        'body_template'     => array('VCHAR:255', ''),
                    ),
                    'PRIMARY_KEY' => 'feed_id',
                    'KEYS' => array(
                        'update_interval'   => array('INDEX', 'update_interval'),
                        'last_update'       => array('INDEX', 'last_update'),
                        'state'       		=> array('INDEX', 'state'),
                    ),
                ),
            ),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_tables'    => array(
                $this->table_prefix . 'feeds_bot',
            ),
        );
    }
}
