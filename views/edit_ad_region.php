<?php
if (!$ad_region->is_new()) {
	?>
<p class="notice"><strong>NOTICE:</strong> If you change the variable name, remember to update your templates accordingly.</p>
	<?php
}
?>
<?php echo Form::header($ad_region) ?>

	<?php echo ModelForm::text($ad_region, 'name') ?>

	<?php echo ModelForm::text($ad_region, 'variable_name','Can only contain letters, numbers, underscores and may not begin with a number. Note that the variable name will be prefixed with "ad_region_" for use in your templates.') ?>

	<?php echo ModelForm::text($ad_region, 'width') ?>

	<?php echo ModelForm::text($ad_region, 'height') ?>

	<?php echo ModelForm::select($BannerAdsManager->rotation_mode_select_options(), $ad_region, 'rotation_mode') ?>

	<?php echo ModelForm::select($BannerAdsManager->rotation_interval_select_options(), $ad_region, 'rotation_interval', 'Defaults to 5 seconds if not specified. Rotation will always pause on hover.') ?>

	<?php echo Form::footer($BannerAdsManager, $ad_region, (!$ad_region->is_new() && !$ad_region->has_banners() && $BannerAdsManager->user_can_delete_ad_region())) ?>
