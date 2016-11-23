/* --------------------------------------------------------------
 slider_flyover.js 2016-02-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gets the size of the biggest image from the applied element and puts the previous and next buttons to the right
 * position, if the screen-width is bigger than 1920px.
 */
gambio.widgets.module(
	'slider_flyover',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ########## VARIABLE INITIALIZATION ##########
		
		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {},
			
			flyover_container = '#slider_flyover_container',
			mouse_pos_x,
			mouse_pos_y,
			actual_area_id,
			request;
		
		
		// ########## PRIVATE FUNCTIONS ##########
		
		var _remove_flyover = function() {
			if (actual_area_id === 0) {
				if (request) {
					request.abort();
				}
				$(flyover_container).remove();
			}
		};
		
		var _create_container = function() {
			if ($(flyover_container).length === 0) {
				$('body').append('<div id="slider_flyover_container"></div>');
			}
		};
		
		var _box_position = function(self) {
			self.off('mousemove');
			
			self.on('mousemove', function(e) {
				mouse_pos_x = e.pageX;
				mouse_pos_y = e.pageY;
			});
		};
		
		var _show_flyover = function(self, response) {
			var id = self.attr('id').split('_');
			if (id[1] === actual_area_id && $.trim(response) !== ''
				&& $.trim(response.replace(/<br \/>/g, '')) !== '') {
				$(flyover_container).addClass(actual_area_id);
				$(flyover_container).html(response);
				$(flyover_container).css('left', mouse_pos_x + 5);
				$(flyover_container).css('top', mouse_pos_y);
				if ((mouse_pos_x - $(document).scrollLeft() + $(flyover_container).width()
					+ 30) >= $(window).width()) {
					
					$(flyover_container)
						.css('left', mouse_pos_x - $(flyover_container).width() - 25);
				}
				if ((mouse_pos_y - $(document).scrollTop() + $(flyover_container).height()
					+ 30) >= $(window).height()) {
					$(flyover_container)
						.css('top', mouse_pos_y - $(flyover_container).height() - 25);
				}
				$(flyover_container).show();
			}
		};
		
		var _get_flyover_info = function(self) {
			var id = self.attr('id').split('_');
			actual_area_id = id[1];
			
			if (actual_area_id !== $(flyover_container).attr('class')) {
				if (request) {
					request.abort();
				}
				
				request = $.ajax({
					type: 'POST',
					url: 'request_port.php?module=Slider',
					async: true,
					data: {'action': 'get_flyover_content', 'slider_image_area_id': actual_area_id},
					success: function(response) {
						_show_flyover(self, response);
					}
				});
			}
		};
		
		// ########## INITIALIZATION ##########
		
		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			var sliderAreaSelectorString = '.swiper-slide area';

			$this
				.on('mouseenter', sliderAreaSelectorString, function() {
					_create_container();
					_box_position($(this));
					_get_flyover_info($(this));
				})
				.on('mouseleave', sliderAreaSelectorString, function() {
					actual_area_id = 0;
					_remove_flyover();
				});

			done();
		};
		
		// Return data to widget engine
		return module;
	});