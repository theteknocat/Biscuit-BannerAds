<?php
/**
 * Model the banner ad DB table
 *
 * @package Modules
 * @subpackage BannerAds
 * @author Peter Epp
 * @version $Id: banner_ad.php 13843 2011-07-27 19:45:49Z teknocat $
 */
class BannerAd extends AbstractModel {
	/**
	 * Nice labels for attributes that don't translate to the desired label with AkInflector::humanize()
	 *
	 * @var string
	 */
	protected $_attr_labels = array(
		'url'       => 'Link to URL',
		'region_id' => 'Region'
	);
	/**
	 * Relationship with ad regions
	 *
	 * @var string
	 */
	protected $_belongs_to = array('AdRegion');
	/**
	 * Define foreign key for banner ad region id since it doesn't use default naming convention
	 *
	 * @var array
	 */
	protected $_belongs_to_key_names = array('AdRegion' => 'region_id');
	/**
	 * Place to cache the name of the region
	 *
	 * @var string
	 */
	private $_region_name;
	/**
	 * Set default sort order on save
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function _set_attribute_defaults() {
		if (!$this->sort_order() || $this->sort_order() == 0) {
			$this->set_sort_order(ModelFactory::instance('BannerAd')->next_highest('sort_order',1,'`region_id` = '.$this->region_id()));
		}
	}
	/**
	 * Return the path to the thumbnail image generated by TinyBrowser
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function image_thumb() {
		if (!$this->is_new()) {
			$image_path = $this->image();
			$image_path = ltrim($image_path,'/');
			$path_bits = explode('/',$image_path);
			$filename = array_pop($path_bits);
			$thumbnail_path = '/'.implode('/',$path_bits).'/_thumbs/_'.$filename;
			return $thumbnail_path;
		}
		return null;
	}
	/**
	 * Return the path to the original image file
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function original_image() {
		if (!$this->is_new()) {
			$image_path = $this->image();
			$image_path = ltrim($image_path,'/');
			$path_bits = explode('/',$image_path);
			$filename = array_pop($path_bits);
			$original_path = '/'.implode('/',$path_bits).'/_originals/'.$filename;
			return $original_path;
		}
		return null;
	}
	/**
	 * Make sure region id is valid
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public function region_id_is_valid() {
		Console::log("Validating region id: ".$this->region_id());
		return ($this->region_id() != null && $this->region_id() != 0);
	}
	/**
	 * Return the URL shortened and with "..." on the end if it's longer than 50 chars
	 *
	 * @return string
	 * @author Peter Epp
	 */
	public function url_shortened() {
		$url = $this->url();
		if (strlen($url) > 40) {
			$url = substr($url,0,40)."...";
		}
		return $url;
	}
	public function __toString() {
		$my_region = ModelFactory::instance('AdRegion')->find($this->region_id());
		return 'the banner ad titled &ldquo;'.$this->title().'&rdquo;, displayed in the &ldquo;'.$my_region->name().'&rdquo; region';
	}
}
?>