BannerRotator = {
	init: function() {
		jQuery('div.banner-ads').each(function() {
			var my_classes = jQuery(this).attr('class').split(' ');
			for (var index in my_classes) {
				var curr_class = my_classes[index];
				if (typeof(curr_class) == 'string') {
					if (curr_class.match(/interval-([0-9]+)/)) {
						var my_interval = curr_class.substr(9);
					}
					if (curr_class.match(/(fade|scrollLeft|scrollRight)/)) {
						var my_effect = curr_class;
					}
				}
			}
			if (my_effect != undefined) {
				if (my_interval == undefined) {
					var my_interval = 5000;
				}
				Biscuit.Console.log("Found banner ad region to cycle: "+this.id);
				Biscuit.Console.log("Effect: "+my_effect);
				Biscuit.Console.log("Interval: "+my_interval);
				jQuery(this).cycle({
					fx: my_effect,
					timeout: my_interval,
					pause: true
				})
			}
		});
	}
}

jQuery(document).ready(function() {
	BannerRotator.init();
});