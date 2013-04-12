<?php
/**
 * Model the banner ad DB table
 *
 * @package Modules
 * @author Peter Epp
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
	 * Place to cache the name of the region
	 *
	 * @var string
	 */
	private $_region_name;
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
	public function region_name() {
		if (!$this->is_new()) {
			if (empty($this->_region_name)) {
				$region_factory = new AdRegionFactory();
				$region = $region_factory->find($this->region_id());
				$this->_region_name = $region->name();
			}
			return $this->_region_name;
		}
		return '';
	}
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
		$region_factory = new AdRegionFactory();
		$my_region = $region_factory->find($this->region_id());
		return 'the banner ad titled &ldquo;'.$this->title().'&rdquo;, displayed in the &ldquo;'.$my_region->name().'&rdquo; region';
	}
}
?>