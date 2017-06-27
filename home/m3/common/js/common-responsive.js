/**
 * common javascript
 * for responsive page
 * charset utf-8
 * depends: jQuery.js
 *
 * log
 * 2017-06-09 created
 *
 */
$(function() {
	/**
	 * extended the jQuery object
	 */
	jQuery.extend({
		tilt: function(){
			var orientation = window.orientation;
			if(orientation == 0){
				$("body").addClass("portrait");
				$("body").removeClass("landscape");
			}else{
				$("body").addClass("landscape");
				$("body").removeClass("portrait");
			}
		}
	});
	
	$.tilt();
	
	$('#wrapper nav .gNavi').on('click', 'li', function(){
		$(".open").not(this).removeClass("open");
		$(this).toggleClass("open");
		var target = "." + $(this).attr('id') + 'Menu';
		$('#gNaviMenu div:not('+target+')').hide().promise().done(function(){
			$('#gNaviMenu '+target).slideToggle(0);
		});
	});
	
	$(window).on('load', function(){
		$(window).resize();
	});
});


(function($) {
	$.event.tap = function(o) {
		o.bind('touchstart', onTouchStart_);
		function onTouchStart_(e) {
			e.preventDefault();
			o.data('event.tap.moved', false)
				.one('touchmove', onTouchMove_)
				.one('touchend', onTouchEnd_);
		}

		function onTouchMove_(e) {
			o.data('event.tap.moved', true);
		}

		function onTouchEnd_(e) {
			if (!o.data('event.tap.moved')) {
				o.unbind('touchmove', onTouchMove_);
				o.trigger('tap').click();
			}
		}
	};

	if ('ontouchend' in document) {
		$.fn.tap = function(data, fn) {
			if (fn == null) {
				fn = data;
				data = null;
			}

			if (arguments.length > 0) {
				this.bind('tap', data, fn);
				$.event.tap(this);
			} else {
				this.trigger('tap');
			}
			return this;
		};

		if ($.attrFn) {
			$.attrFn['tap'] = true;
		}
	} else {
		$.fn.tap = $.fn.click;
	}
})(jQuery);
