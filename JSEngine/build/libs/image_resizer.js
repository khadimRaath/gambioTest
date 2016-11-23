'use strict';

/* --------------------------------------------------------------
 image_resizer.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.image_resizer = jse.libs.image_resizer || {};

/**
 * ## Image Resizer Library
 *
 * Resizes images with respective aspect ratio.
 *
 * @module JSE/Libs/image_resizer
 * @exports jse.libs.image_resizer
 */
(function (exports) {

	'use strict';

	/**
  * Resize an image element with the provided width and height values.
  * 
  * @param {string} element Selector string for the image element to be resized.
  * @param {object} options (optional) This object must contain the "width" and "height" properties.
  */

	exports.resize = function (element, options) {

		var $that = $(element);
		var settings = {
			width: 150,
			height: 150
		};
		options = $.extend(settings, options);

		var maxWidth = options.width;
		var maxHeight = options.height;
		var ratio = 0;
		var width = $that.width();
		var height = $that.height();

		if (width > maxWidth) {
			ratio = maxWidth / width;
			$that.css('width', maxWidth);
			$that.css('height', height * ratio);
		}

		if (height > maxHeight) {
			ratio = maxHeight / height;
			$that.css('height', maxHeight);
			$that.css('width', width * ratio);
		}
	};
})(jse.libs.image_resizer);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImltYWdlX3Jlc2l6ZXIuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImltYWdlX3Jlc2l6ZXIiLCJleHBvcnRzIiwicmVzaXplIiwiZWxlbWVudCIsIm9wdGlvbnMiLCIkdGhhdCIsIiQiLCJzZXR0aW5ncyIsIndpZHRoIiwiaGVpZ2h0IiwiZXh0ZW5kIiwibWF4V2lkdGgiLCJtYXhIZWlnaHQiLCJyYXRpbyIsImNzcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLGFBQVQsR0FBeUJGLElBQUlDLElBQUosQ0FBU0MsYUFBVCxJQUEwQixFQUFuRDs7QUFFQTs7Ozs7Ozs7QUFRQSxDQUFDLFVBQVVDLE9BQVYsRUFBbUI7O0FBRW5COztBQUVBOzs7Ozs7O0FBTUFBLFNBQVFDLE1BQVIsR0FBaUIsVUFBVUMsT0FBVixFQUFtQkMsT0FBbkIsRUFBNEI7O0FBRTVDLE1BQUlDLFFBQVFDLEVBQUVILE9BQUYsQ0FBWjtBQUNBLE1BQUlJLFdBQVc7QUFDZEMsVUFBTyxHQURPO0FBRWRDLFdBQVE7QUFGTSxHQUFmO0FBSUFMLFlBQVVFLEVBQUVJLE1BQUYsQ0FBU0gsUUFBVCxFQUFtQkgsT0FBbkIsQ0FBVjs7QUFFQSxNQUFJTyxXQUFXUCxRQUFRSSxLQUF2QjtBQUNBLE1BQUlJLFlBQVlSLFFBQVFLLE1BQXhCO0FBQ0EsTUFBSUksUUFBUSxDQUFaO0FBQ0EsTUFBSUwsUUFBUUgsTUFBTUcsS0FBTixFQUFaO0FBQ0EsTUFBSUMsU0FBU0osTUFBTUksTUFBTixFQUFiOztBQUVBLE1BQUlELFFBQVFHLFFBQVosRUFBc0I7QUFDckJFLFdBQVFGLFdBQVdILEtBQW5CO0FBQ0FILFNBQU1TLEdBQU4sQ0FBVSxPQUFWLEVBQW1CSCxRQUFuQjtBQUNBTixTQUFNUyxHQUFOLENBQVUsUUFBVixFQUFvQkwsU0FBU0ksS0FBN0I7QUFFQTs7QUFFRCxNQUFJSixTQUFTRyxTQUFiLEVBQXdCO0FBQ3ZCQyxXQUFRRCxZQUFZSCxNQUFwQjtBQUNBSixTQUFNUyxHQUFOLENBQVUsUUFBVixFQUFvQkYsU0FBcEI7QUFDQVAsU0FBTVMsR0FBTixDQUFVLE9BQVYsRUFBbUJOLFFBQVFLLEtBQTNCO0FBRUE7QUFFRCxFQTdCRDtBQStCQSxDQXpDRCxFQXlDR2YsSUFBSUMsSUFBSixDQUFTQyxhQXpDWiIsImZpbGUiOiJpbWFnZV9yZXNpemVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbWFnZV9yZXNpemVyLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UubGlicy5pbWFnZV9yZXNpemVyID0ganNlLmxpYnMuaW1hZ2VfcmVzaXplciB8fCB7fTtcblxuLyoqXG4gKiAjIyBJbWFnZSBSZXNpemVyIExpYnJhcnlcbiAqXG4gKiBSZXNpemVzIGltYWdlcyB3aXRoIHJlc3BlY3RpdmUgYXNwZWN0IHJhdGlvLlxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvaW1hZ2VfcmVzaXplclxuICogQGV4cG9ydHMganNlLmxpYnMuaW1hZ2VfcmVzaXplclxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogUmVzaXplIGFuIGltYWdlIGVsZW1lbnQgd2l0aCB0aGUgcHJvdmlkZWQgd2lkdGggYW5kIGhlaWdodCB2YWx1ZXMuXG5cdCAqIFxuXHQgKiBAcGFyYW0ge3N0cmluZ30gZWxlbWVudCBTZWxlY3RvciBzdHJpbmcgZm9yIHRoZSBpbWFnZSBlbGVtZW50IHRvIGJlIHJlc2l6ZWQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIChvcHRpb25hbCkgVGhpcyBvYmplY3QgbXVzdCBjb250YWluIHRoZSBcIndpZHRoXCIgYW5kIFwiaGVpZ2h0XCIgcHJvcGVydGllcy5cblx0ICovXG5cdGV4cG9ydHMucmVzaXplID0gZnVuY3Rpb24gKGVsZW1lbnQsIG9wdGlvbnMpIHtcblxuXHRcdHZhciAkdGhhdCA9ICQoZWxlbWVudCk7XG5cdFx0dmFyIHNldHRpbmdzID0ge1xuXHRcdFx0d2lkdGg6IDE1MCxcblx0XHRcdGhlaWdodDogMTUwXG5cdFx0fTtcblx0XHRvcHRpb25zID0gJC5leHRlbmQoc2V0dGluZ3MsIG9wdGlvbnMpO1xuXG5cdFx0dmFyIG1heFdpZHRoID0gb3B0aW9ucy53aWR0aDtcblx0XHR2YXIgbWF4SGVpZ2h0ID0gb3B0aW9ucy5oZWlnaHQ7XG5cdFx0dmFyIHJhdGlvID0gMDtcblx0XHR2YXIgd2lkdGggPSAkdGhhdC53aWR0aCgpO1xuXHRcdHZhciBoZWlnaHQgPSAkdGhhdC5oZWlnaHQoKTtcblxuXHRcdGlmICh3aWR0aCA+IG1heFdpZHRoKSB7XG5cdFx0XHRyYXRpbyA9IG1heFdpZHRoIC8gd2lkdGg7XG5cdFx0XHQkdGhhdC5jc3MoJ3dpZHRoJywgbWF4V2lkdGgpO1xuXHRcdFx0JHRoYXQuY3NzKCdoZWlnaHQnLCBoZWlnaHQgKiByYXRpbyk7XG5cblx0XHR9XG5cblx0XHRpZiAoaGVpZ2h0ID4gbWF4SGVpZ2h0KSB7XG5cdFx0XHRyYXRpbyA9IG1heEhlaWdodCAvIGhlaWdodDtcblx0XHRcdCR0aGF0LmNzcygnaGVpZ2h0JywgbWF4SGVpZ2h0KTtcblx0XHRcdCR0aGF0LmNzcygnd2lkdGgnLCB3aWR0aCAqIHJhdGlvKTtcblxuXHRcdH1cblxuXHR9O1xuXG59KShqc2UubGlicy5pbWFnZV9yZXNpemVyKTtcbiJdfQ==
