'use strict';

/* --------------------------------------------------------------
 normalize.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.normalize = jse.libs.normalize || {};

/**
 * ## Normalization Library
 * 
 * This library normalizes input and output (XSS protection). 
 *
 * @module JSE/Libs/normalize
 * @exports jse.libs.normalize
 */
(function (exports) {

  'use strict';

  /**
   * Returns the escaped text from a HTML string.
   *
   * {@link http://stackoverflow.com/a/25207}
   *
   * @param {string} text The text to be escaped.
   *
   * @return {string} Returns the escaped string.
   *
   * @public
   */

  exports.escapeHtml = function (text) {
    return $('<div/>').text(text).html();
  };
})(jse.libs.normalize);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vcm1hbGl6ZS5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwibm9ybWFsaXplIiwiZXhwb3J0cyIsImVzY2FwZUh0bWwiLCJ0ZXh0IiwiJCIsImh0bWwiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxTQUFULEdBQXFCRixJQUFJQyxJQUFKLENBQVNDLFNBQVQsSUFBc0IsRUFBM0M7O0FBRUE7Ozs7Ozs7O0FBUUEsQ0FBQyxVQUFVQyxPQUFWLEVBQW1COztBQUVuQjs7QUFFQTs7Ozs7Ozs7Ozs7O0FBV0FBLFVBQVFDLFVBQVIsR0FBcUIsVUFBU0MsSUFBVCxFQUFlO0FBQ25DLFdBQU9DLEVBQUUsUUFBRixFQUFZRCxJQUFaLENBQWlCQSxJQUFqQixFQUF1QkUsSUFBdkIsRUFBUDtBQUNBLEdBRkQ7QUFJQSxDQW5CRCxFQW1CR1AsSUFBSUMsSUFBSixDQUFTQyxTQW5CWiIsImZpbGUiOiJub3JtYWxpemUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIG5vcm1hbGl6ZS5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMubm9ybWFsaXplID0ganNlLmxpYnMubm9ybWFsaXplIHx8IHt9O1xuXG4vKipcbiAqICMjIE5vcm1hbGl6YXRpb24gTGlicmFyeVxuICogXG4gKiBUaGlzIGxpYnJhcnkgbm9ybWFsaXplcyBpbnB1dCBhbmQgb3V0cHV0IChYU1MgcHJvdGVjdGlvbikuIFxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvbm9ybWFsaXplXG4gKiBAZXhwb3J0cyBqc2UubGlicy5ub3JtYWxpemVcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG5cdC8qKlxuXHQgKiBSZXR1cm5zIHRoZSBlc2NhcGVkIHRleHQgZnJvbSBhIEhUTUwgc3RyaW5nLlxuXHQgKlxuXHQgKiB7QGxpbmsgaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMjUyMDd9XG5cdCAqXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB0ZXh0IFRoZSB0ZXh0IHRvIGJlIGVzY2FwZWQuXG5cdCAqXG5cdCAqIEByZXR1cm4ge3N0cmluZ30gUmV0dXJucyB0aGUgZXNjYXBlZCBzdHJpbmcuXG5cdCAqXG5cdCAqIEBwdWJsaWNcblx0ICovXG5cdGV4cG9ydHMuZXNjYXBlSHRtbCA9IGZ1bmN0aW9uKHRleHQpIHtcblx0XHRyZXR1cm4gJCgnPGRpdi8+JykudGV4dCh0ZXh0KS5odG1sKCk7XG5cdH07XG5cbn0pKGpzZS5saWJzLm5vcm1hbGl6ZSk7XG4iXX0=
