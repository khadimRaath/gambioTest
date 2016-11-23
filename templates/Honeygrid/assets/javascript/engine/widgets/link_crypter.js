'use strict';

/* --------------------------------------------------------------
 link_crypter.js 2016-02-02 
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that replaces the href-attributes of links with the given
 * data if the element gets in focus / hover state. Additionally
 * it is possible to remove every X sign for decryption
 */
gambio.widgets.module('link_crypter', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		decrypt: true, // If true, it uses the period option to decrypt the links
		period: 3 // Remove every X sign of the data given for the url
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########


	/**
  * Function to replace the href value
  * with the URL or a # (depending on
  * the focus / hover state). Additionally
  * it does some decrypting optionally.
  * @param       {object}    e   jQuery-event-object which contains as data the focus state
  * @private
  */
	var _switchUrl = function _switchUrl(e) {
		var $self = $(this),
		    url = $(this).parseModuleData('link_crypter').url;

		if (url) {
			if (e.data.in) {
				// Simple decryption functionality. It removes every x. sign inside the URL. 
				// x is given by options.period
				if (options.decrypt) {
					var decryptedUrl = '';
					for (var i = 0; i < url.length; i++) {
						if (i % options.period) {
							decryptedUrl += url.charAt(i);
						}
					}
					url = decryptedUrl;
				}
				$self.attr('href', url);
			} else {
				$self.attr('href', '#');
			}
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		$this.on('mouseenter focus', 'a', { in: true }, _switchUrl).on('mouseleave blur', 'a', { in: false }, _switchUrl);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbGlua19jcnlwdGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJkZWNyeXB0IiwicGVyaW9kIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zd2l0Y2hVcmwiLCJlIiwiJHNlbGYiLCJ1cmwiLCJwYXJzZU1vZHVsZURhdGEiLCJpbiIsImRlY3J5cHRlZFVybCIsImkiLCJsZW5ndGgiLCJjaGFyQXQiLCJhdHRyIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsY0FBdEIsRUFBc0MsRUFBdEMsRUFBMEMsVUFBU0MsSUFBVCxFQUFlOztBQUV4RDs7QUFFRDs7QUFFQyxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVc7QUFDVkMsV0FBUyxJQURDLEVBQ0s7QUFDZkMsVUFBUSxDQUZFLENBRUE7QUFGQSxFQURaO0FBQUEsS0FLQ0MsVUFBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkgsSUFBN0IsQ0FMWDtBQUFBLEtBTUNELFNBQVMsRUFOVjs7QUFTRDs7O0FBR0M7Ozs7Ozs7O0FBUUEsS0FBSVMsYUFBYSxTQUFiQSxVQUFhLENBQVNDLENBQVQsRUFBWTtBQUM1QixNQUFJQyxRQUFRUixFQUFFLElBQUYsQ0FBWjtBQUFBLE1BQ0NTLE1BQU1ULEVBQUUsSUFBRixFQUFRVSxlQUFSLENBQXdCLGNBQXhCLEVBQXdDRCxHQUQvQzs7QUFHQSxNQUFJQSxHQUFKLEVBQVM7QUFDUixPQUFJRixFQUFFVCxJQUFGLENBQU9hLEVBQVgsRUFBZTtBQUNkO0FBQ0E7QUFDQSxRQUFJUCxRQUFRRixPQUFaLEVBQXFCO0FBQ3BCLFNBQUlVLGVBQWUsRUFBbkI7QUFDQSxVQUFLLElBQUlDLElBQUksQ0FBYixFQUFnQkEsSUFBSUosSUFBSUssTUFBeEIsRUFBZ0NELEdBQWhDLEVBQXFDO0FBQ3BDLFVBQUlBLElBQUlULFFBQVFELE1BQWhCLEVBQXdCO0FBQ3ZCUyx1QkFBZ0JILElBQUlNLE1BQUosQ0FBV0YsQ0FBWCxDQUFoQjtBQUNBO0FBQ0Q7QUFDREosV0FBTUcsWUFBTjtBQUNBO0FBQ0RKLFVBQU1RLElBQU4sQ0FBVyxNQUFYLEVBQW1CUCxHQUFuQjtBQUNBLElBYkQsTUFhTztBQUNORCxVQUFNUSxJQUFOLENBQVcsTUFBWCxFQUFtQixHQUFuQjtBQUNBO0FBQ0Q7QUFDRCxFQXRCRDs7QUF3QkQ7O0FBRUM7Ozs7QUFJQW5CLFFBQU9vQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCbkIsUUFDRW9CLEVBREYsQ0FDSyxrQkFETCxFQUN5QixHQUR6QixFQUM4QixFQUFDUixJQUFJLElBQUwsRUFEOUIsRUFDMENMLFVBRDFDLEVBRUVhLEVBRkYsQ0FFSyxpQkFGTCxFQUV3QixHQUZ4QixFQUU2QixFQUFDUixJQUFJLEtBQUwsRUFGN0IsRUFFMENMLFVBRjFDOztBQUlBWTtBQUNBLEVBTkQ7O0FBUUE7QUFDQSxRQUFPckIsTUFBUDtBQUNBLENBbEVEIiwiZmlsZSI6IndpZGdldHMvbGlua19jcnlwdGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBsaW5rX2NyeXB0ZXIuanMgMjAxNi0wMi0wMiBcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IHJlcGxhY2VzIHRoZSBocmVmLWF0dHJpYnV0ZXMgb2YgbGlua3Mgd2l0aCB0aGUgZ2l2ZW5cbiAqIGRhdGEgaWYgdGhlIGVsZW1lbnQgZ2V0cyBpbiBmb2N1cyAvIGhvdmVyIHN0YXRlLiBBZGRpdGlvbmFsbHlcbiAqIGl0IGlzIHBvc3NpYmxlIHRvIHJlbW92ZSBldmVyeSBYIHNpZ24gZm9yIGRlY3J5cHRpb25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdsaW5rX2NyeXB0ZXInLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdGRlY3J5cHQ6IHRydWUsIC8vIElmIHRydWUsIGl0IHVzZXMgdGhlIHBlcmlvZCBvcHRpb24gdG8gZGVjcnlwdCB0aGUgbGlua3Ncblx0XHRcdHBlcmlvZDogMyAvLyBSZW1vdmUgZXZlcnkgWCBzaWduIG9mIHRoZSBkYXRhIGdpdmVuIGZvciB0aGUgdXJsXG5cdFx0fSxcblx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cblx0LyoqXG5cdCAqIEZ1bmN0aW9uIHRvIHJlcGxhY2UgdGhlIGhyZWYgdmFsdWVcblx0ICogd2l0aCB0aGUgVVJMIG9yIGEgIyAoZGVwZW5kaW5nIG9uXG5cdCAqIHRoZSBmb2N1cyAvIGhvdmVyIHN0YXRlKS4gQWRkaXRpb25hbGx5XG5cdCAqIGl0IGRvZXMgc29tZSBkZWNyeXB0aW5nIG9wdGlvbmFsbHkuXG5cdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgalF1ZXJ5LWV2ZW50LW9iamVjdCB3aGljaCBjb250YWlucyBhcyBkYXRhIHRoZSBmb2N1cyBzdGF0ZVxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9zd2l0Y2hVcmwgPSBmdW5jdGlvbihlKSB7XG5cdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdHVybCA9ICQodGhpcykucGFyc2VNb2R1bGVEYXRhKCdsaW5rX2NyeXB0ZXInKS51cmw7XG5cblx0XHRpZiAodXJsKSB7XG5cdFx0XHRpZiAoZS5kYXRhLmluKSB7XG5cdFx0XHRcdC8vIFNpbXBsZSBkZWNyeXB0aW9uIGZ1bmN0aW9uYWxpdHkuIEl0IHJlbW92ZXMgZXZlcnkgeC4gc2lnbiBpbnNpZGUgdGhlIFVSTC4gXG5cdFx0XHRcdC8vIHggaXMgZ2l2ZW4gYnkgb3B0aW9ucy5wZXJpb2Rcblx0XHRcdFx0aWYgKG9wdGlvbnMuZGVjcnlwdCkge1xuXHRcdFx0XHRcdHZhciBkZWNyeXB0ZWRVcmwgPSAnJztcblx0XHRcdFx0XHRmb3IgKHZhciBpID0gMDsgaSA8IHVybC5sZW5ndGg7IGkrKykge1xuXHRcdFx0XHRcdFx0aWYgKGkgJSBvcHRpb25zLnBlcmlvZCkge1xuXHRcdFx0XHRcdFx0XHRkZWNyeXB0ZWRVcmwgKz0gdXJsLmNoYXJBdChpKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0dXJsID0gZGVjcnlwdGVkVXJsOyBcblx0XHRcdFx0fVxuXHRcdFx0XHQkc2VsZi5hdHRyKCdocmVmJywgdXJsKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdCRzZWxmLmF0dHIoJ2hyZWYnLCAnIycpO1xuXHRcdFx0fVxuXHRcdH1cblx0fTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0LyoqXG5cdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHQgKiBAY29uc3RydWN0b3Jcblx0ICovXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdCR0aGlzXG5cdFx0XHQub24oJ21vdXNlZW50ZXIgZm9jdXMnLCAnYScsIHtpbjogdHJ1ZX0sIF9zd2l0Y2hVcmwpXG5cdFx0XHQub24oJ21vdXNlbGVhdmUgYmx1cicsICdhJywge2luOiBmYWxzZX0sIF9zd2l0Y2hVcmwpO1xuXG5cdFx0ZG9uZSgpO1xuXHR9O1xuXG5cdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0cmV0dXJuIG1vZHVsZTtcbn0pO1xuIl19
