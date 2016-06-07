<?php
/**
*
* @package Carum - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace carum\market\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use carum\carum\includes\php\functionslocation;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	public $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\db\driver\driver */
	public $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\extension\manager "Extension Manager" */
	protected $ext_manager;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Tables prefix */
	public $table_prefix;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth			Auth object
	* @param \phpbb\cache\service				$cache			Cache object
	* @param \phpbb\config\config               $config         Config object
	* @param \phpbb\controller\helper           $helper         Controller helper object
	* @param \phpbb\db\driver\driver			$db				Database object
	* @param \phpbb\request\request				$request		Request object
	* @param \phpbb\template\template           $template       Template object
	* @param \phpbb\user                        $user           User object
	* @param \phpbb\extension\manager			$ext_manager		Extension manager object
	* @param string                             $phpbb_root_path	phpBB root path
	* @param string                             $php_ext        	phpEx
	* @param string								$table_prefix		Name of the table prefix
	* @access public
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\extension\manager $ext_manager,
		$phpbb_root_path,
		$php_ext,
		$table_prefix)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->helper = $helper;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->ext_manager	 = $ext_manager;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
 	}

	/**
	* Event listener
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'	=> 'load_language_on_setup',
		);
	}

	
	public function load_language_on_setup($event)
	{

		// Need to ensure the countries, states, cities are cached on page load
		functionslocation::cache_countries();
		functionslocation::cache_states();
		functionslocation::cache_cities(); 

		$lang_set_ext = $event['lang_set_ext'];

		$lang_set_ext[] = array('ext_name' => 'carum/carum','lang_set' => 'market_categories',);
		$lang_set_ext[] = array('ext_name' => 'carum/market','lang_set' => 'common',);
		// We need this lang for the functionslocation use
		$lang_set_ext[] = array('ext_name' => 'carum/carum','lang_set' => 'common',);

		$event['lang_set_ext'] = $lang_set_ext;  
	}

}
