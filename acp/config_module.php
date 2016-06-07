<?php
/**
*
* @package Carum - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace carum\market\acp;

use carum\carum\includes\php\tables;

if (!defined('IN_PHPBB'))
{
	exit;
}

class config_module
{
	public $u_action;

	function main($id, $mode)
	{
		
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_log;
		global $cache, $phpbb_container, $phpbb_dispatcher, $table_prefix, $request;

		$action	= $request->variable('action', '', true);
		$submit = ($request->is_set_post('submit'))? true : false;
		$debug = '';

		switch ($mode)
		{
			case 'config':

				$this->tpl_name = 'acp_market_config';
				$this->page_title = $user->lang('ACP_MARKET_CONFIG_TITLE');
				add_form_key('carum_market_settings');

				$carum_market_enabled = $request->variable('carum_market_enabled', '1');
				$carum_market_bids_enabled = $request->variable('carum_market_bids_enabled', '1');
				$carum_market_books_enabled = $request->variable('carum_market_books_enabled', '1');
				$bids_enabled = $request->variable('bids_enabled', '1');
				
				if ( $submit )
				{
					if (!check_form_key('carum_market_settings')) {
						trigger_error('FORM_INVALID');
					}
				
					$config->set('carum_market_enabled', $carum_market_enabled);
					$config->set('carum_market_bids_enabled', $carum_market_bids_enabled);
					$config->set('carum_market_books_enabled', $carum_market_books_enabled);
					
					trigger_error($user->lang('ACP_MARKET_SETTINGS_SAVED') . adm_back_link($this->u_action));

				}

				// Template variables
				$template->assign_vars([
					'U_ACTION'					=> $this->u_action,
					'CARUM_MARKET_ENABLED'		=> $config['carum_market_enabled'],
					'CARUM_MARKET_BIDS_ENABLED'	=> $config['carum_market_bids_enabled'],
					'CARUM_MARKET_BOOKS_ENABLED'	=> $config['carum_market_books_enabled'],
				]);

			break;

		}
	}
	
}
