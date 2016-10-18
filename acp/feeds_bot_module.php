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
        global $cache, $config, $db, $phpbb_log, $phpbb_container, $request, $template, $user, $table_prefix;

        $user->add_lang_ext('towen/feeds_bot', 'feeds_bot_acp');
		$submit = $request->is_set_post('submit');

        $form_key = 'acp_feeds_bot';
        add_form_key($form_key);

		if ($submit && !check_form_key($form_key))
		{
			trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

        switch($mode)
        {
            case 'manage':
                $this->tpl_name = 'acp_feeds_bot';
                $action = $request->variable('action', '');

				if (!in_array($action, array('delete', 'add', 'edit', 'list' ,'update')))
				{
					$action = 'list';
				}
				$this->page_title = $user->lang['ACP_FEEDS_BOT_'.strtoupper($action)];

                if (in_array($action, array('edit', 'delete','update')))
                {
                    $feed_id = $request->variable('feed_id', 0);

                    if (!$feed_id)
                    {
                        trigger_error($user->lang['FEEDS_BOT_NO_FEED_ID'] . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                }

                switch($action)
                {
                    case 'add':
                    case 'edit':
						$this->action_add($feed_id, $action, $submit);
					break;

					case 'delete':
						$this->action_delete($feed_id, $action, $mode);
                    break;

					case 'update':
						$this->action_update($feed_id);
					break;

                    case 'list':
                    default:
						$this->action_list();
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

                if ($submit && !check_form_key($form_key))
                {
                    $error[] = $user->lang['FORM_INVALID'];
                }

                if ($feeds_bot_gc < 20)
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

	private function action_add($feed_id, $action, $submit)
	{
		global $db, $template, $request, $user, $table_prefix, $phpbb_container;

		$template_array = $error = array();

		$settings = array(
			'enabled'			 	=> 1,
			'url'			 	=> '',
			'update_interval'	=> 20,
			'poster_username'	=> $user->lang['GUEST'],
			'new_topic'			=> false,
			'forum_id'			=> 0,
			'topic_id'			=> 0,
			'max_msg'			=> 10,
			'enqueue'			=> false,
			'censor_text'		=> true,
			'parse_bbcode'		=> true,
			'strip_html'		=> true,
			'subject_template'	=> "{ENTRY_TITLE}",
			'body_template'		=> "{ENTRY_CONTENT}\n--\n{ENTRY_LINK}",
		);

		if ($action == 'edit')
		{
			$sql = 'SELECT ' . implode(array_keys($settings), ', ') . "
									FROM {$table_prefix}feeds_bot
									WHERE feed_id = {$feed_id}";
			$result = $db->sql_query($sql);
			$settings = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$settings)
			{
				trigger_error($user->lang['FEEDS_BOT_NO_FEED'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		if ($submit)
		{
			foreach($settings as $setting => $value)
			{
				$unicode = in_array($setting, array('poster_username', 'subject_template', 'body_template'));
				$settings[$setting] = $request->variable('feed_'.$setting, $settings[$setting], $unicode);
			}

			// validation
			if ($settings['update_interval'] < 20)
			{
				$error[] = $user->lang('FEEDS_BOT_INVALID_UPDATE_INTERVAL');
			}

			if (!preg_match('#^' . get_preg_expression('url') . '$#iu', $settings['url']) &&
				!preg_match('#^' . get_preg_expression('www_url') . '$#iu', $settings['url']))
			{
				$error[] = $user->lang('FEEDS_BOT_INVALID_URL');
			}

			$feed_bot = $phpbb_container->get('towen.feeds_bot.core.feeds_bot');

			try
			{
				$feed_bot->load_feed($settings['url']);
			}
			catch (\Exception $e)
			{
				$error[] = $user->lang($e->getMessage());
			}

			if ($settings['new_topic'])
			{
				$sql = "SELECT forum_type
									FROM {$table_prefix}forums
									WHERE forum_id = {$settings['forum_id']}";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					$error[] = $user->lang('FEEDS_BOT_NO_FORUM');
				}
				elseif ($row['forum_type'] != FORUM_POST)
				{
					$error[] = $user->lang('FEEDS_BOT_INVALID_FORUM');
				}
			}
			else
			{
				$sql = "SELECT topic_id
									FROM {$table_prefix}topics
									WHERE topic_id = {$settings['topic_id']}";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					$error[] = $user->lang('FEEDS_BOT_NO_TOPIC');
				}
			}

			if (empty($error))
			{
				if ($action == 'edit')
				{
					$sql = "UPDATE {$table_prefix}feeds_bot SET " . $db->sql_build_array('UPDATE', $settings) . "
											WHERE feed_id = {$feed_id}";
					$message = $user->lang("FEEDS_BOT_FEED_UPDATED");
				}
				else
				{
					$sql = "INSERT INTO {$table_prefix}feeds_bot " . $db->sql_build_array('INSERT', $settings);
					$message = $user->lang("FEEDS_BOT_FEED_ADDED");
				}

				$db->sql_query($sql);
				trigger_error($message . adm_back_link($this->u_action), E_USER_NOTICE);
			}
		}

		foreach ($settings as $setting => $value)
		{
			$key = 'FEED_'.strtoupper($setting);
			$template->assign_var($key, $value);
		}

		$template->assign_vars(array(
			'S_EDIT' => true,
			'U_ACTION' => $this->u_action . "&amp;action={$action}" . ($action=='edit' ? "&amp;feed_id={$feed_id}" : ''),

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),
		));
		$this->generate_token_list();
	}

	private function action_delete($feed_id, $action, $mode)
	{
		global $db, $phpbb_log, $user, $table_prefix;

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
	}

	private function action_list()
	{
		global $db, $template, $table_prefix, $user;

		$sql = "SELECT feed_id, enabled, url, last_update FROM {$table_prefix}feeds_bot";
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('feeds', array(
				'URL'	=> $row['url'],
				'ENABLED'	=> $row['enabled'] ? $user->lang['YES'] : $user->lang['NO'],
				'LAST_UPDATE'	=> $user->format_date($row['last_update']),
				'U_EDIT'	=> append_sid($this->u_action, array('action'=>'edit', 'feed_id'=>$row['feed_id'])),
				'U_UPDATE'	=> append_sid($this->u_action, array('action'=>'update', 'feed_id'=>$row['feed_id'])),
				'U_DELETE'	=> append_sid($this->u_action, array('action'=>'delete', 'feed_id'=>$row['feed_id'])),
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'U_ACTION'			=> "{$this->u_action}&amp;action=add",
		));
	}

	private function action_update($feed_id)
	{
		global $phpbb_container, $user;
		$feed_bot = $phpbb_container->get('towen.feeds_bot.core.feeds_bot');
		$feed_config = $feed_bot->get_feeds_data(array($feed_id));

		if (empty($feed_config))
		{
			trigger_error($user->lang['FEEDS_BOT_NO_FEED'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$error = $feed_bot->handle_feed($feed_config[0]);

		if ($error)
		{
			trigger_error($user->lang($error). adm_back_link($this->u_action), E_USER_WARNING);
		}
		else
		{
			trigger_error($user->lang('FEEDS_BOT_FEED_UPDATED'). adm_back_link($this->u_action), E_USER_NOTICE);
		}
	}

	private function generate_token_list()
	{
		global $phpbb_container, $template, $user;

		$tokens = $phpbb_container->get('towen.feeds_bot.core.feed_parser')->get_tokens();

		foreach ($tokens as $token => $null)
		{
			$template->assign_block_vars('token', array(
				'TOKEN'		=>	$token,
				'EXPLAIN'	=>	$user->lang("FEED_BOT_TOKEN_{$token}_EXPLAIN"),
			));
		}
	}
}
