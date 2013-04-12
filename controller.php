<?php
/**
 * Module for managing banner ads, banner ad regions and rendering banners to view, along with Javascript rotation functionality
 *
 * @package Modules
 * @subpackage BannerAds
 * @author Peter Epp
 * @copyright Copyright (c) 2009 Peter Epp (http://teknocat.org)
 * @license GNU Lesser General Public License (http://www.gnu.org/licenses/lgpl.html)
 * @version 2.0 $Id: controller.php 14241 2011-09-12 21:59:40Z teknocat $
 */
class BannerAdsManager extends AbstractModuleController {
	/**
	 * List of dependencies for this module. It requires TinyMce for use of the file manager for uploading banner ads as that makes it much easier to manage
	 *
	 * @var array
	 */
	protected $_dependencies = array('Authenticator','index' => 'TinyMce', 'edit' => 'TinyMce','new' => 'TinyMce');
	/**
	 * List of special actions that require an id
	 *
	 * @var array
	 */
	protected $_actions_requiring_id = array('manage_region_ads');
	/**
	 * Models used by this module
	 *
	 * @var array
	 */
	protected $_models = array(
		'BannerAd' => 'BannerAd',
		'AdRegion' => 'AdRegion'
	);
	/**
	 * Nice labels for attributes that don't translate to the desired label with AkInflector::humanize()
	 *
	 * @var string
	 */
	protected $_attr_labels = array(
		'url' => 'Full Link URL'
	);
	/**
	 * Place to cache the ad region count
	 *
	 * @var int
	 */
	private $_ad_region_count;
	/**
	 * Add a var for the banner ad index view that indicates whether or not regions exist
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_index() {
		$this->register_css(array('filename' => 'banner-ads.css', 'media' => 'screen'));
		$this->register_css(array('filename' => 'ie.css', 'media' => 'screen'),true);
		if ($this->user_can_edit()) {
			$this->register_js('footer','edit.js');
		}
		$this->set_view_var('regions_exist',($this->ad_region_count() > 0));
		if ($this->action() == 'index_ad_region') {
			$this->title('Manager Ad Regions');
		}
		$regions = $this->AdRegion->find_all(array('name' => 'ASC'));
		foreach ($regions as $region) {
			$banner_ads[$region->id()] = $region->banner_ads(array('region_id' => 'ASC', 'sort_order' => 'ASC', 'title' => 'ASC'));
			foreach ($banner_ads[$region->id()] as $index => $banner_ad) {
				if ($banner_ads[$region->id()][$index]->is_active() && !file_exists(SITE_ROOT.$banner_ads[$region->id()][$index]->image())) {
					$banner_ads[$region->id()][$index]->set_is_active(0);
					$banner_ads[$region->id()][$index]->save();
					Session::flash('user_message','The banner ad "'.$banner_ads[$region->id()][$index]->title().'" has been deactivated since it\'s image file ('.$banner_ads[$region->id()][$index]->image().') cannot be found.');
				}
			}
		}
		$this->set_view_var('regions',$regions);
		$this->set_view_var('banner_ads',$banner_ads);
		$this->render();
	}
	/**
	 * Ensure that the abstract index method is called for indexing the ad regions instead of this module's custom action_index method, which is specially
	 * customized for the normal index action for managing the banner ads.
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_index_ad_region() {
		$this->register_css(array('filename' => 'banner-ads.css', 'media' => 'screen'));
		$this->title("Manage Regions");
		parent::action_index();
	}
	/**
	 * Render all active banner ads into variables named per the regions they are set to, eg. $region_default_region
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_secondary() {
		if (LibraryLoader::is_available('JqueryCycle')) {
			// Load jQuery Cycle library if present
			LibraryLoader::load('JqueryCycle');
		} else {
			// Fall back on the copy included with the module:
			$this->register_js('footer','jquery.cycle.all.min.js');
		}
		$this->register_js('footer','banner_ad_rotator.js');
		$regions = $this->AdRegion->find_all();
		$banners = $this->BannerAd->find_all_by('is_active','1',array('sort_order' => 'ASC'));
		$banners_by_region = array();
		if (!empty($banners)) {
			foreach ($banners as $banner) {
				if (file_exists(SITE_ROOT.$banner->image())) {
					$banners_by_region[$banner->region_id()][] = $banner;
				}
			}
		}
		if (!empty($regions)) {
			foreach ($regions as $region) {
				$region_content = '';
				$var_name = $region->variable_name();
				$region_div_id = 'ad-region-'.implode('-',explode('_',$var_name));
				$region_content = '';
				if (!empty($banners_by_region[$region->id()])) {
					$view_filename = 'banner_ads/views/ad_regions/'.$var_name.'.php';
					$view_vars = array(
						'ad_region'     => $region,
						'banner_ads'    => $banners_by_region[$region->id()],
						'region_div_id' => $region_div_id
					);
					if (Crumbs::file_exists_in_load_path($view_filename)) {
						$region_content = Crumbs::capture_include($view_filename,$view_vars);
					} else {
						$region_content = Crumbs::capture_include('banner_ads/views/ad_regions/generic.php',$view_vars);
					}
				}
				$this->set_view_var('ad_region_'.$var_name,$region_content);
			}
		}
	}
	/**
	 * Helper method that counts the number of banner ads that are active
	 *
	 * @param array $banner_ads Array of BannerAd model instances
	 * @return void
	 * @author Peter Epp
	 */
	public function active_banners($banner_ads) {
		$active_banners = 0;
		if (!empty($banner_ads)) {
			foreach ($banner_ads as $banner_ad) {
				if ($banner_ad->is_active()) {
					$active_banners += 1;
				}
			} 
		}
		return $active_banners;
	}
	/**
	 * Custom edit action that sets a view var with an array of the regions for use with the select form field helper
	 *
	 * @param string $mode 'new' or 'edit', defaults to 'edit' per abstract controller
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_edit($mode = 'edit') {
		parent::action_edit($mode);
		$this->register_js('footer','edit.js');
		if ($this->action() == 'edit' || $this->action() == 'new') {
			$regions = $this->AdRegion->find_all(array('name' => 'ASC'));
			$this->set_view_var('region_select_list',Form::models_to_select_data_set($regions,'id','name_with_specs'));
			$this->set_view_var('page_select_options',$this->get_page_select_options());
			$link_target_options = array(
				array('value' => '_top', 'label' => 'Same Window'),
				array('value' => '_blank', 'label' => 'New Window')
			);
			$this->set_view_var('link_target_options',$link_target_options);
		}
	}
	/**
	 * Custom permission check for creating banner ads that returns false if no regions exist
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function user_can_create() {
		if ($this->ad_region_count() < 1) {
			return false;
		}
		return parent::user_can('create');
	}
	/**
	 * Return the record count of ad regions. Cache the value on first retrieval to minimize DB queries
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function ad_region_count() {
		if (empty($this->_ad_region_count)) {
			$this->_ad_region_count = $this->AdRegion->record_count();
		}
		return $this->_ad_region_count;
	}
	/**
	 * For the edit action, add the standalone TinyBrowser JS script to the footer
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_compile_footer() {
		if ($this->is_primary() && ($this->action() == 'index' || $this->action() == 'edit' || $this->action() == 'new')) {
			$this->Biscuit->append_view_var('footer',$this->Biscuit->ExtensionTinyMce()->render_standalone_tb_browser_script());
		}
	}
	/**
	 * Add banner ad management links to admin menu for users with permission
	 *
	 * @param object $caller 
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_build_admin_menu($caller) {
		$menu_items = array();
		if ($this->user_can_create()) {
			$menu_items['Insert New Ad'] = array(
				'url' => $this->url('new'),
				'ui-icon' => 'ui-icon-plus'
			);
		}
		if ($this->user_can_index()) {
			$menu_items['Manage Ads'] = array(
				'url' => $this->url(),
				'ui-icon' => 'ui-icon-wrench'
			);
		}
		if ($this->user_can_index_ad_region()) {
			$menu_items['Manage Regions'] = array(
				'url' => $this->url('index_ad_region'),
				'ui-icon' => 'ui-icon-wrench'
			);
		}
		if (!empty($menu_items)) {
			$caller->add_admin_menu_items('Banner Ads',$menu_items);
		}
	}
	/**
	 * Add help menu link
	 *
	 * @param string $caller 
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_build_help_menu($caller) {
		$caller->add_help_for('BannerAds');
	}
	/**
	 * Return a data set array formatted for use with the form select helper for all the pages in the site.
	 *
	 * @param int $parent_id 
	 * @param int $max_user_level 
	 * @param int $indent 
	 * @return array|null
	 * @author Peter Epp
	 */
	private function get_page_select_options() {
		$all_pages = $this->Biscuit->ExtensionNavigation()->all_pages();
		$sorted_pages = $this->Biscuit->ExtensionNavigation()->sort_pages($all_pages);
		$page_options = '';
		$page_options .= $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, 0, Navigation::WITH_CHILDREN, 'modules/banner_ads/views/page_select_list_options.php');
		$other_menus = $this->Biscuit->ExtensionNavigation()->other_menus();
		if (!empty($other_menus)) {
			foreach ($other_menus as $menu) {
				$page_options .= $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, $menu->id(), Navigation::WITH_CHILDREN, 'modules/banner_ads/views/page_select_list_options.php');
			}
		}
		$page_options .= $this->Biscuit->ExtensionNavigation()->render_pages_hierarchically($sorted_pages, NORMAL_ORPHAN_PAGE, Navigation::WITH_CHILDREN, 'modules/banner_ads/views/page_select_list_options.php');
		return $page_options;
	}
	/**
	 * Return the select list data set for the "Rotation mode" field
	 *
	 * @return array
	 * @author Peter Epp
	 */
	public function rotation_mode_select_options() {
		return array(
			array('value' => 'no-rotate', 'label' => 'None, I\'ll customize it myself'),
			array('value' => 'fade', 'label' => 'Fade'),
			array('value' => 'scrollLeft', 'label' => 'Slide Left'),
			array('value' => 'scrollRight', 'label' => 'Slide Right')
		);
	}
	/**
	 * Return the select list data set for the "Rotation interval" field
	 *
	 * @return array
	 * @author Peter Epp
	 */
	public function rotation_interval_select_options() {
		$rotation_intervals = array();
		for ($i=6;$i <= 20;$i++) {
			$rotation_intervals[] = array('value' => ($i*1000), 'label' => $i.' Seconds');
		}
		return $rotation_intervals;
	}
	/**
	 * Run migrations required for module to be installed properly
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public static function install_migration() {
		DB::query("CREATE TABLE IF NOT EXISTS `ad_regions` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL,
		  `variable_name` varchar(255) NOT NULL default '',
		  `width` int(4) NOT NULL default '0',
		  `height` int(4) NOT NULL default '0',
		  `rotation_mode` varchar(10) NOT NULL default 'fade',
		  `rotation_interval` int(11) default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		DB::query("CREATE TABLE IF NOT EXISTS `banner_ads` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL,
		  `url` varchar(255) default NULL,
		  `link_target` varchar(10) default '_top',
		  `image` text NOT NULL,
		  `region_id` int(11) NOT NULL default '0',
		  `is_active` tinyint(1) NOT NULL default '1',
		  `sort_order` int(11) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `region_id` (`region_id`),
		  CONSTRAINT `banner_ads_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `ad_regions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		// Insert a default ad region
		DB::insert("INSERT INTO `ad_regions` SET `name` = 'Default Region', `variable_name` = 'default_region', `width` = 100, `height` = 100");
		$management_page = DB::fetch_one("SELECT `id` FROM `page_index` WHERE `slug` = 'banner_ads'");
		if (!$management_page) {
			// Add banner_ads page (super admin access by default):
			DB::insert("INSERT INTO `page_index` SET `parent` = 9999999, `slug` = 'banner-ads', `title` = 'Manage Banner Ads', `access_level` = 0");
			// Get module row ID:
			$module_id = DB::fetch_one("SELECT `id` FROM `modules` WHERE `name` = 'BannerAds'");
			// Remove from module pages first to ensure clean install:
			DB::query("DELETE FROM `module_pages` WHERE `module_id` = {$module_id}");
			// Add PageContent to content_editor page:
			DB::insert("INSERT INTO `module_pages` SET `module_id` = {$module_id}, `page_name` = 'banner-ads', `is_primary` = 1");
			// Install as secondary on all other pages:
			DB::insert("INSERT INTO `module_pages` SET `module_id` = {$module_id}, `page_name` = '*', `is_primary` = 0");
		}
		Permissions::add(__CLASS__,array('new' => 99, 'edit' => 99, 'delete' => 99, 'index' => 99, 'new_ad_region' => 99, 'edit_ad_region' => 99, 'delete_ad_region' => 99, 'index_ad_region' => 99),true);
	}
	/**
	 * Run migrations to properly uninstall the module
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public static function uninstall_migration() {
		DB::query("UPDATE `modules` SET `installed` = 0 WHERE `name` = 'BannerAds'");
		$module_id = DB::fetch_one("SELECT `id` FROM `modules` WHERE `name` = 'BannerAds'");
		DB::query("DELETE FROM `module_pages` WHERE `module_id` = {$module_id}");
		DB::query("DELETE FROM `page_index` WHERE `slug` = 'banner_ads'");
		DB::query("DROP TABLE IF EXISTS `banner_ads`");
		DB::query("DROP TABLE IF EXISTS `ad_regions`");
		Permissions::remove(__CLASS__);
	}
}
?>