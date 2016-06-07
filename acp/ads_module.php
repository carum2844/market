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

class ads_module
{
	public $u_action;

	function main($id, $mode)
	{
		
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_log;
		global $cache, $phpbb_container, $phpbb_dispatcher, $table_prefix, $request, $ext_manager;

		$start = $request->variable('start', 0);
		$action	= $request->variable('action', 'list_ads');
		$submit = ($request->is_set_post('submit'))? true : false;
		$debug = '';
/*
echo '('.$mode.'|'.$action.')<br />';
//echo '('.__DIR__.')<br />';
$ext_path = $ext_manager->get_extension_path('carum/userfirmfields', true); 
echo '('.$ext_path.')<br />';
//$folderName = __DIR__;
$folderName = $phpbb_root_path;
//$folderName = $phpbb_root_path.'ext/carum/uploads';
echo $folderName.'<br />';
foreach (new \DirectoryIterator($folderName) as $fileInfo) {
	if ($fileInfo->isDot())
	{
		continue;
	}
	if (time() - $fileInfo->getCTime() >= 2*24*60*60 )
	{
		//unlink($fileInfo->getRealPath());
		echo $fileInfo->getRealPath().'<br />';
	}
}
*/

		switch ($mode)
		{
			case 'ads':
			
				switch ($action)
				{
					case 'list_ads':

						$this->tpl_name = 'acp_market_ads';
						$this->page_title = $user->lang('ACP_MARKET_ADS_TITLE');
						add_form_key('carum_market_ads');
						
						$ads_member = $request->variable('ads_member', '0');
						
						if ( $submit )
						{
							if (!check_form_key('carum_market_ads')) {
								trigger_error('FORM_INVALID');
							}
							
							unset($requests_vars);
							$request_vars = $request->get_super_global(\phpbb\request\request_interface::POST);

							$ads_ids = $request->variable('ads_id_list', '');
							$ads_ids = explode('|', $ads_ids);

							foreach ($ads_ids as $key => $value) {

								if ( $value ){
									/*
									$setting = ( isset($request_vars['lead-'.$value]) )? '1' : '0';

									// Save this ads settings
									$sql = 'UPDATE ' .$table_prefix . tables::MARKET_ADS_TABLE . '
											SET xxxxxxxxxxx = ' . $setting . ' 
											WHERE ad_id = ' . $value;
									$result = $db->sql_query($sql);
									*/
								}
							}

							trigger_error($user->lang('ACP_MARKET_SETTINGS_SAVED') . adm_back_link($this->u_action));

						}
						
						// Mount the user select filter
						$a_users = array();
						$s_ads_member = '';
						$sql = 'SELECT *
								FROM ' . USERS_TABLE . '
								WHERE user_type = 0
								ORDER BY username_clean ASC';
						$result = $db->sql_query($sql);
						$s_ads_member = ( $ads_member == '0' )? '<option value="0" selected="selected">' . $user->lang['SELECT'] . '</option>' : '';
						while( $row = $db->sql_fetchrow($result) )
						{
								$selected = ( $row['user_id'] == $ads_member )? ' selected="selected"' : '';
								$s_ads_member .= '<option value="' . $row['user_id'] . '"' . $selected . '>' . $row['username'] .'</option>' ;
								
								// We profit this while() to create the users array to use later
								$a_users[$row['user_id']] = $row['username'];
						}
						$db->sql_freeresult($result);
						$template->assign_vars(array(
									'S_ADS_MEMBER'		=> $s_ads_member,
						));
						
						// Get the total ads
						$sql = 'SELECT count(*) as total_ads FROM ' . $table_prefix . tables::MARKET_ADS_TABLE;
						$sql .= ( $ads_member )? ' WHERE ad_user_id = ' . $ads_member : '';
						$result = $db->sql_query($sql);
						$total_ads = $db->sql_fetchfield('total_ads');
						$db->sql_freeresult($result);
						
						// Create pagination logic
						$pagination = $phpbb_container->get('pagination');
						$base_url = 'index.php?i=-carum-market-acp-ads_module';
						$base_url = append_sid($base_url);	
						$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_ads, '20', $start);

						$match_any_chars = $db->get_any_char();
						$sql = 'SELECT * FROM ' . $table_prefix . tables::MARKET_ADS_TABLE;
						$sql .= ( $ads_member )? ' WHERE ad_user_id = ' . $ads_member : '';
						$sql .= ' ORDER BY ad_id DESC';
						//$sql .= ($member <> '') ? " AND username " . $db->sql_like_expression($match_any_chars . $member . $match_any_chars) : '';
						$result = $db->sql_query_limit($sql, '20', $start);

						while ($row = $db->sql_fetchrow($result))
						{
							$sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id = ' . $row['ad_user_id'];
							$result_temp = $db->sql_query($sql);
							$username = $db->sql_fetchfield('username');
							$db->sql_freeresult($result_temp);
						
							$base_url = $this->u_action . '&amp;action=edit_ad&amp;ad_id=' . $row['ad_id'];
							//$base_url = './../adm/index.php?i=-carum-market-acp-ads_module&amp;mode=ad&amp;ad_id=' . $row['ad_id'];
							//http://rotuleros.com/adm/index.php?sid=9174b63ff5cf3658dfd2d754fac5b421&i=-carum-market-acp-ads_module&mode=ad
							$base_url = append_sid($base_url);	
						

							$template->assign_block_vars('ads', array(

									'AD_ID'			=> $row['ad_id'],
									'AD_USER'		=> $row['ad_user_id'] . ' - ' . $a_users[$row['ad_user_id']],
									'AD_STATE'		=> $row['ad_state'],
									'AD_ITEM_NAME'	=> $row['ad_item_name'],
									'AD_REGADTE'	=> $row['ad_item_regdate'],
									'AD_ACTIVE'		=> $row['ad_active'],
								  
									'AD_EDIT'		=> $base_url,
							));
						}
						$base_url = $this->u_action . '&amp;action=list_ads';
						$base_url = append_sid($base_url);
						$template->assign_vars(array(
									'U_ACTION'		=> $base_url, 
						));
					break;
					
					case 'edit_ad': 
					
						$this->tpl_name = 'acp_market_ad';
						$this->page_title = $user->lang('ACP_MARKET_AD_TITLE');
						add_form_key('carum_market_ad');

						$data = array(
										'ad_id'						=> $request->variable('ad_id', '0'),
										'ad_user_id'				=> $request->variable('ad_user_id', '0'),
										'ad_state' 					=> $request->variable('ad_state', '0'),
										'ad_type_item' 				=> $request->variable('ad_type_item', '0'),
										'ad_category_id' 			=> $request->variable('ad_category_id', '0'),
										'ad_allow_auction' 			=> $request->variable('ad_allow_auction', '0'),
										'ad_item_name'				=> utf8_normalize_nfc($request->variable('ad_item_name', '', true)),
										'ad_item_short_description'	=> utf8_normalize_nfc($request->variable('ad_item_short_description', '', true)),
										'ad_item_description'		=> utf8_normalize_nfc($request->variable('ad_item_description', '', true)),
										'ad_item_new_used'			=> $request->variable('ad_item_new_used', '0'),
										'ad_item_state' 			=> $request->variable('ad_item_state', '0'),
										'ad_item_action' 			=> $request->variable('ad_item_action', '0'),
										'ad_method' 				=> $request->variable('ad_method', '0'),
										'ad_item_price'			 	=> $request->variable('ad_item_price', '0'),
										'ad_item_is_min_bid' 		=> $request->variable('ad_item_is_min_bid', '0'),
										'ad_item_hide_price'		=> $request->variable('ad_item_hide_price', '0'),
										'ad_item_min_bid_price'		=> $request->variable('ad_item_min_bid_price', '0'),
										'ad_item_show_min_bid' 		=> $request->variable('ad_item_show_min_bid', '0'),
										'ad_item_start_price'		=> $request->variable('ad_item_start_price', '0'),
										'ad_item_actual_price'		=> $request->variable('ad_item_actual_price', '0'),
										'ad_item_date_actual_price'	=> $request->variable('ad_item_date_actual_price', date("d/m/Y", strtotime(date("m/d/Y"))), true),
										'ad_item_buy_now_price'		=> $request->variable('ad_item_buy_now_price', '0'),
										'ad_item_shipment' 			=> $request->variable('ad_item_shipment', '0'),
										'ad_item_regdate' 			=> $request->variable('ad_item_regdate', date("d/m/Y", strtotime(date("m/d/Y"))), true),
										'ad_item_views' 			=> $request->variable('ad_item_views', '0'),
										'ad_item_maker_id'			=> $request->variable('ad_item_maker_id', '-1'),
										'ad_item_model_id'			=> $request->variable('ad_item_model_id', '-1'),
										'ad_item_product_name'		=> $request->variable('ad_item_product_name', '0'),
										'ad_item_alt_maker_name'	=> $request->variable('ad_item_alt_maker_name', '0'),
										'ad_item_model_name'		=> $request->variable('ad_item_model_name', '0'),
										'ad_item_alt_model_name'	=> $request->variable('ad_item_alt_model_name', '0'),
										'ad_item_serial'			=> $request->variable('ad_item_serial', '0'),
										'ad_item_usable_width'		=> $request->variable('ad_item_usable_width', '0'),
										'ad_item_units_usable_width' => $request->variable('ad_item_units_usable_width', '0'),
										'ad_item_weight'			=> $request->variable('ad_item_weight', '0'),
										'ad_item_units_weight'		=> $request->variable('ad_item_units_weight', '0'),
										'ad_item_production_year'	=> $request->variable('ad_item_production_year', '0'),
										'ad_item_adquired_year'		=> $request->variable('ad_item_adquired_year', '0'),
										'ad_item_image_1'			=> $request->variable('ad_item_image_1', ''),
										'ad_item_image_2'			=> $request->variable('ad_item_image_2', ''),
										'ad_item_image_3'			=> $request->variable('ad_item_image_3', ''),
										'ad_item_youtube'			=> $request->variable('ad_item_youtube', '0'),
										'ad_item_country_id'		=> $request->variable('ad_item_country_id', '0'),
										'ad_item_state_id'			=> $request->variable('ad_item_state_id', '0'),
										'ad_item_city_id'			=> $request->variable('ad_item_city_id', '0'),
										'ad_item_alt_city'			=> $request->variable('ad_item_alt_city', '0'),
										'ad_item_booked_by'			=> $request->variable('ad_item_booked_by', '0'),
										'ad_item_booked_date'		=> $request->variable('ad_item_booked_date', date("d/m/Y", strtotime(date("m/d/Y"))), true),
										'ad_item_sos'				=> $request->variable('ad_item_sos', '0'),
										'ad_active'					=> $request->variable('ad_active', '0'),
						);
						$data_temp = array(
										'images_to_delete'			=> $request->variable('images_to_delete', ''),
						);
						if ( $data_temp['images_to_delete'] != '' )
						{
							$images_to_delete = explode( '|', $data_temp['images_to_delete'] );
						}else{
							$images_to_delete = array();
						}

						$submit = (isset($_POST['submit'])) ? true : false;
						$error = array();
						
						if ( $submit )
						{
							if (!check_form_key('carum_market_ad')) {
								trigger_error('FORM_INVALID');
							}
							
							// Validations
							
							if ( empty($data['ad_item_regdate']) )
							{
								$error[] = $user->lang['AD_ITEM_REGDATE_NEEDED'];
							}
							else
							{
								$sep = '[\/\-\.]';
								//$match = '/^(((0? [1-9] |1\d|2 [0-8] ){$sep}(0? [1-9] |1 [012] )|(29|30){$sep}(0? [13456789] |1 [012] )|31{$sep}(0? [13578] |1 [02] )){$sep}(19| [2-9] \d)\d{2}|29{$sep}0?2{$sep}((19| [2-9] \d)(0 [48] | [2468]  [048] | [13579]  [26] )|(( [2468]  [048] | [3579]  [26] )00)))$/';
								//$match = '#^(((0?[1-9]|1d|2[0-8]){$sep}(0?[1-9]|1[012])|(29|30){$sep}(0?[13456789]|1[012])|31{$sep}(0?[13578]|1[02])){$sep}(19|[2-9]d)d{2}|29{$sep}0?2{$sep}((19|[2-9]d)(0[48]|[2468][048]|[13579][26])|(([2468][048]|[3579][26])00)))$#';
								//$match = '/^(19|20)[0-9]{2}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/';
								$match = '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#';
								//$match = '/^\d{1,2}\/\d{1,2}\/\d{4}$/';
								if (!preg_match($match, $data['ad_item_regdate']))
									{
										$error[] = $user->lang['AD_ITEM_REGDATE_BAD'];
									}
							}
							
							if ( empty($data['ad_user_id']) )
							{
								$error[] = $user->lang['AD_USER_NEEDED'];
							}
							
							if ( empty($data['ad_item_name']) )
							{
								$error[] = $user->lang['AD_ITEM_NAME_NEEDED'];
							}
							
							if ( $data['ad_method'] == '1' )
							{
								if ( $data['ad_item_start_price'] == '0' )
								{
									$error[] = $user->lang['AD_ITEM_START_PRICE_NEEDED'];
								}
								if ( $data['ad_item_min_bid_price'] == '0' )
								{
									$error[] = $user->lang['AD_ITEM_MIN_BID_PRICE_NEEDED'];
								}
							}
							if ( !empty($data['ad_item_date_actual_price']) )
							{
								$sep = '[\/\-\.]';
								//$match = '/^(((0? [1-9] |1\d|2 [0-8] ){$sep}(0? [1-9] |1 [012] )|(29|30){$sep}(0? [13456789] |1 [012] )|31{$sep}(0? [13578] |1 [02] )){$sep}(19| [2-9] \d)\d{2}|29{$sep}0?2{$sep}((19| [2-9] \d)(0 [48] | [2468]  [048] | [13579]  [26] )|(( [2468]  [048] | [3579]  [26] )00)))$/';
								//$match = '#^(((0?[1-9]|1d|2[0-8]){$sep}(0?[1-9]|1[012])|(29|30){$sep}(0?[13456789]|1[012])|31{$sep}(0?[13578]|1[02])){$sep}(19|[2-9]d)d{2}|29{$sep}0?2{$sep}((19|[2-9]d)(0[48]|[2468][048]|[13579][26])|(([2468][048]|[3579][26])00)))$#';
								//$match = '/^(19|20)[0-9]{2}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/';
								$match = '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#';
								//$match = '/^\d{1,2}\/\d{1,2}\/\d{4}$/';
								if (!preg_match($match, $data['ad_item_date_actual_price']))
									{
										$error[] = $user->lang['AD_ITEM_DATE_ACTUAL_PRICE_BAD'];
									}
							}
							if ( $data['ad_item_maker_id'] == '0' )
							{
								if ( $data['ad_item_alt_maker_name'] == '' )
								{
									$error[] = $user->lang['AD_ITEM_NAME_NEEDED'];
								}
							}
							if ( !empty($data['ad_item_booked_date']) )
							{
								$sep = '[\/\-\.]';
								//$match = '/^(((0? [1-9] |1\d|2 [0-8] ){$sep}(0? [1-9] |1 [012] )|(29|30){$sep}(0? [13456789] |1 [012] )|31{$sep}(0? [13578] |1 [02] )){$sep}(19| [2-9] \d)\d{2}|29{$sep}0?2{$sep}((19| [2-9] \d)(0 [48] | [2468]  [048] | [13579]  [26] )|(( [2468]  [048] | [3579]  [26] )00)))$/';
								//$match = '#^(((0?[1-9]|1d|2[0-8]){$sep}(0?[1-9]|1[012])|(29|30){$sep}(0?[13456789]|1[012])|31{$sep}(0?[13578]|1[02])){$sep}(19|[2-9]d)d{2}|29{$sep}0?2{$sep}((19|[2-9]d)(0[48]|[2468][048]|[13579][26])|(([2468][048]|[3579][26])00)))$#';
								//$match = '/^(19|20)[0-9]{2}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/';
								$match = '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#';
								//$match = '/^\d{1,2}\/\d{1,2}\/\d{4}$/';
								if (!preg_match($match, $data['ad_item_booked_date']))
									{
										$error[] = $user->lang['AD_ITEM_BOOKED_DATE_BAD'];
									}
							}
							
							if (!sizeof($error))
							{
							
								// Dates
								list($day, $month, $year) = split('/', $data['ad_item_regdate']);
								$ad_item_regdate_timeStamp = mktime(23, 59, 59, $month, $day, $year);
								$data['ad_item_regdate'] = $ad_item_regdate_timeStamp;
								if ( !empty($data['ad_item_date_actual_price']) )
								{
									list($day, $month, $year) = split('/', $data['ad_item_date_actual_price']);
									$ad_item_date_actual_price_timeStamp = mktime(23, 59, 59, $month, $day, $year);
									$data['ad_item_date_actual_price'] = $ad_item_date_actual_price_timeStamp;
								}else{
									$data['ad_item_date_actual_price'] = '0';
								}
								if ( !empty($data['ad_item_booked_date']) )
								{
									list($day, $month, $year) = split('/', $data['ad_item_booked_date']);
									$ad_item_booked_date_timeStamp = mktime(23, 59, 59, $month, $day, $year);
									$data['ad_item_booked_date'] = $ad_item_booked_date_timeStamp;
								}else{
									$data['ad_item_booked_date'] = '0';
								}

								// Now let's move the images
								// Windows
								//$tempDir = $_SERVER["DOCUMENT_ROOT"]."\\images\\temp\\";
								//$destDir = $_SERVER["DOCUMENT_ROOT"]."\\images\\market\\";
								// Linux
								$doc_root = $request->variable('DOCUMENT_ROOT', '', false, \phpbb\request\request_interface::SERVER);
								$tempDir = $doc_root."/ext/carum/carum/uploads/";
								$destDir = $doc_root."/images/market/ads/";
								for ( $x = 1; $x <= 3; $x++) {

									$file_tmp = $thumb_name = '';
									
									if ( $data['ad_item_image_'.$x] != '' ){
										
										// First the image
										copy( $tempDir.$data['ad_item_image_'.$x], $destDir.$data['ad_item_image_'.$x] );
										// Now the thumbnail
										$file_tmp = explode(".",$data['ad_item_image_'.$x]);
										$thumb_name = $file_tmp['0'].'_t.jpg';
										copy( $tempDir.$thumb_name, $destDir.$thumb_name );
									}
									//Delete the images not needed in destination folder
									if ( $data['ad_id'] )
									{
										$sql = 'SELECT ad_item_image_' . $x . ' 
												FROM ' . $table_prefix . tables::MARKET_ADS_TABLE . '
												WHERE ad_id = '. $data['ad_id'];
										$result = $db->sql_query($sql);
										$row = $db->sql_fetchrow($result);
										$db->sql_freeresult($result);
										if ( $row['ad_item_image_'.$x] != $data['ad_item_image_'.$x] ){
											unlink( $destDir.$row['ad_item_image_'.$x] );
											$file_tmp = explode( ".", $row['ad_item_image_'.$x] );
											$thumb_name = $file_tmp['0'].'_t.jpg';
											unlink( $destDir.$thumb_name );
										}
									}
								}
								// Delete the temporary images marked in images_to_delete
								foreach( $images_to_delete as $key => $value ){
									if ( file_exists( $tempDir.$value ) ){
										unlink( $tempDir.$value );
									}
								}
								
								// Insert / Update the record
								if ( $data['ad_id'] )
								{
									$sql = 'UPDATE ' . $table_prefix . tables::MARKET_ADS_TABLE . ' 
											SET ' . $db->sql_build_array('UPDATE', $data) . ' WHERE ad_id = ' . $data['ad_id'];
								}else{
									$sql = 'INSERT INTO ' . $table_prefix . tables::MARKET_ADS_TABLE . ' ' . $db->sql_build_array('INSERT', $data);
								}
								$db->sql_query($sql);
								
								// exit program
								trigger_error($user->lang['MARKET_AD_SAVED'] . adm_back_link($this->u_action));
								
							} // error
						} // submit
						
						if ( !$submit && $data['ad_id'] )
						{
							// Load ad if is an edit
							$sql = 'SELECT *
								FROM ' . $table_prefix . tables::MARKET_ADS_TABLE . '
								WHERE ad_id = ' . $data['ad_id'];
							$result = $db->sql_query($sql);
							$row = $db->sql_fetchrow($result);
							$db->sql_freeresult($result);
							
							$data['ad_id'] = $row['ad_id'];
							$data['ad_user_id'] = $row['ad_user_id'];
							$data['ad_state'] = $row['ad_state'];
							$data['ad_type_item'] = $row['ad_type_item'];
							$data['ad_category_id'] = $row['ad_category_id'];
							$data['ad_allow_auction'] = $row['ad_allow_auction'];
							$data['ad_item_name'] = $row['ad_item_name'];
							$data['ad_item_short_description'] = $row['ad_item_short_description'];
							$data['ad_item_description'] = $row['ad_item_description'];
							$data['ad_item_new_used'] = $row['ad_item_new_used'];
							$data['ad_item_state'] = $row['ad_item_state'];
							$data['ad_item_action'] = $row['ad_item_action'];
							$data['ad_method'] = $row['ad_method'];
							$data['ad_item_price'] = $row['ad_item_price'];
							$data['ad_item_is_min_bid'] = $row['ad_item_is_min_bid'];
							$data['ad_item_hide_price'] = $row['ad_item_hide_price'];
							$data['ad_item_min_bid_price'] = $row['ad_item_min_bid_price'];
							$data['ad_item_show_min_bid'] = $row['ad_item_show_min_bid'];
							$data['ad_item_start_price'] = $row['ad_item_start_price'];
							$data['ad_item_actual_price'] = $row['ad_item_actual_price'];
							$data['ad_item_date_actual_price'] = date("d/m/Y", $row['ad_item_date_actual_price']);
							$data['ad_item_buy_now_price'] = $row['ad_item_buy_now_price'];
							$data['ad_item_shipment'] = $row['ad_item_shipment'];
							$data['ad_item_regdate'] = date("d/m/Y", $row['ad_item_regdate']);
							$data['ad_item_views'] = $row['ad_item_views'];
							$data['ad_item_maker_id'] = $row['ad_item_maker_id'];
							$data['ad_item_model_id'] = $row['ad_item_model_id'];
							$data['ad_item_product_name'] = $row['ad_item_product_name'];
							$data['ad_item_alt_maker_name'] = $row['ad_item_alt_maker_name'];
							$data['ad_item_model_name'] = $row['ad_item_model_name'];
							$data['ad_item_alt_model_name'] = $row['ad_item_alt_model_name'];
							$data['ad_item_serial'] = $row['ad_item_serial'];
							$data['ad_item_usable_width'] = $row['ad_item_usable_width'];
							$data['ad_item_units_usable_width'] = $row['ad_item_units_usable_width'];
							$data['ad_item_weight'] = $row['ad_item_weight'];
							$data['ad_item_units_weight'] = $row['ad_item_units_weight'];
							$data['ad_item_production_year'] = $row['ad_item_production_year'];
							$data['ad_item_adquired_year'] = $row['ad_item_adquired_year'];
							$data['ad_item_image_1'] = $row['ad_item_image_1'];
							$data['ad_item_image_2'] = $row['ad_item_image_2'];
							$data['ad_item_image_3'] = $row['ad_item_image_3'];
							$data['ad_item_youtube'] = $row['ad_item_youtube'];
							$data['ad_item_country_id'] = $row['ad_item_country_id'];
							$data['ad_item_state_id'] = $row['ad_item_state_id'];
							$data['ad_item_city_id'] = $row['ad_item_city_id'];
							$data['ad_item_alt_city'] = $row['ad_item_alt_city'];
							$data['ad_item_booked_by'] = $row['ad_item_booked_by'];
							$data['ad_item_booked_date'] = ($row['ad_item_booked_by'])? $row['ad_item_booked_date'] : '';
							$data['ad_item_sos'] = $row['ad_item_sos'];
							$data['ad_active'] = $row['ad_active'];
							
							// Now let's move the images
							for ( $x = 1; $x <= 3; $x++) {

								if ( $data['ad_item_image_'.$x] != '' ){

									// Windows
									//$tempDir = $_SERVER["DOCUMENT_ROOT"]."\\images\\temp\\";
									//$destDir = $_SERVER["DOCUMENT_ROOT"]."\\images\\market\\";
									// Linux
									$doc_root = $request->variable('DOCUMENT_ROOT', '', false, \phpbb\request\request_interface::SERVER);
									$tempDir = $doc_root."/images/market/ads/";
									$destDir = $doc_root."/ext/carum/carum/uploads/";
									
									// First the image
									if( file_exists ( $tempDir.$data['ad_item_image_'.$x] ) ){
										copy( $tempDir.$data['ad_item_image_'.$x], $destDir.$data['ad_item_image_'.$x] );
										//unlink( $tempDir.$data['ad_item_image_'.$x] );
										$images_to_delete[] = $data['ad_item_image_'.$x];
									}
									// Now the thumbnail
									$file_tmp = explode(".",$data['ad_item_image_'.$x]);
									$thumb_name = $file_tmp['0'].'_t.jpg';
									if( file_exists ( $tempDir.$thumb_name ) ){
										copy( $tempDir.$thumb_name, $destDir.$thumb_name );
										//unlink( $tempDir.$thumb_name );
										$images_to_delete[] = $thumb_name;
									}
								}
							}
						} // !$submit && $data['ad_id']
						
						// Mount the users select
						$s_users = $a_users = array();
						$sql = 'SELECT *
								FROM ' . USERS_TABLE . '
								WHERE user_type = 0
								ORDER BY username_clean ASC';
						$result = $db->sql_query($sql);
						$s_users = ( $data['ad_id'] == '0' )? '<option value="0" selected="selected">' . $user->lang['SELECT'] . '</option>' : '';
						while( $row = $db->sql_fetchrow($result) )
						{
								$selected = ( $row['user_id'] == $data['ad_user_id'] )? ' selected="selected"' : '';
								$s_users .= '<option value="' . $row['user_id'] . '"' . $selected . '>' . $row['username'] .'</option>' ;
								
								// We profit this while() to create the users array to use later
								$a_users[$row['user_id']] = $row['username'];
						}
						$db->sql_freeresult($result);
						
						$separator = "&nbsp;&nbsp;";
						$s_categories = '';
						$sql = 'SELECT * 
								FROM ' . $table_prefix . tables::MARKET_CATEGORIES_TABLE . '
								WHERE category_father = 0
								AND category_active = 1
								ORDER BY category_order';
						$result_0 = $db->sql_query($sql);
						$s_categories = ( $data['ad_category_id'] == '' )? '<option value="0" selected="selected">' . $user->lang['SELECT'] . '</option>' : '';
						while( $row_0 = $db->sql_fetchrow($result_0) )
						{
							$selected = ( $data['ad_category_id'] == $row_0['category_id'] )? ' selected="selected"' : '';
							$s_categories .= '<option value="' . $row_0['category_id'] . '"' . $selected . '>' . $user->lang['CATEGORY_'.$row_0['category_name']] . '</option>';
							if ( $this->categoryHasChild( $row_0['category_id'] ) ){
								$sql = 'SELECT * 
										FROM ' . $table_prefix . tables::MARKET_CATEGORIES_TABLE . '
										WHERE category_father = ' . $row_0['category_id'] . '
										AND category_active = 1
										ORDER BY category_order';
								$result_1 = $db->sql_query($sql);
								while( $row_1 = $db->sql_fetchrow($result_1) )
								{
									$selected = ( $data['ad_category_id'] == $row_1['category_id'] )? ' selected="selected"' : '';
									$s_categories .= '	<option value="' . $row_1['category_id'] . '"' . $selected . '>' . $user->lang['CATEGORY_'.$row_1['category_name']] . '</option>';
									if ( $this->categoryHasChild( $row_1['category_id'] ) ){
										$sql = 'SELECT * 
												FROM ' . $table_prefix . tables::MARKET_CATEGORIES_TABLE . '
												WHERE category_father = ' . $row_1['category_id'] . '
												AND category_active = 1 
												ORDER BY category_order';
										$result_2 = $db->sql_query($sql);
										while( $row_2 = $db->sql_fetchrow($result_2) )
										{
											$selected = ( $data['ad_category_id'] == $row_2['category_id'] )? ' selected="selected"' : '';
											$s_categories .= '	<option value="' . $row_2['category_id'] . '"' . $selected . '>' . $user->lang['CATEGORY_'.$row_2['category_name']] . '</option>';
											if ( $this->categoryHasChild( $row_2['category_id'] ) ){
												$sql = 'SELECT *
														FROM ' . $table_prefix . tables::MARKET_CATEGORIES_TABLE . '
														WHERE category_father = ' . $row_2['category_id'] . '
														AND category_active = 1
														ORDER BY category_order';
												$result_3 = $db->sql_query($sql);
												while( $row_3 = $db->sql_fetchrow($result_3) )
												{
													$selected = ( $data['ad_category_id'] == $row_3['category_id'] )? ' selected="selected"' : '';
													$s_categories .= '	<option value="' . $row_3['category_id'] . '"' . $selected . '>' . $user->lang['CATEGORY_'.$row_3['category_name']] . '</option>';
												}
											}
										}
									}
								}
							}
						}
						// Mount the asks section
						$sql = 'SELECT *
								FROM ' . $table_prefix . tables::MARKET_ADS_ASKS_TABLE . '
								WHERE ad_id = ' . $data['ad_id'] . '
								ORDER BY ask_id ASC';
						$result = $db->sql_query($sql);
						while( $row = $db->sql_fetchrow($result) )
						{
								$template->assign_block_vars('asks', array(
									'AD_ASK_USER'		=> $row['ask_user'] . ' - ' . $a_users[$row['ask_user']],
									'AD_ASK_ASK'		=> $row['ask'],
									'AD_ASK_ANSWER'		=> $row['answer'],
							));
						}
						$db->sql_freeresult($result);

						// Mount the bids section
						$sql = 'SELECT *
								FROM ' . $table_prefix . tables::MARKET_ADS_BIDS_TABLE . '
								WHERE ad_id = ' . $data['ad_id'] . '
								ORDER BY bid_date ASC';
						$result = $db->sql_query($sql);
						while( $row = $db->sql_fetchrow($result) )
						{
								$template->assign_block_vars('bids', array(
									'AD_BID_USER'		=> $row['user_id'] . ' - ' . $a_users[$row['user_id']],
									'AD_BID_DATE'		=> date("d/m/Y G:i:s", $row['bid_date']),
									'AD_BID_PRICE'		=> $row['price'],
							));
						}
						$db->sql_freeresult($result);
						
						// Mount the user book select
						$s_user_book = '';
						$s_user_book = ( $data['ad_item_booked_by'] == '0' )? '<option value="0" selected="selected">' . $user->lang['SELECT'] . '</option>' : '';
						foreach( $a_users as $user_id => $username )
						{
								$selected = ( $user_id == $data['ad_item_booked_by'] )? ' selected="selected"' : '';
								$s_user_book .= '<option value="' . $user_id . '"' . $selected . '>' . $username .'</option>' ;
						}

						$template->assign_vars(array(
										'CARUM_MARKET_ENABLED'	=> $config['carum_market_enabled'],
										'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
						
										'AD_ID'				=> $data['ad_id'],					
										'AD_USER_ID'		=> $data['ad_user_id'],
										'AD_STATE'			=> $data['ad_state'],
										'AD_TYPE_ITEM'		=> $data['ad_type_item'],
										'AD_CATEGORY_ID'	=> $data['ad_category_id'],
										'AD_ALLOW_AUCTION'	=> $data['ad_allow_auction'],
										'AD_ITEM_NAME'		=> $data['ad_item_name'],
										'AD_ITEM_SHORT_DESCRIPTION'	=> $data['ad_item_short_description'],
										'AD_ITEM_DESCRIPTION'		=> $data['ad_item_description'],
										'AD_ITEM_NEW_USED'			=> $data['ad_item_new_used'],
										'AD_ITEM_STATE'				=> $data['ad_item_state'],
										'AD_ITEM_ACTION'			=> $data['ad_item_action'],
										'AD_METHOD'					=> $data['ad_method'],
										'AD_ITEM_PRICE'				=> $data['ad_item_price'],
										'AD_ITEM_IS_MIN_BID'		=> $data['ad_item_is_min_bid'],
										'AD_ITEM_HIDE_PRICE'		=> $data['ad_item_hide_price'],
										'AD_ITEM_MIN_BID_PRICE'		=> $data['ad_item_min_bid_price'],
										'AD_ITEM_SHOW_MIN_BID'		=> $data['ad_item_show_min_bid'],
										'AD_ITEM_START_PRICE'		=> $data['ad_item_start_price'],
										'AD_ITEM_ACTUAL_PRICE'		=> $data['ad_item_actual_price'],
										'AD_ITEM_DATE_ACTUAL_PRICE' => $data['ad_item_date_actual_price'],
										'AD_ITEM_BUY_NOW_PRICE'		=> $data['ad_item_buy_now_price'],
										'AD_ITEM_SHIPMENT'			=> $data['ad_item_shipment'],
										'AD_ITEM_REGDATE'			=> $data['ad_item_regdate'],
										'AD_ITEM_VIEWS'				=> $data['ad_item_views'],
										'AD_ITEM_MAKER_ID'			=> $data['ad_item_maker_id'],
										'AD_ITEM_MODEL_ID'			=> $data['ad_item_model_id'],
										'AD_ITEM_PRODUCT_NAME'		=> $data['ad_item_product_name'],
										'AD_ITEM_ALT_MAKER_NAME'	=> $data['ad_item_alt_maker_name'],
										'AD_ITEM_MODEL_NAME'		=> $data['ad_item_model_name'],
										'AD_ITEM_ALT_MODEL_NAME'	=> $data['ad_item_alt_model_name'],
										'AD_ITEM_SERIAL'			=> $data['ad_item_serial'],
										'AD_ITEM_USABLE_WIDTH'		=> $data['ad_item_usable_width'],
										'AD_ITEM_UNITS_USABLE_WIDTH'	=> $data['ad_item_units_usable_width'],
										'AD_ITEM_WEIGHT'			=> $data['ad_item_weight'],
										'AD_ITEM_UNITS_WEIGHT'		=> $data['ad_item_units_weight'],
										'AD_ITEM_PRODUCTION_YEAR'	=> $data['ad_item_production_year'],
										'AD_ITEM_ADQUIRED_YEAR'		=> $data['ad_item_adquired_year'],
										'AD_ITEM_IMAGE_1'			=> $data['ad_item_image_1'],
										'AD_ITEM_IMAGE_2'			=> $data['ad_item_image_2'],
										'AD_ITEM_IMAGE_3'			=> $data['ad_item_image_3'],
										'AD_ITEM_YOUTUBE'			=> $data['ad_item_youtube'],
										'AD_ITEM_COUNTRY_ID'		=> $data['ad_item_country_id'],
										'AD_ITEM_STATE_ID'			=> $data['ad_item_state_id'],
										'AD_ITEM_CITY_ID'			=> $data['ad_item_city_id'],
										'AD_ITEM_ALT_CITY'			=> $data['ad_item_alt_city'],
										'AD_ITEM_BOOKED_BY'			=> $data['ad_item_booked_by'],
										'AD_ITEM_BOOKED_DATE'		=> $data['ad_item_booked_date'],
										'AD_ITEM_SOS'				=> $data['ad_item_sos'],
										'AD_ACTIVE'					=> $data['ad_active'],
										'S_USERS'					=> $s_users,
										'S_CATEGORIES'				=> $s_categories,
										'BACK_LINK'					=> adm_back_link($this->u_action . '&amp;action=list_ads'),
										
										'AJAX_COUNTRY_INFO' 		=> '/app.php/json_list_countries/COUNTRY_ID',
										'AJAX_STATE_INFO' 			=> '/app.php/json_list_states/COUNTRY_ID/STATE_ID',
										'AJAX_CITY_INFO'	 		=> '/app.php/json_list_cities/COUNTRY_ID/STATE_ID/CITY_ID',
										
										'S_USER_BOOK'				=> $s_user_book,
										
										'IFRAME'					=> '/market/image/request',
										'IMAGES_TO_DELETE'			=> ( sizeof( $images_to_delete ) )? implode( '|', $images_to_delete ) : '',

										'ACTION'		=> 'edit_ad',
										'U_ACTION'		=> $this->u_action . '&amp;ad_id=' . $data['ad_id'],
						));

						break;
				}
		}

		$template->assign_vars([
			'DEBUG'		=> $debug,
		]);

	}
	
	public function categoryHasChild($category_id)
	{
		global $db, $table_prefix;
		
		$num_childs = '';
		$sql = 'SELECT count(*) as num_categories
				FROM ' . $table_prefix . tables::MARKET_CATEGORIES_TABLE . '
				WHERE category_father = ' . $category_id;
		$result = $db->sql_query($sql);
		$num_childs = $db->sql_fetchfield('num_categories');
		$db->sql_freeresult($result);
		return $num_childs;

	}
	public function requestimage(){

		// Send all data to the template file
		return $this->helper->render('market_request_image.html', $this->user->lang('MARKET_TITLE'));
	}
	
}
