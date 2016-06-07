<?php
/**
*
* @package Carum - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace carum\market\acp;

class usermarket_info
{
	function module()
	{
		return array(
			'filename'	=> '\carum\market\acp\usermarket_module',
			'title'		=> 'ACP_MARKET_USERS_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'users'	=> array('title' => 'ACP_MARKET_USERS_TITLE', 'auth' => 'ext_carum/market', 'cat' => array('ACP_CAT_MARKET')),
			),
		);
	}
}
