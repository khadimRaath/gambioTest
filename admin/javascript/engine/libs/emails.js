/* --------------------------------------------------------------
 emails.js 2016-02-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.emails = jse.libs.emails || {};

/**
 * ## Emails Library
 *
 * This library contains all the admin/emails page common functionality and is used by the page 
 * controllers. You might also use this library in other pages where you need to trigger specific 
 * email operations in the server.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * 
 * ```javascript
 * gx.controller.module(
 *   'my_custom_page',
 *
 *   [
 *      gx.source + '/libs/emails'
 *   ],
 *
 *   function(data) {
 *      // Module code ... 
 *   });
 *```
 * 
 * Required Translation Sections: 'admin_labels', 'buttons', 'db_backup', 'emails', 'lightbox_buttons', 'messages'
 *
 * @module Admin/Libs/emails
 * @exports jse.libs.emails
 */
(function(exports) {
	
	'use strict';
	
	exports.CONTACT_TYPE_SENDER = 'sender';
	exports.CONTACT_TYPE_RECIPIENT = 'recipient';
	exports.CONTACT_TYPE_REPLY_TO = 'reply_to';
	exports.CONTACT_TYPE_BCC = 'bcc';
	exports.CONTACT_TYPE_CC = 'cc';
	
	/**
	 * Reset Modal (DOM)
	 *
	 * This method will reset the emails modal back to its initial state. The default
	 * modal markup is used in the admin/emails page, but this method can work without
	 * all the elements too.
	 *
	 * @param {object} $modal jQuery selector for the modal.
	 */
	exports.resetModal = function($modal) {
		// Clear basic elements
		$modal.find('input, textarea').val('');
		$modal.find('select option:first').prop('selected', 'selected');
		
		// Remove validation classes.
		$modal.trigger('validator.reset');
		
		// Remove all rows from DataTables.
		if ($modal.find('.dataTables_wrapper').length > 0) {
			$modal.find('.dataTables_wrapper table').DataTable().clear().draw();
			$modal.find('.dataTables_wrapper').find('.dataTables_length select option:eq(0)').prop(
				'selected', true);
		}
		
		// Set all tab widgets to the first tab.
		if ($modal.find('.tab-headline-wrapper').length > 0) {
			$modal.find('.tab-headline').css('color', '').show();
			$modal.find('.tab-headline-wrapper').each(function() {
				$(this).find('a:eq(0)').trigger('click'); // toggle first tab
			});
		}
		
		// Need to recreate the ckeditor instance every time the modal appears.
		if ($modal.find('#content-html').length > 0) {
			if (CKEDITOR.instances['content-html'] !== undefined) {
				CKEDITOR.instances['content-html'].destroy();
			}
			CKEDITOR.replace('content-html', {
				language: jse.core.config.get('languageCode')
			});
			CKEDITOR.instances['content-html'].setData('');
		}
		
		// If contact type hidden inputs are present then we have to re-apply their value.
		if ($modal.find('#sender-type').length > 0) {
			$modal.find('#sender-type').val('sender');
		}
		if ($modal.find('#recipient-type').length > 0) {
			$modal.find('#recipient-type').val('recipient');
		}
		if ($modal.find('#reply-to-type').length > 0) {
			$modal.find('#reply-to-type').val('reply_to');
		}
		
		// Update Tab Counters
		jse.libs.emails.updateTabCounters($modal);
	};
	
	/**
	 * Returns the email information from modal (DOM).
	 *
	 * The method will grab the values from the modal and bundle them in a single object.
	 * The returned object will have the same structure as the valueMapping object. This
	 * method is recursive.
	 *
	 * @param {object} $modal jQuery selector for the modal.
	 *
	 * @return {object} Returns the email data of the modal.
	 */
	exports.getEmailFromModal = function($modal) {
		var email = {};
		
		// Required Email Fields
		email.sender = {
			email_address: $modal.find('#sender-email').val(),
			contact_name: $modal.find('#sender-name').val(),
			contact_type: exports.CONTACT_TYPE_SENDER
		};
		
		email.recipient = {
			email_address: $modal.find('#recipient-email').val(),
			contact_name: $modal.find('#recipient-name').val(),
			contact_type: exports.CONTACT_TYPE_RECIPIENT
		};
		
		email.subject = $modal.find('#subject').val();
		email.content_html = CKEDITOR.instances['content-html'].getData();
		
		// Optional Email fields
		email.email_id = ($modal.find('#email-id').val() !== '') ? $modal.find('#email-id').val() :
		                 null;
		email.is_pending = ($modal.find('#is-pending').val() === 'true');
		email.content_plain = ($modal.find('#content-plain').val() !== '') ? $modal.find(
			'#content-plain').val() : null;
		
		email.reply_to = ($modal.find('#reply-to-email').val() !== '') ? {} : null;
		if (email.reply_to) {
			email.reply_to.email_address = $modal.find('#reply-to-email').val();
			email.reply_to.contact_name = $modal.find('#reply-to-name').val();
			email.reply_to.contact_type = exports.CONTACT_TYPE_REPLY_TO;
		}
		
		// BCC & CC Contacts
		email.bcc = null;
		email.cc = null;
		var contacts = $modal.find('#contacts-table').DataTable().rows().data();
		
		$.each(contacts, function(index, contact) {
			if (email[contact.type] == null) {
				email[contact.type] = [];
			}
			
			email[contact.type].push({
				email_address: contact.email,
				contact_name: contact.name,
				contact_type: contact.type
			});
		});
		
		// Attachments
		email.attachments = null;
		var attachments = $modal.find('#attachments-table').DataTable().rows().data();
		$.each(attachments, function(index, attachment) {
			if (email.attachments === null) {
				email.attachments = [];
			}
			email.attachments.push(attachment);
		});
		
		return email;
	};
	
	/**
	 * Loads email data on modal (DOM).
	 *
	 * @param {object} email Contains the email data.
	 * @param {object} $modal jQuery selector for the modal.
	 */
	exports.loadEmailOnModal = function(email, $modal) {
		// Required Email Fields
		$modal.find('#sender-email').val(email.sender.email_address);
		$modal.find('#sender-name').val(email.sender.contact_name);
		
		$modal.find('#recipient-email').val(email.recipient.email_address);
		$modal.find('#recipient-name').val(email.recipient.contact_name);
		
		$modal.find('#subject').val(email.subject);
		CKEDITOR.instances['content-html'].setData(email.content_html);
		
		$modal.find('#is-pending').val((email.is_pending) ? 'true' : 'false');
		
		// Optional Email Fields
		
		if (email.email_id !== null) {
			$modal.find('#email-id').val(email.email_id);
		}
		
		if (email.creation_date !== null) {
			$modal.find('#creation-date').val(email.creation_date);
		}
		
		if (email.sent_date !== null) {
			$modal.find('#sent-date').val(email.sent_date);
		}
		
		if (email.reply_to !== null) {
			$modal.find('#reply-to-email').val(email.reply_to.email_address);
			$modal.find('#reply-to-name').val(email.reply_to.contact_name);
		}
		
		if (email.content_plain !== null) {
			$modal.find('#content-plain').val(email.content_plain);
		}
		
		if (email.bcc !== null) {
			$.each(email.bcc, function(index, contact) {
				var row = {
					email: jse.libs.normalize.escapeHtml(contact.email_address),
					name: jse.libs.normalize.escapeHtml(contact.contact_name),
					type: jse.libs.normalize.escapeHtml(contact.contact_type)
				};
				$modal.find('#contacts-table').DataTable().row.add(row).draw();
			});
		}
		
		if (email.cc !== null) {
			$.each(email.cc, function(index, contact) {
				var row = {
					email: jse.libs.normalize.escapeHtml(contact.email_address),
					name: jse.libs.normalize.escapeHtml(contact.contact_name),
					type: jse.libs.normalize.escapeHtml(contact.contact_type)
				};
				$modal.find('#contacts-table').DataTable().row.add(row).draw();
			});
		}
		
		if (email.attachments !== null) {
			$.each(email.attachments, function(index, attachment) {
				attachment.path = jse.libs.normalize.escapeHtml(attachment.path);
				$modal.find('#attachments-table').DataTable().row.add(attachment).draw();
			});
		}
		
		// Update Tab Counters
		jse.libs.emails.updateTabCounters($modal);
	};
	
	/**
	 * Sends an email collection
	 *
	 * Provide an array of email objects and this method will send them to the requested
	 * URL through AJAX POST. You can omit the url and the default EmailsController will
	 * be used.
	 *
	 * @param {array} collection Array of email objects.
	 * @param {string} ajaxUrl (optional) The AJAX URL for the POST request.
	 *
	 * @return {object} Returns a promise object that will provide the server's response.
	 */
	exports.sendCollection = function(collection, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/Send';
		
		var deferred = $.Deferred(),
			data = {
				pageToken: jse.core.config.get('pageToken'),
				collection: collection
			};
		
		$.post(ajaxUrl, data, function(response) {
			if (response.exception) {
				deferred.reject(response);
				return;
			}
			deferred.resolve(response);
		}, 'json');
		
		return deferred.promise();
	};
	
	/**
	 * Queues the email collection
	 *
	 * Provide an array of email objects and this method will queue them to the requested
	 * URL through AJAX POST. You can omit the url and the default EmailsController will
	 * be used.
	 *
	 * @param {array} collection Array of email objects.
	 * @param {string} ajaxUrl (optional) The AJAX URL for the POST request.
	 *
	 * @return {object} Returns a promise object that will provide the server's response.
	 */
	exports.queueCollection = function(collection, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/Queue';
		
		var deferred = $.Deferred(),
			data = {
				pageToken: jse.core.config.get('pageToken'),
				collection: collection
			};
		
		$.post(ajaxUrl, data, function(response) {
			if (response.exception) {
				deferred.reject(response);
				return;
			}
			deferred.resolve(response);
		}, 'json');
		
		return deferred.promise();
	};
	
	/**
	 * Deletes an email collection
	 *
	 * Provide an array of email objects and this method will delete them to the requested
	 * URL through AJAX POST. You can omit the url and the default EmailsController will
	 * be used.
	 *
	 * @param {array} collection Array of email objects.
	 * @param {string} ajaxUrl (optional) The AJAX URL for the POST request.
	 *
	 * @return {object} Returns a promise object that will provide the server's response.
	 */
	exports.deleteCollection = function(collection, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/Delete';
		
		var deferred = $.Deferred(),
			data = {
				pageToken: jse.core.config.get('pageToken'),
				collection: collection
			};
		
		$.post(ajaxUrl, data, function(response) {
			if (response.exception) {
				deferred.reject(response);
				return;
			}
			deferred.resolve(response);
		}, 'json');
		
		return deferred.promise();
	};
	
	/**
	 * Returns default modal buttons
	 *
	 * Used by various sections of the admin/emails page. With the proper use of valueMapping object
	 * you can use this method in other pages too.
	 *
	 * @param {object} $modal jQuery selector for the modal.
	 * @param {object} $table jQuery selector for the main table.
	 *
	 * @return {object} Returns the dialog modal buttons.
	 */
	exports.getDefaultModalButtons = function($modal, $table) {
		var buttons = [
			{
				text: jse.core.lang.translate('close', 'buttons'),
				click: function() {
					$(this).dialog('close');
				}
			},
			{
				text: jse.core.lang.translate('queue', 'buttons'),
				click: function() {
					$modal.find('.tab-content.details').trigger('validator.validate');
					if ($modal.find('.tab-content.details .error').length > 0) {
						return; // There are fields with errors.
					}
					var email = jse.libs.emails.getEmailFromModal($modal);
					jse.libs.emails.queueCollection([email])
						.done(function(response) {
							$table.DataTable().ajax.reload();
							jse.libs.emails.getAttachmentsSize($('#attachments-size'));
						})
						.fail(function(response) {
							jse.libs.modal.message({
								title: jse.core.lang.translate('error', 'messages'),
								content: response.message
							});
						});
					$(this).dialog('close');
				}
			},
			{
				text: jse.core.lang.translate('send', 'buttons'),
				click: function() {
					$modal.find('.tab-content.details').trigger('validator.validate');
					if ($modal.find('.tab-content.details .error').length > 0) {
						return; // There are fields with errors.
					}
					var email = jse.libs.emails.getEmailFromModal($modal);
					jse.libs.emails.sendCollection([email])
						.done(function(response) {
							$table.DataTable().ajax.reload();
							jse.libs.emails.getAttachmentsSize($('#attachments-size'));
						})
						.fail(function(response) {
							jse.libs.modal.message({
								title: jse.core.lang.translate('error', 'messages'),
								content: response.message
							});
						});
					$(this).dialog('close');
				}
			}
		];
		
		return buttons;
	};
	
	/**
	 * Returns preview modal buttons
	 *
	 * This method will return the preview modal buttons for the jQuery UI dialog widget. With the proper
	 * use of valueMapping object you can use this method in other pages too.
	 *
	 * @param {object} $modal jQuery selector for the modal.
	 * @param {object} $table jQuery selector for the main table.
	 *
	 * @return {object} Returns the dialog modal buttons.
	 */
	exports.getPreviewModalButtons = function($modal, $table) {
		var buttons = [
			{
				text: jse.core.lang.translate('close', 'buttons'),
				click: function() {
					$(this).dialog('close');
				}
			},
			{
				text: jse.core.lang.translate('delete', 'buttons'),
				click: function() {
					var modalOptions = {
						title: 'Delete Email Record',
						content: 'Are you sure that you want to delete this email record?',
						buttons: [
							{
								text: jse.core.lang.translate('yes', 'lightbox_buttons'),
								click: function() {
									var email = jse.libs.emails.getEmailFromModal($modal);
									
									jse.libs.emails.deleteCollection([email])
										.done(function(response) {
											$table.DataTable().ajax.reload();
											jse.libs.emails.getAttachmentsSize($('#attachments-size'));
										})
										.fail(function(response) {
											jse.libs.modal.message({
												title: jse.core.lang.translate('error',
													'messages'),
												content: response.message
											});
										});
									$(this).dialog('close');
									$modal.dialog('close');
								}
							},
							{
								text: jse.core.lang.translate('no', 'lightbox_buttons'),
								click: function() {
									$(this).dialog('close');
								}
							}
						]
					};
					
					jse.libs.modal.message(modalOptions);
				}
			},
			{
				text: jse.core.lang.translate('queue', 'buttons'),
				click: function() {
					var email = jse.libs.emails.getEmailFromModal($modal);
					
					// Duplicate record only if the original one is already sent.
					// Otherwise we just need to update the data of the current email record.
					if (!email.is_pending) {
						delete email.email_id; // will duplicate the record
					}
					
					jse.libs.emails.queueCollection([email])
						.done(function(response) {
							$table.DataTable().ajax.reload();
							jse.libs.emails.getAttachmentsSize($('#attachments-size'));
						})
						.fail(function(response) {
							jse.libs.modal.message({
								title: jse.core.lang.translate('error', 'messages'),
								content: response.message
							});
						});
					$(this).dialog('close');
				}
			},
			{
				text: jse.core.lang.translate('send', 'buttons'),
				click: function() {
					var email = jse.libs.emails.getEmailFromModal($modal);
					jse.libs.emails.sendCollection([email])
						.done(function(response) {
							$table.DataTable().ajax.reload();
							jse.libs.emails.getAttachmentsSize($('#attachments-size'));
						})
						.fail(function(response) {
							jse.libs.modal.message({
								title: jse.core.lang.translate('error', 'messages'),
								content: response.message
							});
						});
					$(this).dialog('close');
				}
			}
		];
		
		return buttons;
	};
	
	/**
	 * Colorizes modal buttons for the edit mode
	 *
	 * jQuery UI does not support direct addition of classes to the dialog buttons,
	 * so we need to apply the classes during the "create" event of the dialog.
	 *
	 * @param event {event} Event to trigger this function.
	 * @param ui {object} Dialog UI.
	 */
	exports.colorizeButtonsForEditMode = function(event, ui) {
		$(this).closest('.ui-dialog').find('.ui-button').eq(3).addClass('btn-primary'); // Send Button
	};
	
	/**
	 * Colorizes modal buttons for preview mode
	 *
	 * jQuery UI does not support direct addition of classes to the dialog buttons,
	 * so we need to apply the classes during the "create" event of the dialog.
	 *
	 * @param event {object} Event to trigger this function.
	 * @param ui {object} Dialog UI.
	 */
	exports.colorizeButtonsForPreviewMode = function(event, ui) {
		$(this).closest('.ui-dialog').find('.ui-button').eq(4).addClass('btn-primary'); // Send Button
	};
	
	/**
	 * Deletes old attachments from selected removal date and before.
	 *
	 * @param {date} removalDate The date when the removal should start.
	 * @param {object} ajaxUrl (optional) Specific ajaxUrl to be used for the request.
	 * @returns {object} Returns a promise object to be used when the requests ends.
	 */
	exports.deleteOldAttachments = function(removalDate, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DeleteOldAttachments';
		
		var deferred = $.Deferred(),
			data = {
				pageToken: jse.core.config.get('pageToken'),
				removalDate: removalDate
			};
		
		$.post(ajaxUrl, data, function(response) {
			if (response.exception) {
				deferred.reject(response);
				return;
			}
			deferred.resolve(response);
		}, 'json');
		
		return deferred.promise();
	};
	
	/**
	 * Returns the attachments size in MB and refreshes the UI.
	 *
	 * This method will make a GET request to the server in order to fetch and display
	 * the total attachments size, so that users know when it is time to remove old
	 * attachments.
	 *
	 * @param {object} $target jQuery selector for the element to contain the size info.
	 * @param {string} ajaxUrl (optional) Specific ajaxUrl to be used for the request.
	 *
	 * @return {object} Returns the promise object for chaining callbacks.
	 */
	exports.getAttachmentsSize = function($target, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/GetAttachmentsSize';
		
		var deferred = $.Deferred();
		
		$.get(ajaxUrl, function(response) {
			if (response.exception) {
				jse.libs.modal.message({
					title: jse.core.lang.translate('error', 'messages'),
					content: response.message
				});
				deferred.reject(response);
				return;
			}
			
			var size = (response.size.megabytes !== 0) ? response.size.megabytes + ' MB' : response.size
				.bytes + ' bytes';
			
			$target.text('(' + size + ')');
			deferred.resolve(response);
		}, 'json');
		
		return deferred.promise();
	};
	
	/**
	 * Updates modal tabs counters.
	 *
	 * Displays item number on tabs so that users know how many items there are
	 * included in the contacts and attachments tables.
	 *
	 * @param {object} $modal The modal selector to be updated.
	 * @param {object} $contactsTable (optional) The contacts table selector, default selector: '#contacts-table'.
	 * @param {object} $contactsTab (optional) The contacts tab selector, default selector: '.tab-headline.bcc-cc'.
	 * @param {object} $attachmentsTable (optional) The attachments table selector, default
	 * selector: '#attachments-table'.
	 * @param {object} $attachmentsTab (optional) The attachments tab selector, default
	 * selector: '.tab-headline.attachments'.
	 */
	exports.updateTabCounters = function($modal, $contactsTable, $contactsTab, $attachmentsTable,
		$attachmentsTab) {
		$contactsTable = $contactsTable || $modal.find('#contacts-table');
		$contactsTab = $contactsTab || $modal.find('.tab-headline.bcc-cc');
		$attachmentsTable = $attachmentsTable || $modal.find('#attachments-table');
		$attachmentsTab = $attachmentsTab || $modal.find('.tab-headline.attachments');
		
		if ($contactsTable.length === 0) {
			return; // There is no such table (emails.js unit testing).
		}
		
		var contactsCount = $contactsTable.DataTable().rows().data().length,
			newContactsText = $contactsTab.text().replace(/\(.*\)/g, '(' + contactsCount + ')'),
			attachmentsCount = $attachmentsTable.DataTable().rows().data().length,
			newAttachmentsText = $attachmentsTab.text().replace(/\(.*\)/g, '(' + attachmentsCount + ')');
		
		if (newContactsText.indexOf('(') === -1) {
			newContactsText += ' (' + contactsCount + ')';
		}
		
		if (newAttachmentsText.indexOf('(') === -1) {
			newAttachmentsText += ' (' + attachmentsCount + ')';
		}
		
		$contactsTab.text(newContactsText);
		$attachmentsTab.text(newAttachmentsText);
	};
	
	/**
	 * Returns an object array with the selected emails of the main emails table.
	 *
	 * @param {object} $table (optional) The main table selector, if omitted the "#emails-table" selector
	 * will be used.
	 *
	 * @returns {array} Returns an array with the emails data (collection).
	 */
	exports.getSelectedEmails = function($table) {
		$table = $table || $('#emails-table');
		
		var collection = [];
		
		$table
			.find('tr td input:checked')
			.each(function(index, checkbox) {
				collection.push($(checkbox).parents('tr').data());
			});
		
		return collection;
	};
	
})(jse.libs.emails);
