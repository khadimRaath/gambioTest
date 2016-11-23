'use strict';

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
gambio.widgets.module('slider_flyover', [], function (data) {

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

	var _remove_flyover = function _remove_flyover() {
		if (actual_area_id === 0) {
			if (request) {
				request.abort();
			}
			$(flyover_container).remove();
		}
	};

	var _create_container = function _create_container() {
		if ($(flyover_container).length === 0) {
			$('body').append('<div id="slider_flyover_container"></div>');
		}
	};

	var _box_position = function _box_position(self) {
		self.off('mousemove');

		self.on('mousemove', function (e) {
			mouse_pos_x = e.pageX;
			mouse_pos_y = e.pageY;
		});
	};

	var _show_flyover = function _show_flyover(self, response) {
		var id = self.attr('id').split('_');
		if (id[1] === actual_area_id && $.trim(response) !== '' && $.trim(response.replace(/<br \/>/g, '')) !== '') {
			$(flyover_container).addClass(actual_area_id);
			$(flyover_container).html(response);
			$(flyover_container).css('left', mouse_pos_x + 5);
			$(flyover_container).css('top', mouse_pos_y);
			if (mouse_pos_x - $(document).scrollLeft() + $(flyover_container).width() + 30 >= $(window).width()) {

				$(flyover_container).css('left', mouse_pos_x - $(flyover_container).width() - 25);
			}
			if (mouse_pos_y - $(document).scrollTop() + $(flyover_container).height() + 30 >= $(window).height()) {
				$(flyover_container).css('top', mouse_pos_y - $(flyover_container).height() - 25);
			}
			$(flyover_container).show();
		}
	};

	var _get_flyover_info = function _get_flyover_info(self) {
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
				data: { 'action': 'get_flyover_content', 'slider_image_area_id': actual_area_id },
				success: function success(response) {
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
	module.init = function (done) {
		var sliderAreaSelectorString = '.swiper-slide area';

		$this.on('mouseenter', sliderAreaSelectorString, function () {
			_create_container();
			_box_position($(this));
			_get_flyover_info($(this));
		}).on('mouseleave', sliderAreaSelectorString, function () {
			actual_area_id = 0;
			_remove_flyover();
		});

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2xpZGVyX2ZseW92ZXIuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJmbHlvdmVyX2NvbnRhaW5lciIsIm1vdXNlX3Bvc194IiwibW91c2VfcG9zX3kiLCJhY3R1YWxfYXJlYV9pZCIsInJlcXVlc3QiLCJfcmVtb3ZlX2ZseW92ZXIiLCJhYm9ydCIsInJlbW92ZSIsIl9jcmVhdGVfY29udGFpbmVyIiwibGVuZ3RoIiwiYXBwZW5kIiwiX2JveF9wb3NpdGlvbiIsInNlbGYiLCJvZmYiLCJvbiIsImUiLCJwYWdlWCIsInBhZ2VZIiwiX3Nob3dfZmx5b3ZlciIsInJlc3BvbnNlIiwiaWQiLCJhdHRyIiwic3BsaXQiLCJ0cmltIiwicmVwbGFjZSIsImFkZENsYXNzIiwiaHRtbCIsImNzcyIsImRvY3VtZW50Iiwic2Nyb2xsTGVmdCIsIndpZHRoIiwid2luZG93Iiwic2Nyb2xsVG9wIiwiaGVpZ2h0Iiwic2hvdyIsIl9nZXRfZmx5b3Zlcl9pbmZvIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJhc3luYyIsInN1Y2Nlc3MiLCJpbml0IiwiZG9uZSIsInNsaWRlckFyZWFTZWxlY3RvclN0cmluZyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLGdCQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVcsRUFEWjtBQUFBLEtBRUNDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRlg7QUFBQSxLQUdDRCxTQUFTLEVBSFY7QUFBQSxLQUtDTyxvQkFBb0IsMkJBTHJCO0FBQUEs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QUEwQixPQUExQixDQUF2QixFQUEyRDtBQUMxRCxPQUFJakIsT0FBSixFQUFhO0FBQ1pBLFlBQVFFLEtBQVI7QUFDQTs7QUFFREYsYUFBVVIsRUFBRXdDLElBQUYsQ0FBTztBQUNoQkMsVUFBTSxNQURVO0FBRWhCQyxTQUFLLGdDQUZXO0FBR2hCQyxXQUFPLElBSFM7QUFJaEI3QyxVQUFNLEVBQUMsVUFBVSxxQkFBWCxFQUFrQyx3QkFBd0JTLGNBQTFELEVBSlU7QUFLaEJxQyxhQUFTLGlCQUFTckIsUUFBVCxFQUFtQjtBQUMzQkQsbUJBQWNOLElBQWQsRUFBb0JPLFFBQXBCO0FBQ0E7QUFQZSxJQUFQLENBQVY7QUFTQTtBQUNELEVBbkJEOztBQXFCQTs7QUFFQTs7OztBQUlBMUIsUUFBT2dELElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUIsTUFBSUMsMkJBQTJCLG9CQUEvQjs7QUFFQWhELFFBQ0VtQixFQURGLENBQ0ssWUFETCxFQUNtQjZCLHdCQURuQixFQUM2QyxZQUFXO0FBQ3REbkM7QUFDQUcsaUJBQWNmLEVBQUUsSUFBRixDQUFkO0FBQ0F1QyxxQkFBa0J2QyxFQUFFLElBQUYsQ0FBbEI7QUFDQSxHQUxGLEVBTUVrQixFQU5GLENBTUssWUFOTCxFQU1tQjZCLHdCQU5uQixFQU02QyxZQUFXO0FBQ3REeEMsb0JBQWlCLENBQWpCO0FBQ0FFO0FBQ0EsR0FURjs7QUFXQXFDO0FBQ0EsRUFmRDs7QUFpQkE7QUFDQSxRQUFPakQsTUFBUDtBQUNBLENBdEhGIiwiZmlsZSI6IndpZGdldHMvc2xpZGVyX2ZseW92ZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHNsaWRlcl9mbHlvdmVyLmpzIDIwMTYtMDItMDQgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEdldHMgdGhlIHNpemUgb2YgdGhlIGJpZ2dlc3QgaW1hZ2UgZnJvbSB0aGUgYXBwbGllZCBlbGVtZW50IGFuZCBwdXRzIHRoZSBwcmV2aW91cyBhbmQgbmV4dCBidXR0b25zIHRvIHRoZSByaWdodFxuICogcG9zaXRpb24sIGlmIHRoZSBzY3JlZW4td2lkdGggaXMgYmlnZ2VyIHRoYW4gMTkyMHB4LlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdzbGlkZXJfZmx5b3ZlcicsXG5cdFxuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cdFx0XG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9LFxuXHRcdFx0XG5cdFx0XHRmbHlvdmVyX2NvbnRhaW5lciA9ICcjc2xpZGVyX2ZseW92ZXJfY29udGFpbmVyJyxcblx0XHRcdG1vdXNlX3Bvc194LFxuXHRcdFx0bW91c2VfcG9zX3ksXG5cdFx0XHRhY3R1YWxfYXJlYV9pZCxcblx0XHRcdHJlcXVlc3Q7XG5cdFx0XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBQUklWQVRFIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cdFx0XG5cdFx0dmFyIF9yZW1vdmVfZmx5b3ZlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKGFjdHVhbF9hcmVhX2lkID09PSAwKSB7XG5cdFx0XHRcdGlmIChyZXF1ZXN0KSB7XG5cdFx0XHRcdFx0cmVxdWVzdC5hYm9ydCgpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCQoZmx5b3Zlcl9jb250YWluZXIpLnJlbW92ZSgpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9jcmVhdGVfY29udGFpbmVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJChmbHlvdmVyX2NvbnRhaW5lcikubGVuZ3RoID09PSAwKSB7XG5cdFx0XHRcdCQoJ2JvZHknKS5hcHBlbmQoJzxkaXYgaWQ9XCJzbGlkZXJfZmx5b3Zlcl9jb250YWluZXJcIj48L2Rpdj4nKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfYm94X3Bvc2l0aW9uID0gZnVuY3Rpb24oc2VsZikge1xuXHRcdFx0c2VsZi5vZmYoJ21vdXNlbW92ZScpO1xuXHRcdFx0XG5cdFx0XHRzZWxmLm9uKCdtb3VzZW1vdmUnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdG1vdXNlX3Bvc194ID0gZS5wYWdlWDtcblx0XHRcdFx0bW91c2VfcG9zX3kgPSBlLnBhZ2VZO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3Nob3dfZmx5b3ZlciA9IGZ1bmN0aW9uKHNlbGYsIHJlc3BvbnNlKSB7XG5cdFx0XHR2YXIgaWQgPSBzZWxmLmF0dHIoJ2lkJykuc3BsaXQoJ18nKTtcblx0XHRcdGlmIChpZFsxXSA9PT0gYWN0dWFsX2FyZWFfaWQgJiYgJC50cmltKHJlc3BvbnNlKSAhPT0gJydcblx0XHRcdFx0JiYgJC50cmltKHJlc3BvbnNlLnJlcGxhY2UoLzxiciBcXC8+L2csICcnKSkgIT09ICcnKSB7XG5cdFx0XHRcdCQoZmx5b3Zlcl9jb250YWluZXIpLmFkZENsYXNzKGFjdHVhbF9hcmVhX2lkKTtcblx0XHRcdFx0JChmbHlvdmVyX2NvbnRhaW5lcikuaHRtbChyZXNwb25zZSk7XG5cdFx0XHRcdCQoZmx5b3Zlcl9jb250YWluZXIpLmNzcygnbGVmdCcsIG1vdXNlX3Bvc194ICsgNSk7XG5cdFx0XHRcdCQoZmx5b3Zlcl9jb250YWluZXIpLmNzcygndG9wJywgbW91c2VfcG9zX3kpO1xuXHRcdFx0XHRpZiAoKG1vdXNlX3Bvc194IC0gJChkb2N1bWVudCkuc2Nyb2xsTGVmdCgpICsgJChmbHlvdmVyX2NvbnRhaW5lcikud2lkdGgoKVxuXHRcdFx0XHRcdCsgMzApID49ICQod2luZG93KS53aWR0aCgpKSB7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JChmbHlvdmVyX2NvbnRhaW5lcilcblx0XHRcdFx0XHRcdC5jc3MoJ2xlZnQnLCBtb3VzZV9wb3NfeCAtICQoZmx5b3Zlcl9jb250YWluZXIpLndpZHRoKCkgLSAyNSk7XG5cdFx0XHRcdH1cblx0XHRcdFx0aWYgKChtb3VzZV9wb3NfeSAtICQoZG9jdW1lbnQpLnNjcm9sbFRvcCgpICsgJChmbHlvdmVyX2NvbnRhaW5lcikuaGVpZ2h0KClcblx0XHRcdFx0XHQrIDMwKSA+PSAkKHdpbmRvdykuaGVpZ2h0KCkpIHtcblx0XHRcdFx0XHQkKGZseW92ZXJfY29udGFpbmVyKVxuXHRcdFx0XHRcdFx0LmNzcygndG9wJywgbW91c2VfcG9zX3kgLSAkKGZseW92ZXJfY29udGFpbmVyKS5oZWlnaHQoKSAtIDI1KTtcblx0XHRcdFx0fVxuXHRcdFx0XHQkKGZseW92ZXJfY29udGFpbmVyKS5zaG93KCk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2dldF9mbHlvdmVyX2luZm8gPSBmdW5jdGlvbihzZWxmKSB7XG5cdFx0XHR2YXIgaWQgPSBzZWxmLmF0dHIoJ2lkJykuc3BsaXQoJ18nKTtcblx0XHRcdGFjdHVhbF9hcmVhX2lkID0gaWRbMV07XG5cdFx0XHRcblx0XHRcdGlmIChhY3R1YWxfYXJlYV9pZCAhPT0gJChmbHlvdmVyX2NvbnRhaW5lcikuYXR0cignY2xhc3MnKSkge1xuXHRcdFx0XHRpZiAocmVxdWVzdCkge1xuXHRcdFx0XHRcdHJlcXVlc3QuYWJvcnQoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0cmVxdWVzdCA9ICQuYWpheCh7XG5cdFx0XHRcdFx0dHlwZTogJ1BPU1QnLFxuXHRcdFx0XHRcdHVybDogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPVNsaWRlcicsXG5cdFx0XHRcdFx0YXN5bmM6IHRydWUsXG5cdFx0XHRcdFx0ZGF0YTogeydhY3Rpb24nOiAnZ2V0X2ZseW92ZXJfY29udGVudCcsICdzbGlkZXJfaW1hZ2VfYXJlYV9pZCc6IGFjdHVhbF9hcmVhX2lkfSxcblx0XHRcdFx0XHRzdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0X3Nob3dfZmx5b3ZlcihzZWxmLCByZXNwb25zZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0dmFyIHNsaWRlckFyZWFTZWxlY3RvclN0cmluZyA9ICcuc3dpcGVyLXNsaWRlIGFyZWEnO1xuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ21vdXNlZW50ZXInLCBzbGlkZXJBcmVhU2VsZWN0b3JTdHJpbmcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdF9jcmVhdGVfY29udGFpbmVyKCk7XG5cdFx0XHRcdFx0X2JveF9wb3NpdGlvbigkKHRoaXMpKTtcblx0XHRcdFx0XHRfZ2V0X2ZseW92ZXJfaW5mbygkKHRoaXMpKTtcblx0XHRcdFx0fSlcblx0XHRcdFx0Lm9uKCdtb3VzZWxlYXZlJywgc2xpZGVyQXJlYVNlbGVjdG9yU3RyaW5nLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRhY3R1YWxfYXJlYV9pZCA9IDA7XG5cdFx0XHRcdFx0X3JlbW92ZV9mbHlvdmVyKCk7XG5cdFx0XHRcdH0pO1xuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7Il19
