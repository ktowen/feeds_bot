<?php
/**
 *
 * phpBB Feeds Bot
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\feeds_bot\acp;

/**
 * Class feeds_bot_module
 * @package towen\feeds_bot\acp
 */
class feeds_bot_module
{
    /** @var \phpbb\cache\driver\driver_interface */
    protected $cache;

    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\log\log */
    protected $log;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\template\template */
    protected $emplate;

    /** @var \phpbb\user */
    protected $user;

    /** @var string */
    public $u_action;

    /**
     * @param $id
     * @param $mode
     */
    public function main($id, $mode)
    {
        global $cache, $config, $db, $phpbb_log, $request, $template, $user;

        $this->cache = $cache;
        $this->config = $config;
        $this->db = $db;
        $this->log = $phpbb_log;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;

        $this->user->add_lang_ext('towen/feeds_bot', 'feeds_bot_acp');

        $form_key = 'acp_feeds_bot';
        add_form_key($form_key);

        switch($mode)
        {
            case 'list':
                $this->tpl_name = 'acp_feeds_bot';
                $action = $this->request->variable('action', '');
                $this->page_title = $this->user->lang['ACP_FEEDS_BOT_LIST'];

                if ($action == 'edit' || $action == 'delete')
                {
                    $feed_id = $this->request->variable('feed_id', 0);

                    if (!$feed_id)
                    {
                        trigger_error($this->user->lang['NO_FEED_ID'] . adm_back_link($this->u_action), E_USER_WARNING);
                    }

                    $this->page_title = $this->user->lang['ACP_FEEDS_BOT_'.strtoupper($action)];
                }

                switch($action)
                {
                    case 'edit':

                    // no break;

                    case 'add':

                        $template->assign_vars(array(
                            'S_EDIT' => true,
                        ));
                    break;

                    case 'delete':

                        if (confirm_box(true))
                        {

                            //$this->log.add('admin', 'LOG_FEEDS_BOT_FEED_DELETED'); TODO
                        }
                        else
                        {
                            confirm_box(false, $this->user->lang('ACP_DELETE_CONFIRM'), build_hidden_fields(array(
                                'feed_id'	=> $feed_id,
                                'mode'		=> $mode,
                                'action'	=> $action,
                            )));
                        }
                    break;

                    // case 'list':
                    default:


                    break;
                }

            break;

            case 'config':
                $this->tpl_name = 'acp_feeds_bot_config';
                $this->page_title = $this->user->lang['ACP_FEEDS_BOT_CONFIG'];

                $feeds_bot_enabled = $request->variable('feeds_bot_enabled', (bool)$config['feeds_bot_enabled']);
                $feeds_bot_user_agent = $request->variable('feeds_bot_user_agent', (string)$config['feeds_bot_user_agent'], true);
                $feeds_bot_gc = $request->variable('feeds_bot_gc', (int)($config['feeds_bot_gc']/60));

                $error = array();
                $submit = $this->request->is_set_post('submit');

                if ($submit && !check_form_key($form_key))
                {
                    $error[] = $this->user->lang['FORM_INVALID'];
                }

                if ($feeds_bot_gc < 10)
                {
                    $error[] = $this->user->lang['FEEDS_BOT_GC_INVALID'];
                }

                // Do not write values if there is an error
                if (!sizeof($error) && $submit)
                {
                    set_config('feeds_bot_enabled', $feeds_bot_enabled);
                    set_config('feeds_bot_user_agent', $feeds_bot_user_agent);
                    set_config('feeds_bot_gc', $feeds_bot_gc * 60);

                    //$this->log->add('admin', 'LOG_FEEDS_BOT_CONFIG_UPDATED'); // TODO

                    trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action), E_USER_NOTICE);
                }

                $this->template->assign_vars(array(
                    'L_TITLE'			=> $this->user->lang['ACP_FEEDS_BOT_CONFIG'],

                    'S_ERROR'			=> (sizeof($error)) ? true : false,
                    'ERROR_MSG'			=> implode('<br />', $error),

                    'FEEDS_BOT_ENABLED' => $feeds_bot_enabled,
                    'FEEDS_BOT_USER_AGENT'  => $feeds_bot_user_agent,
                    'FEEDS_BOT_GC'      => $feeds_bot_gc,

                    'U_ACTION'			=> $this->u_action,
                ));
            break;
        }
    }
}
