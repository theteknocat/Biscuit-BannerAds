<?php
$custom_buttons = array();
if ($BannerAdsManager->user_can_edit_ad_region() || $BannerAdsManager->user_can_create_ad_region()) {
	$custom_buttons[] = array('href' => $BannerAdsManager->url('new_ad_region').'?return_url='.$BannerAdsManager->url(), 'label' => 'New Region', 'id' => 'new-ad-region-button', 'classname' => 'new-button');
	$custom_buttons[] = array('href' => $BannerAdsManager->url('index_ad_region'), 'label' => 'Manage Regions', 'id' => 'manage-regions-button');
}
$custom_buttons[] = array('href' => '#image-manager', 'label' => 'Manage Images', 'id' => 'image-manager-button', 'classname' => 'image-button');
print $Navigation->render_admin_bar($BannerAdsManager,NULL,array(
	'bar_title' => 'Administration',
	'custom_buttons' => $custom_buttons
));
if (!$regions_exist) {
	?>
<p class="notice">You will need to create at least one ad region before you can add any banner ads.</p>
	<?php
} else {
	if ($BannerAdsManager->user_can_delete()) {
		?>
<p><strong>Note:</strong> when you delete a banner ad, it will not delete the image file from the system. To delete images, click the "Manage Images" button above to access the file manager.</p>
		<?php
	}
	if ($BannerAdsManager->user_can_edit()) {
		// If the user is allowed to edit ads, output a request token for sortable and activate/deactivate ajax requests
		?>
<script type="text/javascript">
	<?php
	$token_info = RequestTokens::get();
	?>
	var sortable_request_token = '<?php echo $token_info['token']; ?>';
	var sortable_token_form_id = '<?php echo $token_info['form_id']; ?>';
</script>
		<?php
	}
	foreach ($regions as $region) {
		$region_title = $region->name();
		if ($BannerAdsManager->user_can_edit_ad_region()) {
			$region_title .= ' <span class="small">[ <a href="'.$BannerAdsManager->url('edit_ad_region',$region->id()).'">Edit</a> ]</span>';
		}
		?>
<fieldset class="banner-ad-fieldset" id="banner-ad-set-<?php echo $region->id(); ?>">
	<legend><?php echo $region_title ?></legend>
		<?php
		if ($BannerAdsManager->user_can_create()) {
			?><div class="controls first"><a class="new-button" href="<?php echo $BannerAdsManager->url('new') ?>?banner_ad_defaults[region_id]=<?php echo $region->id(); ?>">Insert Banner Ad</a></div><?php
		}
		if (empty($banner_ads[$region->id()])) {
			?><p class="none-found" style="clear: both">No banner ads have been defined for this region.</p><?php
		} else {
			?><dd id="banner-ads-<?php echo $region->id(); ?>" class="banner-ad-set">
			<?php
			foreach ($banner_ads[$region->id()] as $banner_ad) {
				$rowclass = 'banner-ad-item '.$Navigation->tiger_stripe('banner_ads_'.$region->id());
				if (!$banner_ad->is_active()) {
					$rowclass .= ' inactive';
				}
				?><dl class="<?php echo $rowclass ?>" id="region<?php echo $region->id() ?>item_<?php echo $banner_ad->id() ?>"><?php
				if ($BannerAdsManager->user_can_edit() || $BannerAdsManager->user_can_delete()) {
					?><div class="ad-info admin"><div class="controls"><?php
					if ($BannerAdsManager->user_can_edit()) {
						?><div class="ad-activator" style="text-align: center"><input class="activator-checkbox" type="checkbox" name="banner_ad_is_active_<?php echo $banner_ad->id() ?>" id="banner-ad-is-active-<?php echo $banner_ad->id() ?>" value="1"<?php if ($banner_ad->is_active()) { ?> checked="checked"<?php } if (!$has_image) { ?> disabled="disabled"<?php } ?>><label for="banner-ad-is-active-<?php echo $banner_ad->id() ?>">Active</label></div><?php
					}
					if ($BannerAdsManager->user_can_delete()) {
						?><a href="<?php echo $BannerAdsManager->url('delete', $banner_ad->id()); ?>" data-item-type="<?php echo __('Banner Ad'); ?>" data-item-title="<?php echo Crumbs::entitize_utf8(H::purify_text($banner_ad->title())) ?>" class="delete-button">Delete</a><?php
					}
					if ($BannerAdsManager->user_can_edit()) {
						?><a href="<?php echo $BannerAdsManager->url('edit', $banner_ad->id()); ?>" class="edit-button edit_item">Edit</a><?php
					}
					?></div></div><?php
				}
				if ($BannerAdsManager->user_can_edit()) {
					$left_margin = 38;
					?><div class="sort-column ad-info"></div><?php
				} else {
					$left_margin = 16;
				}
				?><div class="ad-info"><?php
				$has_image = (file_exists(SITE_ROOT.$banner_ad->image()));
				if ($has_image) {
					list($width, $height) = getimagesize(SITE_ROOT.$banner_ad->image_thumb());
					$left_margin += $width;
					?><img style="margin: 0;padding:0" src="<?php echo $banner_ad->image_thumb(); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo Crumbs::entitize_utf8(H::purify_text($banner_ad->title())) ?>"><?php
				} else {
					$left_margin += 100;
					?>[Missing]<?php
				}
				$links_to_title = '';
				if ($banner_ad->url() != $banner_ad->url_shortened()) {
					$links_to_title = $banner_ad->url();
				}
				?></div><div class="ad-info details" style="margin: 0 0 0 <?php echo $left_margin; ?>px;">
				<h2><?php echo H::purify_text($banner_ad->title()) ?></h2>
				<span class="small"><?php
				if ($banner_ad->url()) {
					?><strong>Links to:</strong> <a href="<?php echo $banner_ad->url() ?>" target="_blank" title="<?php echo $links_to_title ?>"><?php echo $banner_ad->url_shortened() ?></a><?php
				} else {
					?><strong>No Link</strong><?php
				}
				?></span></div><?php
				?><div class="clearance"></div></dl><?php
			}
			?></dd><?php
		}
		?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.banner-ad-item').mouseover(function() {
				$(this).addClass('hovered');
			});
			$('.banner-ad-item').mouseout(function() {
				$(this).removeClass('hovered');
			});
			<?php
			if (!empty($banner_ads[$region->id()]) && count($banner_ads[$region->id()]) > 1 && $BannerAdsManager->user_can_edit()) {
				// If the user can edit and there's more than one banner in the region, enable drag-and-drop sorting
			?>
			$('#banner-ads-<?php echo $region->id(); ?> .sort-column').addClass('draggable');
			Biscuit.Crumbs.Sortable.create('#banner-ads-<?php echo $region->id(); ?>','/banner-ads',{
				handle: '.sort-column',
				array_name: 'banner_ad_sort',
				axis: 'y',
				onUpdate: function() {
					Biscuit.Crumbs.ShowCoverThrobber('banner-ads-<?php echo $region->id(); ?>','Updating...');
					BannerAds.RestripeBanners(<?php echo $region->id(); ?>);
				},
				onFinish: function() {
					Biscuit.Crumbs.HideCoverThrobber('banner-ads-<?php echo $region->id(); ?>');
				}
			});
			<?php
			}
			?>
		});
	</script>
		<?php
		}
	?>
</fieldset>
<?php
}
?>