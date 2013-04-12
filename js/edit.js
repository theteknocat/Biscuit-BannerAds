BannerAds = {
	AddEditHandlers: function() {
		$('#banner-ad-form').submit(function() {
			new Biscuit.Ajax.FormValidator('banner-ad-form');
			return false;
		});
		$('#ad-region-form').submit(function() {
			new Biscuit.Ajax.FormValidator('ad-region-form');
			return false;
		});
		$('#attr_internal_page_select').change(function() {
			var selected_value = $(this).val();
			$('#attr_url').val(selected_value);
		});
		$('.ad-activator').show();
		$('.ad-activator input.activator-checkbox').click(function() {
			// Provide Ajaxy activation and deactivation of ads.
			var is_checked = $(this).attr('checked');
			if (is_checked) {
				var mode = "activate";
				var confirm_button_text = 'Activate';
			} else {
				var mode = "deactivate";
				var confirm_button_text = 'Deactivate';
			}
			var banner_ad_name = $(this).parent().parent().parent().prev().children('h2').text();
			confirm_button_text += ' "'+banner_ad_name+'"';
			var message_prefix = 'Are you sure you want to '+mode+' the banner &ldquo;'+banner_ad_name+'&rdquo; now?';
			var active_banners_in_set = $(this).parent().parent().parent().parent().parent().children('.banner-ad-item:not(.inactive)');
			if (active_banners_in_set.length == 1 && mode == 'deactivate') {
				message_prefix += '<br><br>WARNING: This is the last banner ad in the region. If you proceed, the region will be empty.';
			}
			var active_checkbox = this;
			Biscuit.Crumbs.Confirm('<h4><strong>'+message_prefix+'</strong></h4><p>This action will take immediate effect.</p>',function() {
				var banner_id = $(active_checkbox).attr('id').substr(20);	// Everything after 'banner-ad-is-active-'
				var post_params = 'request_token='+window.sortable_request_token+'&banner_ad[is_active]='+((is_checked) ? 1 : 0);
				var updated_banner_item_id = $(active_checkbox).parent().parent().parent().parent().attr('id');
				var banner_set_id = $(active_checkbox).parent().parent().parent().parent().parent().attr('id');
				var throbber_id = 'region-throbber-'+banner_set_id.substr(11);
				Biscuit.Crumbs.ShowThrobber(throbber_id);
				Biscuit.Ajax.Request('/banner-ads/edit/'+banner_id,'server_action',{
					type: 'post',
					data: post_params,
					success: function() {
						Biscuit.Crumbs.HideThrobber(throbber_id);
						if (mode == 'activate') {
							$('#'+updated_banner_item_id).removeClass('inactive');
						} else {
							$('#'+updated_banner_item_id).addClass('inactive');
						}
						$('#'+updated_banner_item_id).effect('highlight',{color: '#93f586'});
					}
				});
			},confirm_button_text,function() {
				// Reverse the checked state of the checkbox on cancel
				$(active_checkbox).attr('checked',!$(active_checkbox).attr('checked'));
			});
		});
		$('#image-manager-button').click(function() {
			tinyBrowserPopUp('image',null);
			return false;
		});
	},
	RenumberBanners: function(banner_set_id) {
		var curr_num = 1;
		$('#banner-ads-'+banner_set_id+' .banner-ad-item').each(function() {
			// Restripe:
			$(this).removeClass('stripe-odd');
			$(this).removeClass('stripe-even');
			if (curr_num%2 == 0) {
				$(this).addClass('stripe-even');
			} else {
				$(this).addClass('stripe-odd');
			}
			// Change the sort number
			$('#'+this.id+' .sort-column').html(curr_num);
			curr_num++;
		});
	},
	HighlightBanners: function(region_id) {
		$('#banner-ads-'+region_id+' .banner-ad-item').each(function() {
			$(this).effect('highlight',{color: '#93f586'},1000,function() {
				$(this).css({background: ''});
			});
		});
	}
}

$(document).ready(function() {
	BannerAds.AddEditHandlers();
});