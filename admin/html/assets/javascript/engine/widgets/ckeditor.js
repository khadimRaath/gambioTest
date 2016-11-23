'use strict';

/* --------------------------------------------------------------
 ckeditor.js 2016-03-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## CKEditor Widget
 *
 * Use this widget on a parent container to convert all the textareas with the "wysiwyg" class into 
 * CKEditor instances at once. 
 * 
 * Official CKEditor Website: {@link http://ckeditor.com}
 * 
 * ### Options 
 * 
 * **File Browser URL | `data-ckeditor-filebrowser-browse-url` | String | Optional**
 * 
 * Provide the default URL of the file browser that is integrated within the CKEditor instance. The default
 * value points is 'includes/ckeditor/filemanager/index.html'.
 * 
 * **Base URL | `data-ckeditor-base-href` | String | Optional** 
 * 
 * The base URL of the CKEditor instance. The default value points to the `http://shop.de/admin` directory.
 * 
 * **Enter Mode | `data-ckeditor-enter-mode` | Number | Optional**
 * 
 * Define the enter mode of the CKEditor instance. The default value of this option is CKEDITOR.ENTER_BR which
 * means that the editor will use the `<br>` element for every line break. For a list of possible values visit 
 * this [CKEditor API reference page](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.enterMode).
 * 
 * **Shift Enter Mode | `data-ckeditor-shift-enter-mode` | Number| Optional**
 * 
 * Define the shift-enter mode of the CKEditor instance. The default value of this option is CKEDITOR.ENTER_P which
 * means that the editor will use the `<p>` element for every line break. For a list of possible values visit this
 * [CKEditor API reference page](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.shiftEnterMode).
 * 
 * **Language Code | `data-ckeditor-language` | String | Optional**
 * 
 * Provide a language code for the CKEditor instance. The default value comes from the 
 * `jse.core.config.get('languageCode')` value which has the active language setting of the current page. 
 * 
 * ### Example
 * 
 * When the page loads the textarea element will be converted into a CKEditor instance.
 * 
 * ```html
 * <div data-gx-widget="ckeditor"> 
 *   <textarea class="wysiwyg"></textarea>
 * </div>    
 * ```
 *
 * @module Admin/Widgets/ckeditor
 * @requires CKEditor-Library
 * 
 * @todo Replace the "wysiwyg" class with a simple "convert-to-ckeditor" class which is easier to remember.
 */
gx.widgets.module('ckeditor', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Widget Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Options for Widget
  *
  * @type {object}
  */
	defaults = { // Configuration gets passed to the ckeditor.
		'filebrowserBrowseUrl': 'includes/ckeditor/filemanager/index.html',
		'baseHref': jse.core.config.get('appUrl') + '/admin',
		'enterMode': CKEDITOR.ENTER_BR,
		'shiftEnterMode': CKEDITOR.ENTER_P,
		'language': jse.core.config.get('languageCode'),
		'useRelPath': true
	},


	/**
  * Final Widget Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {},


	/**
  * Editors Selector Object
  *
  * @type {object}
  */
	$editors = null;

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		if (!options.useRelPath) {
			options.filebrowserBrowseUrl += '?mode=mail';
		}

		$editors = $this.filter('.wysiwyg').add($this.find('.wysiwyg'));

		$editors.each(function () {
			var $self = $(this),
			    dataset = $.extend({}, options, $self.data()),
			    // Get textarea specific configuration.
			name = $self.attr('name');
			$self.removeClass('wysiwyg');
			CKEDITOR.replace(name, dataset);
		});

		// Event handler for the update event, which is updating the ckeditor with the value
		// of the textarea.
		$this.on('ckeditor.update', function () {
			$editors.each(function () {
				var $self = $(this),
				    name = $self.attr('name'),
				    editor = CKEDITOR ? CKEDITOR.instances[name] : null;

				if (editor) {
					editor.setData($self.val());
				}
			});
		});

		$this.trigger('widget.initialized', 'ckeditor');

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNrZWRpdG9yLmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJDS0VESVRPUiIsIkVOVEVSX0JSIiwiRU5URVJfUCIsIm9wdGlvbnMiLCJleHRlbmQiLCIkZWRpdG9ycyIsImluaXQiLCJkb25lIiwidXNlUmVsUGF0aCIsImZpbGVicm93c2VyQnJvd3NlVXJsIiwiZmlsdGVyIiwiYWRkIiwiZmluZCIsImVhY2giLCIkc2VsZiIsImRhdGFzZXQiLCJuYW1lIiwiYXR0ciIsInJlbW92ZUNsYXNzIiwicmVwbGFjZSIsIm9uIiwiZWRpdG9yIiwiaW5zdGFuY2VzIiwic2V0RGF0YSIsInZhbCIsInRyaWdnZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBbURBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxVQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBQUU7QUFDWiwwQkFBd0IsMENBRGQ7QUFFVixjQUFZQyxJQUFJQyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFFBQXBCLElBQWdDLFFBRmxDO0FBR1YsZUFBYUMsU0FBU0MsUUFIWjtBQUlWLG9CQUFrQkQsU0FBU0UsT0FKakI7QUFLVixjQUFZTixJQUFJQyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLGNBQXBCLENBTEY7QUFNVixnQkFBYztBQU5KLEVBYlo7OztBQXNCQzs7Ozs7QUFLQUksV0FBVVQsRUFBRVUsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CVCxRQUFuQixFQUE2QkgsSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQUQsVUFBUyxFQWxDVjs7O0FBb0NDOzs7OztBQUtBYyxZQUFXLElBekNaOztBQTJDQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBZCxRQUFPZSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCLE1BQUksQ0FBQ0osUUFBUUssVUFBYixFQUF5QjtBQUN4QkwsV0FBUU0sb0JBQVIsSUFBZ0MsWUFBaEM7QUFDQTs7QUFFREosYUFBV1osTUFDVGlCLE1BRFMsQ0FDRixVQURFLEVBRVRDLEdBRlMsQ0FFTGxCLE1BQU1tQixJQUFOLENBQVcsVUFBWCxDQUZLLENBQVg7O0FBSUFQLFdBQ0VRLElBREYsQ0FDTyxZQUFXO0FBQ2hCLE9BQUlDLFFBQVFwQixFQUFFLElBQUYsQ0FBWjtBQUFBLE9BQ0NxQixVQUFVckIsRUFBRVUsTUFBRixDQUFTLEVBQVQsRUFBYUQsT0FBYixFQUFzQlcsTUFBTXRCLElBQU4sRUFBdEIsQ0FEWDtBQUFBLE9BQ2dEO0FBQy9Dd0IsVUFBT0YsTUFBTUcsSUFBTixDQUFXLE1BQVgsQ0FGUjtBQUdBSCxTQUFNSSxXQUFOLENBQWtCLFNBQWxCO0FBQ0FsQixZQUFTbUIsT0FBVCxDQUFpQkgsSUFBakIsRUFBdUJELE9BQXZCO0FBQ0EsR0FQRjs7QUFTQTtBQUNBO0FBQ0F0QixRQUFNMkIsRUFBTixDQUFTLGlCQUFULEVBQTRCLFlBQVc7QUFDdENmLFlBQ0VRLElBREYsQ0FDTyxZQUFXO0FBQ2hCLFFBQUlDLFFBQVFwQixFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0NzQixPQUFPRixNQUFNRyxJQUFOLENBQVcsTUFBWCxDQURSO0FBQUEsUUFFQ0ksU0FBVXJCLFFBQUQsR0FBYUEsU0FBU3NCLFNBQVQsQ0FBbUJOLElBQW5CLENBQWIsR0FBd0MsSUFGbEQ7O0FBSUEsUUFBSUssTUFBSixFQUFZO0FBQ1hBLFlBQU9FLE9BQVAsQ0FBZVQsTUFBTVUsR0FBTixFQUFmO0FBQ0E7QUFDRCxJQVRGO0FBVUEsR0FYRDs7QUFhQS9CLFFBQU1nQyxPQUFOLENBQWMsb0JBQWQsRUFBb0MsVUFBcEM7O0FBRUFsQjtBQUNBLEVBcENEOztBQXNDQTtBQUNBLFFBQU9oQixNQUFQO0FBQ0EsQ0F2R0YiLCJmaWxlIjoiY2tlZGl0b3IuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNrZWRpdG9yLmpzIDIwMTYtMDMtMDdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIENLRWRpdG9yIFdpZGdldFxuICpcbiAqIFVzZSB0aGlzIHdpZGdldCBvbiBhIHBhcmVudCBjb250YWluZXIgdG8gY29udmVydCBhbGwgdGhlIHRleHRhcmVhcyB3aXRoIHRoZSBcInd5c2l3eWdcIiBjbGFzcyBpbnRvIFxuICogQ0tFZGl0b3IgaW5zdGFuY2VzIGF0IG9uY2UuIFxuICogXG4gKiBPZmZpY2lhbCBDS0VkaXRvciBXZWJzaXRlOiB7QGxpbmsgaHR0cDovL2NrZWRpdG9yLmNvbX1cbiAqIFxuICogIyMjIE9wdGlvbnMgXG4gKiBcbiAqICoqRmlsZSBCcm93c2VyIFVSTCB8IGBkYXRhLWNrZWRpdG9yLWZpbGVicm93c2VyLWJyb3dzZS11cmxgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICogXG4gKiBQcm92aWRlIHRoZSBkZWZhdWx0IFVSTCBvZiB0aGUgZmlsZSBicm93c2VyIHRoYXQgaXMgaW50ZWdyYXRlZCB3aXRoaW4gdGhlIENLRWRpdG9yIGluc3RhbmNlLiBUaGUgZGVmYXVsdFxuICogdmFsdWUgcG9pbnRzIGlzICdpbmNsdWRlcy9ja2VkaXRvci9maWxlbWFuYWdlci9pbmRleC5odG1sJy5cbiAqIFxuICogKipCYXNlIFVSTCB8IGBkYXRhLWNrZWRpdG9yLWJhc2UtaHJlZmAgfCBTdHJpbmcgfCBPcHRpb25hbCoqIFxuICogXG4gKiBUaGUgYmFzZSBVUkwgb2YgdGhlIENLRWRpdG9yIGluc3RhbmNlLiBUaGUgZGVmYXVsdCB2YWx1ZSBwb2ludHMgdG8gdGhlIGBodHRwOi8vc2hvcC5kZS9hZG1pbmAgZGlyZWN0b3J5LlxuICogXG4gKiAqKkVudGVyIE1vZGUgfCBgZGF0YS1ja2VkaXRvci1lbnRlci1tb2RlYCB8IE51bWJlciB8IE9wdGlvbmFsKipcbiAqIFxuICogRGVmaW5lIHRoZSBlbnRlciBtb2RlIG9mIHRoZSBDS0VkaXRvciBpbnN0YW5jZS4gVGhlIGRlZmF1bHQgdmFsdWUgb2YgdGhpcyBvcHRpb24gaXMgQ0tFRElUT1IuRU5URVJfQlIgd2hpY2hcbiAqIG1lYW5zIHRoYXQgdGhlIGVkaXRvciB3aWxsIHVzZSB0aGUgYDxicj5gIGVsZW1lbnQgZm9yIGV2ZXJ5IGxpbmUgYnJlYWsuIEZvciBhIGxpc3Qgb2YgcG9zc2libGUgdmFsdWVzIHZpc2l0IFxuICogdGhpcyBbQ0tFZGl0b3IgQVBJIHJlZmVyZW5jZSBwYWdlXShodHRwOi8vZG9jcy5ja3NvdXJjZS5jb20vY2tlZGl0b3JfYXBpL3N5bWJvbHMvQ0tFRElUT1IuY29uZmlnLmh0bWwjLmVudGVyTW9kZSkuXG4gKiBcbiAqICoqU2hpZnQgRW50ZXIgTW9kZSB8IGBkYXRhLWNrZWRpdG9yLXNoaWZ0LWVudGVyLW1vZGVgIHwgTnVtYmVyfCBPcHRpb25hbCoqXG4gKiBcbiAqIERlZmluZSB0aGUgc2hpZnQtZW50ZXIgbW9kZSBvZiB0aGUgQ0tFZGl0b3IgaW5zdGFuY2UuIFRoZSBkZWZhdWx0IHZhbHVlIG9mIHRoaXMgb3B0aW9uIGlzIENLRURJVE9SLkVOVEVSX1Agd2hpY2hcbiAqIG1lYW5zIHRoYXQgdGhlIGVkaXRvciB3aWxsIHVzZSB0aGUgYDxwPmAgZWxlbWVudCBmb3IgZXZlcnkgbGluZSBicmVhay4gRm9yIGEgbGlzdCBvZiBwb3NzaWJsZSB2YWx1ZXMgdmlzaXQgdGhpc1xuICogW0NLRWRpdG9yIEFQSSByZWZlcmVuY2UgcGFnZV0oaHR0cDovL2RvY3MuY2tzb3VyY2UuY29tL2NrZWRpdG9yX2FwaS9zeW1ib2xzL0NLRURJVE9SLmNvbmZpZy5odG1sIy5zaGlmdEVudGVyTW9kZSkuXG4gKiBcbiAqICoqTGFuZ3VhZ2UgQ29kZSB8IGBkYXRhLWNrZWRpdG9yLWxhbmd1YWdlYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqIFxuICogUHJvdmlkZSBhIGxhbmd1YWdlIGNvZGUgZm9yIHRoZSBDS0VkaXRvciBpbnN0YW5jZS4gVGhlIGRlZmF1bHQgdmFsdWUgY29tZXMgZnJvbSB0aGUgXG4gKiBganNlLmNvcmUuY29uZmlnLmdldCgnbGFuZ3VhZ2VDb2RlJylgIHZhbHVlIHdoaWNoIGhhcyB0aGUgYWN0aXZlIGxhbmd1YWdlIHNldHRpbmcgb2YgdGhlIGN1cnJlbnQgcGFnZS4gXG4gKiBcbiAqICMjIyBFeGFtcGxlXG4gKiBcbiAqIFdoZW4gdGhlIHBhZ2UgbG9hZHMgdGhlIHRleHRhcmVhIGVsZW1lbnQgd2lsbCBiZSBjb252ZXJ0ZWQgaW50byBhIENLRWRpdG9yIGluc3RhbmNlLlxuICogXG4gKiBgYGBodG1sXG4gKiA8ZGl2IGRhdGEtZ3gtd2lkZ2V0PVwiY2tlZGl0b3JcIj4gXG4gKiAgIDx0ZXh0YXJlYSBjbGFzcz1cInd5c2l3eWdcIj48L3RleHRhcmVhPlxuICogPC9kaXY+ICAgIFxuICogYGBgXG4gKlxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL2NrZWRpdG9yXG4gKiBAcmVxdWlyZXMgQ0tFZGl0b3ItTGlicmFyeVxuICogXG4gKiBAdG9kbyBSZXBsYWNlIHRoZSBcInd5c2l3eWdcIiBjbGFzcyB3aXRoIGEgc2ltcGxlIFwiY29udmVydC10by1ja2VkaXRvclwiIGNsYXNzIHdoaWNoIGlzIGVhc2llciB0byByZW1lbWJlci5cbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCdja2VkaXRvcicsXG5cdFxuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBXaWRnZXQgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7IC8vIENvbmZpZ3VyYXRpb24gZ2V0cyBwYXNzZWQgdG8gdGhlIGNrZWRpdG9yLlxuXHRcdFx0XHQnZmlsZWJyb3dzZXJCcm93c2VVcmwnOiAnaW5jbHVkZXMvY2tlZGl0b3IvZmlsZW1hbmFnZXIvaW5kZXguaHRtbCcsXG5cdFx0XHRcdCdiYXNlSHJlZic6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbicsXG5cdFx0XHRcdCdlbnRlck1vZGUnOiBDS0VESVRPUi5FTlRFUl9CUixcblx0XHRcdFx0J3NoaWZ0RW50ZXJNb2RlJzogQ0tFRElUT1IuRU5URVJfUCxcblx0XHRcdFx0J2xhbmd1YWdlJzoganNlLmNvcmUuY29uZmlnLmdldCgnbGFuZ3VhZ2VDb2RlJyksXG5cdFx0XHRcdCd1c2VSZWxQYXRoJzogdHJ1ZVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBFZGl0b3JzIFNlbGVjdG9yIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRlZGl0b3JzID0gbnVsbDtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0aWYgKCFvcHRpb25zLnVzZVJlbFBhdGgpIHtcblx0XHRcdFx0b3B0aW9ucy5maWxlYnJvd3NlckJyb3dzZVVybCArPSAnP21vZGU9bWFpbCc7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCRlZGl0b3JzID0gJHRoaXNcblx0XHRcdFx0LmZpbHRlcignLnd5c2l3eWcnKVxuXHRcdFx0XHQuYWRkKCR0aGlzLmZpbmQoJy53eXNpd3lnJykpO1xuXHRcdFx0XG5cdFx0XHQkZWRpdG9yc1xuXHRcdFx0XHQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCBvcHRpb25zLCAkc2VsZi5kYXRhKCkpLCAvLyBHZXQgdGV4dGFyZWEgc3BlY2lmaWMgY29uZmlndXJhdGlvbi5cblx0XHRcdFx0XHRcdG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJyk7XG5cdFx0XHRcdFx0JHNlbGYucmVtb3ZlQ2xhc3MoJ3d5c2l3eWcnKTtcblx0XHRcdFx0XHRDS0VESVRPUi5yZXBsYWNlKG5hbWUsIGRhdGFzZXQpO1xuXHRcdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gRXZlbnQgaGFuZGxlciBmb3IgdGhlIHVwZGF0ZSBldmVudCwgd2hpY2ggaXMgdXBkYXRpbmcgdGhlIGNrZWRpdG9yIHdpdGggdGhlIHZhbHVlXG5cdFx0XHQvLyBvZiB0aGUgdGV4dGFyZWEuXG5cdFx0XHQkdGhpcy5vbignY2tlZGl0b3IudXBkYXRlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCRlZGl0b3JzXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHRuYW1lID0gJHNlbGYuYXR0cignbmFtZScpLFxuXHRcdFx0XHRcdFx0XHRlZGl0b3IgPSAoQ0tFRElUT1IpID8gQ0tFRElUT1IuaW5zdGFuY2VzW25hbWVdIDogbnVsbDtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0aWYgKGVkaXRvcikge1xuXHRcdFx0XHRcdFx0XHRlZGl0b3Iuc2V0RGF0YSgkc2VsZi52YWwoKSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JHRoaXMudHJpZ2dlcignd2lkZ2V0LmluaXRpYWxpemVkJywgJ2NrZWRpdG9yJyk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
