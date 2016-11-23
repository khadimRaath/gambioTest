'use strict';

/* --------------------------------------------------------------
 toolbar_icons.js 2015-09-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Toolbar Icons Extension
 *
 * This extension will search for specific-class elements inside a container and will prepend them with
 * a new `<i>` element that has the corresponding FontAwesome icon. By doing so you can dynamically inject
 * icons into existing toolbar items by setting the required classes.
 * 
 * In the following list you can see the relations between the classes and their icons: 
 * 
 * - btn-edit: [fa-pencil](http://fortawesome.github.io/Font-Awesome/icon/pencil)
 * - btn-editdoc: [fa-pencil](http://fortawesome.github.io/Font-Awesome/icon/pencil)
 * - btn-view: [fa-eye](http://fortawesome.github.io/Font-Awesome/icon/eye)
 * - btn-delete: [fa-trash-o](http://fortawesome.github.io/Font-Awesome/icon/trash-o)
 * - btn-order: [fa-shopping-cart](http://fortawesome.github.io/Font-Awesome/icon/shopping-cart)
 * - btn-caret: [fa-caret-right](http://fortawesome.github.io/Font-Awesome/icon/caret-right)
 * - btn-folder: [fa-folder-open](http://fortawesome.github.io/Font-Awesome/icon/folder)
 * - btn-multi-action: [fa-check-square-o](http://fortawesome.github.io/Font-Awesome/icon/check-square-o)
 * - btn-cash: [fa-money](http://fortawesome.github.io/Font-Awesome/icon/money)
 * - btn-add: [fa-plus](http://fortawesome.github.io/Font-Awesome/icon/plus)
 * 
 * ### Options
 *
 * The extension contains additional options that can be used to modify the display of the icons. You can
 * use them together at the same time.
 *
 * **Large Icons | `data-toolbar_icons-large` | Boolean | Optional**
 *
 * This option will add the "fa-lg" class to the icons which will make them bigger.
 *
 * ```html
 * <div class="container" data-gx-extension="toolbar_icons" data-toolbar_icons-large="true">
 *   <button class="btn-edit"></button>
 * </div>
 * ```
 *
 * **Fixed Width | `data-toolbar_icons-fixedwidth` | Boolean | Optional**
 *
 * This option will add the "fa-fw" class to the icons which will keep the icon width fixed.
 *
 * ```html
 * <div class="container" data-gx-extension="toolbar_icons" data-toolbar_icons-fixedwidth="true">
 *   <button class="btn-view"></button>
 * </div>
 * ```
 * 
 * ### Example
 * 
 * After the engine is initialized the following button elements will contain the corresponding FontAwesome icons.
 * 
 * ```html
 * <div class="container" data-gx-extension="toolbar_icons"> 
 *   <button class="btn-edit">&amp;nbsp;Edit</button>    
 *   <button class="btn-view">&amp;nbsp;View</button>    
 *   <button class="btn-order">&amp;nbsp;Buy Item</button>    
 * </div>
 * ```
 * 
 * *Note that the use of **&amp;nbsp;** is required only if you want to add some space between the icon and the 
 * text. You can avoid it by styling the margin space between the icon and the text.* 
 * 
 * FontAwesome provides many helper classes that can be used directly on the elements in order to adjust the
 * final visual result. Visit the follow link for more examples and sample code.
 * {@link https://fortawesome.github.io/Font-Awesome/examples}
 *  
 * @module Admin/Extensions/toolbar_icons
 */
gx.extensions.module('toolbar_icons', [], function (data) {

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
  * Default Options for Extension
  *
  * @type {object}
  * 
  * @todo Add default values to the extension. 
  */
	defaults = {},


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
  * Initialize method of the extension, called by the engine.
  */
	module.init = function (done) {

		// Define class names and the respective Font-Awesome classes here
		// @todo The selectors must be dynamic, move these to the "defaults.selectors" property.
		var classes = {
			'.btn-edit': 'fa-pencil',
			'.btn-view': 'fa-eye',
			'.btn-editdoc': 'fa-pencil',
			'.btn-delete': 'fa-trash-o',
			'.btn-order': 'fa-shopping-cart',
			'.btn-caret': 'fa-caret-right',
			'.btn-folder': 'fa-folder-open',
			'.btn-multi-action': 'fa-check-square-o',
			'.btn-cash': 'fa-money',
			'.btn-add': 'fa-plus'
		};

		// Let's rock
		$.each(classes, function (key, value) {
			var composedClassName = [value, options.large ? ' fa-lg' : '',
			// @todo "fixedwidth" must be CamelCase or underscore_separated.
			options.fixedwidth ? ' fa-fw' : ''].join('');

			var $tag = $('<i class="fa ' + composedClassName + '"></i>');
			$this.find(key).prepend($tag);
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRvb2xiYXJfaWNvbnMuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwiY2xhc3NlcyIsImVhY2giLCJrZXkiLCJ2YWx1ZSIsImNvbXBvc2VkQ2xhc3NOYW1lIiwibGFyZ2UiLCJmaXhlZHdpZHRoIiwiam9pbiIsIiR0YWciLCJmaW5kIiwicHJlcGVuZCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFrRUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLGVBREQsRUFHQyxFQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7Ozs7QUFPQUMsWUFBVyxFQWZaOzs7QUFpQkM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBdEJYOzs7QUF3QkM7Ozs7O0FBS0FELFVBQVMsRUE3QlY7O0FBK0JBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FBLFFBQU9PLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCO0FBQ0E7QUFDQSxNQUFJQyxVQUFVO0FBQ2IsZ0JBQWEsV0FEQTtBQUViLGdCQUFhLFFBRkE7QUFHYixtQkFBZ0IsV0FISDtBQUliLGtCQUFlLFlBSkY7QUFLYixpQkFBYyxrQkFMRDtBQU1iLGlCQUFjLGdCQU5EO0FBT2Isa0JBQWUsZ0JBUEY7QUFRYix3QkFBcUIsbUJBUlI7QUFTYixnQkFBYSxVQVRBO0FBVWIsZUFBWTtBQVZDLEdBQWQ7O0FBYUE7QUFDQU4sSUFBRU8sSUFBRixDQUFPRCxPQUFQLEVBQWdCLFVBQVNFLEdBQVQsRUFBY0MsS0FBZCxFQUFxQjtBQUNwQyxPQUFJQyxvQkFBb0IsQ0FDdkJELEtBRHVCLEVBRXRCUCxRQUFRUyxLQUFSLEdBQWdCLFFBQWhCLEdBQTJCLEVBRkw7QUFHdkI7QUFDQ1QsV0FBUVUsVUFBUixHQUFxQixRQUFyQixHQUFnQyxFQUpWLEVBS3RCQyxJQUxzQixDQUtqQixFQUxpQixDQUF4Qjs7QUFPQSxPQUFJQyxPQUFPZCxFQUFFLGtCQUFrQlUsaUJBQWxCLEdBQXNDLFFBQXhDLENBQVg7QUFDQVgsU0FBTWdCLElBQU4sQ0FBV1AsR0FBWCxFQUFnQlEsT0FBaEIsQ0FBd0JGLElBQXhCO0FBQ0EsR0FWRDs7QUFZQVQ7QUFDQSxFQS9CRDs7QUFpQ0E7QUFDQSxRQUFPUixNQUFQO0FBQ0EsQ0F0RkYiLCJmaWxlIjoidG9vbGJhcl9pY29ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gdG9vbGJhcl9pY29ucy5qcyAyMDE1LTA5LTE5IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBUb29sYmFyIEljb25zIEV4dGVuc2lvblxuICpcbiAqIFRoaXMgZXh0ZW5zaW9uIHdpbGwgc2VhcmNoIGZvciBzcGVjaWZpYy1jbGFzcyBlbGVtZW50cyBpbnNpZGUgYSBjb250YWluZXIgYW5kIHdpbGwgcHJlcGVuZCB0aGVtIHdpdGhcbiAqIGEgbmV3IGA8aT5gIGVsZW1lbnQgdGhhdCBoYXMgdGhlIGNvcnJlc3BvbmRpbmcgRm9udEF3ZXNvbWUgaWNvbi4gQnkgZG9pbmcgc28geW91IGNhbiBkeW5hbWljYWxseSBpbmplY3RcbiAqIGljb25zIGludG8gZXhpc3RpbmcgdG9vbGJhciBpdGVtcyBieSBzZXR0aW5nIHRoZSByZXF1aXJlZCBjbGFzc2VzLlxuICogXG4gKiBJbiB0aGUgZm9sbG93aW5nIGxpc3QgeW91IGNhbiBzZWUgdGhlIHJlbGF0aW9ucyBiZXR3ZWVuIHRoZSBjbGFzc2VzIGFuZCB0aGVpciBpY29uczogXG4gKiBcbiAqIC0gYnRuLWVkaXQ6IFtmYS1wZW5jaWxdKGh0dHA6Ly9mb3J0YXdlc29tZS5naXRodWIuaW8vRm9udC1Bd2Vzb21lL2ljb24vcGVuY2lsKVxuICogLSBidG4tZWRpdGRvYzogW2ZhLXBlbmNpbF0oaHR0cDovL2ZvcnRhd2Vzb21lLmdpdGh1Yi5pby9Gb250LUF3ZXNvbWUvaWNvbi9wZW5jaWwpXG4gKiAtIGJ0bi12aWV3OiBbZmEtZXllXShodHRwOi8vZm9ydGF3ZXNvbWUuZ2l0aHViLmlvL0ZvbnQtQXdlc29tZS9pY29uL2V5ZSlcbiAqIC0gYnRuLWRlbGV0ZTogW2ZhLXRyYXNoLW9dKGh0dHA6Ly9mb3J0YXdlc29tZS5naXRodWIuaW8vRm9udC1Bd2Vzb21lL2ljb24vdHJhc2gtbylcbiAqIC0gYnRuLW9yZGVyOiBbZmEtc2hvcHBpbmctY2FydF0oaHR0cDovL2ZvcnRhd2Vzb21lLmdpdGh1Yi5pby9Gb250LUF3ZXNvbWUvaWNvbi9zaG9wcGluZy1jYXJ0KVxuICogLSBidG4tY2FyZXQ6IFtmYS1jYXJldC1yaWdodF0oaHR0cDovL2ZvcnRhd2Vzb21lLmdpdGh1Yi5pby9Gb250LUF3ZXNvbWUvaWNvbi9jYXJldC1yaWdodClcbiAqIC0gYnRuLWZvbGRlcjogW2ZhLWZvbGRlci1vcGVuXShodHRwOi8vZm9ydGF3ZXNvbWUuZ2l0aHViLmlvL0ZvbnQtQXdlc29tZS9pY29uL2ZvbGRlcilcbiAqIC0gYnRuLW11bHRpLWFjdGlvbjogW2ZhLWNoZWNrLXNxdWFyZS1vXShodHRwOi8vZm9ydGF3ZXNvbWUuZ2l0aHViLmlvL0ZvbnQtQXdlc29tZS9pY29uL2NoZWNrLXNxdWFyZS1vKVxuICogLSBidG4tY2FzaDogW2ZhLW1vbmV5XShodHRwOi8vZm9ydGF3ZXNvbWUuZ2l0aHViLmlvL0ZvbnQtQXdlc29tZS9pY29uL21vbmV5KVxuICogLSBidG4tYWRkOiBbZmEtcGx1c10oaHR0cDovL2ZvcnRhd2Vzb21lLmdpdGh1Yi5pby9Gb250LUF3ZXNvbWUvaWNvbi9wbHVzKVxuICogXG4gKiAjIyMgT3B0aW9uc1xuICpcbiAqIFRoZSBleHRlbnNpb24gY29udGFpbnMgYWRkaXRpb25hbCBvcHRpb25zIHRoYXQgY2FuIGJlIHVzZWQgdG8gbW9kaWZ5IHRoZSBkaXNwbGF5IG9mIHRoZSBpY29ucy4gWW91IGNhblxuICogdXNlIHRoZW0gdG9nZXRoZXIgYXQgdGhlIHNhbWUgdGltZS5cbiAqXG4gKiAqKkxhcmdlIEljb25zIHwgYGRhdGEtdG9vbGJhcl9pY29ucy1sYXJnZWAgfCBCb29sZWFuIHwgT3B0aW9uYWwqKlxuICpcbiAqIFRoaXMgb3B0aW9uIHdpbGwgYWRkIHRoZSBcImZhLWxnXCIgY2xhc3MgdG8gdGhlIGljb25zIHdoaWNoIHdpbGwgbWFrZSB0aGVtIGJpZ2dlci5cbiAqXG4gKiBgYGBodG1sXG4gKiA8ZGl2IGNsYXNzPVwiY29udGFpbmVyXCIgZGF0YS1neC1leHRlbnNpb249XCJ0b29sYmFyX2ljb25zXCIgZGF0YS10b29sYmFyX2ljb25zLWxhcmdlPVwidHJ1ZVwiPlxuICogICA8YnV0dG9uIGNsYXNzPVwiYnRuLWVkaXRcIj48L2J1dHRvbj5cbiAqIDwvZGl2PlxuICogYGBgXG4gKlxuICogKipGaXhlZCBXaWR0aCB8IGBkYXRhLXRvb2xiYXJfaWNvbnMtZml4ZWR3aWR0aGAgfCBCb29sZWFuIHwgT3B0aW9uYWwqKlxuICpcbiAqIFRoaXMgb3B0aW9uIHdpbGwgYWRkIHRoZSBcImZhLWZ3XCIgY2xhc3MgdG8gdGhlIGljb25zIHdoaWNoIHdpbGwga2VlcCB0aGUgaWNvbiB3aWR0aCBmaXhlZC5cbiAqXG4gKiBgYGBodG1sXG4gKiA8ZGl2IGNsYXNzPVwiY29udGFpbmVyXCIgZGF0YS1neC1leHRlbnNpb249XCJ0b29sYmFyX2ljb25zXCIgZGF0YS10b29sYmFyX2ljb25zLWZpeGVkd2lkdGg9XCJ0cnVlXCI+XG4gKiAgIDxidXR0b24gY2xhc3M9XCJidG4tdmlld1wiPjwvYnV0dG9uPlxuICogPC9kaXY+XG4gKiBgYGBcbiAqIFxuICogIyMjIEV4YW1wbGVcbiAqIFxuICogQWZ0ZXIgdGhlIGVuZ2luZSBpcyBpbml0aWFsaXplZCB0aGUgZm9sbG93aW5nIGJ1dHRvbiBlbGVtZW50cyB3aWxsIGNvbnRhaW4gdGhlIGNvcnJlc3BvbmRpbmcgRm9udEF3ZXNvbWUgaWNvbnMuXG4gKiBcbiAqIGBgYGh0bWxcbiAqIDxkaXYgY2xhc3M9XCJjb250YWluZXJcIiBkYXRhLWd4LWV4dGVuc2lvbj1cInRvb2xiYXJfaWNvbnNcIj4gXG4gKiAgIDxidXR0b24gY2xhc3M9XCJidG4tZWRpdFwiPiZhbXA7bmJzcDtFZGl0PC9idXR0b24+ICAgIFxuICogICA8YnV0dG9uIGNsYXNzPVwiYnRuLXZpZXdcIj4mYW1wO25ic3A7VmlldzwvYnV0dG9uPiAgICBcbiAqICAgPGJ1dHRvbiBjbGFzcz1cImJ0bi1vcmRlclwiPiZhbXA7bmJzcDtCdXkgSXRlbTwvYnV0dG9uPiAgICBcbiAqIDwvZGl2PlxuICogYGBgXG4gKiBcbiAqICpOb3RlIHRoYXQgdGhlIHVzZSBvZiAqKiZhbXA7bmJzcDsqKiBpcyByZXF1aXJlZCBvbmx5IGlmIHlvdSB3YW50IHRvIGFkZCBzb21lIHNwYWNlIGJldHdlZW4gdGhlIGljb24gYW5kIHRoZSBcbiAqIHRleHQuIFlvdSBjYW4gYXZvaWQgaXQgYnkgc3R5bGluZyB0aGUgbWFyZ2luIHNwYWNlIGJldHdlZW4gdGhlIGljb24gYW5kIHRoZSB0ZXh0LiogXG4gKiBcbiAqIEZvbnRBd2Vzb21lIHByb3ZpZGVzIG1hbnkgaGVscGVyIGNsYXNzZXMgdGhhdCBjYW4gYmUgdXNlZCBkaXJlY3RseSBvbiB0aGUgZWxlbWVudHMgaW4gb3JkZXIgdG8gYWRqdXN0IHRoZVxuICogZmluYWwgdmlzdWFsIHJlc3VsdC4gVmlzaXQgdGhlIGZvbGxvdyBsaW5rIGZvciBtb3JlIGV4YW1wbGVzIGFuZCBzYW1wbGUgY29kZS5cbiAqIHtAbGluayBodHRwczovL2ZvcnRhd2Vzb21lLmdpdGh1Yi5pby9Gb250LUF3ZXNvbWUvZXhhbXBsZXN9XG4gKiAgXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvdG9vbGJhcl9pY29uc1xuICovXG5neC5leHRlbnNpb25zLm1vZHVsZShcblx0J3Rvb2xiYXJfaWNvbnMnLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKiBcblx0XHRcdCAqIEB0b2RvIEFkZCBkZWZhdWx0IHZhbHVlcyB0byB0aGUgZXh0ZW5zaW9uLiBcblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSBleHRlbnNpb24sIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHQvLyBEZWZpbmUgY2xhc3MgbmFtZXMgYW5kIHRoZSByZXNwZWN0aXZlIEZvbnQtQXdlc29tZSBjbGFzc2VzIGhlcmVcblx0XHRcdC8vIEB0b2RvIFRoZSBzZWxlY3RvcnMgbXVzdCBiZSBkeW5hbWljLCBtb3ZlIHRoZXNlIHRvIHRoZSBcImRlZmF1bHRzLnNlbGVjdG9yc1wiIHByb3BlcnR5LlxuXHRcdFx0dmFyIGNsYXNzZXMgPSB7XG5cdFx0XHRcdCcuYnRuLWVkaXQnOiAnZmEtcGVuY2lsJyxcblx0XHRcdFx0Jy5idG4tdmlldyc6ICdmYS1leWUnLFxuXHRcdFx0XHQnLmJ0bi1lZGl0ZG9jJzogJ2ZhLXBlbmNpbCcsXG5cdFx0XHRcdCcuYnRuLWRlbGV0ZSc6ICdmYS10cmFzaC1vJyxcblx0XHRcdFx0Jy5idG4tb3JkZXInOiAnZmEtc2hvcHBpbmctY2FydCcsXG5cdFx0XHRcdCcuYnRuLWNhcmV0JzogJ2ZhLWNhcmV0LXJpZ2h0Jyxcblx0XHRcdFx0Jy5idG4tZm9sZGVyJzogJ2ZhLWZvbGRlci1vcGVuJyxcblx0XHRcdFx0Jy5idG4tbXVsdGktYWN0aW9uJzogJ2ZhLWNoZWNrLXNxdWFyZS1vJyxcblx0XHRcdFx0Jy5idG4tY2FzaCc6ICdmYS1tb25leScsXG5cdFx0XHRcdCcuYnRuLWFkZCc6ICdmYS1wbHVzJ1xuXHRcdFx0fTtcblx0XHRcdFxuXHRcdFx0Ly8gTGV0J3Mgcm9ja1xuXHRcdFx0JC5lYWNoKGNsYXNzZXMsIGZ1bmN0aW9uKGtleSwgdmFsdWUpIHtcblx0XHRcdFx0dmFyIGNvbXBvc2VkQ2xhc3NOYW1lID0gW1xuXHRcdFx0XHRcdHZhbHVlLFxuXHRcdFx0XHRcdChvcHRpb25zLmxhcmdlID8gJyBmYS1sZycgOiAnJyksXG5cdFx0XHRcdFx0Ly8gQHRvZG8gXCJmaXhlZHdpZHRoXCIgbXVzdCBiZSBDYW1lbENhc2Ugb3IgdW5kZXJzY29yZV9zZXBhcmF0ZWQuXG5cdFx0XHRcdFx0KG9wdGlvbnMuZml4ZWR3aWR0aCA/ICcgZmEtZncnIDogJycpIFxuXHRcdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0XHRcblx0XHRcdFx0dmFyICR0YWcgPSAkKCc8aSBjbGFzcz1cImZhICcgKyBjb21wb3NlZENsYXNzTmFtZSArICdcIj48L2k+Jyk7XG5cdFx0XHRcdCR0aGlzLmZpbmQoa2V5KS5wcmVwZW5kKCR0YWcpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
