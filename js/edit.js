BannerAds = {
	AddEditHandlers: function() {
		jQuery('#banner-ad-form').submit(function() {
			new Biscuit.Ajax.FormValidator('banner-ad-form');
			return false;
		});
		jQuery('#ad-region-form').submit(function() {
			new Biscuit.Ajax.FormValidator('ad-region-form');
			return false;
		});
		jQuery('#attr_internal_page_select').change(function() {
			var selected_value = jQuery(this).val();
			jQuery('#attr_url').val(selected_value);
		});
		jQuery('.ad-activator').show();
		jQuery('.ad-activator input.activator-checkbox').click(function() {
			// Provide Ajaxy activation and deactivation of ads.
			var is_checked = jQuery(this).attr('checked');
			if (is_checked) {
				var mode = "activate";
			} else {
				var mode = "deactivate";
			}
			var active_banners_in_set = jQuery(this).parent().parent().parent().children('.banner-ad-item:not(.inactive)');
			if (active_banners_in_set.length == 1 && mode == 'deactivate') {
				var message_prefix = 'WARNING: You are about to deactive the last banner in this region! If you proceed, the region will be empty.\n\nAre you sure you want to proceed?';
			} else {
				var message_prefix = "Are you sure you want to "+mode+" this Banner Ad now?";
			}
			if (confirm(message_prefix+"\n\nThis action will take immediate effect.")) {
				var banner_id = this.id.substr(20);	// Everything after 'banner-ad-is-active-'
				var post_params = {
					request_token: jQuery('#token-field-form input[name=request_token]').val(),
					'banner_ad[is_active]': ((is_checked) ? 1 : 0)
				}
				Biscuit.Console.log("Post params:");
				Biscuit.Console.log(post_params);
				var updated_banner_item_id = jQuery(this).parent().parent().parent().parent().attr('id');
				var banner_set_id = jQuery(this).parent().parent().parent().parent().parent().attr('id');
				var throbber_id = 'region-throbber-'+banner_set_id.substr(11);
				Biscuit.Crumbs.ShowThrobber(throbber_id);
				new Ajax.Request('/banner-ads/edit/'+banner_id,{
					method: 'post',
					parameters: post_params,
					requestHeaders: Biscuit.Ajax.RequestHeaders('server_action'),
					onSuccess: function() {
						Biscuit.Crumbs.HideThrobber(throbber_id);
						if (mode == 'activate') {
							jQuery('#'+updated_banner_item_id).removeClass('inactive');
						} else {
							jQuery('#'+updated_banner_item_id).addClass('inactive');
						}
						new Effect.Highlight(updated_banner_item_id,{startcolor: '#93f586'});
					}
				});
			} else {
				// Returning false reverses the state of the checkbox
				return false;
			}
		});
		jQuery('#image-manager-button').click(function() {
			tinyBrowserPopUp('image',null);
			return false;
		});
	},
	RenumberBanners: function(banner_set_id) {
		var curr_num = 1;
		jQuery('#banner-ads-'+banner_set_id+' div.banner-ad-item').each(function() {
			// Restripe:
			jQuery(this).removeClass('stripe-odd');
			jQuery(this).removeClass('stripe-even');
			if (curr_num%2 == 0) {
				jQuery(this).addClass('stripe-even');
			} else {
				jQuery(this).addClass('stripe-odd');
			}
			// Change the sort number
			jQuery('#'+this.id+' .sort-column').html(curr_num);
			curr_num++;
		});
	},
	HighlightBanners: function(region_id) {
		jQuery('#banner-ads-'+region_id+' div.banner-ad-item').each(function() {
			new Effect.Highlight(this.id,{startcolor: '#93f586', afterFinish: function(obj) {
				$(obj.element).setStyle({backgroundColor: ''});
			}});
		});
	}
}

jQuery(document).ready(function() {
	BannerAds.AddEditHandlers();
});