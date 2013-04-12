<?php
/**
 * Model the ad region DB table
 *
 * @package Modules
 * @author Peter Epp
 */
class AdRegion extends AbstractModel {
	/**
	 * Whether or not the current region has banner ads
	 *
	 * @var bool|null
	 */
	private $_has_banners = null;
	/**
	 * The number of banner ads contained in the current region
	 *
	 * @var int
	 */
	private $_banner_count = 0;
	/**
	 * Return the name of the region with it's specs (width and height) included in brackets
	 *
	 * @return string
	 * @author Peter Epp
	 */
	public function name_with_specs() {
		return $this->name().' ('.$this->width().' X '.$this->height().')';
	}
	/**
	 * Whether or not the variable name is valid. It must not be blank, start with a number, or contain any characters other than letters, numbers and underscores
	 *
	 * @return bool
	 * @author Peter Epp
	 */
	public function variable_name_is_valid() {
		$var_name = $this->variable_name();
		$first_char = '';
		if ($var_name != null) {
			$first_char = substr($var_name,0,1);
		}
		$is_valid = ($var_name != null && preg_match('/([A-Za-z0-9_]+)/',$var_name) && !preg_match('/([0-9]+)/',$first_char));
		if (!$is_valid) {
			// Set a custom error message for this attribute
			$this->set_error('variable_name','Provide a variable name that does not begin with a number and contains only numbers, letters and underscores');
		}
		return $is_valid;
	}
	/**
	 * Whether or not the current region has any banner ads.
	 *
	 * @return bool
	 * @author Peter Epp
	 */
	public function has_banners() {
		if (!$this->is_new()) {
			if ($this->_has_banners === null) {
				// Strictly speaking one model shouldn't know about and deal with another model's db table directly, but we're going to cheat and do that in
				// this case because it's more efficient.
				$this->_banner_count = DB::fetch_one("SELECT COUNT(*) AS `banner_count` FROM `banner_ads` WHERE `region_id` = ?",$this->id());
				$this->_has_banners = ($this->_banner_count > 0);
			}
			return $this->_has_banners;
		}
		return false;
	}
	/**
	 * Return the number of banners in the region
	 *
	 * @return int
	 * @author Peter Epp
	 */
	public function banner_count() {
		if ($this->_has_banners === null) {
			// Call has_banners() to make it fetch the count from the DB in the event that this method was called prior to any calls to has_banners().
			$this->has_banners();
		}
		return $this->_banner_count;
	}
	/**
	 * Whether or not the width is valid. Must be a numeric value greater than zero
	 *
	 * @return bool
	 * @author Peter Epp
	 */
	public function width_is_valid() {
		$width = $this->width();
		$is_valid = ($width != null && $width != 0 && preg_match('/([0-9]+)/',$width));
		if (!$is_valid) {
			$this->set_error('width','Provide a numeric width greater than zero');
		}
		return $is_valid;
	}
	/**
	 * Whether or not the height is valid. Must be a numeric value greater than zero
	 *
	 * @return bool
	 * @author Peter Epp
	 */
	public function height_is_valid() {
		$height = $this->height();
		$is_valid = ($height != null && $height != 0 && preg_match('/([0-9]+)/',$height));
		if (!$is_valid) {
			$this->set_error('height','Provide a numeric height greater than zero');
		}
		return $is_valid;
	}
	public function __toString() {
		return 'the &ldquo;'.$this->name().'&rdquo; banner ad region';
	}
}
?>