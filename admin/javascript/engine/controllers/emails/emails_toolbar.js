/* --------------------------------------------------------------
 emails_toolbar.js 2016-02-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Emails Toolbar Controller
 *
 * This controller will handle the main toolbar operations of the admin/emails page.
 *
 * @module Controllers/emails_toolbar
 */
gx.controllers.module(
	'emails_toolbar',
	
	[
		gx.source + '/libs/emails',
		'url_arguments'
	],
	
	/** @lends module:Controllers/emails_toolbar */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Modal Selector
			 *
			 * @type {object}
			 */
			$modal = $('#emails-modal'),
			
			/**
			 * Table Selector
			 *
			 * @type {object}
			 */
			$table = $('#emails-table'),
			
			/**
			 * Default Module Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Final Module Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {
				model: {
					settings: jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/GetEmailSettings'
				}
			};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * Display create new email modal.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onCreateNewEmail = function(event) {
			// Reset modal elements to initial state.
			jse.libs.emails.resetModal($modal);
			
			// Apply Email Settings to the Email Modal
			if (typeof module.model.settings !== 'undefined' && module.model.settings !== null) {
				// Set the email signature (if not empty). We'll only set the signature to the CKEditor because
				// if the signature contains HTML markup it cannot be sanitized properly for the plain content.
				if (module.model.settings.signature !== null && module.model.settings.signature !== '') {
					var signatureHtml = '<br><p>-----<br>' 
						+ module.model.settings.signature.replace('\n/g', '<br>') + '</p>';
					CKEDITOR.instances['content-html'].setData(signatureHtml);
					var signaturePlain = '\n\n-----\n' + module.model.settings.signature.replace(/(<([^>]+)>)/gi,
							'');
					$modal.find('#content-plain').val(signaturePlain);
				}
				
				// Disable the HTML content if the shop uses only plain content for the emails.
				if (module.model.settings.useHtml === false) {
					$modal.find('.content').find('.tab-headline:eq(0), .tab-content:eq(0)').hide();
					$modal.find('.content').find('.tab-headline:eq(1)').trigger('click');
				}
				
				// Preload sender and reply to contact data if provided.
				if (typeof module.model.settings.replyAddress !== 'undefined' && module.model.settings.replyAddress !==
					'') {
					$modal.find('#sender-email, #reply-to-email').val(module.model.settings.replyAddress);
				}
				if (typeof module.model.settings.replyName !== 'undefined' && module.model.settings.replyName !==
					'') {
					$modal.find('#sender-name, #reply-to-name').val(module.model.settings.replyName);
				}
			}
			
			// Prepare and display new modal window.
			$modal.dialog({
				title: jse.core.lang.translate('new_mail', 'buttons'),
				width: 1000,
				height: 740,
				modal: false,
				dialogClass: 'gx-container',
				closeOnEscape: false,
				buttons: jse.libs.emails.getDefaultModalButtons($modal, $table),
				open: jse.libs.emails.colorizeButtonsForEditMode
			});
		};
		
		/**
		 * Perform search request on the DataTable instance.
		 *
		 * @param {object} event Contains the event data.
		 */
		var _onTableSearchSubmit = function(event) {
			event.preventDefault();
			var keyword = $this.find('#search-keyword').val();
			$table.DataTable().search(keyword).draw();
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			// Set default "#bulk-action" value.
			$this.find('#bulk-action').val('');
			
			// Bind Event Handlers
			$this
				.on('click', '#create-new-email', _onCreateNewEmail)
				.on('submit', '#quick-search', _onTableSearchSubmit);
			
			// Check if the "mail_to" parameter is present and process its value within the new email modal layer.
			var getParameters = jse.libs.url_arguments.getUrlParameters();
			if (typeof getParameters.mailto !== 'undefined') {
				_onCreateNewEmail({}); // Display the new email modal.
				$modal.find('#recipient-email').val(getParameters.mailto);
			}
			
			done();
		};
		
		// Return module object to module engine.
		return module;
	});
