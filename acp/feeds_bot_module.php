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
                $action = $this->request->variable('action', '');

                switch($action)
                {
                    case 'edit':
                        $feed_id = $this->request->variable('feed_id', 0);

                        if (!$feed_id)
                        {
                            $message = $this->user->lang['NO_FEED_ID'];
                            $message_type = E_USER_WARNING;

                            trigger_error($message . adm_back_link($this->u_action), $message_type);
                        }

                        $this->tpl_name = 'acp_board';
                        $this->page_title = $this->user->lang[$display_vars['title']];

                        $display_vars = array(
                            'title'	=> 'ACP_FEEDS_BOT_CONFIG',
                            'vars'	=> array(
                                'legend1'				=> 'GENERAL_SETTINGS',
                                'enabled'   			=> array('lang' => 'ENABLE_EMAIL',			'validate' => 'bool',	'type' => 'radio:enabled_disabled', 'explain' => true),
                                'board_email_form'		=> array('lang' => 'BOARD_EMAIL_FORM',		'validate' => 'bool',	'type' => 'radio:enabled_disabled', 'explain' => true),
                                'email_function_name'	=> array('lang' => 'EMAIL_FUNCTION_NAME',	'validate' => 'string',	'type' => 'text:20:50', 'explain' => true),
                                'email_package_size'	=> array('lang' => 'EMAIL_PACKAGE_SIZE',	'validate' => 'int:0',	'type' => 'number:0:99999', 'explain' => true),
                                'board_contact'			=> array('lang' => 'CONTACT_EMAIL',			'validate' => 'email',	'type' => 'email:25:100', 'explain' => true),
                                'board_contact_name'	=> array('lang' => 'CONTACT_EMAIL_NAME',	'validate' => 'string',	'type' => 'text:25:50', 'explain' => true),
                                'board_email'			=> array('lang' => 'ADMIN_EMAIL',			'validate' => 'email',	'type' => 'email:25:100', 'explain' => true),
                                'board_email_sig'		=> array('lang' => 'EMAIL_SIG',				'validate' => 'string',	'type' => 'textarea:5:30', 'explain' => true),
                                'board_hide_emails'		=> array('lang' => 'BOARD_HIDE_EMAILS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

                                'legend2'					=> 'ACP_SUBMIT_CHANGES',
                            )
                        );

                        $new_config = $this->config;
                        $submit = $request->is_set_post('submit');
                        $cfg_array = ($request->is_set_post('config')) ? utf8_normalize_nfc($this->request->variable('config', array('' => ''), true)) : $new_config;
                        $error = array();

                        validate_config_vars($display_vars['vars'], $cfg_array, $error);

                        if ($submit && !check_form_key($form_key))
                        {
                            $error[] = $this->user->lang['FORM_INVALID'];
                        }

                        // Do not write values if there is an error
                        if (!sizeof($error) && $submit)
                        {
                            foreach ($display_vars['vars'] as $config_name => $null)
                            {
                                if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
                                {
                                    continue;
                                }

                                set_config($config_name, $cfg_array[$config_name]);
                            }

                            //$this->log->add('admin', 'LOG_FEEDS_BOT_CONFIG_UPDATED');

                            $message = $this->user->lang['CONFIG_UPDATED'];
                            $message_type = E_USER_NOTICE;

                            trigger_error($message . adm_back_link($this->u_action), $message_type);
                        }

                        $this->display_config($display_vars, $error, $new_config);
                    break;

                    case 'delete':
                        $feed_id = $this->request->variable('feed_id', 0);

                        if (!$feed_id)
                        {
                            $message = $this->user->lang['NO_FEED_ID'];
                            $message_type = E_USER_WARNING;

                            trigger_error($message . adm_back_link($this->u_action), $message_type);
                        }

                        if (confirm_box(true))
                        {

                            //$this->log.add('admin', 'LOG_FEEDS_BOT_FEED_DELETED');
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
                $this->tpl_name = 'acp_board';
                $this->page_title = $this->user->lang[$display_vars['title']];

                $display_vars = array(
                    'title'	=> 'ACP_FEEDS_BOT_CONFIG',
                    'vars'	=> array(
                        'legend1'				=> 'GENERAL_SETTINGS',
                        'feeds_bot_enabled'		=> array('lang' => 'FEEDS_BOT_ENABLED',		'validate' => 'bool',	'type' => 'radio:enabled_disabled', 'explain' => true),
                        'feeds_bot_user_agent'	=> array('lang' => 'FEEDS_BOT_USER_AGENT',	'validate' => 'string',	'type' => 'text:50:255', 'explain' => true),
                        'feeds_bot_gc'	        => array('lang' => 'FEEDS_BOT_UPDATE_TIME',	'validate' => 'int:15',	'type' => 'number:15', 'explain' => true),

                        'legend2'				=> 'ACP_SUBMIT_CHANGES',
                    )
                );

                $new_config = $this->config;
                $submit = $this->request->is_set_post('submit');
                $cfg_array = ($this->request->is_set_post('config')) ? utf8_normalize_nfc($this->request->variable('config', array('' => ''), true)) : $new_config;
                $error = array();

                validate_config_vars($display_vars['vars'], $cfg_array, $error);

                if ($submit && !check_form_key($form_key))
                {
                    $error[] = $this->user->lang['FORM_INVALID'];
                }

                // Do not write values if there is an error
                if (!sizeof($error) && $submit)
                {
                    foreach ($display_vars['vars'] as $config_name => $null)
                    {
                        if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
                        {
                            continue;
                        }

                        if ($config_name == 'feeds_bot_gc')
                        {
                            $cfg_array[$config_name] *= 60;
                        }
                        set_config($config_name, $cfg_array[$config_name]);
                    }

                    //$this->log->add('admin', 'LOG_FEEDS_BOT_CONFIG_UPDATED');

                    $message = $this->user->lang['CONFIG_UPDATED'];
                    $message_type = E_USER_NOTICE;

                    trigger_error($message . adm_back_link($this->u_action), $message_type);
                }

                $this->display_config($display_vars, $error, $new_config);
            break;
        }
    }

    private function display_config($display_vars, $error, $new_config)
    {
        $this->template->assign_vars(array(
            'L_TITLE'			=> $this->user->lang[$display_vars['title']],
            'L_TITLE_EXPLAIN'	=> $this->user->lang[$display_vars['title'] . '_EXPLAIN'],

            'S_ERROR'			=> (sizeof($error)) ? true : false,
            'ERROR_MSG'			=> implode('<br />', $error),

            'U_ACTION'			=> $this->u_action)
        );

        // Output relevant page
        foreach ($display_vars['vars'] as $config_key => $vars)
        {
            if (!is_array($vars) && strpos($config_key, 'legend') === false)
            {
                continue;
            }

            if (strpos($config_key, 'legend') !== false)
            {
                $this->template->assign_block_vars('options', array(
                    'S_LEGEND'		=> true,
                    'LEGEND'		=> (isset($this->user->lang[$vars])) ? $this->user->lang[$vars] : $vars)
                );

                continue;
            }

            $type = explode(':', $vars['type']);

            $l_explain = '';
            if ($vars['explain'] && isset($vars['lang_explain']))
            {
                $l_explain = (isset($this->user->lang[$vars['lang_explain']])) ? $this->user->lang[$vars['lang_explain']] : $vars['lang_explain'];
            }
            else if ($vars['explain'])
            {
                $l_explain = (isset($this->user->lang[$vars['lang'] . '_EXPLAIN'])) ? $this->user->lang[$vars['lang'] . '_EXPLAIN'] : '';
            }

            if ($config_key == 'feeds_bot_gc' || $config_key == 'update_interval')
            {
                $new_config[$config_key] /= 60;
            }
            $content = build_cfg_template($type, $config_key, $new_config, $config_key, $vars);

            if (empty($content))
            {
                continue;
            }

            $this->template->assign_block_vars('options', array(
                'KEY'			=> $config_key,
                'TITLE'			=> (isset($this->user->lang[$vars['lang']])) ? $this->user->lang[$vars['lang']] : $vars['lang'],
                'S_EXPLAIN'		=> $vars['explain'],
                'TITLE_EXPLAIN'	=> $l_explain,
                'CONTENT'		=> $content,
            ));

            unset($display_vars['vars'][$config_key]);
        }

    }
}
