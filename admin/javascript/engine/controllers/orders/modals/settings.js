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
gx.controllers.module(
	'settings',
	
	[
		'user_configuration_service',
		'loading_spinner'
	],
	
	function(data) {
		
		'use strict';
		
		// --------------------------------------------------------------------
		// VARIABLES
		// --------------------------------------------------------------------
		
		/**
		 * Module Selector
		 *
		 * @type {jQuery}
		 */
		const $this = $(this);

		/**
		 * Module Instance
		 *
		 * @type {Object}
		 */
		const module = {};
		
		
		// --------------------------------------------------------------------
		// FUNCTIONS
		// --------------------------------------------------------------------
		
		/**
		 * Class representing a controller for the orders overview settings modal.
		 */
		class SettingsModalController {
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
			constructor(done, $element, userCfgService, loadingSpinner, userId, translator) {
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
				this.DEFAULT_COLUMN_SETTINGS = [
					'number', 'customer', 'group', 'sum', 'paymentMethod', 'shippingMethod',
					'countryIsoCode', 'date', 'status', 'totalWeight'
				];
				
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
			initialize() {
				// Attach event handler for click action on the submit button.
				this.$submitButton.on('click', event => this._onSubmitButtonClick(event));

				// Attach event handler for click action on the reset-default link.
				this.$resetDefaultLink.on('click', event => this._onResetSettingsLinkClick(event));

				// Attach event handlers to modal.
				this.$modal
					.on('show.bs.modal', event => this._onModalShow(event))
					.on('shown.bs.modal', event => this._onModalShown(event));
				
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
			_onModalShow() {
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
			_onModalShown() {
				this
					._refreshSettings()
					._clearErrorMessage()
					._initSortable();
				
				return this;
			}
			
			/**
			 * Activates the jQuery UI Sortable plugin on the setting list items element.
			 *
			 * @return {SettingsModalController} Same instance for method chaining.
			 *
			 * @private
			 */
			_initSortable() {
				// jQuery UI Sortable plugin options.
				const options = {
					items: '> li',
					axis: 'y',
					cursor: 'move',
					handle: this.sortableHandleSelector,
					containment: 'parent'
				};
				
				// Activate sortable plugin.
				this.$settings
					.sortable(options)
					.disableSelection();
				
				return this;
			}
			
			/**
			 * Returns a sorted array containing the IDs of all activated settings.
			 *
			 * @return {Array}
			 *
			 * @private
			 */
			_serializeColumnSettings() {
				// Map iterator function to remove the 'setting-' prefix from list item ID.
				const removePrefixIterator = item => item.replace(this.settingListItemIdPrefix, '');
				
				// Filter iterator function, to accept only list items with activated checkboxes.
				const filterIterator = item => this.$settings.find('#' + this.settingValueIdPrefix + item)
					.is(':checked');
				
				// Return array with sorted, only active columns.
				return this.$settings
					.sortable('toArray')
					.map(removePrefixIterator)
					.filter(filterIterator);
			}
			
			/**
			 * Returns the value of the selected row height option.
			 *
			 * @return {String}
			 *
			 * @private
			 */
			_serializeRowHeightSetting() {
				return this
					.$element
					.find(this.rowHeightValueSelector)
					.val();
			}
			
			/**
			 * Shows the loading spinner, saves the settings to the user configuration,
			 * closes the modal to finally re-render the datatable.
			 *
			 * @return {SettingsModalController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onSubmitButtonClick() {
				// Retrieve setting values.
				const columnSettings = this._serializeColumnSettings();
				const rowHeightSetting = this._serializeRowHeightSetting();
				
				// Remove any error message and save settings.
				this
					._toggleLoadingSpinner(true)
					._clearErrorMessage()
					._saveColumnSettings(columnSettings)
					.then(() => this._saveRowHeightSetting(rowHeightSetting))
					.then(() => this._onSaveSuccess())
					.catch(() => this._onSaveError());
				
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
			_onResetSettingsLinkClick(event) {
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
			_toggleLoadingSpinner(doShow) {
				if (doShow) {
					// Fade out modal content.
					this.$element.addClass(this.loadingClassName);
					
					// Show loading spinner.
					this.$spinner = this.loadingSpinner.show(this.$element);
					
					// Fix spinner z-index.
					this.$spinner.css({'z-index': 9999});
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
			_onSaveSuccess() {
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
			_clearErrorMessage() {
				// Error message.
				const $errorMessage = this.$modalFooter.find(`.${this.errorMessageClassName}`);
				
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
			_onSaveError() {
				// Error message.
				const errorMessage = this.translator.translate('TXT_SAVE_ERROR', 'admin_general');
				
				// Define error message element.
				const $error = $('<span/>', {class: this.errorMessageClassName, text: errorMessage});
				
				// Hide the loading spinner.
				this._toggleLoadingSpinner(false);
				
				// Add error message to modal footer.
				this.$modalFooter
					.prepend($error)
					.hide()
					.fadeIn();
				
				return this;
			}
			
			/**
			 * Returns the configuration value for the column settings.
			 *
			 * @return {Promise}
			 *
			 * @private
			 */
			_getColumnSettings() {
				// Configuration data.
				const data = {
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
			_getRowHeightSetting() {
				// Configuration data.
				const data = {
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
			_getFromUserCfgService(data) {
				// Promise handler.
				const handler = (resolve, reject) => {
					// User configuration service request options.
					const options = {
						onError: () => reject(),
						onSuccess: response => resolve(response.configurationValue),
						data
					};
					
					// Get configuration value.
					this.userCfgService.get(options);
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
			_setWithUserCfgService(data) {
				// Promise handler.
				const handler = (resolve, reject) => {
					// User configuration service request options.
					const options = {
						onError: () => reject(),
						onSuccess: response => resolve(),
						data
					};
					
					// Set configuration value.
					this.userCfgService.set(options);
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
			_saveColumnSettings(columnSettings) {
				// Check argument.
				if (!columnSettings || !Array.isArray(columnSettings)) {
					throw new Error('Missing or invalid column settings');
				}
				
				// User configuration request data.
				const data = {
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
			_saveRowHeightSetting(rowHeightSetting) {
				// Check argument.
				if (!rowHeightSetting || typeof rowHeightSetting !== 'string') {
					throw new Error('Missing or invalid row height setting');
				}
				
				// User configuration request data.
				const data = {
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
			_refreshSettings() {
				// Show loading spinner.
				this._toggleLoadingSpinner(true);
				
				// Error handler function to specify the behavior on errors while processing.
				const onRefreshSettingsError = error => {
					// Output warning.
					console.warn('Error while refreshing', error);
					
					// Hide the loading spinner.
					this._toggleLoadingSpinner(false);
				};
				
				// Remove any error message, set row height,
				// reorder and update the settings and hide the loading spinner.
				this
					._clearErrorMessage()
					._getRowHeightSetting()
					.then(rowHeightValue => this._setRowHeight(rowHeightValue))
					.then(() => this._getColumnSettings())
					.then(columnSettings => this._setColumnSettings(columnSettings))
					.then(() => this._toggleLoadingSpinner(false))
					.catch(onRefreshSettingsError);
				
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
			_setRowHeight(value = this.DEFAULT_ROW_HEIGHT_SETTING) {
				this
					.$element
					.find(this.rowHeightValueSelector)
					.val(value);
				
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
			_setColumnSettings(columnSettings = this.DEFAULT_COLUMN_SETTINGS) {
				// Regex for escape character.
				const ESCAPE_CHAR = /\\/g;
				
				// No need to parse from JSON on default value as it is an array.
				if (!Array.isArray(columnSettings)) {
					// Remove escape characters from and parse array from JSON.
					columnSettings = columnSettings.replace(ESCAPE_CHAR, '');
					columnSettings = JSON.parse(columnSettings);
				}
				
				// Cache container to temporarily hold all active list items in sorted order.
				// The children of this element will be prepended to the setting list item container to retain the 
				// sorting order.
				const $sortedItems = $('<div/>');
				
				// Iterator function to prepend active list items to the top and activate the checkbox.
				const settingIterator = setting => {
					// List item ID.
					const id = this.settingListItemIdPrefix + setting;
					
					// Affected setting list item.
					const $listItem = this.$settings.find(`#${id}`);
					
					// Checkbox of affected list item.
					const $checkbox = $listItem.find('#' + this.settingValueIdPrefix + setting);
					
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
				$sortedItems
					.children()
					.prependTo(this.$settings);
				
				return this;
			}

			/**
			 * Resets the column order and row height settings to the default.
			 *
			 * @return {SettingsModalController} Same instance for method chaining.
			 *
			 * @private
			 */
			_setDefaultSettings() {
				// Default values.
				const columnSettings = this.DEFAULT_COLUMN_SETTINGS;
				const rowHeight = this.DEFAULT_ROW_HEIGHT_SETTING;

				// Set column settings.
				// Cache container to temporarily hold all active list items in sorted order.
				// The children of this element will be prepended to the setting list item container to retain the 
				// sorting order.
				const $sortedItems = $('<div/>');

				// Iterator function to prepend active list items to the top and activate the checkbox.
				const settingIterator = setting => {
					// List item ID.
					const id = this.settingListItemIdPrefix + setting;

					// Affected setting list item.
					const $listItem = this.$settings.find(`#${id}`);

					// Checkbox of affected list item.
					const $checkbox = $listItem.find('#' + this.settingValueIdPrefix + setting);

					// Activate checkbox.
					if (!$checkbox.is(':checked')) {
						$checkbox.parent().trigger('click');
					}

					// Move to cache container.
					$listItem.appendTo($sortedItems);
				};

				// Deactivate all checkboxes.
				this
					.$settings
					.find(':checkbox')
					.each((index, element) => {
						const $checkbox = $(element);

						if ($checkbox.is(':checked')) {
							$checkbox.parent().trigger('click');
						}
					});

				// Move active list items to the top bearing the sorting order in mind.
				columnSettings.forEach(settingIterator);

				// Prepend cached elements to item list.
				$sortedItems
					.children()
					.prependTo(this.$settings);

				// Set row height.
				this
					.$element
					.find(this.rowHeightValueSelector)
					.val(rowHeight);

				return this;
			}
		}
		
		// --------------------------------------------------------------------
		// INITIALIZATION
		// --------------------------------------------------------------------
		
		module.init = function(done) {
			// Dependencies.
			const userCfgService = jse.libs.user_configuration_service;
			const loadingSpinner = jse.libs.loading_spinner;
			const userId = data.userId;
			const translator = jse.core.lang;
			
			// Create a new instance and load settings.
			const settingsModal = new SettingsModalController(done, $this, userCfgService, loadingSpinner,
				userId, translator);
			
			settingsModal.initialize();
		};
		
		return module;
	});
