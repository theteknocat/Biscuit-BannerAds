<div id="help-tabs">
	<ul>
		<li><a href="#help-basics">Managing Banners</a></li>
		<li><a href="#help-ad-form">Insert/Edit Banner Form</a></li>
		<?php
		if ($Biscuit->ModuleBannerAds()->user_can_index_ad_region()) {
			?>
		<li><a href="#help-manage-regions">Managing Regions</a></li>
			<?php
		}
		?>
	</ul>
	<div id="help-basics">
		<h4>Accessing Banner Management</h4>
		<p>Unlike most other Biscuit modules, the banner ad module does not normally provide buttons within the page for accessing the management features. Your web developer, however, may have customized your version to provide those. Whether or not that is the case, you can always access the banner ad management features using the wrench menu in the top-left corner of your website when logged in.</p>
		<h4>Managing Banners</h4>
		<p>When you get to the banner management page, you'll see an administration bar at the top:</p>
		<?php
		if ($Biscuit->ModuleBannerAds()->user_can_index_ad_region()) {
			$admin_bar_image = 'admin-bar-super.gif';
		} else {
			$admin_bar_image = 'admin-bar-regular.gif';
		}
		?>
		<p><img src="/modules/banner_ads/images/help/<?php echo $admin_bar_image; ?>" alt="Administration bar"></p>
		<p>Clicking the "Manage Images" button will open a popup window to the file manager where you can manage the images you use for your banner ads in general. This is provided as a convenience so you don't have to insert or edit a banner just to access the file manager. It's useful if you want to upload and organize a bunch of banner images before inserting them into a region.</p>
		<?php
		if ($Biscuit->ModuleBannerAds()->user_can_index_ad_region()) {
			?>
		<p>Clicking on "Manage Regions" or "New Region" allows you to setup the regions used in the site. Note that these functions should only be accessible by a web developer. Never provide the client with access to these functions.</p>
			<?php
		}
		?>
		<p>After the administration bar you will see a labeled box for each of the banner regions that have been configured for your website, for example:</p>
		<p><img src="/modules/banner_ads/images/help/banner-region-example.gif" alt="Banner region"></p>
		<p>The box shows all the ads in the region, in this example only one. Inactive banners will be greyed out, except when you hover the mouse over them.</p>
		<p>You can perform the following functions:</p>
		<ul>
			<li><strong>Insert New Banner</strong><br>
				Click the "Insert Banner Ad" button to insert a banner into the region.</li>
			<li><strong>Activate/Deactivate</strong><br>
				Click the "Active" checkbox next to an add to activate or de-activate it. You will be prompted for confirmation.</li>
			<li><strong>Edit the Ad</strong></li>
			<li><strong>Delete the Ad</strong></li>
			<li><strong>Re-Order the Ads</strong><br>
				If there is more than one banner in a region, there will be a small dragging icon (<img src="/modules/banner_ads/images/help/drag-widget.jpg" style="vertical-align: bottom;" alt="Drag widget">) to the left of the banner thumbnail. Click and drag this icon to move the ad to a new position within the region.</li>
		</ul>
		<h4>Adding/Editing Banners</h4>
		<p>For details on filling out the form for inserting or editing a banner, please see the "Insert/Edit Banner Form" tab above.</p>
	</div>
	<div id="help-ad-form">
		<h4>Banner Ad Form Fields</h4>
		<ul>
			<li><strong>Region</strong> (required)<br>
				Select the region in which you want to insert the ad. This is always pre-set for new banners, but you can always change your mind if you want to insert a new banner into another region. When editing an existing ad, you can use this to move it into a different region, but be aware that different regions may be different sizes and you may therefore need to change the image when changing regions.</li>
			<li><strong>Title</strong> (required)<br>
				Enter a descriptive title for the banner. This will be used as the alternative text that appears when a user mouses over the image and will be picked up by search engines and screen readers.</li>
			<li><strong>Link to URL</strong><br>
				Leave this blank if you do not want the banner to be a link.</li>
			<li><strong>Link Target</strong><br>
				Applicable only if you entered a link URL. Choose between "Same Window" or "New Window" for the link to open in.</li>
			<li><strong>Image</strong> (required)<br>
				Either paste a URL to an external image, or click the Browse/Upload button to access the file manager to upload and/or select an image to use.</li>
			<li><strong>Is Active</strong> (required)<br>
				Choose whether or not the banner should be active.</li>
		</ul>
	</div>
	<?php
	if ($Biscuit->ModuleBannerAds()->user_can_index_ad_region()) {
		?>
	<div id="help-manage-regions">
		<p><strong>IMPORTANT: This help and it's associated functionality is intended for the web developer only.</strong> Do not give your client permission to access these functions. If permissions are correctly set, your client will not see this help section.</p>
		<p>The region management interface allows you to setup the regions that you will then code into the website's theme in order to output the banners. The region management view lists the regions, indicating how many ads a region has, the PHP variable name you can use in the template and the path to a custom view file it will look for first, so you can create it if you do not want it to use the generic region view.</p>
		<p>To render a banner ad region in a template, you need only output the region's variable name, like so:</p>
		<pre><code>&lt;?php echo $ad_region_var_name; ?&gt;</code></pre>
		<p>Please see the developer documentation for more details on how to customize the view for any given region.</p>
		<h4>Create/Edit Region</h4>
		<p>The following fields are available when adding/editing a region:</p>
		<ul>
			<li><strong>Name</strong> (required)<br>
				A friendly name for the region. Best to use one that describes it's position on the page. This will be seen by the client when they are managing their banners.</li>
			<li><strong>Variable Name</strong> (required)<br>
				Enter the variable name you want to use in your templates. Will be prefixed with "ad_region_" and must follow the rules for allowed variable names in PHP. If you change this for any existing region, you will of course have to update the templates accordingly.</li>
			<li><strong>Width and Height</strong> (required)<br>
				Enter the exact pixel dimensions of the region. Use these when coding the CSS layout. The client will be shown the dimensions of the region when inserting/editing a banner ad.</li>
			<li><strong>Rotation Mode</strong> (required)<br>
				Choose from 3 basic jQuery Cycle presets, or "None, I'll customize it myself" if you intend to setup a custom view or custom script to initiate the cycling.</li>
			<li><strong>Rotation Interval</strong><br>
				Number of seconds between banners. Defaults to 5 if none chosen. Cycling always pauses on hover.</li>
		</ul>
	</div>
		<?php
	}
	?>
</div>