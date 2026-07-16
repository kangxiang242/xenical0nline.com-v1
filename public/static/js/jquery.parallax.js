/*
Parallax Plugin V 1.0
Author: Nino Zumstein
Date  : 12.09.2016
*/
(function ($) {
	$.fn.parallax = function (options) {

		var parallax_element = this;
		var settings = {
			speed: '100',
			ascending: true,
			delay: '1000',
            deviation:0
		};

		if (options) {
			$.extend(settings, options);
		}

		var ad = "+";
		if (!settings['ascending'] == true) {
			var ad = "-";
		}

		$(window).scroll(function () {

			var wScroll = $(this).scrollTop();
            var sz = wScroll / settings['speed'] - settings['deviation'];

			parallax_element.css({
                "background-position":"50% "+sz+"%",
				"transition-duration": settings['delay'] + "ms"
			});
		});
	}
}(jQuery));
