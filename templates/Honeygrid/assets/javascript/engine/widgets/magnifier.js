'use strict';

/* --------------------------------------------------------------
 magnifier.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that shows a zoom image on mouseover at a specific target
 */
gambio.widgets.module('magnifier', [gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    $target = null,
	    dataWasSet = false,
	    defaults = {
		// Default zoom image target selector
		target: null,
		// If true, the zoom image will always fill the whole target container
		keepInView: true,
		// The class that gets added to the body while the magnifier window is visible
		bodyClass: 'magnifier-active',
		// Maximum breakpoint for mobile view mode
		breakpoint: 60
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function to calculate the sizes and positions
  * (that doesn't alter until the browser gets resized).
  * The data object is stored at the source image and returned
  * to the caller function
  * @param    {object}        $self           jQuery selection of the source image
  * @param    {object}        $thisTarget     jQuery selection of the zoom image target container
  * @param    {object}        $image          jQuery selection of the zoom image itself
  * @return   {object}                        JSON object which contains the calculated sizes and positions
  * @private
  */
	var _prepareData = function _prepareData($self, $thisTarget, $image) {
		var dataset = {
			offset: $self.offset(),
			height: $self.height(),
			width: $self.width(),
			targetWidth: $thisTarget.width(),
			targetHeight: $thisTarget.height(),
			imageWidth: $image.width(),
			imageHeight: $image.height()
		};

		dataset.aspectX = -1 / (dataset.width / dataset.imageWidth);
		dataset.aspectY = -1 / (dataset.height / dataset.imageHeight);
		dataset.boundaryX = -1 * (dataset.imageWidth - dataset.targetWidth);
		dataset.boundaryY = -1 * (dataset.imageHeight - dataset.targetHeight);

		$self.data('magnifier', dataset);
		dataWasSet = true;

		return $.extend({}, dataset);
	};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the mousemove event. If the cursor gets
  * moved over the image, the cursor position will be scaled to
  * the zoom target and the zoom image gets positioned at that point
  * @param       {object}        e       jQuery event object
  * @private
  */
	var _mouseMoveHandler = function _mouseMoveHandler(e) {
		var $self = $(this),
		    dataset = $self.data('magnifier'),
		    $image = $target.children('img');

		dataset = dataset || _prepareData($self, $target, $image);

		var marginTop = dataset.aspectY * (e.pageY - dataset.offset.top) + dataset.targetHeight / 2,
		    marginLeft = dataset.aspectX * (e.pageX - dataset.offset.left) + dataset.targetWidth / 2;

		// If this setting is true, the zoomed image will always
		// fill the whole preview container
		if (options.keepInView) {
			marginTop = Math.min(0, marginTop);
			marginTop = Math.max(dataset.boundaryY, marginTop);
			marginLeft = Math.min(0, marginLeft);
			marginLeft = Math.max(dataset.boundaryX, marginLeft);
		}

		// Set the calculated styles
		$image.css({
			'margin-top': marginTop + 'px',
			'margin-left': marginLeft + 'px'
		});
	};

	/**
  * Event handler for the mouse enter event
  * on the target. It creates the zoom image
  * and embeds it to the magnifier target
  * @private
  */
	var _mouseEnterHandler = function _mouseEnterHandler(e) {

		// Only open in desktop mode
		if (jse.libs.template.responsive.breakpoint().id > options.breakpoint) {

			var $self = $(this),
			    dataset = $self.data(),
			    $preloader = $target.find('.preloader'),
			    $image = $('<img />'),
			    alt = $self.attr('alt'),
			    title = $self.attr('title');

			// CleansUp the magnifier target
			$target.children('img').remove();

			$preloader.show();
			$body.addClass(options.bodyClass);

			// Creates the image element and binds
			// a load handler to it, so that the
			// preloader gets hidden after the image
			// is loaded by the browser
			$image.one('load', function () {
				$image.css({
					'height': this.height + 'px',
					'width': this.width + 'px'
				});
				$preloader.hide();

				// Bind the mousemove handler to zoom to
				// the correct position of the image
				$self.off('mousemove.magnifier').on('mousemove.magnifier', _mouseMoveHandler);
			}).attr({ src: dataset.magnifierSrc, alt: alt, title: title });

			// Append the image to the maginifier target
			$target.append($image).show();
		}
	};

	/**
  * Handler for the browser resize event.
  * It removes all stored data so that a
  * recalculation is forced
  * @private
  */
	var _resizeHandler = function _resizeHandler() {
		if (dataWasSet) {
			$this.find('img[data-magnifier-src]').removeData('magnifier');

			dataWasSet = false;
		}
	};

	/**
  * Event handler for the mouseleave event. In case
  * the cursor leaves the image, the zoom target gets
  * hidden
  * @private
  */
	var _mouseLeaveHandler = function _mouseLeaveHandler() {
		$target.hide();
		$body.removeClass(options.bodyClass);

		$this.off('mouseenter').on('mouseenter', 'img[data-magnifier-src]', _mouseEnterHandler);
	};

	/**
  * Removes the mouseenter handler on touchstart,
  * so that the magnifier not starts on touch.
  * The function gets reactivated in the mouseleave
  * handler
  * @private
  */
	var _touchHandler = function _touchHandler() {
		$this.off('mouseenter');
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$target = $(options.target);

		$this.on('touchstart', 'img[data-magnifier-src]', _touchHandler).on('mouseenter', 'img[data-magnifier-src]', _mouseEnterHandler).on('mouseleave', 'img[data-magnifier-src]', _mouseLeaveHandler);

		$(window).on('resize', _resizeHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbWFnbmlmaWVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkdGFyZ2V0IiwiZGF0YVdhc1NldCIsImRlZmF1bHRzIiwidGFyZ2V0Iiwia2VlcEluVmlldyIsImJvZHlDbGFzcyIsImJyZWFrcG9pbnQiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3ByZXBhcmVEYXRhIiwiJHNlbGYiLCIkdGhpc1RhcmdldCIsIiRpbWFnZSIsImRhdGFzZXQiLCJvZmZzZXQiLCJoZWlnaHQiLCJ3aWR0aCIsInRhcmdldFdpZHRoIiwidGFyZ2V0SGVpZ2h0IiwiaW1hZ2VXaWR0aCIsImltYWdlSGVpZ2h0IiwiYXNwZWN0WCIsImFzcGVjdFkiLCJib3VuZGFyeVgiLCJib3VuZGFyeVkiLCJfbW91c2VNb3ZlSGFuZGxlciIsImUiLCJjaGlsZHJlbiIsIm1hcmdpblRvcCIsInBhZ2VZIiwidG9wIiwibWFyZ2luTGVmdCIsInBhZ2VYIiwibGVmdCIsIk1hdGgiLCJtaW4iLCJtYXgiLCJjc3MiLCJfbW91c2VFbnRlckhhbmRsZXIiLCJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJyZXNwb25zaXZlIiwiaWQiLCIkcHJlbG9hZGVyIiwiZmluZCIsImFsdCIsImF0dHIiLCJ0aXRsZSIsInJlbW92ZSIsInNob3ciLCJhZGRDbGFzcyIsIm9uZSIsImhpZGUiLCJvZmYiLCJvbiIsInNyYyIsIm1hZ25pZmllclNyYyIsImFwcGVuZCIsIl9yZXNpemVIYW5kbGVyIiwicmVtb3ZlRGF0YSIsIl9tb3VzZUxlYXZlSGFuZGxlciIsInJlbW92ZUNsYXNzIiwiX3RvdWNoSGFuZGxlciIsImluaXQiLCJkb25lIiwid2luZG93Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxXQURELEVBR0MsQ0FDQ0YsT0FBT0csTUFB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QVpEOztBQWNBO0FBQ0EsUUFBT3ZFLE1BQVA7QUFDQSxDQTVORiIsImZpbGUiOiJ3aWRnZXRzL21hZ25pZmllci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbWFnbmlmaWVyLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IHNob3dzIGEgem9vbSBpbWFnZSBvbiBtb3VzZW92ZXIgYXQgYSBzcGVjaWZpYyB0YXJnZXRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnbWFnbmlmaWVyJyxcblxuXHRbXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHQkdGFyZ2V0ID0gbnVsbCxcblx0XHRcdGRhdGFXYXNTZXQgPSBmYWxzZSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQvLyBEZWZhdWx0IHpvb20gaW1hZ2UgdGFyZ2V0IHNlbGVjdG9yXG5cdFx0XHRcdHRhcmdldDogbnVsbCxcblx0XHRcdFx0Ly8gSWYgdHJ1ZSwgdGhlIHpvb20gaW1hZ2Ugd2lsbCBhbHdheXMgZmlsbCB0aGUgd2hvbGUgdGFyZ2V0IGNvbnRhaW5lclxuXHRcdFx0XHRrZWVwSW5WaWV3OiB0cnVlLFxuXHRcdFx0XHQvLyBUaGUgY2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIHRoZSBib2R5IHdoaWxlIHRoZSBtYWduaWZpZXIgd2luZG93IGlzIHZpc2libGVcblx0XHRcdFx0Ym9keUNsYXNzOiAnbWFnbmlmaWVyLWFjdGl2ZScsXG5cdFx0XHRcdC8vIE1heGltdW0gYnJlYWtwb2ludCBmb3IgbW9iaWxlIHZpZXcgbW9kZVxuXHRcdFx0XHRicmVha3BvaW50OiA2MFxuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBjYWxjdWxhdGUgdGhlIHNpemVzIGFuZCBwb3NpdGlvbnNcblx0XHQgKiAodGhhdCBkb2Vzbid0IGFsdGVyIHVudGlsIHRoZSBicm93c2VyIGdldHMgcmVzaXplZCkuXG5cdFx0ICogVGhlIGRhdGEgb2JqZWN0IGlzIHN0b3JlZCBhdCB0aGUgc291cmNlIGltYWdlIGFuZCByZXR1cm5lZFxuXHRcdCAqIHRvIHRoZSBjYWxsZXIgZnVuY3Rpb25cblx0XHQgKiBAcGFyYW0gICAge29iamVjdH0gICAgICAgICRzZWxmICAgICAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBzb3VyY2UgaW1hZ2Vcblx0XHQgKiBAcGFyYW0gICAge29iamVjdH0gICAgICAgICR0aGlzVGFyZ2V0ICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSB6b29tIGltYWdlIHRhcmdldCBjb250YWluZXJcblx0XHQgKiBAcGFyYW0gICAge29iamVjdH0gICAgICAgICRpbWFnZSAgICAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSB6b29tIGltYWdlIGl0c2VsZlxuXHRcdCAqIEByZXR1cm4gICB7b2JqZWN0fSAgICAgICAgICAgICAgICAgICAgICAgIEpTT04gb2JqZWN0IHdoaWNoIGNvbnRhaW5zIHRoZSBjYWxjdWxhdGVkIHNpemVzIGFuZCBwb3NpdGlvbnNcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcHJlcGFyZURhdGEgPSBmdW5jdGlvbigkc2VsZiwgJHRoaXNUYXJnZXQsICRpbWFnZSkge1xuXHRcdFx0dmFyIGRhdGFzZXQgPSB7XG5cdFx0XHRcdG9mZnNldDogJHNlbGYub2Zmc2V0KCksXG5cdFx0XHRcdGhlaWdodDogJHNlbGYuaGVpZ2h0KCksXG5cdFx0XHRcdHdpZHRoOiAkc2VsZi53aWR0aCgpLFxuXHRcdFx0XHR0YXJnZXRXaWR0aDogJHRoaXNUYXJnZXQud2lkdGgoKSxcblx0XHRcdFx0dGFyZ2V0SGVpZ2h0OiAkdGhpc1RhcmdldC5oZWlnaHQoKSxcblx0XHRcdFx0aW1hZ2VXaWR0aDogJGltYWdlLndpZHRoKCksXG5cdFx0XHRcdGltYWdlSGVpZ2h0OiAkaW1hZ2UuaGVpZ2h0KClcblx0XHRcdH07XG5cblx0XHRcdGRhdGFzZXQuYXNwZWN0WCA9IC0xIC8gKGRhdGFzZXQud2lkdGggLyBkYXRhc2V0LmltYWdlV2lkdGgpO1xuXHRcdFx0ZGF0YXNldC5hc3BlY3RZID0gLTEgLyAoZGF0YXNldC5oZWlnaHQgLyBkYXRhc2V0LmltYWdlSGVpZ2h0KTtcblx0XHRcdGRhdGFzZXQuYm91bmRhcnlYID0gLTEgKiAoZGF0YXNldC5pbWFnZVdpZHRoIC0gZGF0YXNldC50YXJnZXRXaWR0aCk7XG5cdFx0XHRkYXRhc2V0LmJvdW5kYXJ5WSA9IC0xICogKGRhdGFzZXQuaW1hZ2VIZWlnaHQgLSBkYXRhc2V0LnRhcmdldEhlaWdodCk7XG5cblx0XHRcdCRzZWxmLmRhdGEoJ21hZ25pZmllcicsIGRhdGFzZXQpO1xuXHRcdFx0ZGF0YVdhc1NldCA9IHRydWU7XG5cblx0XHRcdHJldHVybiAkLmV4dGVuZCh7fSwgZGF0YXNldCk7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNlbW92ZSBldmVudC4gSWYgdGhlIGN1cnNvciBnZXRzXG5cdFx0ICogbW92ZWQgb3ZlciB0aGUgaW1hZ2UsIHRoZSBjdXJzb3IgcG9zaXRpb24gd2lsbCBiZSBzY2FsZWQgdG9cblx0XHQgKiB0aGUgem9vbSB0YXJnZXQgYW5kIHRoZSB6b29tIGltYWdlIGdldHMgcG9zaXRpb25lZCBhdCB0aGF0IHBvaW50XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VNb3ZlSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdGRhdGFzZXQgPSAkc2VsZi5kYXRhKCdtYWduaWZpZXInKSxcblx0XHRcdFx0JGltYWdlID0gJHRhcmdldC5jaGlsZHJlbignaW1nJyk7XG5cblx0XHRcdGRhdGFzZXQgPSBkYXRhc2V0IHx8IF9wcmVwYXJlRGF0YSgkc2VsZiwgJHRhcmdldCwgJGltYWdlKTtcblxuXHRcdFx0dmFyIG1hcmdpblRvcCA9IGRhdGFzZXQuYXNwZWN0WSAqIChlLnBhZ2VZIC0gZGF0YXNldC5vZmZzZXQudG9wKSArIGRhdGFzZXQudGFyZ2V0SGVpZ2h0IC8gMixcblx0XHRcdFx0bWFyZ2luTGVmdCA9IGRhdGFzZXQuYXNwZWN0WCAqIChlLnBhZ2VYIC0gZGF0YXNldC5vZmZzZXQubGVmdCkgKyBkYXRhc2V0LnRhcmdldFdpZHRoIC8gMjtcblxuXHRcdFx0Ly8gSWYgdGhpcyBzZXR0aW5nIGlzIHRydWUsIHRoZSB6b29tZWQgaW1hZ2Ugd2lsbCBhbHdheXNcblx0XHRcdC8vIGZpbGwgdGhlIHdob2xlIHByZXZpZXcgY29udGFpbmVyXG5cdFx0XHRpZiAob3B0aW9ucy5rZWVwSW5WaWV3KSB7XG5cdFx0XHRcdG1hcmdpblRvcCA9IE1hdGgubWluKDAsIG1hcmdpblRvcCk7XG5cdFx0XHRcdG1hcmdpblRvcCA9IE1hdGgubWF4KGRhdGFzZXQuYm91bmRhcnlZLCBtYXJnaW5Ub3ApO1xuXHRcdFx0XHRtYXJnaW5MZWZ0ID0gTWF0aC5taW4oMCwgbWFyZ2luTGVmdCk7XG5cdFx0XHRcdG1hcmdpbkxlZnQgPSBNYXRoLm1heChkYXRhc2V0LmJvdW5kYXJ5WCwgbWFyZ2luTGVmdCk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIFNldCB0aGUgY2FsY3VsYXRlZCBzdHlsZXNcblx0XHRcdCRpbWFnZS5jc3Moe1xuXHRcdFx0XHQgICAgICAgICAgICdtYXJnaW4tdG9wJzogbWFyZ2luVG9wICsgJ3B4Jyxcblx0XHRcdFx0ICAgICAgICAgICAnbWFyZ2luLWxlZnQnOiBtYXJnaW5MZWZ0ICsgJ3B4J1xuXHRcdFx0ICAgICAgICAgICB9KTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNlIGVudGVyIGV2ZW50XG5cdFx0ICogb24gdGhlIHRhcmdldC4gSXQgY3JlYXRlcyB0aGUgem9vbSBpbWFnZVxuXHRcdCAqIGFuZCBlbWJlZHMgaXQgdG8gdGhlIG1hZ25pZmllciB0YXJnZXRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VFbnRlckhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cblx0XHRcdC8vIE9ubHkgb3BlbiBpbiBkZXNrdG9wIG1vZGVcblx0XHRcdGlmIChqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCkge1xuXG5cdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0ZGF0YXNldCA9ICRzZWxmLmRhdGEoKSxcblx0XHRcdFx0XHQkcHJlbG9hZGVyID0gJHRhcmdldC5maW5kKCcucHJlbG9hZGVyJyksXG5cdFx0XHRcdFx0JGltYWdlID0gJCgnPGltZyAvPicpLFxuXHRcdFx0XHRcdGFsdCA9ICRzZWxmLmF0dHIoJ2FsdCcpLFxuXHRcdFx0XHRcdHRpdGxlID0gJHNlbGYuYXR0cigndGl0bGUnKTtcblxuXHRcdFx0XHQvLyBDbGVhbnNVcCB0aGUgbWFnbmlmaWVyIHRhcmdldFxuXHRcdFx0XHQkdGFyZ2V0XG5cdFx0XHRcdFx0LmNoaWxkcmVuKCdpbWcnKVxuXHRcdFx0XHRcdC5yZW1vdmUoKTtcblxuXHRcdFx0XHQkcHJlbG9hZGVyLnNob3coKTtcblx0XHRcdFx0JGJvZHkuYWRkQ2xhc3Mob3B0aW9ucy5ib2R5Q2xhc3MpO1xuXG5cdFx0XHRcdC8vIENyZWF0ZXMgdGhlIGltYWdlIGVsZW1lbnQgYW5kIGJpbmRzXG5cdFx0XHRcdC8vIGEgbG9hZCBoYW5kbGVyIHRvIGl0LCBzbyB0aGF0IHRoZVxuXHRcdFx0XHQvLyBwcmVsb2FkZXIgZ2V0cyBoaWRkZW4gYWZ0ZXIgdGhlIGltYWdlXG5cdFx0XHRcdC8vIGlzIGxvYWRlZCBieSB0aGUgYnJvd3NlclxuXHRcdFx0XHQkaW1hZ2Uub25lKCdsb2FkJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0ICAgICAgJGltYWdlLmNzcyh7XG5cdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICdoZWlnaHQnOiB0aGlzLmhlaWdodCArICdweCcsXG5cdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICd3aWR0aCc6IHRoaXMud2lkdGggKyAncHgnXG5cdFx0XHRcdFx0ICAgICAgICAgICAgICAgICB9KTtcblx0XHRcdFx0XHQgICAgICAkcHJlbG9hZGVyLmhpZGUoKTtcblxuXHRcdFx0XHRcdCAgICAgIC8vIEJpbmQgdGhlIG1vdXNlbW92ZSBoYW5kbGVyIHRvIHpvb20gdG9cblx0XHRcdFx0XHQgICAgICAvLyB0aGUgY29ycmVjdCBwb3NpdGlvbiBvZiB0aGUgaW1hZ2Vcblx0XHRcdFx0XHQgICAgICAkc2VsZlxuXHRcdFx0XHRcdFx0ICAgICAgLm9mZignbW91c2Vtb3ZlLm1hZ25pZmllcicpXG5cdFx0XHRcdFx0XHQgICAgICAub24oJ21vdXNlbW92ZS5tYWduaWZpZXInLCBfbW91c2VNb3ZlSGFuZGxlcik7XG5cdFx0XHRcdCAgICAgIH0pXG5cdFx0XHRcdCAgICAgIC5hdHRyKHtzcmM6IGRhdGFzZXQubWFnbmlmaWVyU3JjLCBhbHQ6IGFsdCwgdGl0bGU6IHRpdGxlfSk7XG5cblx0XHRcdFx0Ly8gQXBwZW5kIHRoZSBpbWFnZSB0byB0aGUgbWFnaW5pZmllciB0YXJnZXRcblx0XHRcdFx0JHRhcmdldFxuXHRcdFx0XHRcdC5hcHBlbmQoJGltYWdlKVxuXHRcdFx0XHRcdC5zaG93KCk7XG5cblx0XHRcdH1cblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIGZvciB0aGUgYnJvd3NlciByZXNpemUgZXZlbnQuXG5cdFx0ICogSXQgcmVtb3ZlcyBhbGwgc3RvcmVkIGRhdGEgc28gdGhhdCBhXG5cdFx0ICogcmVjYWxjdWxhdGlvbiBpcyBmb3JjZWRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcmVzaXplSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKGRhdGFXYXNTZXQpIHtcblx0XHRcdFx0JHRoaXNcblx0XHRcdFx0XHQuZmluZCgnaW1nW2RhdGEtbWFnbmlmaWVyLXNyY10nKVxuXHRcdFx0XHRcdC5yZW1vdmVEYXRhKCdtYWduaWZpZXInKTtcblxuXHRcdFx0XHRkYXRhV2FzU2V0ID0gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZWxlYXZlIGV2ZW50LiBJbiBjYXNlXG5cdFx0ICogdGhlIGN1cnNvciBsZWF2ZXMgdGhlIGltYWdlLCB0aGUgem9vbSB0YXJnZXQgZ2V0c1xuXHRcdCAqIGhpZGRlblxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9tb3VzZUxlYXZlSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JHRhcmdldC5oaWRlKCk7XG5cdFx0XHQkYm9keS5yZW1vdmVDbGFzcyhvcHRpb25zLmJvZHlDbGFzcyk7XG5cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vZmYoJ21vdXNlZW50ZXInKVxuXHRcdFx0XHQub24oJ21vdXNlZW50ZXInLCAnaW1nW2RhdGEtbWFnbmlmaWVyLXNyY10nLCBfbW91c2VFbnRlckhhbmRsZXIpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBSZW1vdmVzIHRoZSBtb3VzZWVudGVyIGhhbmRsZXIgb24gdG91Y2hzdGFydCxcblx0XHQgKiBzbyB0aGF0IHRoZSBtYWduaWZpZXIgbm90IHN0YXJ0cyBvbiB0b3VjaC5cblx0XHQgKiBUaGUgZnVuY3Rpb24gZ2V0cyByZWFjdGl2YXRlZCBpbiB0aGUgbW91c2VsZWF2ZVxuXHRcdCAqIGhhbmRsZXJcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdG91Y2hIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkdGhpcy5vZmYoJ21vdXNlZW50ZXInKTtcblx0XHR9O1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHQkdGFyZ2V0ID0gJChvcHRpb25zLnRhcmdldCk7XG5cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbigndG91Y2hzdGFydCcsICdpbWdbZGF0YS1tYWduaWZpZXItc3JjXScsIF90b3VjaEhhbmRsZXIpXG5cdFx0XHRcdC5vbignbW91c2VlbnRlcicsICdpbWdbZGF0YS1tYWduaWZpZXItc3JjXScsIF9tb3VzZUVudGVySGFuZGxlcilcblx0XHRcdFx0Lm9uKCdtb3VzZWxlYXZlJywgJ2ltZ1tkYXRhLW1hZ25pZmllci1zcmNdJywgX21vdXNlTGVhdmVIYW5kbGVyKTtcblxuXHRcdFx0JCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfcmVzaXplSGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
