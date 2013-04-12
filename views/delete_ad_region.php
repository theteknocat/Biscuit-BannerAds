<p><strong>Are you sure you want to delete <?php echo $representation ?>?</strong></p>
<?php
if ($representation->has_banners()) {
	?>
<p><strong>WARNING:</strong> <?php echo $representation->banner_count() ?> banner ad<?php echo (($representation->banner_count() > 1) ? "s" : "" ) ?> will also be removed.</p>
	<?php
}
?>
<p>This action cannot be undone.</p>
<?php require('modules/generic_views/common_delete_confirmation_form.php') ?>