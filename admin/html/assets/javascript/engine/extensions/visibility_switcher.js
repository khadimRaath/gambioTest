'use strict';

/* --------------------------------------------------------------
 visibility_switcher.js 2015-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Visibility Switcher Extension
 * 
 * Use this extension in a parent element to easily define the visibility of child elements during the 
 * mouse hover of their containers. When the "mouseleave" event is triggered the children will be hidden.
 * 
 * ### Options 
 * 
 * **Rows | data-visibility_switcher-rows | String | Required**
 *
 * Provide a jQuery selector string which points to the elements that have the "hover" event. 
 * 
 * **Selections | data-visibility_switcher-selections | String | Required** 
 * 
 * Provide a jQuery selector string which points to the elements to be displayed upon the "hover" event.
 * 
 * ### Example 
 * 
 * In the following example the .row-action elements will be visible whenever the user hovers above of the 
 * `<tr>` element. The initial state of the elements must be hidden (thus the 'hidden' class).
 * 
 * ```html
 * <table data-gx-extension="visibility_switcher" 
 *       data-visibility_switcher-rows="tr.row" 
 *       data-visibility_switcher-selections="i.row-action"> 
 *   <tr class="row">
 *     <td>#1</td>
 *     <td>John Doe</td>
 *     <td>
 *       <i class="fa fa-pencil row-action edit hidden"></i>
 *       <i class="fa fa-trash row-action delete hidden"></i>
 *     </td>
 *   </tr>
 * </table>
 * ```
 * 
 * *Whenever the user hovers at the table rows the .row-action elements will be visible and whenever the 
 * mouse leaves the rows they will be hidden.*
 *
 * @module Admin/Extensions/visibility_switcher
 */
gx.extensions.module('visibility_switcher', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  * 
  * @todo Rename 'rows' option to 'containerSelector' and 'selections' to 'childrenSelector'.
  */
	defaults = {
		'rows': '.visibility_switcher',
		'selections': '.tooltip-icon'
	},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	var _visibility = function _visibility(e) {
		var $self = $(this);
		$self.filter(options.selections).add($self.find(options.selections)).css('visibility', e.data.state);
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		$this.on('mouseenter', options.rows, { 'state': 'visible' }, _visibility).on('mouseleave', options.rows, { 'state': 'hidden' }, _visibility);

		$this.find(options.rows + ' ' + options.selections).css('visibility', 'hidden');

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInZpc2liaWxpdHlfc3dpdGNoZXIuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl92aXNpYmlsaXR5IiwiZSIsIiRzZWxmIiwiZmlsdGVyIiwic2VsZWN0aW9ucyIsImFkZCIsImZpbmQiLCJjc3MiLCJzdGF0ZSIsImluaXQiLCJkb25lIiwib24iLCJyb3dzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUNBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FDQyxxQkFERCxFQUdDLEVBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7OztBQU9BQyxZQUFXO0FBQ1YsVUFBUSxzQkFERTtBQUVWLGdCQUFjO0FBRkosRUFmWjs7O0FBb0JDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXpCWDs7O0FBMkJDOzs7OztBQUtBRCxVQUFTLEVBaENWOztBQWtDQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSU8sY0FBYyxTQUFkQSxXQUFjLENBQVNDLENBQVQsRUFBWTtBQUM3QixNQUFJQyxRQUFRTixFQUFFLElBQUYsQ0FBWjtBQUNBTSxRQUNFQyxNQURGLENBQ1NMLFFBQVFNLFVBRGpCLEVBRUVDLEdBRkYsQ0FFTUgsTUFBTUksSUFBTixDQUFXUixRQUFRTSxVQUFuQixDQUZOLEVBR0VHLEdBSEYsQ0FHTSxZQUhOLEVBR29CTixFQUFFUCxJQUFGLENBQU9jLEtBSDNCO0FBSUEsRUFORDs7QUFRQTtBQUNBO0FBQ0E7O0FBRUFmLFFBQU9nQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1QmYsUUFDRWdCLEVBREYsQ0FDSyxZQURMLEVBQ21CYixRQUFRYyxJQUQzQixFQUNpQyxFQUFDLFNBQVMsU0FBVixFQURqQyxFQUN1RFosV0FEdkQsRUFFRVcsRUFGRixDQUVLLFlBRkwsRUFFbUJiLFFBQVFjLElBRjNCLEVBRWlDLEVBQUMsU0FBUyxRQUFWLEVBRmpDLEVBRXNEWixXQUZ0RDs7QUFJQUwsUUFDRVcsSUFERixDQUNPUixRQUFRYyxJQUFSLEdBQWUsR0FBZixHQUFxQmQsUUFBUU0sVUFEcEMsRUFFRUcsR0FGRixDQUVNLFlBRk4sRUFFb0IsUUFGcEI7O0FBSUFHO0FBRUEsRUFaRDs7QUFjQSxRQUFPakIsTUFBUDtBQUNBLENBOUVGIiwiZmlsZSI6InZpc2liaWxpdHlfc3dpdGNoZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHZpc2liaWxpdHlfc3dpdGNoZXIuanMgMjAxNS0wOS0yMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgVmlzaWJpbGl0eSBTd2l0Y2hlciBFeHRlbnNpb25cbiAqIFxuICogVXNlIHRoaXMgZXh0ZW5zaW9uIGluIGEgcGFyZW50IGVsZW1lbnQgdG8gZWFzaWx5IGRlZmluZSB0aGUgdmlzaWJpbGl0eSBvZiBjaGlsZCBlbGVtZW50cyBkdXJpbmcgdGhlIFxuICogbW91c2UgaG92ZXIgb2YgdGhlaXIgY29udGFpbmVycy4gV2hlbiB0aGUgXCJtb3VzZWxlYXZlXCIgZXZlbnQgaXMgdHJpZ2dlcmVkIHRoZSBjaGlsZHJlbiB3aWxsIGJlIGhpZGRlbi5cbiAqIFxuICogIyMjIE9wdGlvbnMgXG4gKiBcbiAqICoqUm93cyB8IGRhdGEtdmlzaWJpbGl0eV9zd2l0Y2hlci1yb3dzIHwgU3RyaW5nIHwgUmVxdWlyZWQqKlxuICpcbiAqIFByb3ZpZGUgYSBqUXVlcnkgc2VsZWN0b3Igc3RyaW5nIHdoaWNoIHBvaW50cyB0byB0aGUgZWxlbWVudHMgdGhhdCBoYXZlIHRoZSBcImhvdmVyXCIgZXZlbnQuIFxuICogXG4gKiAqKlNlbGVjdGlvbnMgfCBkYXRhLXZpc2liaWxpdHlfc3dpdGNoZXItc2VsZWN0aW9ucyB8IFN0cmluZyB8IFJlcXVpcmVkKiogXG4gKiBcbiAqIFByb3ZpZGUgYSBqUXVlcnkgc2VsZWN0b3Igc3RyaW5nIHdoaWNoIHBvaW50cyB0byB0aGUgZWxlbWVudHMgdG8gYmUgZGlzcGxheWVkIHVwb24gdGhlIFwiaG92ZXJcIiBldmVudC5cbiAqIFxuICogIyMjIEV4YW1wbGUgXG4gKiBcbiAqIEluIHRoZSBmb2xsb3dpbmcgZXhhbXBsZSB0aGUgLnJvdy1hY3Rpb24gZWxlbWVudHMgd2lsbCBiZSB2aXNpYmxlIHdoZW5ldmVyIHRoZSB1c2VyIGhvdmVycyBhYm92ZSBvZiB0aGUgXG4gKiBgPHRyPmAgZWxlbWVudC4gVGhlIGluaXRpYWwgc3RhdGUgb2YgdGhlIGVsZW1lbnRzIG11c3QgYmUgaGlkZGVuICh0aHVzIHRoZSAnaGlkZGVuJyBjbGFzcykuXG4gKiBcbiAqIGBgYGh0bWxcbiAqIDx0YWJsZSBkYXRhLWd4LWV4dGVuc2lvbj1cInZpc2liaWxpdHlfc3dpdGNoZXJcIiBcbiAqICAgICAgIGRhdGEtdmlzaWJpbGl0eV9zd2l0Y2hlci1yb3dzPVwidHIucm93XCIgXG4gKiAgICAgICBkYXRhLXZpc2liaWxpdHlfc3dpdGNoZXItc2VsZWN0aW9ucz1cImkucm93LWFjdGlvblwiPiBcbiAqICAgPHRyIGNsYXNzPVwicm93XCI+XG4gKiAgICAgPHRkPiMxPC90ZD5cbiAqICAgICA8dGQ+Sm9obiBEb2U8L3RkPlxuICogICAgIDx0ZD5cbiAqICAgICAgIDxpIGNsYXNzPVwiZmEgZmEtcGVuY2lsIHJvdy1hY3Rpb24gZWRpdCBoaWRkZW5cIj48L2k+XG4gKiAgICAgICA8aSBjbGFzcz1cImZhIGZhLXRyYXNoIHJvdy1hY3Rpb24gZGVsZXRlIGhpZGRlblwiPjwvaT5cbiAqICAgICA8L3RkPlxuICogICA8L3RyPlxuICogPC90YWJsZT5cbiAqIGBgYFxuICogXG4gKiAqV2hlbmV2ZXIgdGhlIHVzZXIgaG92ZXJzIGF0IHRoZSB0YWJsZSByb3dzIHRoZSAucm93LWFjdGlvbiBlbGVtZW50cyB3aWxsIGJlIHZpc2libGUgYW5kIHdoZW5ldmVyIHRoZSBcbiAqIG1vdXNlIGxlYXZlcyB0aGUgcm93cyB0aGV5IHdpbGwgYmUgaGlkZGVuLipcbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvdmlzaWJpbGl0eV9zd2l0Y2hlclxuICovXG5neC5leHRlbnNpb25zLm1vZHVsZShcblx0J3Zpc2liaWxpdHlfc3dpdGNoZXInLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKiBcblx0XHRcdCAqIEB0b2RvIFJlbmFtZSAncm93cycgb3B0aW9uIHRvICdjb250YWluZXJTZWxlY3RvcicgYW5kICdzZWxlY3Rpb25zJyB0byAnY2hpbGRyZW5TZWxlY3RvcicuXG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQncm93cyc6ICcudmlzaWJpbGl0eV9zd2l0Y2hlcicsIFxuXHRcdFx0XHQnc2VsZWN0aW9ucyc6ICcudG9vbHRpcC1pY29uJ1xuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX3Zpc2liaWxpdHkgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpO1xuXHRcdFx0JHNlbGZcblx0XHRcdFx0LmZpbHRlcihvcHRpb25zLnNlbGVjdGlvbnMpXG5cdFx0XHRcdC5hZGQoJHNlbGYuZmluZChvcHRpb25zLnNlbGVjdGlvbnMpKVxuXHRcdFx0XHQuY3NzKCd2aXNpYmlsaXR5JywgZS5kYXRhLnN0YXRlKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbignbW91c2VlbnRlcicsIG9wdGlvbnMucm93cywgeydzdGF0ZSc6ICd2aXNpYmxlJ30sIF92aXNpYmlsaXR5KVxuXHRcdFx0XHQub24oJ21vdXNlbGVhdmUnLCBvcHRpb25zLnJvd3MsIHsnc3RhdGUnOiAnaGlkZGVuJ30sIF92aXNpYmlsaXR5KTtcblx0XHRcdFxuXHRcdFx0JHRoaXNcblx0XHRcdFx0LmZpbmQob3B0aW9ucy5yb3dzICsgJyAnICsgb3B0aW9ucy5zZWxlY3Rpb25zKVxuXHRcdFx0XHQuY3NzKCd2aXNpYmlsaXR5JywgJ2hpZGRlbicpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
