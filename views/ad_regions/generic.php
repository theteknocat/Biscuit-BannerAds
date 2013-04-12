<?php
// This view is used to render ads if a custom view file based on the variable name is not found
$interval_classname = ' interval-'.(!$ad_region->rotation_interval() ? '5000' : $ad_region->rotation_interval());
?>
<div style="width: <?php echo $ad_region->width() ?>px; height: <?php echo $ad_region->height() ?>px; overflow:hidden" id="<?php echo $region_div_id ?>" class="banner-ads <?php echo $ad_region->rotation_mode(); echo $interval_classname; ?>">
	<?php
	foreach ($banner_ads as $banner) {
		$clean_title = H::purify_text($banner->title());
		if ($banner->url()) {
			?><a href="<?php echo $banner->url() ?>" target="<?php echo $banner->link_target() ?>" title="<?php echo addslashes($clean_title) ?>" style="width: <?php echo $ad_region->width() ?>px; height: <?php echo $ad_region->height() ?>px;"><?php
		}
		?><img src="<?php echo $banner->image() ?>" alt="<?php echo addslashes($clean_title) ?>" style="width: <?php echo $ad_region->width() ?>px; height: <?php echo $ad_region->height() ?>px;"><?php
		if ($banner->url()) {
			?></a><?php
		}
	}
	?>
</div>
