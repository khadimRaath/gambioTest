'use strict';

/* --------------------------------------------------------------
 image_gallery.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that opens the gallery modal layer (which is
 * used for the article pictures)
 */
gambio.widgets.module('image_gallery', [gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal', gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $template = null,
	    $body = $('body'),
	    layer = null,
	    configuration = { // Modal layer configuration
		noTemplate: false,
		preloader: true,
		closeOnOuter: true,
		dialogClass: 'product_images',
		gallery: {
			enabled: true
		}
	},
	    defaults = {
		target: '.swiper-slide', // Selector for the click event listener
		template: '#product_image_layer', // Template that is used for the layer
		breakpoint: 40 // Maximum breakpoint for mobile view mode
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Click event handler that configures the swiper(s)
  * inside the layer and opens it afterwards
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _clickHandler = function _clickHandler(e) {
		e.preventDefault();

		// Only open in desktop mode
		if (jse.libs.template.responsive.breakpoint().id > options.breakpoint) {
			var $self = $(this),
			    $swiper = $template.find('[data-swiper-slider-options]'),
			    dataset = $self.data(),
			    index = dataset.index || dataset.swiperSlideIndex || 0;

			// Loop that replaces the initial slide of
			// each swiper inside the layer
			$swiper.each(function () {
				$(this).attr('data-swiper-init-slide', index);
			});

			// Opens the modal layer
			layer = jse.libs.template.modal.custom(configuration);
		}
	};

	/**
  * Handler which closes an opened gallery if the
  * screen width gets under the size of an desktop mode
  * @private
  */
	var _breakpointHandler = function _breakpointHandler() {
		if (jse.libs.template.responsive.breakpoint().id <= options.breakpoint && layer) {
			layer.close(true);
		}
	};

	/**
  * Event handler to append / remove slides from the
  * gallery layer swipers
  * @param       {object}        e           jQuery event object
  * @param       {object}        d           JSON data of the images
  * @private
  */
	var _addSlides = function _addSlides(e, d) {

		// Loops through all swipers inside the layer
		$template.find('.swiper-container template').each(function () {
			var $tpl = $(this),
			    $slideContainer = $tpl.siblings('.swiper-wrapper');

			// Loops through each category inside the images array
			$.each(d, function (category, dataset) {
				var catName = category + '-category',
				    add = '',
				    markup = $tpl.html();

				// Generate the markup for the new slides
				// and replace the old images of that category
				// eith the new ones
				$.each(dataset || [], function (i, v) {
					v.className = catName;
					add += Mustache.render(markup, v);
				});

				$slideContainer.find('.' + catName).remove();

				$slideContainer.append(add);
			});
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  *
  * @constructor
  */
	module.init = function (done) {
		configuration.template = options.template;
		$template = $(options.template);

		$this.on('click', options.target, _clickHandler).on(jse.libs.template.events.SLIDES_UPDATE(), _addSlides);

		$body.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW1hZ2VfZ2FsbGVyeS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0ZW1wbGF0ZSIsIiRib2R5IiwibGF5ZXIiLCJjb25maWd1cmF0aW9uIiwibm9UZW1wbGF0ZSIsInByZWxvYWRlciIsImNsb3NlT25PdXRlciIsImRpYWxvZ0NsYXNzIiwiZ2FsbGVyeSIsImVuYWJsZWQiLCJkZWZhdWx0cyIsInRhcmdldCIsInRlbXBsYXRlIiwiYnJlYWtwb2ludCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2xpY2tIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwianNlIiwibGlicyIsInJlc3BvbnNpdmUiLCJpZCIsIiRzZWxmIiwiJHN3aXBlciIsImZpbmQiLCJkYXRhc2V0IiwiaW5kZXgiLCJzd2lwZXJTbGlkZUluZGV4IiwiZWFjaCIsImF0dHIiLCJtb2RhbCIsImN1c3RvbSIsIl9icmVha3BvaW50SGFuZGxlciIsImNsb3NlIiwiX2FkZFNsaWRlcyIsImQiLCIkdHBsIiwiJHNsaWRlQ29udGFpbmVyIiwic2libGluZ3MiLCJjYXRlZ29yeSIsImNhdE5hbWUiLCJhZGQiLCJtYXJrdXAiLCJodG1sIiwiaSIsInYiLCJjbGFzc05hbWUiLCJNdXN0YWNoZSIsInJlbmRlciIsInJlbW92ZSIsImFwcGVuZCIsImluaXQiLCJkb25lIiwib24iLCJldmVudHMiLCJTTElERVNfVVBEQVRFIiwiQlJFQUtQT0lOVCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLGVBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLDBCQURqQixFQUVDSCxPQUFPRyxNQUFQLEdBQWdCLGFBRmpCLEVBR0NILE9BQU9HLE1BQVAsR0FBZ0IsY0FIakIsRUFJQ0gsT0FBT0csTUFBUCxHQUFnQixrQkFKakIsQ0FI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RixDQUNPLE1BQU1nQixPQURiLEVBRUVTLE1BRkY7O0FBSUFaLG9CQUFnQmEsTUFBaEIsQ0FBdUJULEdBQXZCO0FBQ0EsSUFsQkQ7QUFtQkEsR0ExQkY7QUEyQkEsRUE5QkQ7O0FBaUNGOztBQUVFOzs7OztBQUtBL0MsUUFBT3lELElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJsRCxnQkFBY1MsUUFBZCxHQUF5QkUsUUFBUUYsUUFBakM7QUFDQVosY0FBWUQsRUFBRWUsUUFBUUYsUUFBVixDQUFaOztBQUVBZCxRQUNFd0QsRUFERixDQUNLLE9BREwsRUFDY3hDLFFBQVFILE1BRHRCLEVBQzhCSyxhQUQ5QixFQUVFc0MsRUFGRixDQUVLbkMsSUFBSUMsSUFBSixDQUFTUixRQUFULENBQWtCMkMsTUFBbEIsQ0FBeUJDLGFBQXpCLEVBRkwsRUFFK0NyQixVQUYvQzs7QUFJQWxDLFFBQ0VxRCxFQURGLENBQ0tuQyxJQUFJQyxJQUFKLENBQVNSLFFBQVQsQ0FBa0IyQyxNQUFsQixDQUF5QkUsVUFBekIsRUFETCxFQUM0Q3hCLGtCQUQ1Qzs7QUFHQW9CO0FBQ0EsRUFaRDs7QUFjQTtBQUNBLFFBQU8xRCxNQUFQO0FBQ0EsQ0E3SUYiLCJmaWxlIjoid2lkZ2V0cy9pbWFnZV9nYWxsZXJ5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbWFnZV9nYWxsZXJ5LmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IG9wZW5zIHRoZSBnYWxsZXJ5IG1vZGFsIGxheWVyICh3aGljaCBpc1xuICogdXNlZCBmb3IgdGhlIGFydGljbGUgcGljdHVyZXMpXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J2ltYWdlX2dhbGxlcnknLFxuXG5cdFtcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL21vZGFsLmV4dC1tYWduaWZpYycsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbCcsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvcmVzcG9uc2l2ZSdcblx0XSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdCR0ZW1wbGF0ZSA9IG51bGwsXG5cdFx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHRcdGxheWVyID0gbnVsbCxcblx0XHRcdGNvbmZpZ3VyYXRpb24gPSB7ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gTW9kYWwgbGF5ZXIgY29uZmlndXJhdGlvblxuXHRcdFx0XHRub1RlbXBsYXRlOiBmYWxzZSxcblx0XHRcdFx0cHJlbG9hZGVyOiB0cnVlLFxuXHRcdFx0XHRjbG9zZU9uT3V0ZXI6IHRydWUsXG5cdFx0XHRcdGRpYWxvZ0NsYXNzOiAncHJvZHVjdF9pbWFnZXMnLFxuXHRcdFx0XHRnYWxsZXJ5OiB7XG5cdFx0XHRcdFx0ZW5hYmxlZDogdHJ1ZVxuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdHRhcmdldDogJy5zd2lwZXItc2xpZGUnLCAvLyBTZWxlY3RvciBmb3IgdGhlIGNsaWNrIGV2ZW50IGxpc3RlbmVyXG5cdFx0XHRcdHRlbXBsYXRlOiAnI3Byb2R1Y3RfaW1hZ2VfbGF5ZXInLCAvLyBUZW1wbGF0ZSB0aGF0IGlzIHVzZWQgZm9yIHRoZSBsYXllclxuXHRcdFx0XHRicmVha3BvaW50OiA0MCAvLyBNYXhpbXVtIGJyZWFrcG9pbnQgZm9yIG1vYmlsZSB2aWV3IG1vZGVcblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogQ2xpY2sgZXZlbnQgaGFuZGxlciB0aGF0IGNvbmZpZ3VyZXMgdGhlIHN3aXBlcihzKVxuXHRcdCAqIGluc2lkZSB0aGUgbGF5ZXIgYW5kIG9wZW5zIGl0IGFmdGVyd2FyZHNcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblxuXHRcdFx0Ly8gT25seSBvcGVuIGluIGRlc2t0b3AgbW9kZVxuXHRcdFx0aWYgKGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkID4gb3B0aW9ucy5icmVha3BvaW50KSB7XG5cdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0JHN3aXBlciA9ICR0ZW1wbGF0ZS5maW5kKCdbZGF0YS1zd2lwZXItc2xpZGVyLW9wdGlvbnNdJyksXG5cdFx0XHRcdFx0ZGF0YXNldCA9ICRzZWxmLmRhdGEoKSxcblx0XHRcdFx0XHRpbmRleCA9IGRhdGFzZXQuaW5kZXggfHwgZGF0YXNldC5zd2lwZXJTbGlkZUluZGV4IHx8IDA7XG5cblx0XHRcdFx0Ly8gTG9vcCB0aGF0IHJlcGxhY2VzIHRoZSBpbml0aWFsIHNsaWRlIG9mXG5cdFx0XHRcdC8vIGVhY2ggc3dpcGVyIGluc2lkZSB0aGUgbGF5ZXJcblx0XHRcdFx0JHN3aXBlci5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCQodGhpcykuYXR0cignZGF0YS1zd2lwZXItaW5pdC1zbGlkZScsIGluZGV4KTtcblx0XHRcdFx0fSk7XG5cblx0XHRcdFx0Ly8gT3BlbnMgdGhlIG1vZGFsIGxheWVyXG5cdFx0XHRcdGxheWVyID0ganNlLmxpYnMudGVtcGxhdGUubW9kYWwuY3VzdG9tKGNvbmZpZ3VyYXRpb24pO1xuXHRcdFx0fVxuXG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgd2hpY2ggY2xvc2VzIGFuIG9wZW5lZCBnYWxsZXJ5IGlmIHRoZVxuXHRcdCAqIHNjcmVlbiB3aWR0aCBnZXRzIHVuZGVyIHRoZSBzaXplIG9mIGFuIGRlc2t0b3AgbW9kZVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9icmVha3BvaW50SGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCAmJiBsYXllcikge1xuXHRcdFx0XHRsYXllci5jbG9zZSh0cnVlKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciB0byBhcHBlbmQgLyByZW1vdmUgc2xpZGVzIGZyb20gdGhlXG5cdFx0ICogZ2FsbGVyeSBsYXllciBzd2lwZXJzXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBkICAgICAgICAgICBKU09OIGRhdGEgb2YgdGhlIGltYWdlc1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9hZGRTbGlkZXMgPSBmdW5jdGlvbihlLCBkKSB7XG5cblx0XHRcdC8vIExvb3BzIHRocm91Z2ggYWxsIHN3aXBlcnMgaW5zaWRlIHRoZSBsYXllclxuXHRcdFx0JHRlbXBsYXRlXG5cdFx0XHRcdC5maW5kKCcuc3dpcGVyLWNvbnRhaW5lciB0ZW1wbGF0ZScpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkdHBsID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdCRzbGlkZUNvbnRhaW5lciA9ICR0cGwuc2libGluZ3MoJy5zd2lwZXItd3JhcHBlcicpO1xuXG5cdFx0XHRcdFx0Ly8gTG9vcHMgdGhyb3VnaCBlYWNoIGNhdGVnb3J5IGluc2lkZSB0aGUgaW1hZ2VzIGFycmF5XG5cdFx0XHRcdFx0JC5lYWNoKGQsIGZ1bmN0aW9uKGNhdGVnb3J5LCBkYXRhc2V0KSB7XG5cdFx0XHRcdFx0XHR2YXIgY2F0TmFtZSA9IGNhdGVnb3J5ICsgJy1jYXRlZ29yeScsXG5cdFx0XHRcdFx0XHRcdGFkZCA9ICcnLFxuXHRcdFx0XHRcdFx0XHRtYXJrdXAgPSAkdHBsLmh0bWwoKTtcblxuXHRcdFx0XHRcdFx0Ly8gR2VuZXJhdGUgdGhlIG1hcmt1cCBmb3IgdGhlIG5ldyBzbGlkZXNcblx0XHRcdFx0XHRcdC8vIGFuZCByZXBsYWNlIHRoZSBvbGQgaW1hZ2VzIG9mIHRoYXQgY2F0ZWdvcnlcblx0XHRcdFx0XHRcdC8vIGVpdGggdGhlIG5ldyBvbmVzXG5cdFx0XHRcdFx0XHQkLmVhY2goZGF0YXNldCB8fCBbXSwgZnVuY3Rpb24oaSwgdikge1xuXHRcdFx0XHRcdFx0XHR2LmNsYXNzTmFtZSA9IGNhdE5hbWU7XG5cdFx0XHRcdFx0XHRcdGFkZCArPSBNdXN0YWNoZS5yZW5kZXIobWFya3VwLCB2KTtcblx0XHRcdFx0XHRcdH0pO1xuXG5cdFx0XHRcdFx0XHQkc2xpZGVDb250YWluZXJcblx0XHRcdFx0XHRcdFx0LmZpbmQoJy4nICsgY2F0TmFtZSlcblx0XHRcdFx0XHRcdFx0LnJlbW92ZSgpO1xuXG5cdFx0XHRcdFx0XHQkc2xpZGVDb250YWluZXIuYXBwZW5kKGFkZCk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH0pO1xuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKlxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Y29uZmlndXJhdGlvbi50ZW1wbGF0ZSA9IG9wdGlvbnMudGVtcGxhdGU7XG5cdFx0XHQkdGVtcGxhdGUgPSAkKG9wdGlvbnMudGVtcGxhdGUpO1xuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2NsaWNrJywgb3B0aW9ucy50YXJnZXQsIF9jbGlja0hhbmRsZXIpXG5cdFx0XHRcdC5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuU0xJREVTX1VQREFURSgpLCBfYWRkU2xpZGVzKTtcblxuXHRcdFx0JGJvZHlcblx0XHRcdFx0Lm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5CUkVBS1BPSU5UKCksIF9icmVha3BvaW50SGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
