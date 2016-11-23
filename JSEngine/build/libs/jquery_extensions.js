'use strict';

/* --------------------------------------------------------------
 jquery_extensions.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
  * Add ":attr" pseudo selector.
  *
  * This selector enables jQuery to use regular expressions for attribute name matching. Although useful,
  * the engine will remove all dependencies to jQuery and thus it must be moved into an external library
  * or file.
  */

	if ($.expr.pseudos.attr === undefined) {
		$.expr.pseudos.attr = $.expr.createPseudo(function (selector) {
			var regexp = new RegExp(selector);
			return function (elem) {
				for (var i = 0; i < elem.attributes.length; i++) {
					var attr = elem.attributes[i];
					if (regexp.test(attr.name)) {
						return true;
					}
				}
				return false;
			};
		});
	}
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImpxdWVyeV9leHRlbnNpb25zLmpzIl0sIm5hbWVzIjpbIiQiLCJleHByIiwicHNldWRvcyIsImF0dHIiLCJ1bmRlZmluZWQiLCJjcmVhdGVQc2V1ZG8iLCJzZWxlY3RvciIsInJlZ2V4cCIsIlJlZ0V4cCIsImVsZW0iLCJpIiwiYXR0cmlidXRlcyIsImxlbmd0aCIsInRlc3QiLCJuYW1lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUEsQ0FBQyxZQUFXOztBQUVYOztBQUVBOzs7Ozs7OztBQU9BLEtBQUlBLEVBQUVDLElBQUYsQ0FBT0MsT0FBUCxDQUFlQyxJQUFmLEtBQXdCQyxTQUE1QixFQUF1QztBQUN0Q0osSUFBRUMsSUFBRixDQUFPQyxPQUFQLENBQWVDLElBQWYsR0FBc0JILEVBQUVDLElBQUYsQ0FBT0ksWUFBUCxDQUFvQixVQUFTQyxRQUFULEVBQW1CO0FBQzVELE9BQUlDLFNBQVMsSUFBSUMsTUFBSixDQUFXRixRQUFYLENBQWI7QUFDQSxVQUFPLFVBQVNHLElBQVQsRUFBZTtBQUNyQixTQUFJLElBQUlDLElBQUksQ0FBWixFQUFlQSxJQUFJRCxLQUFLRSxVQUFMLENBQWdCQyxNQUFuQyxFQUEyQ0YsR0FBM0MsRUFBZ0Q7QUFDL0MsU0FBSVAsT0FBT00sS0FBS0UsVUFBTCxDQUFnQkQsQ0FBaEIsQ0FBWDtBQUNBLFNBQUdILE9BQU9NLElBQVAsQ0FBWVYsS0FBS1csSUFBakIsQ0FBSCxFQUEyQjtBQUMxQixhQUFPLElBQVA7QUFDQTtBQUNEO0FBQ0QsV0FBTyxLQUFQO0FBQ0EsSUFSRDtBQVNBLEdBWHFCLENBQXRCO0FBWUE7QUFFRCxDQTFCRCIsImZpbGUiOiJqcXVlcnlfZXh0ZW5zaW9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBqcXVlcnlfZXh0ZW5zaW9ucy5qcyAyMDE2LTA2LTIyXHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuKGZ1bmN0aW9uKCkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvKipcclxuXHQgKiBBZGQgXCI6YXR0clwiIHBzZXVkbyBzZWxlY3Rvci5cclxuXHQgKlxyXG5cdCAqIFRoaXMgc2VsZWN0b3IgZW5hYmxlcyBqUXVlcnkgdG8gdXNlIHJlZ3VsYXIgZXhwcmVzc2lvbnMgZm9yIGF0dHJpYnV0ZSBuYW1lIG1hdGNoaW5nLiBBbHRob3VnaCB1c2VmdWwsXHJcblx0ICogdGhlIGVuZ2luZSB3aWxsIHJlbW92ZSBhbGwgZGVwZW5kZW5jaWVzIHRvIGpRdWVyeSBhbmQgdGh1cyBpdCBtdXN0IGJlIG1vdmVkIGludG8gYW4gZXh0ZXJuYWwgbGlicmFyeVxyXG5cdCAqIG9yIGZpbGUuXHJcblx0ICovXHJcblx0aWYgKCQuZXhwci5wc2V1ZG9zLmF0dHIgPT09IHVuZGVmaW5lZCkge1xyXG5cdFx0JC5leHByLnBzZXVkb3MuYXR0ciA9ICQuZXhwci5jcmVhdGVQc2V1ZG8oZnVuY3Rpb24oc2VsZWN0b3IpIHtcclxuXHRcdFx0bGV0IHJlZ2V4cCA9IG5ldyBSZWdFeHAoc2VsZWN0b3IpO1xyXG5cdFx0XHRyZXR1cm4gZnVuY3Rpb24oZWxlbSkge1xyXG5cdFx0XHRcdGZvcihsZXQgaSA9IDA7IGkgPCBlbGVtLmF0dHJpYnV0ZXMubGVuZ3RoOyBpKyspIHtcclxuXHRcdFx0XHRcdGxldCBhdHRyID0gZWxlbS5hdHRyaWJ1dGVzW2ldO1xyXG5cdFx0XHRcdFx0aWYocmVnZXhwLnRlc3QoYXR0ci5uYW1lKSkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9O1xyXG5cdFx0fSk7XHJcblx0fVxyXG5cdFxyXG59KSgpOyJdfQ==
