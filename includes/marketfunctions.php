<?php
/**
*
* @package Carum - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace carum\market\includes;

use carum\carum\includes\php\tables;

class marketfunctions
{

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string table_prefix */
	protected $table_prefix;
  
	/** @var \phpbb\extension\manager "Extension Manager" */
	protected $ext_manager;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config			$config				Config object
	 * @param \phpbb\controller\helper		$helper				Controller helper object
	 * @param \phpbb\cache\service			$cache				Cache object
	 * @param \phpbb\db\driver\driver		$db					Database object
	 * @param \phpbb\template\template		$template			Template object
	 * @param \phpbb\user					$user				User object
	 * @param string						$table_prefix		phpBB tables prefix 
	 * @param \phpbb\extension\manager		$ext_manager		Extension manager object
	 * @param \phpbb\path_helper			$path_helper		Path helper object
	 */
	public function __construct(
			\phpbb\config\config $config,
			\phpbb\controller\helper $helper,
			\phpbb\cache\service $cache,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\template\template $template,
			\phpbb\user $user,
			$table_prefix,
			\phpbb\extension\manager $ext_manager,
			\phpbb\path_helper $path_helper)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->cache = $cache;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->table_prefix = $table_prefix; 
		$this->ext_manager	 = $ext_manager;
		$this->path_helper	 = $path_helper;

		$this->ext_path = $this->ext_manager->get_extension_path('carum/market', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}

	/**
	 * Get list_flags
	 *
	 * @param int $flag_id
	 * @return string flag_options
	 */

	public function categoryHasChild($category_id)
	{
		$num_childs = '';
		$sql = 'SELECT count(*) as num_categories
				FROM ' . $this->table_prefix . tables::MARKET_CATEGORIES_TABLE . '
				WHERE category_father = ' . $category_id;
		$result = $this->db->sql_query($sql);
		$num_childs = $this->db->sql_fetchfield('num_categories');
		$this->db->sql_freeresult($result);
		return $num_childs;

	}
	/**
	 * Get json_list_makers
	 *
	 * @param int $maker_id
	 *
	 * @return json string makers
	 *
	 */
	public function json_list_makers( $maker_id )
	{
		global $user;
		
		$sql = 'SELECT *
				FROM ' . $this->table_prefix . tables::MARKET_MAKERS_TABLE . '
				ORDER BY maker_name';
		$result = $this->db->sql_query($sql);

		$jsondata = array();
		if( $maker_id == '-1') $jsondata['-1'] = '- '.$this->user->lang['SELECT'].' -';
		$jsondata['0'] = '-> '.$this->user->lang['NOT_IN_LIST'].' <-';
		if( $result->num_rows > 0 ) {
			while( $row = $this->db->sql_fetchrow($result) ) {
				//$jsondata["data"]["users"][] es un array no asociativo. Tendremos que utilizar JSON_FORCE_OBJECT en json_enconde
				//si no queremos recibir un array en lugar de un objeto JSON en la respuesta
				//ver http://www.php.net/manual/es/function.json-encode.php para más info
				$jsondata[$row['maker_id']] = $row['maker_name'];
			}
		}
		//asort($jsondata);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($jsondata, JSON_FORCE_OBJECT);
		exit();
	}

	/**
	 * Get json_list_states
	 *
	 * @param int $country_id
	 * @param int $state_id
	 *
	 * @return json string states_options
	 *
	 */
	public function json_list_models( $maker_id, $model_id = false )
	{
		global $user;

		$sql = 'SELECT *
				FROM ' . $this->table_prefix . tables::MARKET_MODELS_TABLE . '
				WHERE maker_id = ' . $maker_id . '
				ORDER BY model_name, version';
		$result = $this->db->sql_query($sql);

		$jsondata = array();
		if( $model_id == '-1') $jsondata['-1'] = '- '.$this->user->lang['SELECT'].' -';
		$jsondata['0'] = '-> '.$this->user->lang['NOT_IN_LIST'].' <-';
		if( $result->num_rows > 0 ) {
			while( $row = $this->db->sql_fetchrow($result) ) {
				//$jsondata["data"]["users"][] es un array no asociativo. Tendremos que utilizar JSON_FORCE_OBJECT en json_enconde
				//si no queremos recibir un array en lugar de un objeto JSON en la respuesta
				//ver http://www.php.net/manual/es/function.json-encode.php para más info
				$jsondata[$row['model_id']] = $row['model_name'].(($row['version'])? ' - '.$row['version'] : '');
			}
		}
		//asort($jsondata);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($jsondata, JSON_FORCE_OBJECT);
		exit();

	}
}
