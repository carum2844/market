<?php
/**
*
* @package phpBB Extension - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace carum\market\controller;

use phpbb\exception\http_exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use carum\carum\includes\php\tables;

/**
* Main controller
*/
class main_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\auth\auth */
	protected $auth;
	
	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\user */
	public $user;

	/** @var \phpbb\cache\service */
	public $cache;  
	
	/** @var \phpbb\extension\manager "Extension Manager" */
	protected $ext_manager;
  
	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string Table prefix */
	protected $table_prefix;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\config\config               $config					Config object
	* @param \phpbb\auth\auth					$auth					phpBB auth object
	* @param \phpbb\db\driver\driver			$db						Database object
	* @param \phpbb\pagination					$pagination				Pagination object
	* @param \phpbb\controller\helper			$helper					Controller helper object
	* @param \phpbb\request\request				$request				Request object
	* @param \phpbb\template\template			$template				Template object
	* @param ContainerInterface					$container				Service container interface
	* @param \phpbb\user						$user					User object
	* @param \phpbb\cache\service				$cache					Cache object
	* @param \phpbb\extension\manager			$ext_manager			Extension manager object
	* @param string								$phpbb_root_path		phpBB root path
	* @param string								$table_prefix			Name of the table prefix
	* @access public 
	*/

	public function __construct(
			\phpbb\config\config $config,
			\phpbb\auth\auth $auth,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\pagination $pagination,
			\phpbb\controller\helper $helper,
			\phpbb\request\request $request,
			\phpbb\template\template $template,
			ContainerInterface $container,
			\phpbb\user $user,
			\phpbb\cache\service $cache,
			\phpbb\extension\manager $ext_manager,
			$phpbb_root_path,
			$table_prefix)
	{
		$this->config = $config;
		$this->auth = $auth;
		$this->db = $db;
		$this->pagination = $pagination;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->container = $container;
		$this->user = $user;
		$this->cache = $cache;
		$this->ext_manager	 = $ext_manager; 
		$this->phpbb_root_path = $phpbb_root_path;
		$this->table_prefix = $table_prefix;

		$this->ext_path = $this->ext_manager->get_extension_path('carum/market', true);
		
		// Let the template know wich css and js we want to include
		$this->template->assign_vars(array(
			'S_MARKET'        => $this->config['carum_market_enabled'],
		));
		
		$market_link = append_sid("{$this->phpbb_root_path}".$this->user->lang('MARKET_LINK'));
		$this->template->assign_vars(array(
			'U_MARKET'	=> $market_link,
		));

		$this->template->assign_vars(array(
			'S_IS_ADMIN'		=> ($this->auth->acl_gets('a_')) ? true : false,
			'S_IS_MODERATOR'	=> ($this->auth->acl_gets('m_')) ? true : false,
		));
	}

	/**
	 * Display the main page with the pressnotes
	 *
	 * @access public
	 */
	public function displaylist(){
		
		$time = time();
		$search_limit = $this->request->variable('search_limit', 15);
		$limit = '10';
		$start = $this->request->variable('start', '0');
		$pagination_url = $this->u_action;
		// Category selection
		$category_selected = $this->request->variable('category_selected', '0');
		$this->template->assign_vars(array(
			'CATEGORY_SELECTED'	=>	$category_selected,
		));

		// Categories array
		$categories = array();
		$sql = 'SELECT category_id, category_name FROM ' .  $this->table_prefix . tables::OFFERS_CATEGORIES_TABLE . ' WHERE active = 1';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result) ) {
			$categories[$row['category_id']] = $row['category_name'];
		}
		$this->db->sql_freeresult($result);

		// Categories select
		$s_categories_options = '';
		if ($category_selected == '0'){
			$s_categories_options = '<option value="0" selected="selected">'.$this->user->lang['SELECT'].'</option>';
		}else{
			$s_categories_options = '<option value="0" selected="selected">'.$this->user->lang['ALL'].'</option>';
		}
		foreach ($categories as $key => $category_name){
			$s_categories_options .= '<option value="'.$key.'"';
			if($category_selected == $key) $s_categories_options .= 'selected="selected"';
			$s_categories_options .= '>'.$category_name.'</option>';
		}
		$this->template->assign_vars(array(
			'S_CATEGORY_OPTIONS'	=>	$s_categories_options,
		));

		// Lets show the selected ads
		$sql = 'SELECT *
				FROM ' .  $this->table_prefix . tables::MARKET_ADS_TABLE;
		$filters = array();
		$filters[] = ' ad_active = 1 ';
		if($category_selected != '0'){
			$filters[] = ' ad_category_id = "'.$category_selected.'"';
		}
		$sql .= ' WHERE ';
		$sql .= implode(' AND ', $filters);
		$sql .= " ORDER BY ad_id DESC";

		// Create pagination logic
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrowset($result);
		$total_records = (int) count($row);
		$this->db->sql_freeresult($result);
		unset($row);
		$pagination = $this->container->get('pagination');
		$base_url = $this->user->lang['MARKET_LINK'];
		$base_url = append_sid($base_url);	
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_records, $limit, $start);

		// Send to template the number of ads and the link to create a new one
		$this->template->assign_vars(array(
			'TOTAL_RECORDS'		=> $total_records,
			'MARKET_REQUEST'	=> '/'.$this->user->lang['MARKET_LINK'].'/'.$this->user->lang['MARKET_LINK_ADD_AD'],
		));
		
		// Generate template array
		$result = $this->db->sql_query_limit($sql, $limit, $start);
		while ($row = $this->db->sql_fetchrow($result) ) {
			
			$sql = 'SELECT username FROM ' .  USERS_TABLE . ' WHERE user_id = ' . $row['ad_user_id'];
			$result_temp = $this->db->sql_query($sql);
			$username = $this->db->sql_fetchfield('username');
			$this->db->sql_freeresult($result_temp);
		
			$this->template->assign_block_vars('ads', array(
				'AD_ID' 			=> $row['ad_id'],
				'AD_USER_ID'		=> $row['ad_user_id'],
				'AD_USER_NAME'		=> $username,
				'AD_CATEGORY_ID' 	=> $row['ad_category_id'],
				'AD_METHOD' 		=> $row['ad_method'],
				'AD_ITEM_ACTION' 	=> $row['ad_item_action'],
				'AD_ITEM_NAME' 		=> (strlen($row['ad_item_name']) > 50) ? censor_text(substr($row['ad_item_name'], 0, 50)."...") : censor_text($row['ad_item_name']),
				'AD_ITEM_SHORT_DESCRIPTION' => (strlen($row['ad_item_short_description']) > 50) ? censor_text(substr($row['ad_item_short_description'], 0, 50)."...") : censor_text($row['ad_item_short_description']),
				'AD_HIDE_PRICE'		=> $row['ad_item_hide_price'],
				'AD_ITEM_PRICE' 	=> number_format($row['ad_item_price'], 0, ',', '.'),
				'BUY_NOW_PRICE' 	=> $row['ad_item_buy_now_price'],
				'AD_ITEM_IS_MINBID'	=> $row['ad_item_is_min_bid'],
				'ACTUAL_PRICE'		=> $row['ad_item_actual_price'],
				'AD_ITEM_IMAGE' 	=> ($row['ad_item_image_1'])? $row['ad_item_image_1'] : 'no-picture.png',
				'AD_ITEM_STATE_ID'	=> $row['ad_item_state_id'],
				'AD_ITEM_LINK'		=> '/'.$this->user->lang('MARKET_LINK').'/'.$this->user->lang('MARKET_LINK_DISPLAY_ITEM').'/'.$row['ad_id'],
				));
		}
		$this->db->sql_freeresult($result);

		return $this->helper->render('list_body.html', $this->user->lang('MARKET_TITLE'));
	}

	public function displayitem( $item_id )
	{

		$sql = 'SELECT * FROM ' .  $this->table_prefix . tables::PRESS_NOTES_TABLE . ' WHERE pressnote_id = ' . $item_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$data = array(
					'pressnote_id'		=> $row['pressnote_id'],
					'country_id'		=> $row['pressnote_country_id'],
					'title'				=> $row['pressnote_title'],
					'description'		=> $row['pressnote_description'],
					'category'			=> $row['pressnote_category'],
					'starts'			=> $row['pressnote_date'],
					'url'				=> $row['pressnote_url'],
					'visits'			=> $row['pressnote_visits'],
		);
		$this->db->sql_freeresult($result);

		// First we add a visit to the pressnote
		$max_visitas = $data['visits'] + 1;
		$sql = 'UPDATE ' .  $this->table_prefix . tables::PRESS_NOTES_TABLE . ' SET pressnote_visits = ' . $max_visitas . ' WHERE pressnote_id = ' . $item_id;
		$result = $this->db->sql_query($sql);


		$this->template->assign_vars(array(
			'PRESSNOTE_NUM'		=> $data['pressnote_id'],
			'COUNTRY_ID'		=> $data['country_id'],
			'CATEGORY_NAME'		=> $data['category'],
			'TITLE' 			=> $data['title'],
			'DESCRIPTION'		=> $data['description'],
			'IFRAME'			=> '/'.$this->user->lang['LINK_PRESSNOTE'].'/pressnotedescription/' . $data['pressnote_id'],
			'STARTS'			=> $this->user->format_date($data['starts'], 'D, j M Y'),
			'URL'				=> $data['url'],
			'VISITS'			=> $data['visits'],

		));

		// Send all data to the template file
		return $this->helper->render('list_body.html', $data['title'].' - '.$data['company']);
	}
	public function displaydescription( $item_id ){
		//$item_id=30;
		// Read the description field
		$sql = 'SELECT pressnote_description FROM ' .  $this->table_prefix . tables::PRESS_NOTES_TABLE . ' WHERE pressnote_id = ' . $item_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$description = $row['pressnote_description'];
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'DESCRIPTION'			=> html_entity_decode($description),

		));
		// Send all data to the template file
		return $this->helper->render('pressnote_description.html', $this->user->lang('PRESSNOTE_TITLE'));
	}
	public function request(){

		$today = time();
		$error = array();
		// Data entry
		$this->user->add_lang_ext('carum/carum', 'countries');
		$data = array(
				'pressnote_id'		=> request_var('pressnote_id', '0'),
				'country_id'		=> request_var('country_id', $this->config['default_country']),
				'user_id'			=> request_var('user_id', '0'),
				'date'				=> request_var('date', date("d/m/Y", strtotime(date("m/d/Y"))), true),
				'title'				=> utf8_normalize_nfc(request_var('title', '', true)),
				'description'		=> utf8_normalize_nfc(request_var('description', '', true)),
				'emissor'			=> utf8_normalize_nfc(request_var('emissor', '', true)),
				'url'				=> utf8_normalize_nfc(request_var('url', '', true)),
				'category'			=> utf8_normalize_nfc(request_var('category', '', true)),
				'visits'			=> utf8_normalize_nfc(request_var('visits', '', true)),
				'approved'			=> request_var('approved', '0'),
				
		);
		$submit	= $this->request->is_set_post('submit');
		

		// If it is submit, check & insert in database
		if ($submit){

			
			if ( $data['country_id'] == '0' )
			{
				$error[] = $this->user->lang['COUNTRY_NEEDED'];
			}

			if (empty($data['title']))
			{
				$error[] = $this->user->lang['TITLE_NEEDED'];
			}

			if (empty($data['description']))
			{
				$error[] = $this->user->lang['DESCRIPTION_NEEDED'];
			}

			if (empty($data['date']))
			{
				$error[] = $this->user->lang['DATE_NEEDED'];
			}
			else
			{
				$sep = '[\/\-\.]';
				//$match = '/^(((0? [1-9] |1\d|2 [0-8] ){$sep}(0? [1-9] |1 [012] )|(29|30){$sep}(0? [13456789] |1 [012] )|31{$sep}(0? [13578] |1 [02] )){$sep}(19| [2-9] \d)\d{2}|29{$sep}0?2{$sep}((19| [2-9] \d)(0 [48] | [2468]  [048] | [13579]  [26] )|(( [2468]  [048] | [3579]  [26] )00)))$/';
				//$match = '#^(((0?[1-9]|1d|2[0-8]){$sep}(0?[1-9]|1[012])|(29|30){$sep}(0?[13456789]|1[012])|31{$sep}(0?[13578]|1[02])){$sep}(19|[2-9]d)d{2}|29{$sep}0?2{$sep}((19|[2-9]d)(0[48]|[2468][048]|[13579][26])|(([2468][048]|[3579][26])00)))$#';
				//$match = '/^(19|20)[0-9]{2}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/';
				$match = '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#';
				//$match = '/^\d{1,2}\/\d{1,2}\/\d{4}$/';
				if (!preg_match($match, $data['date']))
					{
						$error[] = $this->user->lang['DATE_BAD'];
					}
			}


			/*
			if (empty($data['promo_code']))
			{
				$error[] = $this->user->lang['PROMO_CODE_NEEDED'];
			}
			*/
			/*
			if( empty($data['colectivo']) )
			{
				$error[] = $this->user->lang['COLECTIVE_NEEDED'];
			}
			*/
			if (!sizeof($error)){
			
				// Insert the pressnote
				$sql = 'SELECT max(pressnote_id) + 1 as pressnote_id FROM ' .  $this->table_prefix . tables::PRESS_NOTES_TABLE;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$pressnote_id = $row['pressnote_id'];
				if (!isset($pressnote_id)){
					$data['pressnote_id'] = '1';
				}else{
					$data['pressnote_id'] = $pressnote_id;
				}
				
				// Dates
				list($day, $month, $year) = split('/', $data['date']);
				$date_timeStamp = mktime(23, 59, 59, $month, $day, $year);
				
				
				$sql = 'INSERT INTO ' .  $this->table_prefix . tables::PRESS_NOTES_TABLE . ' SET
						pressnote_id = '.$data['pressnote_id'].',
						pressnote_country_id = '.$data['country_id'].',
						pressnote_user_id = '.$data['user_id'].',
						pressnote_date = '.$today.',
						pressnote_title = "'.$this->db->sql_escape($data['title']).'",
						pressnote_description = "'.$this->db->sql_escape($data['description']).'",
						pressnote_emissor = "'.$this->db->sql_escape($data['emissor']).'",
						pressnote_url = "'.$this->db->sql_escape($data['url']).'",
						pressnote_category = "'.$this->db->sql_escape($data['category']).'",
						pressnote_visits = "0",
						pressnote_aproved = "1"';
				$result = $this->db->sql_query($sql);

				// Back to the list
				$message = $this->user->lang['PRESSNOTE_MESSAGE_OK'];
				$message .= '<br /><br />';
				$message .= '<a href="' . append_sid("/".$this->user->lang['PRESSNOTES_LINK_PRESSNOTES']) . '">';
				$message .= $this->user->lang['GO_PRESSNOTES'];
				$message .= '</a>';
				trigger_error($message);
			} //!sizeof($error)
		} //if submit

		// Category selection
		$category_selected = $this->request->variable('category_selected', '0');
		$this->template->assign_vars(array(
			'CATEGORY_SELECTED'	=>	$category_selected,
		));

		// Categories array
		$categories = array();
		//$sql = 'SELECT category_id, category_name FROM ' .  $this->table_prefix . tables::OFFERS_CATEGORIES_TABLE . ' WHERE active = 1';
		$sql = 'SELECT DISTINCT pressnote_category FROM ' .  $this->table_prefix . tables::PRESS_NOTES_TABLE . ' where pressnote_category <> "" GROUP BY pressnote_category';
		$result = $this->db->sql_query($sql);
		$i = 1;
		while ($row = $this->db->sql_fetchrow($result) ) {
			$categories[$row['pressnote_category']] = $row['pressnote_category'];
			$i++;
		}
		$this->db->sql_freeresult($result);

		$s_categorias_options = '';
		// Categories select
		$s_categories_options = '';
		if ($category_selected == '0'){
			$s_categories_options = '<option value="0" selected="selected">'.$this->user->lang['PRESSNOTE_SELECT'].'</option>';
		}else{
			$s_categories_options = '<option value="0" selected="selected">'.$this->user->lang['ALL'].'</option>';
		}
		foreach ($categories as $key => $category_name){
			
			$s_categories_options .= '<option value="'.$key.'"';
			if($category_selected == $key) $s_categories_options .= 'selected="selected"';
			$s_categories_options .= '>'.$category_name.'</option>';
		}
		
		$this->template->assign_vars(array(
			'S_CATEGORY_OPTIONS'	=>	$s_categories_options,
		));


		// Determine board url - we may need it later
		$board_url = generate_board_url() . '/';
		// This path is sent with the base template paths in the assign_vars()
		// call below. We need to correct it in case we are accessing from a
		// controller because the web paths will be incorrect otherwise.
		$phpbb_path_helper = $this->container->get('path_helper');
		$corrected_path = $phpbb_path_helper->get_web_root_path();
		$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : $corrected_path;

		$this->template->assign_vars(array(
		
			'CARUM_PRESSNOTES'		=> '1',
			'ERROR'				=> (sizeof($error)) ? implode('<br />', $error) : '',

			'PRESSNOTE_ID'			=> $data['pressnote_id'],

			'COUNTRY_ID'			=> $data['country_id'],
			'S_COUNTRY' 			=> functionslocation::get_countries_option_list($this->config['default_country']),
			'USER_ID'				=> $data['user_id'],
			'DATE'					=> $data['date'],
			'TITLE'					=> $data['title'],
			'DESCRIPTION'			=> $data['description'],
			'EMISSOR'				=> $data['emissor'],
			'URL'					=> $data['url'],
			'CATEGORY'				=> $data['category'],
			'VISITS'				=> $data['visits'],
			'APPROVED'				=> $data['approved'],
			
			)
		);
		
		
		// Send all data to the template file
		return $this->helper->render('request_body.html', $this->user->lang('PRESSNOTE_TITLE'));
	}
	public function requestimage(){

		// Send all data to the template file
		return $this->helper->render('market_request_image.html', $this->user->lang('PRESSNOTE_TITLE'));
	}
}
