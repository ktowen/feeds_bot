<?php
/**
*
* phpBB Feeds Bot
* @copyright (c) 2016 towen - [towenpa@gmail.com]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace towen\feeds_bot\acp;

class feeds_bot_info
{
    public function module()
    {
        return array(
            'filename'	=> '\towen\feeds_bot\acp\feeds_bot_info',
            'title'		=> 'ACP_FEEDS_BOT',
            'version'	=> '1.0.0',
            'modes'		=> array(
                'list'	=> array(
                    'title'	=> 'ACP_FEEDS_BOT_LIST',
                    'auth'	=> 'ext_towen/feeds_bot && acl_a_towen_feeds_bot_manage',
                    'cat'	=> array('ACP_FEEDS_BOT')),
                'config'	=> array(
                    'title'	=> 'ACP_FEEDS_BOT_CONFIG',
                    'auth'	=> 'ext_towen/feeds_bot && acl_a_towen_feeds_bot_manage',
                    'cat'	=> array('ACP_FEEDS_BOT')),
            ),
        );
    }
}
