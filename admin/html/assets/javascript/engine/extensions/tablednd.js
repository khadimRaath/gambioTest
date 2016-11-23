'use strict';

/* --------------------------------------------------------------
 tablednd.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Table Dnd Extension
 *
 * Sorts lines in connected tables.
 *
 * @module Admin/Extensions/tablednd
 * @ignore
 */
gx.extensions.module('tablednd', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Extension Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Table Body Selector
  *
  * @type {object}
  */
	$tbody = null,


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {
		'addclass': 'clsDnd', // classname added to body
		'disabledclass': 'sort-disabled', // classname added to body
		'handle': false // handler which enables the sortable
	},


	/**
  * Final Extension Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Setup Dummies
  */
	var setupDummies = function setupDummies() {
		// On drag stop, update dummy line visibility
		$tbody.each(function () {
			var $self = $(this),
			    $sortDisabled = $self.find('.' + options.disabledclass);

			if ($self.children().length > 1) {
				$sortDisabled.hide();
			} else {
				$sortDisabled.show();
			}

			var rowHidden = $sortDisabled.clone();
			$sortDisabled.remove();
			$self.prepend(rowHidden);
		});
	};

	/**
  * Initialize method of the extension, called by the engine.
  */
	module.init = function (done) {
		$tbody = $this.find('tbody');
		var strTimestamp = parseInt(new Date().getTime() * Math.random(), 10),
		    strClsDnd = options.addclass + '_' + strTimestamp,
		    config = {
			'handle': options.handle,
			'connectWith': '.' + strClsDnd,
			'containment': $this,
			'sort': function sort(event, ui) {
				$(event.target).each(function () {
					var $self = $(this),
					    $sortDisabled = $self.find('.' + options.disabledclass);

					if ($self.children().length > 2) {
						$sortDisabled.hide();
					} else {
						$sortDisabled.show();
						var rowHidden = $sortDisabled.clone();
						$sortDisabled.remove();
						$self.append(rowHidden);
					}
				});
			},
			'stop': function stop(event, ui) {
				setupDummies();
				// Trigger an update event on table
				$this.trigger('tablednd.update', []);
			}
		};

		// Add a special class and start the sortable plugin.
		$tbody.addClass(strClsDnd).sortable(config);

		setupDummies();

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRhYmxlZG5kLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkdGJvZHkiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJzZXR1cER1bW1pZXMiLCJlYWNoIiwiJHNlbGYiLCIkc29ydERpc2FibGVkIiwiZmluZCIsImRpc2FibGVkY2xhc3MiLCJjaGlsZHJlbiIsImxlbmd0aCIsImhpZGUiLCJzaG93Iiwicm93SGlkZGVuIiwiY2xvbmUiLCJyZW1vdmUiLCJwcmVwZW5kIiwiaW5pdCIsImRvbmUiLCJzdHJUaW1lc3RhbXAiLCJwYXJzZUludCIsIkRhdGUiLCJnZXRUaW1lIiwiTWF0aCIsInJhbmRvbSIsInN0ckNsc0RuZCIsImFkZGNsYXNzIiwiY29uZmlnIiwiaGFuZGxlIiwiZXZlbnQiLCJ1aSIsInRhcmdldCIsImFwcGVuZCIsInRyaWdnZXIiLCJhZGRDbGFzcyIsInNvcnRhYmxlIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7O0FBUUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLFVBREQsRUFHQyxFQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFVBQVMsSUFiVjs7O0FBZUM7Ozs7O0FBS0FDLFlBQVc7QUFDVixjQUFZLFFBREYsRUFDWTtBQUN0QixtQkFBaUIsZUFGUCxFQUV3QjtBQUNsQyxZQUFVLEtBSEEsQ0FHTTtBQUhOLEVBcEJaOzs7QUEwQkM7Ozs7O0FBS0FDLFdBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJKLElBQTdCLENBL0JYOzs7QUFpQ0M7Ozs7O0FBS0FELFVBQVMsRUF0Q1Y7O0FBd0NBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0EsS0FBSVEsZUFBZSxTQUFmQSxZQUFlLEdBQVc7QUFDN0I7QUFDQUosU0FBT0ssSUFBUCxDQUFZLFlBQVc7QUFDdEIsT0FBSUMsUUFBUVAsRUFBRSxJQUFGLENBQVo7QUFBQSxPQUNDUSxnQkFBZ0JELE1BQU1FLElBQU4sQ0FBVyxNQUFNTixRQUFRTyxhQUF6QixDQURqQjs7QUFHQSxPQUFJSCxNQUFNSSxRQUFOLEdBQWlCQyxNQUFqQixHQUEwQixDQUE5QixFQUFpQztBQUNoQ0osa0JBQWNLLElBQWQ7QUFDQSxJQUZELE1BRU87QUFDTkwsa0JBQWNNLElBQWQ7QUFDQTs7QUFFRCxPQUFJQyxZQUFZUCxjQUFjUSxLQUFkLEVBQWhCO0FBQ0FSLGlCQUFjUyxNQUFkO0FBQ0FWLFNBQU1XLE9BQU4sQ0FBY0gsU0FBZDtBQUVBLEdBZEQ7QUFlQSxFQWpCRDs7QUFtQkE7OztBQUdBbEIsUUFBT3NCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJuQixXQUFTRixNQUFNVSxJQUFOLENBQVcsT0FBWCxDQUFUO0FBQ0EsTUFBSVksZUFBZUMsU0FBUyxJQUFJQyxJQUFKLEdBQVdDLE9BQVgsS0FBdUJDLEtBQUtDLE1BQUwsRUFBaEMsRUFBK0MsRUFBL0MsQ0FBbkI7QUFBQSxNQUNDQyxZQUFZeEIsUUFBUXlCLFFBQVIsR0FBbUIsR0FBbkIsR0FBeUJQLFlBRHRDO0FBQUEsTUFFQ1EsU0FBUztBQUNSLGFBQVUxQixRQUFRMkIsTUFEVjtBQUVSLGtCQUFlLE1BQU1ILFNBRmI7QUFHUixrQkFBZTVCLEtBSFA7QUFJUixXQUFRLGNBQVNnQyxLQUFULEVBQWdCQyxFQUFoQixFQUFvQjtBQUMzQmhDLE1BQUUrQixNQUFNRSxNQUFSLEVBQWdCM0IsSUFBaEIsQ0FBcUIsWUFBVztBQUMvQixTQUFJQyxRQUFRUCxFQUFFLElBQUYsQ0FBWjtBQUFBLFNBQ0NRLGdCQUFnQkQsTUFBTUUsSUFBTixDQUFXLE1BQU1OLFFBQVFPLGFBQXpCLENBRGpCOztBQUdBLFNBQUlILE1BQU1JLFFBQU4sR0FBaUJDLE1BQWpCLEdBQTBCLENBQTlCLEVBQWlDO0FBQ2hDSixvQkFBY0ssSUFBZDtBQUNBLE1BRkQsTUFFTztBQUNOTCxvQkFBY00sSUFBZDtBQUNBLFVBQUlDLFlBQVlQLGNBQWNRLEtBQWQsRUFBaEI7QUFDQVIsb0JBQWNTLE1BQWQ7QUFDQVYsWUFBTTJCLE1BQU4sQ0FBYW5CLFNBQWI7QUFDQTtBQUNELEtBWkQ7QUFjQSxJQW5CTztBQW9CUixXQUFRLGNBQVNnQixLQUFULEVBQWdCQyxFQUFoQixFQUFvQjtBQUMzQjNCO0FBQ0E7QUFDQU4sVUFBTW9DLE9BQU4sQ0FBYyxpQkFBZCxFQUFpQyxFQUFqQztBQUNBO0FBeEJPLEdBRlY7O0FBNkJBO0FBQ0FsQyxTQUNFbUMsUUFERixDQUNXVCxTQURYLEVBRUVVLFFBRkYsQ0FFV1IsTUFGWDs7QUFJQXhCOztBQUVBZTtBQUNBLEVBdkNEOztBQXlDQTtBQUNBLFFBQU92QixNQUFQO0FBQ0EsQ0E3SEYiLCJmaWxlIjoidGFibGVkbmQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHRhYmxlZG5kLmpzIDIwMTUtMDktMTcgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFRhYmxlIERuZCBFeHRlbnNpb25cbiAqXG4gKiBTb3J0cyBsaW5lcyBpbiBjb25uZWN0ZWQgdGFibGVzLlxuICpcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9ucy90YWJsZWRuZFxuICogQGlnbm9yZVxuICovXG5neC5leHRlbnNpb25zLm1vZHVsZShcblx0J3RhYmxlZG5kJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIEV4dGVuc2lvbiBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVGFibGUgQm9keSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0Ym9keSA9IG51bGwsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBFeHRlbnNpb25cblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0J2FkZGNsYXNzJzogJ2Nsc0RuZCcsIC8vIGNsYXNzbmFtZSBhZGRlZCB0byBib2R5XG5cdFx0XHRcdCdkaXNhYmxlZGNsYXNzJzogJ3NvcnQtZGlzYWJsZWQnLCAvLyBjbGFzc25hbWUgYWRkZWQgdG8gYm9keVxuXHRcdFx0XHQnaGFuZGxlJzogZmFsc2UgLy8gaGFuZGxlciB3aGljaCBlbmFibGVzIHRoZSBzb3J0YWJsZVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNldHVwIER1bW1pZXNcblx0XHQgKi9cblx0XHR2YXIgc2V0dXBEdW1taWVzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBPbiBkcmFnIHN0b3AsIHVwZGF0ZSBkdW1teSBsaW5lIHZpc2liaWxpdHlcblx0XHRcdCR0Ym9keS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdCRzb3J0RGlzYWJsZWQgPSAkc2VsZi5maW5kKCcuJyArIG9wdGlvbnMuZGlzYWJsZWRjbGFzcyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoJHNlbGYuY2hpbGRyZW4oKS5sZW5ndGggPiAxKSB7XG5cdFx0XHRcdFx0JHNvcnREaXNhYmxlZC5oaWRlKCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JHNvcnREaXNhYmxlZC5zaG93KCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdHZhciByb3dIaWRkZW4gPSAkc29ydERpc2FibGVkLmNsb25lKCk7XG5cdFx0XHRcdCRzb3J0RGlzYWJsZWQucmVtb3ZlKCk7XG5cdFx0XHRcdCRzZWxmLnByZXBlbmQocm93SGlkZGVuKTtcblx0XHRcdFx0XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSBleHRlbnNpb24sIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRib2R5ID0gJHRoaXMuZmluZCgndGJvZHknKTtcblx0XHRcdHZhciBzdHJUaW1lc3RhbXAgPSBwYXJzZUludChuZXcgRGF0ZSgpLmdldFRpbWUoKSAqIE1hdGgucmFuZG9tKCksIDEwKSxcblx0XHRcdFx0c3RyQ2xzRG5kID0gb3B0aW9ucy5hZGRjbGFzcyArICdfJyArIHN0clRpbWVzdGFtcCxcblx0XHRcdFx0Y29uZmlnID0ge1xuXHRcdFx0XHRcdCdoYW5kbGUnOiBvcHRpb25zLmhhbmRsZSxcblx0XHRcdFx0XHQnY29ubmVjdFdpdGgnOiAnLicgKyBzdHJDbHNEbmQsXG5cdFx0XHRcdFx0J2NvbnRhaW5tZW50JzogJHRoaXMsXG5cdFx0XHRcdFx0J3NvcnQnOiBmdW5jdGlvbihldmVudCwgdWkpIHtcblx0XHRcdFx0XHRcdCQoZXZlbnQudGFyZ2V0KS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHRcdCRzb3J0RGlzYWJsZWQgPSAkc2VsZi5maW5kKCcuJyArIG9wdGlvbnMuZGlzYWJsZWRjbGFzcyk7XG5cdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRpZiAoJHNlbGYuY2hpbGRyZW4oKS5sZW5ndGggPiAyKSB7XG5cdFx0XHRcdFx0XHRcdFx0JHNvcnREaXNhYmxlZC5oaWRlKCk7XG5cdFx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdFx0JHNvcnREaXNhYmxlZC5zaG93KCk7XG5cdFx0XHRcdFx0XHRcdFx0dmFyIHJvd0hpZGRlbiA9ICRzb3J0RGlzYWJsZWQuY2xvbmUoKTtcblx0XHRcdFx0XHRcdFx0XHQkc29ydERpc2FibGVkLnJlbW92ZSgpO1xuXHRcdFx0XHRcdFx0XHRcdCRzZWxmLmFwcGVuZChyb3dIaWRkZW4pO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0J3N0b3AnOiBmdW5jdGlvbihldmVudCwgdWkpIHtcblx0XHRcdFx0XHRcdHNldHVwRHVtbWllcygpO1xuXHRcdFx0XHRcdFx0Ly8gVHJpZ2dlciBhbiB1cGRhdGUgZXZlbnQgb24gdGFibGVcblx0XHRcdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ3RhYmxlZG5kLnVwZGF0ZScsIFtdKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8vIEFkZCBhIHNwZWNpYWwgY2xhc3MgYW5kIHN0YXJ0IHRoZSBzb3J0YWJsZSBwbHVnaW4uXG5cdFx0XHQkdGJvZHlcblx0XHRcdFx0LmFkZENsYXNzKHN0ckNsc0RuZClcblx0XHRcdFx0LnNvcnRhYmxlKGNvbmZpZyk7XG5cdFx0XHRcblx0XHRcdHNldHVwRHVtbWllcygpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
