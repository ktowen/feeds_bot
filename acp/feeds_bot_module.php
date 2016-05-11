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
	/** @var string */
	public $u_action;

    /**
     * @param $id
     * @param $mode
     */
    public function main($id, $mode)
    {
        global $cache, $config, $db, $phpbb_log, $request, $template, $user, $table_prefix;

        $user->add_lang_ext('towen/feeds_bot', 'feeds_bot_acp');

        $form_key = 'acp_feeds_bot';
        add_form_key($form_key);

        switch($mode)
        {
            case 'list':
                $this->tpl_name = 'acp_feeds_bot';
                $action = $request->variable('action', '');

				if (!in_array($action, array('delete', 'add', 'edit', 'list')))
				{
					$action = 'list';
				}
				$this->page_title = $user->lang['ACP_FEEDS_BOT_'.strtoupper($action)];

                if ($action == 'edit' || $action == 'delete')
                {
                    $feed_id = $request->variable('feed_id', 0);

                    if (!$feed_id)
                    {
                        trigger_error($user->lang['NO_FEED_ID'] . adm_back_link($this->u_action), E_USER_WARNING);
                    }
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
							$sql = 'DELETE FROM ' . "{$table_prefix}feeds_bot" . " WHERE feed_id = {$feed_id}";
							$db->sql_query($sql);

							//$phpbb_log->add('admin', 'LOG_FEEDS_BOT_FEED_DELETED'); TODO
							trigger_error($user->lang['FEEDS_BOT_FEED_DELETED'] . adm_back_link($this->u_action));
                        }
                        else
                        {
                            confirm_box(false, $user->lang('ACP_DELETE_CONFIRM'), build_hidden_fields(array(
                                'feed_id'	=> $feed_id,
                                'mode'		=> $mode,
                                'action'	=> $action,
                            )));
                        }
                    break;

                    case 'list':
                    default:


						$template->assign_vars(array(
							'U_ACTION'			=> $this->u_action . '&amp;action=add',
						));
                    break;
                }

            break;

            case 'config':
                $this->tpl_name = 'acp_feeds_bot_config';
                $this->page_title = $user->lang['ACP_FEEDS_BOT_CONFIG'];

                $feeds_bot_enabled = $request->variable('feeds_bot_enabled', (bool)$config['feeds_bot_enabled']);
                $feeds_bot_user_agent = $request->variable('feeds_bot_user_agent', (string)$config['feeds_bot_user_agent'], true);
                $feeds_bot_gc = $request->variable('feeds_bot_gc', (int)($config['feeds_bot_gc']/60));

                $error = array();
                $submit = $request->is_set_post('submit');

                if ($submit && !check_form_key($form_key))
                {
                    $error[] = $user->lang['FORM_INVALID'];
                }

                if ($feeds_bot_gc < 10)
                {
                    $error[] = $user->lang['FEEDS_BOT_GC_INVALID'];
                }

                // Do not write values if there is an error
                if (!sizeof($error) && $submit)
                {
                    set_config('feeds_bot_enabled', $feeds_bot_enabled);
                    set_config('feeds_bot_user_agent', $feeds_bot_user_agent);
                    set_config('feeds_bot_gc', $feeds_bot_gc * 60);

                    //$phpbb_log->add('admin', 'LOG_FEEDS_BOT_CONFIG_UPDATED'); // TODO

                    trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action), E_USER_NOTICE);
                }

                $template->assign_vars(array(
                    'L_TITLE'			=> $user->lang['ACP_FEEDS_BOT_CONFIG'],

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
