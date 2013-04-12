<?php
$custom_buttons = array();
if ($BannerAdsManager->user_can_create_region()) {
	$custom_buttons[] = array('href' => $BannerAdsManager->url('new_ad_region'), 'label' => 'New Region');
}
$custom_buttons[] = array('href' => $BannerAdsManager->url(), 'label' => 'Manage Banner Ads');
print $Navigation->render_admin_bar($BannerAdsManager,NULL,array(
	'bar_title' => 'Ad Region Administration',
	'custom_buttons' => $custom_buttons
));
if (!empty($ad_regions)) {
	?>
<p><strong>Note:</strong> If you delete a region, remember to update your templates.</p>
<table width="100%" id="ad-region-list">
	<?php
	foreach ($ad_regions as $ad_region) {
?>
	<tr class="<?php echo $Navigation->tiger_stripe('ad_region_list') ?>">
		<td><?php
			echo '<h2>'.H::purify_text($ad_region->name_with_specs());
			if ($ad_region->has_banners()) {
				?> <span class="small"> &ndash; has <?php echo $ad_region->banner_count() ?> ad<?php echo ($ad_region->banner_count() > 1) ? 's' : '' ?></span><?php
			}
			echo '</h2>';
		?><span class="small"><strong>Template variable:</strong> $ad_region_<?php echo $ad_region->variable_name() ?><br>
			<strong>Custom view filename:</strong> banner_ads/views/ad_regions/<?php echo $ad_region->variable_name() ?>.php</span></td>
		<?php
		// We're going to assume that the user has permission to both edit and delete regions rather than check permissions because there should never be a case
		// where the user has permission to view this page but not delete or edit.
		?>
		<td style="text-align: right"><div class="controls">
			<a href="<?php echo $BannerAdsManager->url('delete_ad_region',$ad_region->id()) ?>" class="delete-button" rel="Ad Region|<?php echo addslashes(H::purify_text($ad_region->name())); if ($ad_region->has_banners()) { echo "|".$ad_region->banner_count()." banner ad".(($ad_region->banner_count() > 1) ? "s" : "")." will also be deleted."; } ?>">Delete</a>
			<a href="<?php echo $BannerAdsManager->url('edit_ad_region',$ad_region->id()) ?>">Edit</a>
		</div></td>
	</tr>
<?php
	}
	?>
</table>
	<?php
} else {
	?>
<p>There are presently no banner ad regions in the system.</p>
	<?php
}
?>