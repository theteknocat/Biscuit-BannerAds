<?php
$custom_buttons = array();
if ($BannerAdsManager->user_can_edit_ad_region() || $BannerAdsManager->user_can_create_ad_region()) {
	$custom_buttons[] = array('href' => $BannerAdsManager->url('new_ad_region').'?return_url='.$BannerAdsManager->url(), 'label' => 'New Region', 'id' => 'new-ad-region-button');
	$custom_buttons[] = array('href' => $BannerAdsManager->url('index_ad_region'), 'label' => 'Manage Regions', 'id' => 'manage-regions-button');
}
$custom_buttons[] = array('href' => '#image-manager', 'label' => 'Manage Images', 'id' => 'image-manager-button');
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
		// If the user is allowed to edit ads, we output a form with a request token field as that will be needed in order for the Ajax sumbit for activating/deactivating ads
		?>
<form id="token-field-form" name="token-field-form" action="#" method="post" accept-charset="utf-8">
	<?php echo RequestTokens::render_token_field(); ?>
</form>
		<?php
	}
	foreach ($region_names as $region_id => $region_name) {
		$region_title = $region_name;
		if ($BannerAdsManager->user_can_edit_ad_region()) {
			$region_title .= ' <span class="small">[ <a href="'.$BannerAdsManager->url('edit_ad_region',$region_id).'">Edit</a> ]</span>';
		}
		?>
<fieldset class="banner-ad-fieldset" id="banner-ad-set-<?php echo $region_id ?>">
	<legend><?php echo $region_title ?></legend>
		<?php
		if ($BannerAdsManager->user_can_create()) {
			?><div class="controls first"><a href="<?php echo $BannerAdsManager->url('new') ?>?banner_ad_defaults[region_id]=<?php echo $region_id ?>">Insert Banner Ad</a></div><?php
		}
		if (empty($banner_ads[$region_id])) {
			?><p class="none-found" style="clear: both">No banner ads have been defined for this region.</p><?php
		} else {
			if ($BannerAdsManager->user_can_edit()) {
				?><div class="throbber" id="region-throbber-<?php echo $region_id ?>" style="display: none">Loading...</div><?php
			}
			?><div id="banner-ads-<?php echo $region_id ?>" class="banner-ad-set">
			<?php
			foreach ($banner_ads[$region_id] as $index => $banner_ad) {
				$rowclass = 'banner-ad-item '.$Navigation->tiger_stripe('banner_ads_'.$region_id);
				if (!$banner_ad->is_active()) {
					$rowclass .= ' inactive';
				}
				?><div class="<?php echo $rowclass ?>" id="region<?php echo $region_id ?>item_<?php echo $banner_ad->id() ?>"><?php
				if ($BannerAdsManager->user_can_edit() || $BannerAdsManager->user_can_delete()) {
					?><div class="ad-info admin"><div class="controls"><?php
					if ($BannerAdsManager->user_can_edit()) {
						?><div class="ad-activator" style="text-align: center"><input class="activator-checkbox" type="checkbox" name="banner_ad_is_active_<?php echo $banner_ad->id() ?>" id="banner-ad-is-active-<?php echo $banner_ad->id() ?>" value="1"<?php if ($banner_ad->is_active()) { ?> checked="checked"<?php } if (!$has_image) { ?> disabled="disabled"<?php } ?>><label for="banner-ad-is-active-<?php echo $banner_ad->id() ?>">Active</label></div><?php
					}
					if ($BannerAdsManager->user_can_delete()) {
						?><a href="<?php echo $BannerAdsManager->url('delete', $banner_ad->id()); ?>" rel="Banner Ad|<?php echo addslashes(H::purify_text($banner_ad->title())) ?>" class="delete-button">Delete</a><?php
					}
					if ($BannerAdsManager->user_can_edit()) {
						?><a href="<?php echo $BannerAdsManager->url('edit', $banner_ad->id()); ?>" class="edit_item">Edit</a><?php
					}
					?></div></div><?php
				}
				if ($BannerAdsManager->user_can_edit()) {
					$left_margin = 56;
					?><div class="sort-column ad-info"><?php echo $index+1; ?></div><?php
				} else {
					$left_margin = 16;
				}
				$has_image = (file_exists(SITE_ROOT.$banner_ad->image()));
				?><div class="ad-info"><?php
				if ($has_image) {
					list($width, $height) = getimagesize(SITE_ROOT.$banner_ad->image_thumb());
					$left_margin += $width;
					?><img style="margin: 0;padding:0;" width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="<?php echo $banner_ad->image_thumb() ?>" alt="<?php echo addslashes(H::purify_text($banner_ad->title())) ?>"><?php
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
				?>
				<div class="clearance"></div>
				</div><?php
			}
			?></div><?php
		}
		if (!empty($banner_ads[$region_id]) && count($banner_ads[$region_id]) > 1 && $BannerAdsManager->user_can_edit()) {
			// If the user can edit and there's more than one banner in the region, enable drag-and-drop sorting
		?>
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function() {
			jQuery('#banner-ads-<?php echo $region_id ?> .sort-column').addClass('draggable');
			Biscuit.Crumbs.Sortable.create('banner-ads-<?php echo $region_id ?>','div','/banner-ads',null,{
				handle: 'sort-column',
				array_name: 'banner_ad_sort',
				throbber_id: 'region-throbber-<?php echo $region_id ?>',
				onChange: function() {
					BannerAds.RenumberBanners(<?php echo $region_id ?>);
				},
				onUpdate: function() {
					BannerAds.HighlightBanners(<?php echo $region_id ?>);
				}
			});
		});
	</script>
		<?php
		}
	?>
</fieldset>
<?php
	}
}
?>