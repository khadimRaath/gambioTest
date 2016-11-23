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
  const $this = $(this);

  /**
   * Parcel service list element.
   * 
   * @type {jQuery}
   */
  const $parcelServiceList = $('#parcel_services_wrapper');

  /**
   * Module data set.
   *
   * @type {Object}
   */
  const dataset = $this.data();

  /**
   * Module Instance
   *
   * @type {Object}
   */
  const module = {};

  // ------------------------------------------------------------------------
  // EVENT HANDLERS
  // ------------------------------------------------------------------------

  /**
   * Handles the button click event in parcel services removal confirmation modal.
   * @param {Event} event Triggered event.
   */
  const _handleDeleteAction = event => {
    // Clicked button element.      
    const $button = $(event.target);

    // CSS class to indicate already clicked button.
    const activeButtonClass = 'active';

    // AJAX request URL.
    const url = 'request_port.php?module=ParcelServices&action=delete_parcel_service';

    // AJAX request POST data.
    const data = {
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
    const request = jse.libs.xhr.post({ url, data });

    // AJAX request success handler.
    request.done(response => {
      $parcelServiceList.html(response.html);
      $.lightbox_plugin('close', dataset.lightboxParams.identifier);
    });

    // AJAX request error handler.
    request.fail((jqXHR, exception) => $.lightbox_plugin('error', dataset.lightboxParams.identifier, jqXHR, exception));

  };

  // ------------------------------------------------------------------------
  // INITIALIZATION
  // ------------------------------------------------------------------------

  module.init = done => {
    $this.on('click', '.delete', _handleDeleteAction);
    done();
  };

  return module;
});
