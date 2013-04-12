<?php echo Form::header($banner_ad) ?>

	<?php echo ModelForm::select($region_select_list,$banner_ad,'region_id') ?>

	<?php echo ModelForm::text($banner_ad,'title','This will appear as a tooltip when the user mouses over the ad.') ?>

	<?php echo ModelForm::text($banner_ad,'url','Enter a fully qualified URL starting with <strong>http://</strong>, or select an internal web page below.') ?>

	<?php $Navigation->tiger_stripe('striped_BannerAd_form') ?>
	<p class="<?php echo $Navigation->tiger_stripe('striped_BannerAd_form') ?>">
		<label for="internal_page_select">&nbsp;</label>
		<select id="attr_internal_page_select" name="internal_page_select">
			<option value="">Select Internal Page...</option>
			<?php print $page_select_options ?>
		</select>
	</p>

	<?php echo ModelForm::radios($link_target_options,$banner_ad,'link_target') ?>

	<p class="<?php echo $Navigation->tiger_stripe('striped_BannerAd_form') ?>">
		<?php echo $TinyMce->render_standalone_file_browser_field('image','image','banner_ad[image]',$banner_ad->image_label(),$banner_ad->image(),$banner_ad->image_is_required(),$banner_ad->image_is_valid(),array('maxlength' => '255')) ?>
		<span class="instructions">
			<strong>Remember:</strong> if you later move, rename or delete the file using the file manager you will need to update this banner ad accordingly.
		</span>
	</p>

	<?php echo ModelForm::text($banner_ad,'sort_order','Items with no sort order will come first sorted by title. Note that you can modify the sort order any time from the main ad manager screen by drag-and-drop.') ?>

	<?php echo ModelForm::checkbox('1','0',$banner_ad,'is_active') ?>

	<?php echo Form::footer($BannerAdsManager, $banner_ad, (!$banner_ad->is_new() && $BannerAdsManager->user_can_delete())) ?>
