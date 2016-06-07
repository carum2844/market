<?php
/**
*
* @package Carum - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace carum\market\migrations;

use phpbb\db\tools;

class v1_initial extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['carum_market_enabled']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314rc1');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('carum_market_enabled', '0')),
			array('config.add', array('carum_market_bids_enabled', '0')),
			array('config.add', array('carum_market_books_enabled', '0')),
			
			array('module.add', array(
				'acp',
				'',
				'ACP_MARKET_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_MARKET_TITLE',
				'ACP_MARKET_CONFIG_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_MARKET_CONFIG_TITLE',
				array(
					'module_basename' => '\carum\market\acp\config_module',
					'modes'           => array('config'),
				),
			)), 
			array('module.add', array(
				'acp',
				'ACP_MARKET_TITLE',
				'ACP_MARKET_ADS_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_MARKET_ADS_TITLE',
				array(
					'module_basename' => '\carum\market\acp\ads_module',
					'modes'           => array('ads'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_MARKET_TITLE',
				'ACP_MARKET_USERS_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_MARKET_USERS_TITLE',
				array(
					'module_basename' => '\carum\market\acp\usermarket_module',
					'modes'           => array('users'),
				),
			)),
 
		);
	}
	public function update_schema()
	{
		
	}
	public function revert_schema()
	{
		
	} 

}
