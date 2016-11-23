'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 settings.js 2016-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Handles the settings modal.
 *
 * It retrieves the settings data via the user configuration service and sets the values.
 * You are able to change the column sort order and the visibility of each column. Additionally
 * you can change the height of the table rows.
 */
gx.controllers.module('settings', ['user_configuration_service', 'loading_spinner'], function (data) {

	'use strict';

	// --------------------------------------------------------------------
	// VARIABLES
	// --------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// --------------------------------------------------------------------
	// FUNCTIONS
	// --------------------------------------------------------------------

	/**
  * Class representing a controller for the orders overview settings modal.
  */

	var SettingsModalController = function () {
		/**
   * Creates an instance of OrdersOverviewSettingsModalController.
   *
   * @param {Function}  done            Module finish callback function.
   * @param {jQuery}    $element        Module element.
   * @param {Object}    userCfgService  User configuration service library.
   * @param {Object}    loadingSpinner  Loading spinner library.
   * @param {Number}    userId          ID of currently signed in user.
   * @param {Object}    translator      Translator library.
   */
		function SettingsModalController(done, $element, userCfgService, loadingSpinner, userId, translator) {
			_classCallCheck(this, SettingsModalController);

			// Elements
			this.$element = $element;
			this.$submitButton = $element.find('button.submit-button');
			this.$settings = $element.find('ul.settings');
			this.$modal = $element.parents('.modal');
			this.$modalFooter = $element.find('.modal-footer');
			this.$resetDefaultLink = $element.find('a.reset-action');

			// Loading spinner
			this.$spinner = null;

			// Selector strings
			this.sortableHandleSelector = 'span.sort-handle';
			this.rowHeightValueSelector = 'select#setting-value-row-height';

			// Class names
			this.errorMessageClassName = 'error-message';
			this.loadingClassName = 'loading';

			// Libraries
			this.userCfgService = userCfgService;
			this.loadingSpinner = loadingSpinner;
			this.translator = translator;

			// Prefixes
			this.settingListItemIdPrefix = 'setting-';
			this.settingValueIdPrefix = 'setting-value-';

			// User configuration keys
			this.CONFIG_KEY_COLUMN_SETTINGS = 'ordersOverviewSettingsColumns';
			this.CONFIG_KEY_ROW_HEIGHT_SETTINGS = 'ordersOverviewSettingsRowHeight';

			// Default values
			this.DEFAULT_ROW_HEIGHT_SETTING = 'large';
			this.DEFAULT_COLUMN_SETTINGS = ['number', 'customer', 'group', 'sum', 'paymentMethod', 'shippingMethod', 'countryIsoCode', 'date', 'status', 'totalWeight'];

			// ID of currently signed in user.
			this.userId = userId;

			// Call module finish callback.
			done();
		}

		/**
   * Binds the event handlers.
   *
   * @return {SettingsModalController} Same instance for method chaining.
   */


		_createClass(SettingsModalController, [{
			key: 'initialize',
			value: function initialize() {
				var _this = this;

				// Attach event handler for click action on the submit button.
				this.$submitButton.on('click', function (event) {
					return _this._onSubmitButtonClick(event);
				});

				// Attach event handler for click action on the reset-default link.
				this.$resetDefaultLink.on('click', function (event) {
					return _this._onResetSettingsLinkClick(event);
				});

				// Attach event handlers to modal.
				this.$modal.on('show.bs.modal', function (event) {
					return _this._onModalShow(event);
				}).on('shown.bs.modal', function (event) {
					return _this._onModalShown(event);
				});

				return this;
			}

			/**
    * Fades out the modal content.
    *
    * @param {jQuery.Event} event Fired event.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onModalShow',
			value: function _onModalShow() {
				this.$element.addClass(this.loadingClassName);

				return this;
			}

			/**
    * Updates the settings, clears any error messages and initializes the sortable plugin.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onModalShown',
			value: function _onModalShown() {
				this._refreshSettings()._clearErrorMessage()._initSortable();

				return this;
			}

			/**
    * Activates the jQuery UI Sortable plugin on the setting list items element.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_initSortable',
			value: function _initSortable() {
				// jQuery UI Sortable plugin options.
				var options = {
					items: '> li',
					axis: 'y',
					cursor: 'move',
					handle: this.sortableHandleSelector,
					containment: 'parent'
				};

				// Activate sortable plugin.
				this.$settings.sortable(options).disableSelection();

				return this;
			}

			/**
    * Returns a sorted array containing the IDs of all activated settings.
    *
    * @return {Array}
    *
    * @private
    */

		}, {
			key: '_serializeColumnSettings',
			value: function _serializeColumnSettings() {
				var _this2 = this;

				// Map iterator function to remove the 'setting-' prefix from list item ID.
				var removePrefixIterator = function removePrefixIterator(item) {
					return item.replace(_this2.settingListItemIdPrefix, '');
				};

				// Filter iterator function, to accept only list items with activated checkboxes.
				var filterIterator = function filterIterator(item) {
					return _this2.$settings.find('#' + _this2.settingValueIdPrefix + item).is(':checked');
				};

				// Return array with sorted, only active columns.
				return this.$settings.sortable('toArray').map(removePrefixIterator).filter(filterIterator);
			}

			/**
    * Returns the value of the selected row height option.
    *
    * @return {String}
    *
    * @private
    */

		}, {
			key: '_serializeRowHeightSetting',
			value: function _serializeRowHeightSetting() {
				return this.$element.find(this.rowHeightValueSelector).val();
			}

			/**
    * Shows the loading spinner, saves the settings to the user configuration,
    * closes the modal to finally re-render the datatable.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onSubmitButtonClick',
			value: function _onSubmitButtonClick() {
				var _this3 = this;

				// Retrieve setting values.
				var columnSettings = this._serializeColumnSettings();
				var rowHeightSetting = this._serializeRowHeightSetting();

				// Remove any error message and save settings.
				this._toggleLoadingSpinner(true)._clearErrorMessage()._saveColumnSettings(columnSettings).then(function () {
					return _this3._saveRowHeightSetting(rowHeightSetting);
				}).then(function () {
					return _this3._onSaveSuccess();
				}).catch(function () {
					return _this3._onSaveError();
				});

				return this;
			}

			/**
    * Prevents the browser to apply the default behavoir and
    * resets the column order and row size to the default setting values.
    *
    * @param {jQuery.Event} event Fired event.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onResetSettingsLinkClick',
			value: function _onResetSettingsLinkClick(event) {
				// Prevent default behavior.
				event.preventDefault();
				event.stopPropagation();

				// Reset to default settings.
				this._setDefaultSettings();

				return this;
			}

			/**
    * Shows and hides the loading spinner.
    *
    * @param {Boolean} doShow Show the loading spinner?
    *
    * @return {SettingsModalController} Same instance for method chaining.
    */

		}, {
			key: '_toggleLoadingSpinner',
			value: function _toggleLoadingSpinner(doShow) {
				if (doShow) {
					// Fade out modal content.
					this.$element.addClass(this.loadingClassName);

					// Show loading spinner.
					this.$spinner = this.loadingSpinner.show(this.$element);

					// Fix spinner z-index.
					this.$spinner.css({ 'z-index': 9999 });
				} else {
					// Fade out modal content.
					this.$element.removeClass(this.loadingClassName);

					// Hide the loading spinner.
					this.loadingSpinner.hide(this.$spinner);
				}

				return this;
			}

			/**
    * Handles the behavior on successful setting save action.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onSaveSuccess',
			value: function _onSaveSuccess() {
				window.location.reload();
				return this;
			}

			/**
    * Removes any error message, if found.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_clearErrorMessage',
			value: function _clearErrorMessage() {
				// Error message.
				var $errorMessage = this.$modalFooter.find('.' + this.errorMessageClassName);

				// Remove if it exists.
				if ($errorMessage.length) {
					$errorMessage.remove();
				}

				return this;
			}

			/**
    * Handles the behavior on thrown error while saving settings.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onSaveError',
			value: function _onSaveError() {
				// Error message.
				var errorMessage = this.translator.translate('TXT_SAVE_ERROR', 'admin_general');

				// Define error message element.
				var $error = $('<span/>', { class: this.errorMessageClassName, text: errorMessage });

				// Hide the loading spinner.
				this._toggleLoadingSpinner(false);

				// Add error message to modal footer.
				this.$modalFooter.prepend($error).hide().fadeIn();

				return this;
			}

			/**
    * Returns the configuration value for the column settings.
    *
    * @return {Promise}
    *
    * @private
    */

		}, {
			key: '_getColumnSettings',
			value: function _getColumnSettings() {
				// Configuration data.
				var data = {
					userId: this.userId,
					configurationKey: this.CONFIG_KEY_COLUMN_SETTINGS
				};

				// Request data from user configuration service.
				return this._getFromUserCfgService(data);
			}

			/**
    * Returns the configuration value for the row heights.
    *
    * @return {Promise}
    *
    * @private
    */

		}, {
			key: '_getRowHeightSetting',
			value: function _getRowHeightSetting() {
				// Configuration data.
				var data = {
					userId: this.userId,
					configurationKey: this.CONFIG_KEY_ROW_HEIGHT_SETTINGS
				};

				// Request data from user configuration service.
				return this._getFromUserCfgService(data);
			}

			/**
    * Returns the value for the passed user configuration data.
    *
    * @param {Object} data                   User configuration data.
    * @param {Number} data.userId            User ID.
    * @param {String} data.configurationKey  User configuration key.
    *
    * @return {Promise}
    *
    * @private
    */

		}, {
			key: '_getFromUserCfgService',
			value: function _getFromUserCfgService(data) {
				var _this4 = this;

				// Promise handler.
				var handler = function handler(resolve, reject) {
					// User configuration service request options.
					var options = {
						onError: function onError() {
							return reject();
						},
						onSuccess: function onSuccess(response) {
							return resolve(response.configurationValue);
						},
						data: data
					};

					// Get configuration value.
					_this4.userCfgService.get(options);
				};

				return new Promise(handler);
			}

			/**
    * Saves the data via the user configuration service.
    *
    * @param {Object} data                     User configuration data.
    * @param {Number} data.userId              User ID.
    * @param {String} data.configurationKey    User configuration key.
    * @param {String} data.configurationValue  User configuration value.
    *
    * @return {Promise}
    *
    * @private
    */

		}, {
			key: '_setWithUserCfgService',
			value: function _setWithUserCfgService(data) {
				var _this5 = this;

				// Promise handler.
				var handler = function handler(resolve, reject) {
					// User configuration service request options.
					var options = {
						onError: function onError() {
							return reject();
						},
						onSuccess: function onSuccess(response) {
							return resolve();
						},
						data: data
					};

					// Set configuration value.
					_this5.userCfgService.set(options);
				};

				return new Promise(handler);
			}

			/**
    * Saves the column settings via the user configuration service.
    *
    * @param {String[]} columnSettings Sorted array with active column.
    *
    * @return {Promise}
    *
    * @private
    */

		}, {
			key: '_saveColumnSettings',
			value: function _saveColumnSettings(columnSettings) {
				// Check argument.
				if (!columnSettings || !Array.isArray(columnSettings)) {
					throw new Error('Missing or invalid column settings');
				}

				// User configuration request data.
				var data = {
					userId: this.userId,
					configurationKey: this.CONFIG_KEY_COLUMN_SETTINGS,
					configurationValue: JSON.stringify(columnSettings)
				};

				// Save via user configuration service.
				return this._setWithUserCfgService(data);
			}

			/**
    * Saves the row height setting via the user configuration service.
    *
    * @param {String} rowHeightSetting Value of the selected row height setting.
    *
    * @return {Promise}
    *
    * @private
    */

		}, {
			key: '_saveRowHeightSetting',
			value: function _saveRowHeightSetting(rowHeightSetting) {
				// Check argument.
				if (!rowHeightSetting || typeof rowHeightSetting !== 'string') {
					throw new Error('Missing or invalid row height setting');
				}

				// User configuration request data.
				var data = {
					userId: this.userId,
					configurationKey: this.CONFIG_KEY_ROW_HEIGHT_SETTINGS,
					configurationValue: rowHeightSetting
				};

				// Save via user configuration service.
				return this._setWithUserCfgService(data);
			}

			/**
    * Retrieves the saved setting configuration and reorders/updates the settings.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_refreshSettings',
			value: function _refreshSettings() {
				var _this6 = this;

				// Show loading spinner.
				this._toggleLoadingSpinner(true);

				// Error handler function to specify the behavior on errors while processing.
				var onRefreshSettingsError = function onRefreshSettingsError(error) {
					// Output warning.
					console.warn('Error while refreshing', error);

					// Hide the loading spinner.
					_this6._toggleLoadingSpinner(false);
				};

				// Remove any error message, set row height,
				// reorder and update the settings and hide the loading spinner.
				this._clearErrorMessage()._getRowHeightSetting().then(function (rowHeightValue) {
					return _this6._setRowHeight(rowHeightValue);
				}).then(function () {
					return _this6._getColumnSettings();
				}).then(function (columnSettings) {
					return _this6._setColumnSettings(columnSettings);
				}).then(function () {
					return _this6._toggleLoadingSpinner(false);
				}).catch(onRefreshSettingsError);

				return this;
			}

			/**
    * Sets the row height setting value.
    *
    * @param {String} value Row height value.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_setRowHeight',
			value: function _setRowHeight() {
				var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.DEFAULT_ROW_HEIGHT_SETTING;

				this.$element.find(this.rowHeightValueSelector).val(value);

				return this;
			}

			/**
    * Reorders and updates the column setting values.
    *
    * @param {String|Array} columnSettings Stringified JSON array containing the saved column settings.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_setColumnSettings',
			value: function _setColumnSettings() {
				var _this7 = this;

				var columnSettings = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.DEFAULT_COLUMN_SETTINGS;

				// Regex for escape character.
				var ESCAPE_CHAR = /\\/g;

				// No need to parse from JSON on default value as it is an array.
				if (!Array.isArray(columnSettings)) {
					// Remove escape characters from and parse array from JSON.
					columnSettings = columnSettings.replace(ESCAPE_CHAR, '');
					columnSettings = JSON.parse(columnSettings);
				}

				// Cache container to temporarily hold all active list items in sorted order.
				// The children of this element will be prepended to the setting list item container to retain the 
				// sorting order.
				var $sortedItems = $('<div/>');

				// Iterator function to prepend active list items to the top and activate the checkbox.
				var settingIterator = function settingIterator(setting) {
					// List item ID.
					var id = _this7.settingListItemIdPrefix + setting;

					// Affected setting list item.
					var $listItem = _this7.$settings.find('#' + id);

					// Checkbox of affected list item.
					var $checkbox = $listItem.find('#' + _this7.settingValueIdPrefix + setting);

					// Activate checkbox.
					if (!$checkbox.is(':checked')) {
						$checkbox.parent().trigger('click');
					}

					// Move to cache container.
					$listItem.appendTo($sortedItems);
				};

				// Move active list items to the top bearing the sorting order in mind.
				columnSettings.forEach(settingIterator);

				// Prepend cached elements to item list.
				$sortedItems.children().prependTo(this.$settings);

				return this;
			}

			/**
    * Resets the column order and row height settings to the default.
    *
    * @return {SettingsModalController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_setDefaultSettings',
			value: function _setDefaultSettings() {
				var _this8 = this;

				// Default values.
				var columnSettings = this.DEFAULT_COLUMN_SETTINGS;
				var rowHeight = this.DEFAULT_ROW_HEIGHT_SETTING;

				// Set column settings.
				// Cache container to temporarily hold all active list items in sorted order.
				// The children of this element will be prepended to the setting list item container to retain the 
				// sorting order.
				var $sortedItems = $('<div/>');

				// Iterator function to prepend active list items to the top and activate the checkbox.
				var settingIterator = function settingIterator(setting) {
					// List item ID.
					var id = _this8.settingListItemIdPrefix + setting;

					// Affected setting list item.
					var $listItem = _this8.$settings.find('#' + id);

					// Checkbox of affected list item.
					var $checkbox = $listItem.find('#' + _this8.settingValueIdPrefix + setting);

					// Activate checkbox.
					if (!$checkbox.is(':checked')) {
						$checkbox.parent().trigger('click');
					}

					// Move to cache container.
					$listItem.appendTo($sortedItems);
				};

				// Deactivate all checkboxes.
				this.$settings.find(':checkbox').each(function (index, element) {
					var $checkbox = $(element);

					if ($checkbox.is(':checked')) {
						$checkbox.parent().trigger('click');
					}
				});

				// Move active list items to the top bearing the sorting order in mind.
				columnSettings.forEach(settingIterator);

				// Prepend cached elements to item list.
				$sortedItems.children().prependTo(this.$settings);

				// Set row height.
				this.$element.find(this.rowHeightValueSelector).val(rowHeight);

				return this;
			}
		}]);

		return SettingsModalController;
	}();

	// --------------------------------------------------------------------
	// INITIALIZATION
	// --------------------------------------------------------------------

	module.init = function (done) {
		// Dependencies.
		var userCfgService = jse.libs.user_configuration_service;
		var loadingSpinner = jse.libs.loading_spinner;
		var userId = data.userId;
		var translator = jse.core.lang;

		// Create a new instance and load settings.
		var settingsModal = new SettingsModalController(done, $this, userCfgService, loadingSpinner, userId, translator);

		settingsModal.initialize();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvc2V0dGluZ3MuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJTZXR0aW5nc01vZGFsQ29udHJvbGxlciIsImRvbmUiLCIkZWxlbWVudCIsInVzZXJDZmdTZXJ2aWNlIiwibG9hZGluZ1NwaW5uZXIiLCJ1c2VySWQiLCJ0cmFuc2xhdG9yIiwiJHN1Ym1pdEJ1dHRvbiIsImZpbmQiLCIkc2V0dGluZ3MiLCIkbW9kYWwiLCJwYXJlbnRzIiwiJG1vZGFsRm9vdGVyIiwiJHJlc2V0RGVmYXVsdExpbmsiLCIkc3Bpbm5lciIsInNvcnRhYmxlSGFuZGxlU2VsZWN0b3IiLCJyb3dIZWlnaHRWYWx1ZVNlbGVjdG9yIiwiZXJyb3JNZXNzYWdlQ2xhc3NOYW1lIiwibG9hZGluZ0NsYXNzTmFtZSIsInNldHRpbmdMaXN0SXRlbUlkUHJlZml4Iiwic2V0dGluZ1ZhbHVlSWRQcmVmaXgiLCJDT05GSUdfS0VZX0NPTFVNTl9TRVRUSU5HUyIsIkNPTkZJR19LRVlfUk9XX0hFSUdIVF9TRVRUSU5HUyIsIkRFRkFVTFRfUk9XX0hFSUdIVF9TRVRUSU5HIiwiREVGQVVMVF9DT0xVTU5fU0VUVElOR1MiLCJvbiIsIl9vblN1Ym1pdEJ1dHRvbkNsaWNrIiwiZXZlbnQiLCJfb25SZXNldFNldHRpbmdzTGlua0NsaWNrIiwiX29uTW9kYWxTaG93IiwiX29uTW9kYWxTaG93biIsImFkZENsYXNzIiwiX3JlZnJlc2hTZXR0aW5ncyIsIl9jbGVhckVycm9yTWVzc2FnZSIsIl9pbml0U29ydGFibGUiLCJvcHRpb25zIiwiaXRlbXMiLCJheGlzIiwiY3Vyc29yIiwiaGFuZGxlIiwiY29udGFpbm1lbnQiLCJzb3J0YWJsZSIsImRpc2FibGVTZWxlY3Rpb24iLCJyZW1vdmVQcmVmaXhJdGVyYXRvciIsIml0ZW0iLCJyZXBsYWNlIiwiZmlsdGVySXRlcmF0b3IiLCJpcyIsIm1hcCIsImZpbHRlciIsInZhbCIsImNvbHVtblNldHRpbmdzIiwiX3NlcmlhbGl6ZUNvbHVtblNldHRpbmdzIiwicm93SGVpZ2h0U2V0dGluZyIsIl9zZXJpYWxpemVSb3dIZWlnaHRTZXR0aW5nIiwiX3RvZ2dsZUxvYWRpbmdTcGlubmVyIiwiX3NhdmVDb2x1bW5TZXR0aW5ncyIsInRoZW4iLCJfc2F2ZVJvd0hlaWdodFNldHRpbmciLCJfb25TYXZlU3VjY2VzcyIsImNhdGNoIiwiX29uU2F2ZUVycm9yIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJfc2V0RGVmYXVsdFNldHRpbmdzIiwiZG9TaG93Iiwic2hvdyIsImNzcyIsInJlbW92ZUNsYXNzIiwiaGlkZSIsIndpbmRvdyIsImxvY2F0aW9uIiwicmVsb2FkIiwiJGVycm9yTWVzc2FnZSIsImxlbmd0aCIsInJlbW92ZSIsImVycm9yTWVzc2FnZSIsInRyYW5zbGF0ZSIsIiRlcnJvciIsImNsYXNzIiwidGV4dCIsInByZXBlbmQiLCJmYWRlSW4iLCJjb25maWd1cmF0aW9uS2V5IiwiX2dldEZyb21Vc2VyQ2ZnU2VydmljZSIsImhhbmRsZXIiLCJyZXNvbHZlIiwicmVqZWN0Iiwib25FcnJvciIsIm9uU3VjY2VzcyIsInJlc3BvbnNlIiwiY29uZmlndXJhdGlvblZhbHVlIiwiZ2V0IiwiUHJvbWlzZSIsInNldCIsIkFycmF5IiwiaXNBcnJheSIsIkVycm9yIiwiSlNPTiIsInN0cmluZ2lmeSIsIl9zZXRXaXRoVXNlckNmZ1NlcnZpY2UiLCJvblJlZnJlc2hTZXR0aW5nc0Vycm9yIiwiY29uc29sZSIsIndhcm4iLCJlcnJvciIsIl9nZXRSb3dIZWlnaHRTZXR0aW5nIiwiX3NldFJvd0hlaWdodCIsInJvd0hlaWdodFZhbHVlIiwiX2dldENvbHVtblNldHRpbmdzIiwiX3NldENvbHVtblNldHRpbmdzIiwidmFsdWUiLCJFU0NBUEVfQ0hBUiIsInBhcnNlIiwiJHNvcnRlZEl0ZW1zIiwic2V0dGluZ0l0ZXJhdG9yIiwiaWQiLCJzZXR0aW5nIiwiJGxpc3RJdGVtIiwiJGNoZWNrYm94IiwicGFyZW50IiwidHJpZ2dlciIsImFwcGVuZFRvIiwiZm9yRWFjaCIsImNoaWxkcmVuIiwicHJlcGVuZFRvIiwicm93SGVpZ2h0IiwiZWFjaCIsImluZGV4IiwiZWxlbWVudCIsImluaXQiLCJqc2UiLCJsaWJzIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCJsb2FkaW5nX3NwaW5uZXIiLCJjb3JlIiwibGFuZyIsInNldHRpbmdzTW9kYWwiLCJpbml0aWFsaXplIl0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLFVBREQsRUFHQyxDQUNDLDRCQURELEVBRUMsaUJBRkQsQ0FIRCxFQVFDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFHQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7QUEzQmMsS0E4QlJJLHVCQTlCUTtBQStCYjs7Ozs7Ozs7OztBQVVBLG1DQUFZQyxJQUFaLEVBQWtCQyxRQUFsQixFQUE0QkMsY0FBNUIsRUFBNENDLGNBQTVDLEVBQTREQyxNQUE1RCxFQUFvRUMsVUFBcEUsRUFBZ0Y7QUFBQTs7QUFDL0U7QUFDQSxRQUFLSixRQUFMLEdBQWdCQSxRQUFoQjtBQUNBLFFBQUtLLGFBQUwsR0FBcUJMLFNBQVNNLElBQVQsQ0FBYyxzQkFBZCxDQUFyQjtBQUNBLFFBQUtDLFNBQUwsR0FBaUJQLFNBQVNNLElBQVQsQ0FBYyxhQUFkLENBQWpCO0FBQ0EsUUFBS0UsTUFBTCxHQUFjUixTQUFTUyxPQUFULENBQWlCLFFBQWpCLENBQWQ7QUFDQSxRQUFLQyxZQUFMLEdBQW9CVixTQUFTTSxJQUFULENBQWMsZUFBZCxDQUFwQjtBQUNBLFFBQUtLLGlCQUFMLEdBQXlCWCxTQUFTTSxJQUFULENBQWMsZ0JBQWQsQ0FBekI7O0FBRUE7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QUFsQyxFQUF5Q0ssY0FBekMsRUFBeURDLGNBQXpELEVBQ3JCQyxNQURxQixFQUNiQyxVQURhLENBQXRCOztBQUdBa0ksZ0JBQWNDLFVBQWQ7QUFDQSxFQVpEOztBQWNBLFFBQU83SSxNQUFQO0FBQ0EsQ0F2cUJGIiwiZmlsZSI6Im9yZGVycy9tb2RhbHMvc2V0dGluZ3MuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHNldHRpbmdzLmpzIDIwMTYtMDYtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEhhbmRsZXMgdGhlIHNldHRpbmdzIG1vZGFsLlxuICpcbiAqIEl0IHJldHJpZXZlcyB0aGUgc2V0dGluZ3MgZGF0YSB2aWEgdGhlIHVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlIGFuZCBzZXRzIHRoZSB2YWx1ZXMuXG4gKiBZb3UgYXJlIGFibGUgdG8gY2hhbmdlIHRoZSBjb2x1bW4gc29ydCBvcmRlciBhbmQgdGhlIHZpc2liaWxpdHkgb2YgZWFjaCBjb2x1bW4uIEFkZGl0aW9uYWxseVxuICogeW91IGNhbiBjaGFuZ2UgdGhlIGhlaWdodCBvZiB0aGUgdGFibGUgcm93cy5cbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnc2V0dGluZ3MnLFxuXHRcblx0W1xuXHRcdCd1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZScsXG5cdFx0J2xvYWRpbmdfc3Bpbm5lcidcblx0XSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdCAqXG5cdFx0ICogQHR5cGUge2pRdWVyeX1cblx0XHQgKi9cblx0XHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XG5cblx0XHQvKipcblx0XHQgKiBNb2R1bGUgSW5zdGFuY2Vcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XG5cdFx0ICovXG5cdFx0Y29uc3QgbW9kdWxlID0ge307XG5cdFx0XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENsYXNzIHJlcHJlc2VudGluZyBhIGNvbnRyb2xsZXIgZm9yIHRoZSBvcmRlcnMgb3ZlcnZpZXcgc2V0dGluZ3MgbW9kYWwuXG5cdFx0ICovXG5cdFx0Y2xhc3MgU2V0dGluZ3NNb2RhbENvbnRyb2xsZXIge1xuXHRcdFx0LyoqXG5cdFx0XHQgKiBDcmVhdGVzIGFuIGluc3RhbmNlIG9mIE9yZGVyc092ZXJ2aWV3U2V0dGluZ3NNb2RhbENvbnRyb2xsZXIuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtGdW5jdGlvbn0gIGRvbmUgICAgICAgICAgICBNb2R1bGUgZmluaXNoIGNhbGxiYWNrIGZ1bmN0aW9uLlxuXHRcdFx0ICogQHBhcmFtIHtqUXVlcnl9ICAgICRlbGVtZW50ICAgICAgICBNb2R1bGUgZWxlbWVudC5cblx0XHRcdCAqIEBwYXJhbSB7T2JqZWN0fSAgICB1c2VyQ2ZnU2VydmljZSAgVXNlciBjb25maWd1cmF0aW9uIHNlcnZpY2UgbGlicmFyeS5cblx0XHRcdCAqIEBwYXJhbSB7T2JqZWN0fSAgICBsb2FkaW5nU3Bpbm5lciAgTG9hZGluZyBzcGlubmVyIGxpYnJhcnkuXG5cdFx0XHQgKiBAcGFyYW0ge051bWJlcn0gICAgdXNlcklkICAgICAgICAgIElEIG9mIGN1cnJlbnRseSBzaWduZWQgaW4gdXNlci5cblx0XHRcdCAqIEBwYXJhbSB7T2JqZWN0fSAgICB0cmFuc2xhdG9yICAgICAgVHJhbnNsYXRvciBsaWJyYXJ5LlxuXHRcdFx0ICovXG5cdFx0XHRjb25zdHJ1Y3Rvcihkb25lLCAkZWxlbWVudCwgdXNlckNmZ1NlcnZpY2UsIGxvYWRpbmdTcGlubmVyLCB1c2VySWQsIHRyYW5zbGF0b3IpIHtcblx0XHRcdFx0Ly8gRWxlbWVudHNcblx0XHRcdFx0dGhpcy4kZWxlbWVudCA9ICRlbGVtZW50O1xuXHRcdFx0XHR0aGlzLiRzdWJtaXRCdXR0b24gPSAkZWxlbWVudC5maW5kKCdidXR0b24uc3VibWl0LWJ1dHRvbicpO1xuXHRcdFx0XHR0aGlzLiRzZXR0aW5ncyA9ICRlbGVtZW50LmZpbmQoJ3VsLnNldHRpbmdzJyk7XG5cdFx0XHRcdHRoaXMuJG1vZGFsID0gJGVsZW1lbnQucGFyZW50cygnLm1vZGFsJyk7XG5cdFx0XHRcdHRoaXMuJG1vZGFsRm9vdGVyID0gJGVsZW1lbnQuZmluZCgnLm1vZGFsLWZvb3RlcicpO1xuXHRcdFx0XHR0aGlzLiRyZXNldERlZmF1bHRMaW5rID0gJGVsZW1lbnQuZmluZCgnYS5yZXNldC1hY3Rpb24nKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIExvYWRpbmcgc3Bpbm5lclxuXHRcdFx0XHR0aGlzLiRzcGlubmVyID0gbnVsbDtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNlbGVjdG9yIHN0cmluZ3Ncblx0XHRcdFx0dGhpcy5zb3J0YWJsZUhhbmRsZVNlbGVjdG9yID0gJ3NwYW4uc29ydC1oYW5kbGUnO1xuXHRcdFx0XHR0aGlzLnJvd0hlaWdodFZhbHVlU2VsZWN0b3IgPSAnc2VsZWN0I3NldHRpbmctdmFsdWUtcm93LWhlaWdodCc7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBDbGFzcyBuYW1lc1xuXHRcdFx0XHR0aGlzLmVycm9yTWVzc2FnZUNsYXNzTmFtZSA9ICdlcnJvci1tZXNzYWdlJztcblx0XHRcdFx0dGhpcy5sb2FkaW5nQ2xhc3NOYW1lID0gJ2xvYWRpbmcnO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gTGlicmFyaWVzXG5cdFx0XHRcdHRoaXMudXNlckNmZ1NlcnZpY2UgPSB1c2VyQ2ZnU2VydmljZTtcblx0XHRcdFx0dGhpcy5sb2FkaW5nU3Bpbm5lciA9IGxvYWRpbmdTcGlubmVyO1xuXHRcdFx0XHR0aGlzLnRyYW5zbGF0b3IgPSB0cmFuc2xhdG9yO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUHJlZml4ZXNcblx0XHRcdFx0dGhpcy5zZXR0aW5nTGlzdEl0ZW1JZFByZWZpeCA9ICdzZXR0aW5nLSc7XG5cdFx0XHRcdHRoaXMuc2V0dGluZ1ZhbHVlSWRQcmVmaXggPSAnc2V0dGluZy12YWx1ZS0nO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gVXNlciBjb25maWd1cmF0aW9uIGtleXNcblx0XHRcdFx0dGhpcy5DT05GSUdfS0VZX0NPTFVNTl9TRVRUSU5HUyA9ICdvcmRlcnNPdmVydmlld1NldHRpbmdzQ29sdW1ucyc7XG5cdFx0XHRcdHRoaXMuQ09ORklHX0tFWV9ST1dfSEVJR0hUX1NFVFRJTkdTID0gJ29yZGVyc092ZXJ2aWV3U2V0dGluZ3NSb3dIZWlnaHQnO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gRGVmYXVsdCB2YWx1ZXNcblx0XHRcdFx0dGhpcy5ERUZBVUxUX1JPV19IRUlHSFRfU0VUVElORyA9ICdsYXJnZSc7XG5cdFx0XHRcdHRoaXMuREVGQVVMVF9DT0xVTU5fU0VUVElOR1MgPSBbXG5cdFx0XHRcdFx0J251bWJlcicsICdjdXN0b21lcicsICdncm91cCcsICdzdW0nLCAncGF5bWVudE1ldGhvZCcsICdzaGlwcGluZ01ldGhvZCcsXG5cdFx0XHRcdFx0J2NvdW50cnlJc29Db2RlJywgJ2RhdGUnLCAnc3RhdHVzJywgJ3RvdGFsV2VpZ2h0J1xuXHRcdFx0XHRdO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gSUQgb2YgY3VycmVudGx5IHNpZ25lZCBpbiB1c2VyLlxuXHRcdFx0XHR0aGlzLnVzZXJJZCA9IHVzZXJJZDtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIENhbGwgbW9kdWxlIGZpbmlzaCBjYWxsYmFjay5cblx0XHRcdFx0ZG9uZSgpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEJpbmRzIHRoZSBldmVudCBoYW5kbGVycy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICovXG5cdFx0XHRpbml0aWFsaXplKCkge1xuXHRcdFx0XHQvLyBBdHRhY2ggZXZlbnQgaGFuZGxlciBmb3IgY2xpY2sgYWN0aW9uIG9uIHRoZSBzdWJtaXQgYnV0dG9uLlxuXHRcdFx0XHR0aGlzLiRzdWJtaXRCdXR0b24ub24oJ2NsaWNrJywgZXZlbnQgPT4gdGhpcy5fb25TdWJtaXRCdXR0b25DbGljayhldmVudCkpO1xuXG5cdFx0XHRcdC8vIEF0dGFjaCBldmVudCBoYW5kbGVyIGZvciBjbGljayBhY3Rpb24gb24gdGhlIHJlc2V0LWRlZmF1bHQgbGluay5cblx0XHRcdFx0dGhpcy4kcmVzZXREZWZhdWx0TGluay5vbignY2xpY2snLCBldmVudCA9PiB0aGlzLl9vblJlc2V0U2V0dGluZ3NMaW5rQ2xpY2soZXZlbnQpKTtcblxuXHRcdFx0XHQvLyBBdHRhY2ggZXZlbnQgaGFuZGxlcnMgdG8gbW9kYWwuXG5cdFx0XHRcdHRoaXMuJG1vZGFsXG5cdFx0XHRcdFx0Lm9uKCdzaG93LmJzLm1vZGFsJywgZXZlbnQgPT4gdGhpcy5fb25Nb2RhbFNob3coZXZlbnQpKVxuXHRcdFx0XHRcdC5vbignc2hvd24uYnMubW9kYWwnLCBldmVudCA9PiB0aGlzLl9vbk1vZGFsU2hvd24oZXZlbnQpKTtcblx0XHRcdFx0XG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZhZGVzIG91dCB0aGUgbW9kYWwgY29udGVudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgRmlyZWQgZXZlbnQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7U2V0dGluZ3NNb2RhbENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfb25Nb2RhbFNob3coKSB7XG5cdFx0XHRcdHRoaXMuJGVsZW1lbnQuYWRkQ2xhc3ModGhpcy5sb2FkaW5nQ2xhc3NOYW1lKTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBVcGRhdGVzIHRoZSBzZXR0aW5ncywgY2xlYXJzIGFueSBlcnJvciBtZXNzYWdlcyBhbmQgaW5pdGlhbGl6ZXMgdGhlIHNvcnRhYmxlIHBsdWdpbi5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9vbk1vZGFsU2hvd24oKSB7XG5cdFx0XHRcdHRoaXNcblx0XHRcdFx0XHQuX3JlZnJlc2hTZXR0aW5ncygpXG5cdFx0XHRcdFx0Ll9jbGVhckVycm9yTWVzc2FnZSgpXG5cdFx0XHRcdFx0Ll9pbml0U29ydGFibGUoKTtcblx0XHRcdFx0XG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEFjdGl2YXRlcyB0aGUgalF1ZXJ5IFVJIFNvcnRhYmxlIHBsdWdpbiBvbiB0aGUgc2V0dGluZyBsaXN0IGl0ZW1zIGVsZW1lbnQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7U2V0dGluZ3NNb2RhbENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfaW5pdFNvcnRhYmxlKCkge1xuXHRcdFx0XHQvLyBqUXVlcnkgVUkgU29ydGFibGUgcGx1Z2luIG9wdGlvbnMuXG5cdFx0XHRcdGNvbnN0IG9wdGlvbnMgPSB7XG5cdFx0XHRcdFx0aXRlbXM6ICc+IGxpJyxcblx0XHRcdFx0XHRheGlzOiAneScsXG5cdFx0XHRcdFx0Y3Vyc29yOiAnbW92ZScsXG5cdFx0XHRcdFx0aGFuZGxlOiB0aGlzLnNvcnRhYmxlSGFuZGxlU2VsZWN0b3IsXG5cdFx0XHRcdFx0Y29udGFpbm1lbnQ6ICdwYXJlbnQnXG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBY3RpdmF0ZSBzb3J0YWJsZSBwbHVnaW4uXG5cdFx0XHRcdHRoaXMuJHNldHRpbmdzXG5cdFx0XHRcdFx0LnNvcnRhYmxlKG9wdGlvbnMpXG5cdFx0XHRcdFx0LmRpc2FibGVTZWxlY3Rpb24oKTtcblx0XHRcdFx0XG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFJldHVybnMgYSBzb3J0ZWQgYXJyYXkgY29udGFpbmluZyB0aGUgSURzIG9mIGFsbCBhY3RpdmF0ZWQgc2V0dGluZ3MuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7QXJyYXl9XG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X3NlcmlhbGl6ZUNvbHVtblNldHRpbmdzKCkge1xuXHRcdFx0XHQvLyBNYXAgaXRlcmF0b3IgZnVuY3Rpb24gdG8gcmVtb3ZlIHRoZSAnc2V0dGluZy0nIHByZWZpeCBmcm9tIGxpc3QgaXRlbSBJRC5cblx0XHRcdFx0Y29uc3QgcmVtb3ZlUHJlZml4SXRlcmF0b3IgPSBpdGVtID0+IGl0ZW0ucmVwbGFjZSh0aGlzLnNldHRpbmdMaXN0SXRlbUlkUHJlZml4LCAnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBGaWx0ZXIgaXRlcmF0b3IgZnVuY3Rpb24sIHRvIGFjY2VwdCBvbmx5IGxpc3QgaXRlbXMgd2l0aCBhY3RpdmF0ZWQgY2hlY2tib3hlcy5cblx0XHRcdFx0Y29uc3QgZmlsdGVySXRlcmF0b3IgPSBpdGVtID0+IHRoaXMuJHNldHRpbmdzLmZpbmQoJyMnICsgdGhpcy5zZXR0aW5nVmFsdWVJZFByZWZpeCArIGl0ZW0pXG5cdFx0XHRcdFx0LmlzKCc6Y2hlY2tlZCcpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUmV0dXJuIGFycmF5IHdpdGggc29ydGVkLCBvbmx5IGFjdGl2ZSBjb2x1bW5zLlxuXHRcdFx0XHRyZXR1cm4gdGhpcy4kc2V0dGluZ3Ncblx0XHRcdFx0XHQuc29ydGFibGUoJ3RvQXJyYXknKVxuXHRcdFx0XHRcdC5tYXAocmVtb3ZlUHJlZml4SXRlcmF0b3IpXG5cdFx0XHRcdFx0LmZpbHRlcihmaWx0ZXJJdGVyYXRvcik7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogUmV0dXJucyB0aGUgdmFsdWUgb2YgdGhlIHNlbGVjdGVkIHJvdyBoZWlnaHQgb3B0aW9uLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge1N0cmluZ31cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfc2VyaWFsaXplUm93SGVpZ2h0U2V0dGluZygpIHtcblx0XHRcdFx0cmV0dXJuIHRoaXNcblx0XHRcdFx0XHQuJGVsZW1lbnRcblx0XHRcdFx0XHQuZmluZCh0aGlzLnJvd0hlaWdodFZhbHVlU2VsZWN0b3IpXG5cdFx0XHRcdFx0LnZhbCgpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFNob3dzIHRoZSBsb2FkaW5nIHNwaW5uZXIsIHNhdmVzIHRoZSBzZXR0aW5ncyB0byB0aGUgdXNlciBjb25maWd1cmF0aW9uLFxuXHRcdFx0ICogY2xvc2VzIHRoZSBtb2RhbCB0byBmaW5hbGx5IHJlLXJlbmRlciB0aGUgZGF0YXRhYmxlLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge1NldHRpbmdzTW9kYWxDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X29uU3VibWl0QnV0dG9uQ2xpY2soKSB7XG5cdFx0XHRcdC8vIFJldHJpZXZlIHNldHRpbmcgdmFsdWVzLlxuXHRcdFx0XHRjb25zdCBjb2x1bW5TZXR0aW5ncyA9IHRoaXMuX3NlcmlhbGl6ZUNvbHVtblNldHRpbmdzKCk7XG5cdFx0XHRcdGNvbnN0IHJvd0hlaWdodFNldHRpbmcgPSB0aGlzLl9zZXJpYWxpemVSb3dIZWlnaHRTZXR0aW5nKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBSZW1vdmUgYW55IGVycm9yIG1lc3NhZ2UgYW5kIHNhdmUgc2V0dGluZ3MuXG5cdFx0XHRcdHRoaXNcblx0XHRcdFx0XHQuX3RvZ2dsZUxvYWRpbmdTcGlubmVyKHRydWUpXG5cdFx0XHRcdFx0Ll9jbGVhckVycm9yTWVzc2FnZSgpXG5cdFx0XHRcdFx0Ll9zYXZlQ29sdW1uU2V0dGluZ3MoY29sdW1uU2V0dGluZ3MpXG5cdFx0XHRcdFx0LnRoZW4oKCkgPT4gdGhpcy5fc2F2ZVJvd0hlaWdodFNldHRpbmcocm93SGVpZ2h0U2V0dGluZykpXG5cdFx0XHRcdFx0LnRoZW4oKCkgPT4gdGhpcy5fb25TYXZlU3VjY2VzcygpKVxuXHRcdFx0XHRcdC5jYXRjaCgoKSA9PiB0aGlzLl9vblNhdmVFcnJvcigpKTtcblx0XHRcdFx0XG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIFByZXZlbnRzIHRoZSBicm93c2VyIHRvIGFwcGx5IHRoZSBkZWZhdWx0IGJlaGF2b2lyIGFuZFxuXHRcdFx0ICogcmVzZXRzIHRoZSBjb2x1bW4gb3JkZXIgYW5kIHJvdyBzaXplIHRvIHRoZSBkZWZhdWx0IHNldHRpbmcgdmFsdWVzLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBGaXJlZCBldmVudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9vblJlc2V0U2V0dGluZ3NMaW5rQ2xpY2soZXZlbnQpIHtcblx0XHRcdFx0Ly8gUHJldmVudCBkZWZhdWx0IGJlaGF2aW9yLlxuXHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuXHRcdFx0XHQvLyBSZXNldCB0byBkZWZhdWx0IHNldHRpbmdzLlxuXHRcdFx0XHR0aGlzLl9zZXREZWZhdWx0U2V0dGluZ3MoKTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBTaG93cyBhbmQgaGlkZXMgdGhlIGxvYWRpbmcgc3Bpbm5lci5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0ge0Jvb2xlYW59IGRvU2hvdyBTaG93IHRoZSBsb2FkaW5nIHNwaW5uZXI/XG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7U2V0dGluZ3NNb2RhbENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqL1xuXHRcdFx0X3RvZ2dsZUxvYWRpbmdTcGlubmVyKGRvU2hvdykge1xuXHRcdFx0XHRpZiAoZG9TaG93KSB7XG5cdFx0XHRcdFx0Ly8gRmFkZSBvdXQgbW9kYWwgY29udGVudC5cblx0XHRcdFx0XHR0aGlzLiRlbGVtZW50LmFkZENsYXNzKHRoaXMubG9hZGluZ0NsYXNzTmFtZSk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gU2hvdyBsb2FkaW5nIHNwaW5uZXIuXG5cdFx0XHRcdFx0dGhpcy4kc3Bpbm5lciA9IHRoaXMubG9hZGluZ1NwaW5uZXIuc2hvdyh0aGlzLiRlbGVtZW50KTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBGaXggc3Bpbm5lciB6LWluZGV4LlxuXHRcdFx0XHRcdHRoaXMuJHNwaW5uZXIuY3NzKHsnei1pbmRleCc6IDk5OTl9KTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHQvLyBGYWRlIG91dCBtb2RhbCBjb250ZW50LlxuXHRcdFx0XHRcdHRoaXMuJGVsZW1lbnQucmVtb3ZlQ2xhc3ModGhpcy5sb2FkaW5nQ2xhc3NOYW1lKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBIaWRlIHRoZSBsb2FkaW5nIHNwaW5uZXIuXG5cdFx0XHRcdFx0dGhpcy5sb2FkaW5nU3Bpbm5lci5oaWRlKHRoaXMuJHNwaW5uZXIpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIYW5kbGVzIHRoZSBiZWhhdmlvciBvbiBzdWNjZXNzZnVsIHNldHRpbmcgc2F2ZSBhY3Rpb24uXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7U2V0dGluZ3NNb2RhbENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfb25TYXZlU3VjY2VzcygpIHtcblx0XHRcdFx0d2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZW1vdmVzIGFueSBlcnJvciBtZXNzYWdlLCBpZiBmb3VuZC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9jbGVhckVycm9yTWVzc2FnZSgpIHtcblx0XHRcdFx0Ly8gRXJyb3IgbWVzc2FnZS5cblx0XHRcdFx0Y29uc3QgJGVycm9yTWVzc2FnZSA9IHRoaXMuJG1vZGFsRm9vdGVyLmZpbmQoYC4ke3RoaXMuZXJyb3JNZXNzYWdlQ2xhc3NOYW1lfWApO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUmVtb3ZlIGlmIGl0IGV4aXN0cy5cblx0XHRcdFx0aWYgKCRlcnJvck1lc3NhZ2UubGVuZ3RoKSB7XG5cdFx0XHRcdFx0JGVycm9yTWVzc2FnZS5yZW1vdmUoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0cmV0dXJuIHRoaXM7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogSGFuZGxlcyB0aGUgYmVoYXZpb3Igb24gdGhyb3duIGVycm9yIHdoaWxlIHNhdmluZyBzZXR0aW5ncy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9vblNhdmVFcnJvcigpIHtcblx0XHRcdFx0Ly8gRXJyb3IgbWVzc2FnZS5cblx0XHRcdFx0Y29uc3QgZXJyb3JNZXNzYWdlID0gdGhpcy50cmFuc2xhdG9yLnRyYW5zbGF0ZSgnVFhUX1NBVkVfRVJST1InLCAnYWRtaW5fZ2VuZXJhbCcpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gRGVmaW5lIGVycm9yIG1lc3NhZ2UgZWxlbWVudC5cblx0XHRcdFx0Y29uc3QgJGVycm9yID0gJCgnPHNwYW4vPicsIHtjbGFzczogdGhpcy5lcnJvck1lc3NhZ2VDbGFzc05hbWUsIHRleHQ6IGVycm9yTWVzc2FnZX0pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gSGlkZSB0aGUgbG9hZGluZyBzcGlubmVyLlxuXHRcdFx0XHR0aGlzLl90b2dnbGVMb2FkaW5nU3Bpbm5lcihmYWxzZSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBZGQgZXJyb3IgbWVzc2FnZSB0byBtb2RhbCBmb290ZXIuXG5cdFx0XHRcdHRoaXMuJG1vZGFsRm9vdGVyXG5cdFx0XHRcdFx0LnByZXBlbmQoJGVycm9yKVxuXHRcdFx0XHRcdC5oaWRlKClcblx0XHRcdFx0XHQuZmFkZUluKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZXR1cm5zIHRoZSBjb25maWd1cmF0aW9uIHZhbHVlIGZvciB0aGUgY29sdW1uIHNldHRpbmdzLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge1Byb21pc2V9XG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X2dldENvbHVtblNldHRpbmdzKCkge1xuXHRcdFx0XHQvLyBDb25maWd1cmF0aW9uIGRhdGEuXG5cdFx0XHRcdGNvbnN0IGRhdGEgPSB7XG5cdFx0XHRcdFx0dXNlcklkOiB0aGlzLnVzZXJJZCxcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiB0aGlzLkNPTkZJR19LRVlfQ09MVU1OX1NFVFRJTkdTXG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBSZXF1ZXN0IGRhdGEgZnJvbSB1c2VyIGNvbmZpZ3VyYXRpb24gc2VydmljZS5cblx0XHRcdFx0cmV0dXJuIHRoaXMuX2dldEZyb21Vc2VyQ2ZnU2VydmljZShkYXRhKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZXR1cm5zIHRoZSBjb25maWd1cmF0aW9uIHZhbHVlIGZvciB0aGUgcm93IGhlaWdodHMuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7UHJvbWlzZX1cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfZ2V0Um93SGVpZ2h0U2V0dGluZygpIHtcblx0XHRcdFx0Ly8gQ29uZmlndXJhdGlvbiBkYXRhLlxuXHRcdFx0XHRjb25zdCBkYXRhID0ge1xuXHRcdFx0XHRcdHVzZXJJZDogdGhpcy51c2VySWQsXG5cdFx0XHRcdFx0Y29uZmlndXJhdGlvbktleTogdGhpcy5DT05GSUdfS0VZX1JPV19IRUlHSFRfU0VUVElOR1Ncblx0XHRcdFx0fTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFJlcXVlc3QgZGF0YSBmcm9tIHVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlLlxuXHRcdFx0XHRyZXR1cm4gdGhpcy5fZ2V0RnJvbVVzZXJDZmdTZXJ2aWNlKGRhdGEpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFJldHVybnMgdGhlIHZhbHVlIGZvciB0aGUgcGFzc2VkIHVzZXIgY29uZmlndXJhdGlvbiBkYXRhLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhICAgICAgICAgICAgICAgICAgIFVzZXIgY29uZmlndXJhdGlvbiBkYXRhLlxuXHRcdFx0ICogQHBhcmFtIHtOdW1iZXJ9IGRhdGEudXNlcklkICAgICAgICAgICAgVXNlciBJRC5cblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBkYXRhLmNvbmZpZ3VyYXRpb25LZXkgIFVzZXIgY29uZmlndXJhdGlvbiBrZXkuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7UHJvbWlzZX1cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfZ2V0RnJvbVVzZXJDZmdTZXJ2aWNlKGRhdGEpIHtcblx0XHRcdFx0Ly8gUHJvbWlzZSBoYW5kbGVyLlxuXHRcdFx0XHRjb25zdCBoYW5kbGVyID0gKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuXHRcdFx0XHRcdC8vIFVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlIHJlcXVlc3Qgb3B0aW9ucy5cblx0XHRcdFx0XHRjb25zdCBvcHRpb25zID0ge1xuXHRcdFx0XHRcdFx0b25FcnJvcjogKCkgPT4gcmVqZWN0KCksXG5cdFx0XHRcdFx0XHRvblN1Y2Nlc3M6IHJlc3BvbnNlID0+IHJlc29sdmUocmVzcG9uc2UuY29uZmlndXJhdGlvblZhbHVlKSxcblx0XHRcdFx0XHRcdGRhdGFcblx0XHRcdFx0XHR9O1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEdldCBjb25maWd1cmF0aW9uIHZhbHVlLlxuXHRcdFx0XHRcdHRoaXMudXNlckNmZ1NlcnZpY2UuZ2V0KG9wdGlvbnMpO1xuXHRcdFx0XHR9O1xuXHRcdFx0XHRcblx0XHRcdFx0cmV0dXJuIG5ldyBQcm9taXNlKGhhbmRsZXIpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFNhdmVzIHRoZSBkYXRhIHZpYSB0aGUgdXNlciBjb25maWd1cmF0aW9uIHNlcnZpY2UuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtPYmplY3R9IGRhdGEgICAgICAgICAgICAgICAgICAgICBVc2VyIGNvbmZpZ3VyYXRpb24gZGF0YS5cblx0XHRcdCAqIEBwYXJhbSB7TnVtYmVyfSBkYXRhLnVzZXJJZCAgICAgICAgICAgICAgVXNlciBJRC5cblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBkYXRhLmNvbmZpZ3VyYXRpb25LZXkgICAgVXNlciBjb25maWd1cmF0aW9uIGtleS5cblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBkYXRhLmNvbmZpZ3VyYXRpb25WYWx1ZSAgVXNlciBjb25maWd1cmF0aW9uIHZhbHVlLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge1Byb21pc2V9XG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X3NldFdpdGhVc2VyQ2ZnU2VydmljZShkYXRhKSB7XG5cdFx0XHRcdC8vIFByb21pc2UgaGFuZGxlci5cblx0XHRcdFx0Y29uc3QgaGFuZGxlciA9IChyZXNvbHZlLCByZWplY3QpID0+IHtcblx0XHRcdFx0XHQvLyBVc2VyIGNvbmZpZ3VyYXRpb24gc2VydmljZSByZXF1ZXN0IG9wdGlvbnMuXG5cdFx0XHRcdFx0Y29uc3Qgb3B0aW9ucyA9IHtcblx0XHRcdFx0XHRcdG9uRXJyb3I6ICgpID0+IHJlamVjdCgpLFxuXHRcdFx0XHRcdFx0b25TdWNjZXNzOiByZXNwb25zZSA9PiByZXNvbHZlKCksXG5cdFx0XHRcdFx0XHRkYXRhXG5cdFx0XHRcdFx0fTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBTZXQgY29uZmlndXJhdGlvbiB2YWx1ZS5cblx0XHRcdFx0XHR0aGlzLnVzZXJDZmdTZXJ2aWNlLnNldChvcHRpb25zKTtcblx0XHRcdFx0fTtcblx0XHRcdFx0XG5cdFx0XHRcdHJldHVybiBuZXcgUHJvbWlzZShoYW5kbGVyKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBTYXZlcyB0aGUgY29sdW1uIHNldHRpbmdzIHZpYSB0aGUgdXNlciBjb25maWd1cmF0aW9uIHNlcnZpY2UuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtTdHJpbmdbXX0gY29sdW1uU2V0dGluZ3MgU29ydGVkIGFycmF5IHdpdGggYWN0aXZlIGNvbHVtbi5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtQcm9taXNlfVxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9zYXZlQ29sdW1uU2V0dGluZ3MoY29sdW1uU2V0dGluZ3MpIHtcblx0XHRcdFx0Ly8gQ2hlY2sgYXJndW1lbnQuXG5cdFx0XHRcdGlmICghY29sdW1uU2V0dGluZ3MgfHwgIUFycmF5LmlzQXJyYXkoY29sdW1uU2V0dGluZ3MpKSB7XG5cdFx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIG9yIGludmFsaWQgY29sdW1uIHNldHRpbmdzJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIFVzZXIgY29uZmlndXJhdGlvbiByZXF1ZXN0IGRhdGEuXG5cdFx0XHRcdGNvbnN0IGRhdGEgPSB7XG5cdFx0XHRcdFx0dXNlcklkOiB0aGlzLnVzZXJJZCxcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiB0aGlzLkNPTkZJR19LRVlfQ09MVU1OX1NFVFRJTkdTLFxuXHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25WYWx1ZTogSlNPTi5zdHJpbmdpZnkoY29sdW1uU2V0dGluZ3MpXG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBTYXZlIHZpYSB1c2VyIGNvbmZpZ3VyYXRpb24gc2VydmljZS5cblx0XHRcdFx0cmV0dXJuIHRoaXMuX3NldFdpdGhVc2VyQ2ZnU2VydmljZShkYXRhKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBTYXZlcyB0aGUgcm93IGhlaWdodCBzZXR0aW5nIHZpYSB0aGUgdXNlciBjb25maWd1cmF0aW9uIHNlcnZpY2UuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtTdHJpbmd9IHJvd0hlaWdodFNldHRpbmcgVmFsdWUgb2YgdGhlIHNlbGVjdGVkIHJvdyBoZWlnaHQgc2V0dGluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtQcm9taXNlfVxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9zYXZlUm93SGVpZ2h0U2V0dGluZyhyb3dIZWlnaHRTZXR0aW5nKSB7XG5cdFx0XHRcdC8vIENoZWNrIGFyZ3VtZW50LlxuXHRcdFx0XHRpZiAoIXJvd0hlaWdodFNldHRpbmcgfHwgdHlwZW9mIHJvd0hlaWdodFNldHRpbmcgIT09ICdzdHJpbmcnKSB7XG5cdFx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIG9yIGludmFsaWQgcm93IGhlaWdodCBzZXR0aW5nJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIFVzZXIgY29uZmlndXJhdGlvbiByZXF1ZXN0IGRhdGEuXG5cdFx0XHRcdGNvbnN0IGRhdGEgPSB7XG5cdFx0XHRcdFx0dXNlcklkOiB0aGlzLnVzZXJJZCxcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiB0aGlzLkNPTkZJR19LRVlfUk9XX0hFSUdIVF9TRVRUSU5HUyxcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uVmFsdWU6IHJvd0hlaWdodFNldHRpbmdcblx0XHRcdFx0fTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNhdmUgdmlhIHVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlLlxuXHRcdFx0XHRyZXR1cm4gdGhpcy5fc2V0V2l0aFVzZXJDZmdTZXJ2aWNlKGRhdGEpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFJldHJpZXZlcyB0aGUgc2F2ZWQgc2V0dGluZyBjb25maWd1cmF0aW9uIGFuZCByZW9yZGVycy91cGRhdGVzIHRoZSBzZXR0aW5ncy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9yZWZyZXNoU2V0dGluZ3MoKSB7XG5cdFx0XHRcdC8vIFNob3cgbG9hZGluZyBzcGlubmVyLlxuXHRcdFx0XHR0aGlzLl90b2dnbGVMb2FkaW5nU3Bpbm5lcih0cnVlKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIEVycm9yIGhhbmRsZXIgZnVuY3Rpb24gdG8gc3BlY2lmeSB0aGUgYmVoYXZpb3Igb24gZXJyb3JzIHdoaWxlIHByb2Nlc3NpbmcuXG5cdFx0XHRcdGNvbnN0IG9uUmVmcmVzaFNldHRpbmdzRXJyb3IgPSBlcnJvciA9PiB7XG5cdFx0XHRcdFx0Ly8gT3V0cHV0IHdhcm5pbmcuXG5cdFx0XHRcdFx0Y29uc29sZS53YXJuKCdFcnJvciB3aGlsZSByZWZyZXNoaW5nJywgZXJyb3IpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEhpZGUgdGhlIGxvYWRpbmcgc3Bpbm5lci5cblx0XHRcdFx0XHR0aGlzLl90b2dnbGVMb2FkaW5nU3Bpbm5lcihmYWxzZSk7XG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBSZW1vdmUgYW55IGVycm9yIG1lc3NhZ2UsIHNldCByb3cgaGVpZ2h0LFxuXHRcdFx0XHQvLyByZW9yZGVyIGFuZCB1cGRhdGUgdGhlIHNldHRpbmdzIGFuZCBoaWRlIHRoZSBsb2FkaW5nIHNwaW5uZXIuXG5cdFx0XHRcdHRoaXNcblx0XHRcdFx0XHQuX2NsZWFyRXJyb3JNZXNzYWdlKClcblx0XHRcdFx0XHQuX2dldFJvd0hlaWdodFNldHRpbmcoKVxuXHRcdFx0XHRcdC50aGVuKHJvd0hlaWdodFZhbHVlID0+IHRoaXMuX3NldFJvd0hlaWdodChyb3dIZWlnaHRWYWx1ZSkpXG5cdFx0XHRcdFx0LnRoZW4oKCkgPT4gdGhpcy5fZ2V0Q29sdW1uU2V0dGluZ3MoKSlcblx0XHRcdFx0XHQudGhlbihjb2x1bW5TZXR0aW5ncyA9PiB0aGlzLl9zZXRDb2x1bW5TZXR0aW5ncyhjb2x1bW5TZXR0aW5ncykpXG5cdFx0XHRcdFx0LnRoZW4oKCkgPT4gdGhpcy5fdG9nZ2xlTG9hZGluZ1NwaW5uZXIoZmFsc2UpKVxuXHRcdFx0XHRcdC5jYXRjaChvblJlZnJlc2hTZXR0aW5nc0Vycm9yKTtcblx0XHRcdFx0XG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFNldHMgdGhlIHJvdyBoZWlnaHQgc2V0dGluZyB2YWx1ZS5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0ge1N0cmluZ30gdmFsdWUgUm93IGhlaWdodCB2YWx1ZS5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9zZXRSb3dIZWlnaHQodmFsdWUgPSB0aGlzLkRFRkFVTFRfUk9XX0hFSUdIVF9TRVRUSU5HKSB7XG5cdFx0XHRcdHRoaXNcblx0XHRcdFx0XHQuJGVsZW1lbnRcblx0XHRcdFx0XHQuZmluZCh0aGlzLnJvd0hlaWdodFZhbHVlU2VsZWN0b3IpXG5cdFx0XHRcdFx0LnZhbCh2YWx1ZSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZW9yZGVycyBhbmQgdXBkYXRlcyB0aGUgY29sdW1uIHNldHRpbmcgdmFsdWVzLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfEFycmF5fSBjb2x1bW5TZXR0aW5ncyBTdHJpbmdpZmllZCBKU09OIGFycmF5IGNvbnRhaW5pbmcgdGhlIHNhdmVkIGNvbHVtbiBzZXR0aW5ncy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9zZXRDb2x1bW5TZXR0aW5ncyhjb2x1bW5TZXR0aW5ncyA9IHRoaXMuREVGQVVMVF9DT0xVTU5fU0VUVElOR1MpIHtcblx0XHRcdFx0Ly8gUmVnZXggZm9yIGVzY2FwZSBjaGFyYWN0ZXIuXG5cdFx0XHRcdGNvbnN0IEVTQ0FQRV9DSEFSID0gL1xcXFwvZztcblx0XHRcdFx0XG5cdFx0XHRcdC8vIE5vIG5lZWQgdG8gcGFyc2UgZnJvbSBKU09OIG9uIGRlZmF1bHQgdmFsdWUgYXMgaXQgaXMgYW4gYXJyYXkuXG5cdFx0XHRcdGlmICghQXJyYXkuaXNBcnJheShjb2x1bW5TZXR0aW5ncykpIHtcblx0XHRcdFx0XHQvLyBSZW1vdmUgZXNjYXBlIGNoYXJhY3RlcnMgZnJvbSBhbmQgcGFyc2UgYXJyYXkgZnJvbSBKU09OLlxuXHRcdFx0XHRcdGNvbHVtblNldHRpbmdzID0gY29sdW1uU2V0dGluZ3MucmVwbGFjZShFU0NBUEVfQ0hBUiwgJycpO1xuXHRcdFx0XHRcdGNvbHVtblNldHRpbmdzID0gSlNPTi5wYXJzZShjb2x1bW5TZXR0aW5ncyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIENhY2hlIGNvbnRhaW5lciB0byB0ZW1wb3JhcmlseSBob2xkIGFsbCBhY3RpdmUgbGlzdCBpdGVtcyBpbiBzb3J0ZWQgb3JkZXIuXG5cdFx0XHRcdC8vIFRoZSBjaGlsZHJlbiBvZiB0aGlzIGVsZW1lbnQgd2lsbCBiZSBwcmVwZW5kZWQgdG8gdGhlIHNldHRpbmcgbGlzdCBpdGVtIGNvbnRhaW5lciB0byByZXRhaW4gdGhlIFxuXHRcdFx0XHQvLyBzb3J0aW5nIG9yZGVyLlxuXHRcdFx0XHRjb25zdCAkc29ydGVkSXRlbXMgPSAkKCc8ZGl2Lz4nKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIEl0ZXJhdG9yIGZ1bmN0aW9uIHRvIHByZXBlbmQgYWN0aXZlIGxpc3QgaXRlbXMgdG8gdGhlIHRvcCBhbmQgYWN0aXZhdGUgdGhlIGNoZWNrYm94LlxuXHRcdFx0XHRjb25zdCBzZXR0aW5nSXRlcmF0b3IgPSBzZXR0aW5nID0+IHtcblx0XHRcdFx0XHQvLyBMaXN0IGl0ZW0gSUQuXG5cdFx0XHRcdFx0Y29uc3QgaWQgPSB0aGlzLnNldHRpbmdMaXN0SXRlbUlkUHJlZml4ICsgc2V0dGluZztcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBBZmZlY3RlZCBzZXR0aW5nIGxpc3QgaXRlbS5cblx0XHRcdFx0XHRjb25zdCAkbGlzdEl0ZW0gPSB0aGlzLiRzZXR0aW5ncy5maW5kKGAjJHtpZH1gKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBDaGVja2JveCBvZiBhZmZlY3RlZCBsaXN0IGl0ZW0uXG5cdFx0XHRcdFx0Y29uc3QgJGNoZWNrYm94ID0gJGxpc3RJdGVtLmZpbmQoJyMnICsgdGhpcy5zZXR0aW5nVmFsdWVJZFByZWZpeCArIHNldHRpbmcpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEFjdGl2YXRlIGNoZWNrYm94LlxuXHRcdFx0XHRcdGlmICghJGNoZWNrYm94LmlzKCc6Y2hlY2tlZCcpKSB7XG5cdFx0XHRcdFx0XHQkY2hlY2tib3gucGFyZW50KCkudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gTW92ZSB0byBjYWNoZSBjb250YWluZXIuXG5cdFx0XHRcdFx0JGxpc3RJdGVtLmFwcGVuZFRvKCRzb3J0ZWRJdGVtcyk7XG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBNb3ZlIGFjdGl2ZSBsaXN0IGl0ZW1zIHRvIHRoZSB0b3AgYmVhcmluZyB0aGUgc29ydGluZyBvcmRlciBpbiBtaW5kLlxuXHRcdFx0XHRjb2x1bW5TZXR0aW5ncy5mb3JFYWNoKHNldHRpbmdJdGVyYXRvcik7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBQcmVwZW5kIGNhY2hlZCBlbGVtZW50cyB0byBpdGVtIGxpc3QuXG5cdFx0XHRcdCRzb3J0ZWRJdGVtc1xuXHRcdFx0XHRcdC5jaGlsZHJlbigpXG5cdFx0XHRcdFx0LnByZXBlbmRUbyh0aGlzLiRzZXR0aW5ncyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZXNldHMgdGhlIGNvbHVtbiBvcmRlciBhbmQgcm93IGhlaWdodCBzZXR0aW5ncyB0byB0aGUgZGVmYXVsdC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtTZXR0aW5nc01vZGFsQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9zZXREZWZhdWx0U2V0dGluZ3MoKSB7XG5cdFx0XHRcdC8vIERlZmF1bHQgdmFsdWVzLlxuXHRcdFx0XHRjb25zdCBjb2x1bW5TZXR0aW5ncyA9IHRoaXMuREVGQVVMVF9DT0xVTU5fU0VUVElOR1M7XG5cdFx0XHRcdGNvbnN0IHJvd0hlaWdodCA9IHRoaXMuREVGQVVMVF9ST1dfSEVJR0hUX1NFVFRJTkc7XG5cblx0XHRcdFx0Ly8gU2V0IGNvbHVtbiBzZXR0aW5ncy5cblx0XHRcdFx0Ly8gQ2FjaGUgY29udGFpbmVyIHRvIHRlbXBvcmFyaWx5IGhvbGQgYWxsIGFjdGl2ZSBsaXN0IGl0ZW1zIGluIHNvcnRlZCBvcmRlci5cblx0XHRcdFx0Ly8gVGhlIGNoaWxkcmVuIG9mIHRoaXMgZWxlbWVudCB3aWxsIGJlIHByZXBlbmRlZCB0byB0aGUgc2V0dGluZyBsaXN0IGl0ZW0gY29udGFpbmVyIHRvIHJldGFpbiB0aGUgXG5cdFx0XHRcdC8vIHNvcnRpbmcgb3JkZXIuXG5cdFx0XHRcdGNvbnN0ICRzb3J0ZWRJdGVtcyA9ICQoJzxkaXYvPicpO1xuXG5cdFx0XHRcdC8vIEl0ZXJhdG9yIGZ1bmN0aW9uIHRvIHByZXBlbmQgYWN0aXZlIGxpc3QgaXRlbXMgdG8gdGhlIHRvcCBhbmQgYWN0aXZhdGUgdGhlIGNoZWNrYm94LlxuXHRcdFx0XHRjb25zdCBzZXR0aW5nSXRlcmF0b3IgPSBzZXR0aW5nID0+IHtcblx0XHRcdFx0XHQvLyBMaXN0IGl0ZW0gSUQuXG5cdFx0XHRcdFx0Y29uc3QgaWQgPSB0aGlzLnNldHRpbmdMaXN0SXRlbUlkUHJlZml4ICsgc2V0dGluZztcblxuXHRcdFx0XHRcdC8vIEFmZmVjdGVkIHNldHRpbmcgbGlzdCBpdGVtLlxuXHRcdFx0XHRcdGNvbnN0ICRsaXN0SXRlbSA9IHRoaXMuJHNldHRpbmdzLmZpbmQoYCMke2lkfWApO1xuXG5cdFx0XHRcdFx0Ly8gQ2hlY2tib3ggb2YgYWZmZWN0ZWQgbGlzdCBpdGVtLlxuXHRcdFx0XHRcdGNvbnN0ICRjaGVja2JveCA9ICRsaXN0SXRlbS5maW5kKCcjJyArIHRoaXMuc2V0dGluZ1ZhbHVlSWRQcmVmaXggKyBzZXR0aW5nKTtcblxuXHRcdFx0XHRcdC8vIEFjdGl2YXRlIGNoZWNrYm94LlxuXHRcdFx0XHRcdGlmICghJGNoZWNrYm94LmlzKCc6Y2hlY2tlZCcpKSB7XG5cdFx0XHRcdFx0XHQkY2hlY2tib3gucGFyZW50KCkudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHQvLyBNb3ZlIHRvIGNhY2hlIGNvbnRhaW5lci5cblx0XHRcdFx0XHQkbGlzdEl0ZW0uYXBwZW5kVG8oJHNvcnRlZEl0ZW1zKTtcblx0XHRcdFx0fTtcblxuXHRcdFx0XHQvLyBEZWFjdGl2YXRlIGFsbCBjaGVja2JveGVzLlxuXHRcdFx0XHR0aGlzXG5cdFx0XHRcdFx0LiRzZXR0aW5nc1xuXHRcdFx0XHRcdC5maW5kKCc6Y2hlY2tib3gnKVxuXHRcdFx0XHRcdC5lYWNoKChpbmRleCwgZWxlbWVudCkgPT4ge1xuXHRcdFx0XHRcdFx0Y29uc3QgJGNoZWNrYm94ID0gJChlbGVtZW50KTtcblxuXHRcdFx0XHRcdFx0aWYgKCRjaGVja2JveC5pcygnOmNoZWNrZWQnKSkge1xuXHRcdFx0XHRcdFx0XHQkY2hlY2tib3gucGFyZW50KCkudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHQvLyBNb3ZlIGFjdGl2ZSBsaXN0IGl0ZW1zIHRvIHRoZSB0b3AgYmVhcmluZyB0aGUgc29ydGluZyBvcmRlciBpbiBtaW5kLlxuXHRcdFx0XHRjb2x1bW5TZXR0aW5ncy5mb3JFYWNoKHNldHRpbmdJdGVyYXRvcik7XG5cblx0XHRcdFx0Ly8gUHJlcGVuZCBjYWNoZWQgZWxlbWVudHMgdG8gaXRlbSBsaXN0LlxuXHRcdFx0XHQkc29ydGVkSXRlbXNcblx0XHRcdFx0XHQuY2hpbGRyZW4oKVxuXHRcdFx0XHRcdC5wcmVwZW5kVG8odGhpcy4kc2V0dGluZ3MpO1xuXG5cdFx0XHRcdC8vIFNldCByb3cgaGVpZ2h0LlxuXHRcdFx0XHR0aGlzXG5cdFx0XHRcdFx0LiRlbGVtZW50XG5cdFx0XHRcdFx0LmZpbmQodGhpcy5yb3dIZWlnaHRWYWx1ZVNlbGVjdG9yKVxuXHRcdFx0XHRcdC52YWwocm93SGVpZ2h0KTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBEZXBlbmRlbmNpZXMuXG5cdFx0XHRjb25zdCB1c2VyQ2ZnU2VydmljZSA9IGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlO1xuXHRcdFx0Y29uc3QgbG9hZGluZ1NwaW5uZXIgPSBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXI7XG5cdFx0XHRjb25zdCB1c2VySWQgPSBkYXRhLnVzZXJJZDtcblx0XHRcdGNvbnN0IHRyYW5zbGF0b3IgPSBqc2UuY29yZS5sYW5nO1xuXHRcdFx0XG5cdFx0XHQvLyBDcmVhdGUgYSBuZXcgaW5zdGFuY2UgYW5kIGxvYWQgc2V0dGluZ3MuXG5cdFx0XHRjb25zdCBzZXR0aW5nc01vZGFsID0gbmV3IFNldHRpbmdzTW9kYWxDb250cm9sbGVyKGRvbmUsICR0aGlzLCB1c2VyQ2ZnU2VydmljZSwgbG9hZGluZ1NwaW5uZXIsXG5cdFx0XHRcdHVzZXJJZCwgdHJhbnNsYXRvcik7XG5cdFx0XHRcblx0XHRcdHNldHRpbmdzTW9kYWwuaW5pdGlhbGl6ZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
