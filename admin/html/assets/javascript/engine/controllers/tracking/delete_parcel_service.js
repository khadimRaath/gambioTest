'use strict';

/* --------------------------------------------------------------
 delete_parcel_service.js 2016-07-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Delete Parcel Service Controller
 */
gx.controllers.module('delete_parcel_service', ['xhr'], function () {

  'use strict';

  // ------------------------------------------------------------------------
  // VARIABLES
  // ------------------------------------------------------------------------

  /**
   * Module Selector
   *
   * @type {jQuery}
   */

  var $this = $(this);

  /**
   * Parcel service list element.
   * 
   * @type {jQuery}
   */
  var $parcelServiceList = $('#parcel_services_wrapper');

  /**
   * Module data set.
   *
   * @type {Object}
   */
  var dataset = $this.data();

  /**
   * Module Instance
   *
   * @type {Object}
   */
  var module = {};

  // ------------------------------------------------------------------------
  // EVENT HANDLERS
  // ------------------------------------------------------------------------

  /**
   * Handles the button click event in parcel services removal confirmation modal.
   * @param {Event} event Triggered event.
   */
  var _handleDeleteAction = function _handleDeleteAction(event) {
    // Clicked button element.      
    var $button = $(event.target);

    // CSS class to indicate already clicked button.
    var activeButtonClass = 'active';

    // AJAX request URL.
    var url = 'request_port.php?module=ParcelServices&action=delete_parcel_service';

    // AJAX request POST data.
    var data = {
      'parcel_service_id': dataset.lightboxParams.parcel_service_id,
      'page_token': dataset.lightboxParams.page_token
    };

    // Prevent default behavior and prevent event bubbling.
    event.preventDefault();
    event.stopPropagation();

    // Exit immediately if button has been already clicked.
    if ($button.hasClass(activeButtonClass)) {
      return false;
    }

    // Mark button as clicked to prevent multiple clicks. 
    $button.addClass(activeButtonClass);

    // Perform AJAX POST request.
    var request = jse.libs.xhr.post({ url: url, data: data });

    // AJAX request success handler.
    request.done(function (response) {
      $parcelServiceList.html(response.html);
      $.lightbox_plugin('close', dataset.lightboxParams.identifier);
    });

    // AJAX request error handler.
    request.fail(function (jqXHR, exception) {
      return $.lightbox_plugin('error', dataset.lightboxParams.identifier, jqXHR, exception);
    });
  };

  // ------------------------------------------------------------------------
  // INITIALIZATION
  // ------------------------------------------------------------------------

  module.init = function (done) {
    $this.on('click', '.delete', _handleDeleteAction);
    done();
  };

  return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRyYWNraW5nL2RlbGV0ZV9wYXJjZWxfc2VydmljZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiJHRoaXMiLCIkIiwiJHBhcmNlbFNlcnZpY2VMaXN0IiwiZGF0YXNldCIsImRhdGEiLCJfaGFuZGxlRGVsZXRlQWN0aW9uIiwiJGJ1dHRvbiIsImV2ZW50IiwidGFyZ2V0IiwiYWN0aXZlQnV0dG9uQ2xhc3MiLCJ1cmwiLCJsaWdodGJveFBhcmFtcyIsInBhcmNlbF9zZXJ2aWNlX2lkIiwicGFnZV90b2tlbiIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiaGFzQ2xhc3MiLCJhZGRDbGFzcyIsInJlcXVlc3QiLCJqc2UiLCJsaWJzIiwieGhyIiwicG9zdCIsImRvbmUiLCJodG1sIiwicmVzcG9uc2UiLCJsaWdodGJveF9wbHVnaW4iLCJpZGVudGlmaWVyIiwiZmFpbCIsImpxWEhSIiwiZXhjZXB0aW9uIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsdUJBQXRCLEVBQStDLENBQUMsS0FBRCxDQUEvQyxFQUF3RCxZQUFZOztBQUVsRTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLE1BQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLE1BQU1DLHFCQUFxQkQsRUFBRSwwQkFBRixDQUEzQjs7QUFFQTs7Ozs7QUFLQSxNQUFNRSxVQUFVSCxNQUFNSSxJQUFOLEVBQWhCOztBQUVBOzs7OztBQUtBLE1BQU1MLFNBQVMsRUFBZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7QUFJQSxNQUFNTSxzQkFBc0IsU0FBdEJBLG1CQUFzQixRQUFTO0FBQ25DO0FBQ0EsUUFBTUMsVUFBVUwsRUFBRU0sTUFBTUMsTUFBUixDQUFoQjs7QUFFQTtBQUNBLFFBQU1DLG9CQUFvQixRQUExQjs7QUFFQTtBQUNBLFFBQU1DLE1BQU0scUVBQVo7O0FBRUE7QUFDQSxRQUFNTixPQUFPO0FBQ1gsMkJBQXFCRCxRQUFRUSxjQUFSLENBQXVCQyxpQkFEakM7QUFFWCxvQkFBY1QsUUFBUVEsY0FBUixDQUF1QkU7QUFGMUIsS0FBYjs7QUFLQTtBQUNBTixVQUFNTyxjQUFOO0FBQ0FQLFVBQU1RLGVBQU47O0FBRUE7QUFDQSxRQUFJVCxRQUFRVSxRQUFSLENBQWlCUCxpQkFBakIsQ0FBSixFQUF5QztBQUN2QyxhQUFPLEtBQVA7QUFDRDs7QUFFRDtBQUNBSCxZQUFRVyxRQUFSLENBQWlCUixpQkFBakI7O0FBRUE7QUFDQSxRQUFNUyxVQUFVQyxJQUFJQyxJQUFKLENBQVNDLEdBQVQsQ0FBYUMsSUFBYixDQUFrQixFQUFFWixRQUFGLEVBQU9OLFVBQVAsRUFBbEIsQ0FBaEI7O0FBRUE7QUFDQWMsWUFBUUssSUFBUixDQUFhLG9CQUFZO0FBQ3ZCckIseUJBQW1Cc0IsSUFBbkIsQ0FBd0JDLFNBQVNELElBQWpDO0FBQ0F2QixRQUFFeUIsZUFBRixDQUFrQixPQUFsQixFQUEyQnZCLFFBQVFRLGNBQVIsQ0FBdUJnQixVQUFsRDtBQUNELEtBSEQ7O0FBS0E7QUFDQVQsWUFBUVUsSUFBUixDQUFhLFVBQUNDLEtBQUQsRUFBUUMsU0FBUjtBQUFBLGFBQXNCN0IsRUFBRXlCLGVBQUYsQ0FBa0IsT0FBbEIsRUFBMkJ2QixRQUFRUSxjQUFSLENBQXVCZ0IsVUFBbEQsRUFBOERFLEtBQTlELEVBQXFFQyxTQUFyRSxDQUF0QjtBQUFBLEtBQWI7QUFFRCxHQXhDRDs7QUEwQ0E7QUFDQTtBQUNBOztBQUVBL0IsU0FBT2dDLElBQVAsR0FBYyxnQkFBUTtBQUNwQi9CLFVBQU1nQyxFQUFOLENBQVMsT0FBVCxFQUFrQixTQUFsQixFQUE2QjNCLG1CQUE3QjtBQUNBa0I7QUFDRCxHQUhEOztBQUtBLFNBQU94QixNQUFQO0FBQ0QsQ0FoR0QiLCJmaWxlIjoidHJhY2tpbmcvZGVsZXRlX3BhcmNlbF9zZXJ2aWNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkZWxldGVfcGFyY2VsX3NlcnZpY2UuanMgMjAxNi0wNy0yN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRGVsZXRlIFBhcmNlbCBTZXJ2aWNlIENvbnRyb2xsZXJcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKCdkZWxldGVfcGFyY2VsX3NlcnZpY2UnLCBbJ3hociddLCBmdW5jdGlvbiAoKSB7XG5cbiAgJ3VzZSBzdHJpY3QnO1xuXG4gIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAvLyBWQVJJQUJMRVNcbiAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgLyoqXG4gICAqIE1vZHVsZSBTZWxlY3RvclxuICAgKlxuICAgKiBAdHlwZSB7alF1ZXJ5fVxuICAgKi9cbiAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuXG4gIC8qKlxuICAgKiBQYXJjZWwgc2VydmljZSBsaXN0IGVsZW1lbnQuXG4gICAqIFxuICAgKiBAdHlwZSB7alF1ZXJ5fVxuICAgKi9cbiAgY29uc3QgJHBhcmNlbFNlcnZpY2VMaXN0ID0gJCgnI3BhcmNlbF9zZXJ2aWNlc193cmFwcGVyJyk7XG5cbiAgLyoqXG4gICAqIE1vZHVsZSBkYXRhIHNldC5cbiAgICpcbiAgICogQHR5cGUge09iamVjdH1cbiAgICovXG4gIGNvbnN0IGRhdGFzZXQgPSAkdGhpcy5kYXRhKCk7XG5cbiAgLyoqXG4gICAqIE1vZHVsZSBJbnN0YW5jZVxuICAgKlxuICAgKiBAdHlwZSB7T2JqZWN0fVxuICAgKi9cbiAgY29uc3QgbW9kdWxlID0ge307XG5cbiAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gIC8vIEVWRU5UIEhBTkRMRVJTXG4gIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHRoZSBidXR0b24gY2xpY2sgZXZlbnQgaW4gcGFyY2VsIHNlcnZpY2VzIHJlbW92YWwgY29uZmlybWF0aW9uIG1vZGFsLlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudCBUcmlnZ2VyZWQgZXZlbnQuXG4gICAqL1xuICBjb25zdCBfaGFuZGxlRGVsZXRlQWN0aW9uID0gZXZlbnQgPT4ge1xuICAgIC8vIENsaWNrZWQgYnV0dG9uIGVsZW1lbnQuICAgICAgXG4gICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQudGFyZ2V0KTtcblxuICAgIC8vIENTUyBjbGFzcyB0byBpbmRpY2F0ZSBhbHJlYWR5IGNsaWNrZWQgYnV0dG9uLlxuICAgIGNvbnN0IGFjdGl2ZUJ1dHRvbkNsYXNzID0gJ2FjdGl2ZSc7XG5cbiAgICAvLyBBSkFYIHJlcXVlc3QgVVJMLlxuICAgIGNvbnN0IHVybCA9ICdyZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1QYXJjZWxTZXJ2aWNlcyZhY3Rpb249ZGVsZXRlX3BhcmNlbF9zZXJ2aWNlJztcblxuICAgIC8vIEFKQVggcmVxdWVzdCBQT1NUIGRhdGEuXG4gICAgY29uc3QgZGF0YSA9IHtcbiAgICAgICdwYXJjZWxfc2VydmljZV9pZCc6IGRhdGFzZXQubGlnaHRib3hQYXJhbXMucGFyY2VsX3NlcnZpY2VfaWQsXG4gICAgICAncGFnZV90b2tlbic6IGRhdGFzZXQubGlnaHRib3hQYXJhbXMucGFnZV90b2tlblxuICAgIH07XG5cbiAgICAvLyBQcmV2ZW50IGRlZmF1bHQgYmVoYXZpb3IgYW5kIHByZXZlbnQgZXZlbnQgYnViYmxpbmcuXG4gICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgIC8vIEV4aXQgaW1tZWRpYXRlbHkgaWYgYnV0dG9uIGhhcyBiZWVuIGFscmVhZHkgY2xpY2tlZC5cbiAgICBpZiAoJGJ1dHRvbi5oYXNDbGFzcyhhY3RpdmVCdXR0b25DbGFzcykpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICAvLyBNYXJrIGJ1dHRvbiBhcyBjbGlja2VkIHRvIHByZXZlbnQgbXVsdGlwbGUgY2xpY2tzLiBcbiAgICAkYnV0dG9uLmFkZENsYXNzKGFjdGl2ZUJ1dHRvbkNsYXNzKTtcblxuICAgIC8vIFBlcmZvcm0gQUpBWCBQT1NUIHJlcXVlc3QuXG4gICAgY29uc3QgcmVxdWVzdCA9IGpzZS5saWJzLnhoci5wb3N0KHsgdXJsLCBkYXRhIH0pO1xuXG4gICAgLy8gQUpBWCByZXF1ZXN0IHN1Y2Nlc3MgaGFuZGxlci5cbiAgICByZXF1ZXN0LmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgJHBhcmNlbFNlcnZpY2VMaXN0Lmh0bWwocmVzcG9uc2UuaHRtbCk7XG4gICAgICAkLmxpZ2h0Ym94X3BsdWdpbignY2xvc2UnLCBkYXRhc2V0LmxpZ2h0Ym94UGFyYW1zLmlkZW50aWZpZXIpO1xuICAgIH0pO1xuXG4gICAgLy8gQUpBWCByZXF1ZXN0IGVycm9yIGhhbmRsZXIuXG4gICAgcmVxdWVzdC5mYWlsKChqcVhIUiwgZXhjZXB0aW9uKSA9PiAkLmxpZ2h0Ym94X3BsdWdpbignZXJyb3InLCBkYXRhc2V0LmxpZ2h0Ym94UGFyYW1zLmlkZW50aWZpZXIsIGpxWEhSLCBleGNlcHRpb24pKTtcblxuICB9O1xuXG4gIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAvLyBJTklUSUFMSVpBVElPTlxuICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICBtb2R1bGUuaW5pdCA9IGRvbmUgPT4ge1xuICAgICR0aGlzLm9uKCdjbGljaycsICcuZGVsZXRlJywgX2hhbmRsZURlbGV0ZUFjdGlvbik7XG4gICAgZG9uZSgpO1xuICB9O1xuXG4gIHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==
