'use strict';

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
(function (exports) {

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
	exports.resetModal = function ($modal) {
		// Clear basic elements
		$modal.find('input, textarea').val('');
		$modal.find('select option:first').prop('selected', 'selected');

		// Remove validation classes.
		$modal.trigger('validator.reset');

		// Remove all rows from DataTables.
		if ($modal.find('.dataTables_wrapper').length > 0) {
			$modal.find('.dataTables_wrapper table').DataTable().clear().draw();
			$modal.find('.dataTables_wrapper').find('.dataTables_length select option:eq(0)').prop('selected', true);
		}

		// Set all tab widgets to the first tab.
		if ($modal.find('.tab-headline-wrapper').length > 0) {
			$modal.find('.tab-headline').css('color', '').show();
			$modal.find('.tab-headline-wrapper').each(function () {
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
	exports.getEmailFromModal = function ($modal) {
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
		email.email_id = $modal.find('#email-id').val() !== '' ? $modal.find('#email-id').val() : null;
		email.is_pending = $modal.find('#is-pending').val() === 'true';
		email.content_plain = $modal.find('#content-plain').val() !== '' ? $modal.find('#content-plain').val() : null;

		email.reply_to = $modal.find('#reply-to-email').val() !== '' ? {} : null;
		if (email.reply_to) {
			email.reply_to.email_address = $modal.find('#reply-to-email').val();
			email.reply_to.contact_name = $modal.find('#reply-to-name').val();
			email.reply_to.contact_type = exports.CONTACT_TYPE_REPLY_TO;
		}

		// BCC & CC Contacts
		email.bcc = null;
		email.cc = null;
		var contacts = $modal.find('#contacts-table').DataTable().rows().data();

		$.each(contacts, function (index, contact) {
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
		$.each(attachments, function (index, attachment) {
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
	exports.loadEmailOnModal = function (email, $modal) {
		// Required Email Fields
		$modal.find('#sender-email').val(email.sender.email_address);
		$modal.find('#sender-name').val(email.sender.contact_name);

		$modal.find('#recipient-email').val(email.recipient.email_address);
		$modal.find('#recipient-name').val(email.recipient.contact_name);

		$modal.find('#subject').val(email.subject);
		CKEDITOR.instances['content-html'].setData(email.content_html);

		$modal.find('#is-pending').val(email.is_pending ? 'true' : 'false');

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
			$.each(email.bcc, function (index, contact) {
				var row = {
					email: jse.libs.normalize.escapeHtml(contact.email_address),
					name: jse.libs.normalize.escapeHtml(contact.contact_name),
					type: jse.libs.normalize.escapeHtml(contact.contact_type)
				};
				$modal.find('#contacts-table').DataTable().row.add(row).draw();
			});
		}

		if (email.cc !== null) {
			$.each(email.cc, function (index, contact) {
				var row = {
					email: jse.libs.normalize.escapeHtml(contact.email_address),
					name: jse.libs.normalize.escapeHtml(contact.contact_name),
					type: jse.libs.normalize.escapeHtml(contact.contact_type)
				};
				$modal.find('#contacts-table').DataTable().row.add(row).draw();
			});
		}

		if (email.attachments !== null) {
			$.each(email.attachments, function (index, attachment) {
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
	exports.sendCollection = function (collection, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/Send';

		var deferred = $.Deferred(),
		    data = {
			pageToken: jse.core.config.get('pageToken'),
			collection: collection
		};

		$.post(ajaxUrl, data, function (response) {
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
	exports.queueCollection = function (collection, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/Queue';

		var deferred = $.Deferred(),
		    data = {
			pageToken: jse.core.config.get('pageToken'),
			collection: collection
		};

		$.post(ajaxUrl, data, function (response) {
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
	exports.deleteCollection = function (collection, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/Delete';

		var deferred = $.Deferred(),
		    data = {
			pageToken: jse.core.config.get('pageToken'),
			collection: collection
		};

		$.post(ajaxUrl, data, function (response) {
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
	exports.getDefaultModalButtons = function ($modal, $table) {
		var buttons = [{
			text: jse.core.lang.translate('close', 'buttons'),
			click: function click() {
				$(this).dialog('close');
			}
		}, {
			text: jse.core.lang.translate('queue', 'buttons'),
			click: function click() {
				$modal.find('.tab-content.details').trigger('validator.validate');
				if ($modal.find('.tab-content.details .error').length > 0) {
					return; // There are fields with errors.
				}
				var email = jse.libs.emails.getEmailFromModal($modal);
				jse.libs.emails.queueCollection([email]).done(function (response) {
					$table.DataTable().ajax.reload();
					jse.libs.emails.getAttachmentsSize($('#attachments-size'));
				}).fail(function (response) {
					jse.libs.modal.message({
						title: jse.core.lang.translate('error', 'messages'),
						content: response.message
					});
				});
				$(this).dialog('close');
			}
		}, {
			text: jse.core.lang.translate('send', 'buttons'),
			click: function click() {
				$modal.find('.tab-content.details').trigger('validator.validate');
				if ($modal.find('.tab-content.details .error').length > 0) {
					return; // There are fields with errors.
				}
				var email = jse.libs.emails.getEmailFromModal($modal);
				jse.libs.emails.sendCollection([email]).done(function (response) {
					$table.DataTable().ajax.reload();
					jse.libs.emails.getAttachmentsSize($('#attachments-size'));
				}).fail(function (response) {
					jse.libs.modal.message({
						title: jse.core.lang.translate('error', 'messages'),
						content: response.message
					});
				});
				$(this).dialog('close');
			}
		}];

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
	exports.getPreviewModalButtons = function ($modal, $table) {
		var buttons = [{
			text: jse.core.lang.translate('close', 'buttons'),
			click: function click() {
				$(this).dialog('close');
			}
		}, {
			text: jse.core.lang.translate('delete', 'buttons'),
			click: function click() {
				var modalOptions = {
					title: 'Delete Email Record',
					content: 'Are you sure that you want to delete this email record?',
					buttons: [{
						text: jse.core.lang.translate('yes', 'lightbox_buttons'),
						click: function click() {
							var email = jse.libs.emails.getEmailFromModal($modal);

							jse.libs.emails.deleteCollection([email]).done(function (response) {
								$table.DataTable().ajax.reload();
								jse.libs.emails.getAttachmentsSize($('#attachments-size'));
							}).fail(function (response) {
								jse.libs.modal.message({
									title: jse.core.lang.translate('error', 'messages'),
									content: response.message
								});
							});
							$(this).dialog('close');
							$modal.dialog('close');
						}
					}, {
						text: jse.core.lang.translate('no', 'lightbox_buttons'),
						click: function click() {
							$(this).dialog('close');
						}
					}]
				};

				jse.libs.modal.message(modalOptions);
			}
		}, {
			text: jse.core.lang.translate('queue', 'buttons'),
			click: function click() {
				var email = jse.libs.emails.getEmailFromModal($modal);

				// Duplicate record only if the original one is already sent.
				// Otherwise we just need to update the data of the current email record.
				if (!email.is_pending) {
					delete email.email_id; // will duplicate the record
				}

				jse.libs.emails.queueCollection([email]).done(function (response) {
					$table.DataTable().ajax.reload();
					jse.libs.emails.getAttachmentsSize($('#attachments-size'));
				}).fail(function (response) {
					jse.libs.modal.message({
						title: jse.core.lang.translate('error', 'messages'),
						content: response.message
					});
				});
				$(this).dialog('close');
			}
		}, {
			text: jse.core.lang.translate('send', 'buttons'),
			click: function click() {
				var email = jse.libs.emails.getEmailFromModal($modal);
				jse.libs.emails.sendCollection([email]).done(function (response) {
					$table.DataTable().ajax.reload();
					jse.libs.emails.getAttachmentsSize($('#attachments-size'));
				}).fail(function (response) {
					jse.libs.modal.message({
						title: jse.core.lang.translate('error', 'messages'),
						content: response.message
					});
				});
				$(this).dialog('close');
			}
		}];

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
	exports.colorizeButtonsForEditMode = function (event, ui) {
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
	exports.colorizeButtonsForPreviewMode = function (event, ui) {
		$(this).closest('.ui-dialog').find('.ui-button').eq(4).addClass('btn-primary'); // Send Button
	};

	/**
  * Deletes old attachments from selected removal date and before.
  *
  * @param {date} removalDate The date when the removal should start.
  * @param {object} ajaxUrl (optional) Specific ajaxUrl to be used for the request.
  * @returns {object} Returns a promise object to be used when the requests ends.
  */
	exports.deleteOldAttachments = function (removalDate, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DeleteOldAttachments';

		var deferred = $.Deferred(),
		    data = {
			pageToken: jse.core.config.get('pageToken'),
			removalDate: removalDate
		};

		$.post(ajaxUrl, data, function (response) {
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
	exports.getAttachmentsSize = function ($target, ajaxUrl) {
		ajaxUrl = ajaxUrl || jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/GetAttachmentsSize';

		var deferred = $.Deferred();

		$.get(ajaxUrl, function (response) {
			if (response.exception) {
				jse.libs.modal.message({
					title: jse.core.lang.translate('error', 'messages'),
					content: response.message
				});
				deferred.reject(response);
				return;
			}

			var size = response.size.megabytes !== 0 ? response.size.megabytes + ' MB' : response.size.bytes + ' bytes';

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
	exports.updateTabCounters = function ($modal, $contactsTable, $contactsTab, $attachmentsTable, $attachmentsTab) {
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
	exports.getSelectedEmails = function ($table) {
		$table = $table || $('#emails-table');

		var collection = [];

		$table.find('tr td input:checked').each(function (index, checkbox) {
			collection.push($(checkbox).parents('tr').data());
		});

		return collection;
	};
})(jse.libs.emails);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVtYWlscy5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwiZW1haWxzIiwiZXhwb3J0cyIsIkNPTlRBQ1RfVFlQRV9TRU5ERVIiLCJDT05UQUNUX1RZUEVfUkVDSVBJRU5UIiwiQ09OVEFDVF9UWVBFX1JFUExZX1RPIiwiQ09OVEFDVF9UWVBFX0JDQyIsIkNPTlRBQ1RfVFlQRV9DQyIsInJlc2V0TW9kYWwiLCIkbW9kYWwiLCJmaW5kIiwidmFsIiwicHJvcCIsInRyaWdnZXIiLCJsZW5ndGgiLCJEYXRhVGFibGUiLCJjbGVhciIsImRyYXciLCJjc3MiLCJzaG93IiwiZWFjaCIsIiQiLCJDS0VESVRPUiIsImluc3RhbmNlcyIsInVuZGVmaW5lZCIsImRlc3Ryb3kiLCJyZXBsYWNlIiwibGFuZ3VhZ2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0Iiwic2V0RGF0YSIsInVwZGF0ZVRhYkNvdW50ZXJzIiwiZ2V0RW1haWxGcm9tTW9kYWwiLCJlbWFpbCIsInNlbmRlciIsImVtYWlsX2FkZHJlc3MiLCJjb250YWN0X25hbWUiLCJjb250YWN0X3R5cGUiLCJyZWNpcGllbnQiLCJzdWJqZWN0IiwiY29udGVudF9odG1sIiwiZ2V0RGF0YSIsImVtYWlsX2lkIiwiaXNfcGVuZGluZyIsImNvbnRlbnRfcGxhaW4iLCJyZXBseV90byIsImJjYyIsImNjIiwiY29udGFjdHMiLCJyb3dzIiwiZGF0YSIsImluZGV4IiwiY29udGFjdCIsInR5cGUiLCJwdXNoIiwibmFtZSIsImF0dGFjaG1lbnRzIiwiYXR0YWNobWVudCIsImxvYWRFbWFpbE9uTW9kYWwiLCJjcmVhdGlvbl9kYXRlIiwic2VudF9kYXRlIiwicm93Iiwibm9ybWFsaXplIiwiZXNjYXBlSHRtbCIsImFkZCIsInBhdGgiLCJzZW5kQ29sbGVjdGlvbiIsImNvbGxlY3Rpb24iLCJhamF4VXJsIiwiZGVmZXJyZWQiLCJEZWZlcnJlZCIsInBhZ2VUb2tlbiIsInBvc3QiLCJyZXNwb25zZSIsImV4Y2VwdGlvbiIsInJlamVjdCIsInJlc29sdmUiLCJwcm9taXNlIiwicXVldWVDb2xsZWN0aW9uIiwiZGVsZXRlQ29sbGVjdGlvbiIsImdldERlZmF1bHRNb2RhbEJ1dHRvbnMiLCIkdGFibGUiLCJidXR0b25zIiwidGV4dCIsImxhbmciLCJ0cmFuc2xhdGUiLCJjbGljayIsImRpYWxvZyIsImRvbmUiLCJhamF4IiwicmVsb2FkIiwiZ2V0QXR0YWNobWVudHNTaXplIiwiZmFpbCIsIm1vZGFsIiwibWVzc2FnZSIsInRpdGxlIiwiY29udGVudCIsImdldFByZXZpZXdNb2RhbEJ1dHRvbnMiLCJtb2RhbE9wdGlvbnMiLCJjb2xvcml6ZUJ1dHRvbnNGb3JFZGl0TW9kZSIsImV2ZW50IiwidWkiLCJjbG9zZXN0IiwiZXEiLCJhZGRDbGFzcyIsImNvbG9yaXplQnV0dG9uc0ZvclByZXZpZXdNb2RlIiwiZGVsZXRlT2xkQXR0YWNobWVudHMiLCJyZW1vdmFsRGF0ZSIsIiR0YXJnZXQiLCJzaXplIiwibWVnYWJ5dGVzIiwiYnl0ZXMiLCIkY29udGFjdHNUYWJsZSIsIiRjb250YWN0c1RhYiIsIiRhdHRhY2htZW50c1RhYmxlIiwiJGF0dGFjaG1lbnRzVGFiIiwiY29udGFjdHNDb3VudCIsIm5ld0NvbnRhY3RzVGV4dCIsImF0dGFjaG1lbnRzQ291bnQiLCJuZXdBdHRhY2htZW50c1RleHQiLCJpbmRleE9mIiwiZ2V0U2VsZWN0ZWRFbWFpbHMiLCJjaGVja2JveCIsInBhcmVudHMiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxNQUFULEdBQWtCRixJQUFJQyxJQUFKLENBQVNDLE1BQVQsSUFBbUIsRUFBckM7O0FBRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTJCQSxDQUFDLFVBQVNDLE9BQVQsRUFBa0I7O0FBRWxCOztBQUVBQSxTQUFRQyxtQkFBUixHQUE4QixRQUE5QjtBQUNBRCxTQUFRRSxzQkFBUixHQUFpQyxXQUFqQztBQUNBRixTQUFRRyxxQkFBUixHQUFnQyxVQUFoQztB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QUFoQixFQUEwQjtBQUMvQnZELGNBQVdiLElBQVgsQ0FBZ0JsQyxFQUFFc0csUUFBRixFQUFZQyxPQUFaLENBQW9CLElBQXBCLEVBQTBCekUsSUFBMUIsRUFBaEI7QUFDQSxHQUpGOztBQU1BLFNBQU9pQixVQUFQO0FBQ0EsRUFaRDtBQWNBLENBbG9CRCxFQWtvQkdyRSxJQUFJQyxJQUFKLENBQVNDLE1BbG9CWiIsImZpbGUiOiJlbWFpbHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGVtYWlscy5qcyAyMDE2LTAyLTIyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMuZW1haWxzID0ganNlLmxpYnMuZW1haWxzIHx8IHt9O1xuXG4vKipcbiAqICMjIEVtYWlscyBMaWJyYXJ5XG4gKlxuICogVGhpcyBsaWJyYXJ5IGNvbnRhaW5zIGFsbCB0aGUgYWRtaW4vZW1haWxzIHBhZ2UgY29tbW9uIGZ1bmN0aW9uYWxpdHkgYW5kIGlzIHVzZWQgYnkgdGhlIHBhZ2UgXG4gKiBjb250cm9sbGVycy4gWW91IG1pZ2h0IGFsc28gdXNlIHRoaXMgbGlicmFyeSBpbiBvdGhlciBwYWdlcyB3aGVyZSB5b3UgbmVlZCB0byB0cmlnZ2VyIHNwZWNpZmljIFxuICogZW1haWwgb3BlcmF0aW9ucyBpbiB0aGUgc2VydmVyLlxuICpcbiAqIFlvdSB3aWxsIG5lZWQgdG8gcHJvdmlkZSB0aGUgZnVsbCBVUkwgaW4gb3JkZXIgdG8gbG9hZCB0aGlzIGxpYnJhcnkgYXMgYSBkZXBlbmRlbmN5IHRvIGEgbW9kdWxlOlxuICogXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiBneC5jb250cm9sbGVyLm1vZHVsZShcbiAqICAgJ215X2N1c3RvbV9wYWdlJyxcbiAqXG4gKiAgIFtcbiAqICAgICAgZ3guc291cmNlICsgJy9saWJzL2VtYWlscydcbiAqICAgXSxcbiAqXG4gKiAgIGZ1bmN0aW9uKGRhdGEpIHtcbiAqICAgICAgLy8gTW9kdWxlIGNvZGUgLi4uIFxuICogICB9KTtcbiAqYGBgXG4gKiBcbiAqIFJlcXVpcmVkIFRyYW5zbGF0aW9uIFNlY3Rpb25zOiAnYWRtaW5fbGFiZWxzJywgJ2J1dHRvbnMnLCAnZGJfYmFja3VwJywgJ2VtYWlscycsICdsaWdodGJveF9idXR0b25zJywgJ21lc3NhZ2VzJ1xuICpcbiAqIEBtb2R1bGUgQWRtaW4vTGlicy9lbWFpbHNcbiAqIEBleHBvcnRzIGpzZS5saWJzLmVtYWlsc1xuICovXG4oZnVuY3Rpb24oZXhwb3J0cykge1xuXHRcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0ZXhwb3J0cy5DT05UQUNUX1RZUEVfU0VOREVSID0gJ3NlbmRlcic7XG5cdGV4cG9ydHMuQ09OVEFDVF9UWVBFX1JFQ0lQSUVOVCA9ICdyZWNpcGllbnQnO1xuXHRleHBvcnRzLkNPTlRBQ1RfVFlQRV9SRVBMWV9UTyA9ICdyZXBseV90byc7XG5cdGV4cG9ydHMuQ09OVEFDVF9UWVBFX0JDQyA9ICdiY2MnO1xuXHRleHBvcnRzLkNPTlRBQ1RfVFlQRV9DQyA9ICdjYyc7XG5cdFxuXHQvKipcblx0ICogUmVzZXQgTW9kYWwgKERPTSlcblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd2lsbCByZXNldCB0aGUgZW1haWxzIG1vZGFsIGJhY2sgdG8gaXRzIGluaXRpYWwgc3RhdGUuIFRoZSBkZWZhdWx0XG5cdCAqIG1vZGFsIG1hcmt1cCBpcyB1c2VkIGluIHRoZSBhZG1pbi9lbWFpbHMgcGFnZSwgYnV0IHRoaXMgbWV0aG9kIGNhbiB3b3JrIHdpdGhvdXRcblx0ICogYWxsIHRoZSBlbGVtZW50cyB0b28uXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkbW9kYWwgalF1ZXJ5IHNlbGVjdG9yIGZvciB0aGUgbW9kYWwuXG5cdCAqL1xuXHRleHBvcnRzLnJlc2V0TW9kYWwgPSBmdW5jdGlvbigkbW9kYWwpIHtcblx0XHQvLyBDbGVhciBiYXNpYyBlbGVtZW50c1xuXHRcdCRtb2RhbC5maW5kKCdpbnB1dCwgdGV4dGFyZWEnKS52YWwoJycpO1xuXHRcdCRtb2RhbC5maW5kKCdzZWxlY3Qgb3B0aW9uOmZpcnN0JykucHJvcCgnc2VsZWN0ZWQnLCAnc2VsZWN0ZWQnKTtcblx0XHRcblx0XHQvLyBSZW1vdmUgdmFsaWRhdGlvbiBjbGFzc2VzLlxuXHRcdCRtb2RhbC50cmlnZ2VyKCd2YWxpZGF0b3IucmVzZXQnKTtcblx0XHRcblx0XHQvLyBSZW1vdmUgYWxsIHJvd3MgZnJvbSBEYXRhVGFibGVzLlxuXHRcdGlmICgkbW9kYWwuZmluZCgnLmRhdGFUYWJsZXNfd3JhcHBlcicpLmxlbmd0aCA+IDApIHtcblx0XHRcdCRtb2RhbC5maW5kKCcuZGF0YVRhYmxlc193cmFwcGVyIHRhYmxlJykuRGF0YVRhYmxlKCkuY2xlYXIoKS5kcmF3KCk7XG5cdFx0XHQkbW9kYWwuZmluZCgnLmRhdGFUYWJsZXNfd3JhcHBlcicpLmZpbmQoJy5kYXRhVGFibGVzX2xlbmd0aCBzZWxlY3Qgb3B0aW9uOmVxKDApJykucHJvcChcblx0XHRcdFx0J3NlbGVjdGVkJywgdHJ1ZSk7XG5cdFx0fVxuXHRcdFxuXHRcdC8vIFNldCBhbGwgdGFiIHdpZGdldHMgdG8gdGhlIGZpcnN0IHRhYi5cblx0XHRpZiAoJG1vZGFsLmZpbmQoJy50YWItaGVhZGxpbmUtd3JhcHBlcicpLmxlbmd0aCA+IDApIHtcblx0XHRcdCRtb2RhbC5maW5kKCcudGFiLWhlYWRsaW5lJykuY3NzKCdjb2xvcicsICcnKS5zaG93KCk7XG5cdFx0XHQkbW9kYWwuZmluZCgnLnRhYi1oZWFkbGluZS13cmFwcGVyJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0JCh0aGlzKS5maW5kKCdhOmVxKDApJykudHJpZ2dlcignY2xpY2snKTsgLy8gdG9nZ2xlIGZpcnN0IHRhYlxuXHRcdFx0fSk7XG5cdFx0fVxuXHRcdFxuXHRcdC8vIE5lZWQgdG8gcmVjcmVhdGUgdGhlIGNrZWRpdG9yIGluc3RhbmNlIGV2ZXJ5IHRpbWUgdGhlIG1vZGFsIGFwcGVhcnMuXG5cdFx0aWYgKCRtb2RhbC5maW5kKCcjY29udGVudC1odG1sJykubGVuZ3RoID4gMCkge1xuXHRcdFx0aWYgKENLRURJVE9SLmluc3RhbmNlc1snY29udGVudC1odG1sJ10gIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRDS0VESVRPUi5pbnN0YW5jZXNbJ2NvbnRlbnQtaHRtbCddLmRlc3Ryb3koKTtcblx0XHRcdH1cblx0XHRcdENLRURJVE9SLnJlcGxhY2UoJ2NvbnRlbnQtaHRtbCcsIHtcblx0XHRcdFx0bGFuZ3VhZ2U6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpXG5cdFx0XHR9KTtcblx0XHRcdENLRURJVE9SLmluc3RhbmNlc1snY29udGVudC1odG1sJ10uc2V0RGF0YSgnJyk7XG5cdFx0fVxuXHRcdFxuXHRcdC8vIElmIGNvbnRhY3QgdHlwZSBoaWRkZW4gaW5wdXRzIGFyZSBwcmVzZW50IHRoZW4gd2UgaGF2ZSB0byByZS1hcHBseSB0aGVpciB2YWx1ZS5cblx0XHRpZiAoJG1vZGFsLmZpbmQoJyNzZW5kZXItdHlwZScpLmxlbmd0aCA+IDApIHtcblx0XHRcdCRtb2RhbC5maW5kKCcjc2VuZGVyLXR5cGUnKS52YWwoJ3NlbmRlcicpO1xuXHRcdH1cblx0XHRpZiAoJG1vZGFsLmZpbmQoJyNyZWNpcGllbnQtdHlwZScpLmxlbmd0aCA+IDApIHtcblx0XHRcdCRtb2RhbC5maW5kKCcjcmVjaXBpZW50LXR5cGUnKS52YWwoJ3JlY2lwaWVudCcpO1xuXHRcdH1cblx0XHRpZiAoJG1vZGFsLmZpbmQoJyNyZXBseS10by10eXBlJykubGVuZ3RoID4gMCkge1xuXHRcdFx0JG1vZGFsLmZpbmQoJyNyZXBseS10by10eXBlJykudmFsKCdyZXBseV90bycpO1xuXHRcdH1cblx0XHRcblx0XHQvLyBVcGRhdGUgVGFiIENvdW50ZXJzXG5cdFx0anNlLmxpYnMuZW1haWxzLnVwZGF0ZVRhYkNvdW50ZXJzKCRtb2RhbCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyB0aGUgZW1haWwgaW5mb3JtYXRpb24gZnJvbSBtb2RhbCAoRE9NKS5cblx0ICpcblx0ICogVGhlIG1ldGhvZCB3aWxsIGdyYWIgdGhlIHZhbHVlcyBmcm9tIHRoZSBtb2RhbCBhbmQgYnVuZGxlIHRoZW0gaW4gYSBzaW5nbGUgb2JqZWN0LlxuXHQgKiBUaGUgcmV0dXJuZWQgb2JqZWN0IHdpbGwgaGF2ZSB0aGUgc2FtZSBzdHJ1Y3R1cmUgYXMgdGhlIHZhbHVlTWFwcGluZyBvYmplY3QuIFRoaXNcblx0ICogbWV0aG9kIGlzIHJlY3Vyc2l2ZS5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRtb2RhbCBqUXVlcnkgc2VsZWN0b3IgZm9yIHRoZSBtb2RhbC5cblx0ICpcblx0ICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIHRoZSBlbWFpbCBkYXRhIG9mIHRoZSBtb2RhbC5cblx0ICovXG5cdGV4cG9ydHMuZ2V0RW1haWxGcm9tTW9kYWwgPSBmdW5jdGlvbigkbW9kYWwpIHtcblx0XHR2YXIgZW1haWwgPSB7fTtcblx0XHRcblx0XHQvLyBSZXF1aXJlZCBFbWFpbCBGaWVsZHNcblx0XHRlbWFpbC5zZW5kZXIgPSB7XG5cdFx0XHRlbWFpbF9hZGRyZXNzOiAkbW9kYWwuZmluZCgnI3NlbmRlci1lbWFpbCcpLnZhbCgpLFxuXHRcdFx0Y29udGFjdF9uYW1lOiAkbW9kYWwuZmluZCgnI3NlbmRlci1uYW1lJykudmFsKCksXG5cdFx0XHRjb250YWN0X3R5cGU6IGV4cG9ydHMuQ09OVEFDVF9UWVBFX1NFTkRFUlxuXHRcdH07XG5cdFx0XG5cdFx0ZW1haWwucmVjaXBpZW50ID0ge1xuXHRcdFx0ZW1haWxfYWRkcmVzczogJG1vZGFsLmZpbmQoJyNyZWNpcGllbnQtZW1haWwnKS52YWwoKSxcblx0XHRcdGNvbnRhY3RfbmFtZTogJG1vZGFsLmZpbmQoJyNyZWNpcGllbnQtbmFtZScpLnZhbCgpLFxuXHRcdFx0Y29udGFjdF90eXBlOiBleHBvcnRzLkNPTlRBQ1RfVFlQRV9SRUNJUElFTlRcblx0XHR9O1xuXHRcdFxuXHRcdGVtYWlsLnN1YmplY3QgPSAkbW9kYWwuZmluZCgnI3N1YmplY3QnKS52YWwoKTtcblx0XHRlbWFpbC5jb250ZW50X2h0bWwgPSBDS0VESVRPUi5pbnN0YW5jZXNbJ2NvbnRlbnQtaHRtbCddLmdldERhdGEoKTtcblx0XHRcblx0XHQvLyBPcHRpb25hbCBFbWFpbCBmaWVsZHNcblx0XHRlbWFpbC5lbWFpbF9pZCA9ICgkbW9kYWwuZmluZCgnI2VtYWlsLWlkJykudmFsKCkgIT09ICcnKSA/ICRtb2RhbC5maW5kKCcjZW1haWwtaWQnKS52YWwoKSA6XG5cdFx0ICAgICAgICAgICAgICAgICBudWxsO1xuXHRcdGVtYWlsLmlzX3BlbmRpbmcgPSAoJG1vZGFsLmZpbmQoJyNpcy1wZW5kaW5nJykudmFsKCkgPT09ICd0cnVlJyk7XG5cdFx0ZW1haWwuY29udGVudF9wbGFpbiA9ICgkbW9kYWwuZmluZCgnI2NvbnRlbnQtcGxhaW4nKS52YWwoKSAhPT0gJycpID8gJG1vZGFsLmZpbmQoXG5cdFx0XHQnI2NvbnRlbnQtcGxhaW4nKS52YWwoKSA6IG51bGw7XG5cdFx0XG5cdFx0ZW1haWwucmVwbHlfdG8gPSAoJG1vZGFsLmZpbmQoJyNyZXBseS10by1lbWFpbCcpLnZhbCgpICE9PSAnJykgPyB7fSA6IG51bGw7XG5cdFx0aWYgKGVtYWlsLnJlcGx5X3RvKSB7XG5cdFx0XHRlbWFpbC5yZXBseV90by5lbWFpbF9hZGRyZXNzID0gJG1vZGFsLmZpbmQoJyNyZXBseS10by1lbWFpbCcpLnZhbCgpO1xuXHRcdFx0ZW1haWwucmVwbHlfdG8uY29udGFjdF9uYW1lID0gJG1vZGFsLmZpbmQoJyNyZXBseS10by1uYW1lJykudmFsKCk7XG5cdFx0XHRlbWFpbC5yZXBseV90by5jb250YWN0X3R5cGUgPSBleHBvcnRzLkNPTlRBQ1RfVFlQRV9SRVBMWV9UTztcblx0XHR9XG5cdFx0XG5cdFx0Ly8gQkNDICYgQ0MgQ29udGFjdHNcblx0XHRlbWFpbC5iY2MgPSBudWxsO1xuXHRcdGVtYWlsLmNjID0gbnVsbDtcblx0XHR2YXIgY29udGFjdHMgPSAkbW9kYWwuZmluZCgnI2NvbnRhY3RzLXRhYmxlJykuRGF0YVRhYmxlKCkucm93cygpLmRhdGEoKTtcblx0XHRcblx0XHQkLmVhY2goY29udGFjdHMsIGZ1bmN0aW9uKGluZGV4LCBjb250YWN0KSB7XG5cdFx0XHRpZiAoZW1haWxbY29udGFjdC50eXBlXSA9PSBudWxsKSB7XG5cdFx0XHRcdGVtYWlsW2NvbnRhY3QudHlwZV0gPSBbXTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZW1haWxbY29udGFjdC50eXBlXS5wdXNoKHtcblx0XHRcdFx0ZW1haWxfYWRkcmVzczogY29udGFjdC5lbWFpbCxcblx0XHRcdFx0Y29udGFjdF9uYW1lOiBjb250YWN0Lm5hbWUsXG5cdFx0XHRcdGNvbnRhY3RfdHlwZTogY29udGFjdC50eXBlXG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvLyBBdHRhY2htZW50c1xuXHRcdGVtYWlsLmF0dGFjaG1lbnRzID0gbnVsbDtcblx0XHR2YXIgYXR0YWNobWVudHMgPSAkbW9kYWwuZmluZCgnI2F0dGFjaG1lbnRzLXRhYmxlJykuRGF0YVRhYmxlKCkucm93cygpLmRhdGEoKTtcblx0XHQkLmVhY2goYXR0YWNobWVudHMsIGZ1bmN0aW9uKGluZGV4LCBhdHRhY2htZW50KSB7XG5cdFx0XHRpZiAoZW1haWwuYXR0YWNobWVudHMgPT09IG51bGwpIHtcblx0XHRcdFx0ZW1haWwuYXR0YWNobWVudHMgPSBbXTtcblx0XHRcdH1cblx0XHRcdGVtYWlsLmF0dGFjaG1lbnRzLnB1c2goYXR0YWNobWVudCk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0cmV0dXJuIGVtYWlsO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIExvYWRzIGVtYWlsIGRhdGEgb24gbW9kYWwgKERPTSkuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBlbWFpbCBDb250YWlucyB0aGUgZW1haWwgZGF0YS5cblx0ICogQHBhcmFtIHtvYmplY3R9ICRtb2RhbCBqUXVlcnkgc2VsZWN0b3IgZm9yIHRoZSBtb2RhbC5cblx0ICovXG5cdGV4cG9ydHMubG9hZEVtYWlsT25Nb2RhbCA9IGZ1bmN0aW9uKGVtYWlsLCAkbW9kYWwpIHtcblx0XHQvLyBSZXF1aXJlZCBFbWFpbCBGaWVsZHNcblx0XHQkbW9kYWwuZmluZCgnI3NlbmRlci1lbWFpbCcpLnZhbChlbWFpbC5zZW5kZXIuZW1haWxfYWRkcmVzcyk7XG5cdFx0JG1vZGFsLmZpbmQoJyNzZW5kZXItbmFtZScpLnZhbChlbWFpbC5zZW5kZXIuY29udGFjdF9uYW1lKTtcblx0XHRcblx0XHQkbW9kYWwuZmluZCgnI3JlY2lwaWVudC1lbWFpbCcpLnZhbChlbWFpbC5yZWNpcGllbnQuZW1haWxfYWRkcmVzcyk7XG5cdFx0JG1vZGFsLmZpbmQoJyNyZWNpcGllbnQtbmFtZScpLnZhbChlbWFpbC5yZWNpcGllbnQuY29udGFjdF9uYW1lKTtcblx0XHRcblx0XHQkbW9kYWwuZmluZCgnI3N1YmplY3QnKS52YWwoZW1haWwuc3ViamVjdCk7XG5cdFx0Q0tFRElUT1IuaW5zdGFuY2VzWydjb250ZW50LWh0bWwnXS5zZXREYXRhKGVtYWlsLmNvbnRlbnRfaHRtbCk7XG5cdFx0XG5cdFx0JG1vZGFsLmZpbmQoJyNpcy1wZW5kaW5nJykudmFsKChlbWFpbC5pc19wZW5kaW5nKSA/ICd0cnVlJyA6ICdmYWxzZScpO1xuXHRcdFxuXHRcdC8vIE9wdGlvbmFsIEVtYWlsIEZpZWxkc1xuXHRcdFxuXHRcdGlmIChlbWFpbC5lbWFpbF9pZCAhPT0gbnVsbCkge1xuXHRcdFx0JG1vZGFsLmZpbmQoJyNlbWFpbC1pZCcpLnZhbChlbWFpbC5lbWFpbF9pZCk7XG5cdFx0fVxuXHRcdFxuXHRcdGlmIChlbWFpbC5jcmVhdGlvbl9kYXRlICE9PSBudWxsKSB7XG5cdFx0XHQkbW9kYWwuZmluZCgnI2NyZWF0aW9uLWRhdGUnKS52YWwoZW1haWwuY3JlYXRpb25fZGF0ZSk7XG5cdFx0fVxuXHRcdFxuXHRcdGlmIChlbWFpbC5zZW50X2RhdGUgIT09IG51bGwpIHtcblx0XHRcdCRtb2RhbC5maW5kKCcjc2VudC1kYXRlJykudmFsKGVtYWlsLnNlbnRfZGF0ZSk7XG5cdFx0fVxuXHRcdFxuXHRcdGlmIChlbWFpbC5yZXBseV90byAhPT0gbnVsbCkge1xuXHRcdFx0JG1vZGFsLmZpbmQoJyNyZXBseS10by1lbWFpbCcpLnZhbChlbWFpbC5yZXBseV90by5lbWFpbF9hZGRyZXNzKTtcblx0XHRcdCRtb2RhbC5maW5kKCcjcmVwbHktdG8tbmFtZScpLnZhbChlbWFpbC5yZXBseV90by5jb250YWN0X25hbWUpO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoZW1haWwuY29udGVudF9wbGFpbiAhPT0gbnVsbCkge1xuXHRcdFx0JG1vZGFsLmZpbmQoJyNjb250ZW50LXBsYWluJykudmFsKGVtYWlsLmNvbnRlbnRfcGxhaW4pO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoZW1haWwuYmNjICE9PSBudWxsKSB7XG5cdFx0XHQkLmVhY2goZW1haWwuYmNjLCBmdW5jdGlvbihpbmRleCwgY29udGFjdCkge1xuXHRcdFx0XHR2YXIgcm93ID0ge1xuXHRcdFx0XHRcdGVtYWlsOiBqc2UubGlicy5ub3JtYWxpemUuZXNjYXBlSHRtbChjb250YWN0LmVtYWlsX2FkZHJlc3MpLFxuXHRcdFx0XHRcdG5hbWU6IGpzZS5saWJzLm5vcm1hbGl6ZS5lc2NhcGVIdG1sKGNvbnRhY3QuY29udGFjdF9uYW1lKSxcblx0XHRcdFx0XHR0eXBlOiBqc2UubGlicy5ub3JtYWxpemUuZXNjYXBlSHRtbChjb250YWN0LmNvbnRhY3RfdHlwZSlcblx0XHRcdFx0fTtcblx0XHRcdFx0JG1vZGFsLmZpbmQoJyNjb250YWN0cy10YWJsZScpLkRhdGFUYWJsZSgpLnJvdy5hZGQocm93KS5kcmF3KCk7XG5cdFx0XHR9KTtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKGVtYWlsLmNjICE9PSBudWxsKSB7XG5cdFx0XHQkLmVhY2goZW1haWwuY2MsIGZ1bmN0aW9uKGluZGV4LCBjb250YWN0KSB7XG5cdFx0XHRcdHZhciByb3cgPSB7XG5cdFx0XHRcdFx0ZW1haWw6IGpzZS5saWJzLm5vcm1hbGl6ZS5lc2NhcGVIdG1sKGNvbnRhY3QuZW1haWxfYWRkcmVzcyksXG5cdFx0XHRcdFx0bmFtZToganNlLmxpYnMubm9ybWFsaXplLmVzY2FwZUh0bWwoY29udGFjdC5jb250YWN0X25hbWUpLFxuXHRcdFx0XHRcdHR5cGU6IGpzZS5saWJzLm5vcm1hbGl6ZS5lc2NhcGVIdG1sKGNvbnRhY3QuY29udGFjdF90eXBlKVxuXHRcdFx0XHR9O1xuXHRcdFx0XHQkbW9kYWwuZmluZCgnI2NvbnRhY3RzLXRhYmxlJykuRGF0YVRhYmxlKCkucm93LmFkZChyb3cpLmRyYXcoKTtcblx0XHRcdH0pO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoZW1haWwuYXR0YWNobWVudHMgIT09IG51bGwpIHtcblx0XHRcdCQuZWFjaChlbWFpbC5hdHRhY2htZW50cywgZnVuY3Rpb24oaW5kZXgsIGF0dGFjaG1lbnQpIHtcblx0XHRcdFx0YXR0YWNobWVudC5wYXRoID0ganNlLmxpYnMubm9ybWFsaXplLmVzY2FwZUh0bWwoYXR0YWNobWVudC5wYXRoKTtcblx0XHRcdFx0JG1vZGFsLmZpbmQoJyNhdHRhY2htZW50cy10YWJsZScpLkRhdGFUYWJsZSgpLnJvdy5hZGQoYXR0YWNobWVudCkuZHJhdygpO1xuXHRcdFx0fSk7XG5cdFx0fVxuXHRcdFxuXHRcdC8vIFVwZGF0ZSBUYWIgQ291bnRlcnNcblx0XHRqc2UubGlicy5lbWFpbHMudXBkYXRlVGFiQ291bnRlcnMoJG1vZGFsKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBTZW5kcyBhbiBlbWFpbCBjb2xsZWN0aW9uXG5cdCAqXG5cdCAqIFByb3ZpZGUgYW4gYXJyYXkgb2YgZW1haWwgb2JqZWN0cyBhbmQgdGhpcyBtZXRob2Qgd2lsbCBzZW5kIHRoZW0gdG8gdGhlIHJlcXVlc3RlZFxuXHQgKiBVUkwgdGhyb3VnaCBBSkFYIFBPU1QuIFlvdSBjYW4gb21pdCB0aGUgdXJsIGFuZCB0aGUgZGVmYXVsdCBFbWFpbHNDb250cm9sbGVyIHdpbGxcblx0ICogYmUgdXNlZC5cblx0ICpcblx0ICogQHBhcmFtIHthcnJheX0gY29sbGVjdGlvbiBBcnJheSBvZiBlbWFpbCBvYmplY3RzLlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gYWpheFVybCAob3B0aW9uYWwpIFRoZSBBSkFYIFVSTCBmb3IgdGhlIFBPU1QgcmVxdWVzdC5cblx0ICpcblx0ICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QgdGhhdCB3aWxsIHByb3ZpZGUgdGhlIHNlcnZlcidzIHJlc3BvbnNlLlxuXHQgKi9cblx0ZXhwb3J0cy5zZW5kQ29sbGVjdGlvbiA9IGZ1bmN0aW9uKGNvbGxlY3Rpb24sIGFqYXhVcmwpIHtcblx0XHRhamF4VXJsID0gYWpheFVybCB8fCBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPUVtYWlscy9TZW5kJztcblx0XHRcblx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRkYXRhID0ge1xuXHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpLFxuXHRcdFx0XHRjb2xsZWN0aW9uOiBjb2xsZWN0aW9uXG5cdFx0XHR9O1xuXHRcdFxuXHRcdCQucG9zdChhamF4VXJsLCBkYXRhLCBmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0aWYgKHJlc3BvbnNlLmV4Y2VwdGlvbikge1xuXHRcdFx0XHRkZWZlcnJlZC5yZWplY3QocmVzcG9uc2UpO1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRkZWZlcnJlZC5yZXNvbHZlKHJlc3BvbnNlKTtcblx0XHR9LCAnanNvbicpO1xuXHRcdFxuXHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUXVldWVzIHRoZSBlbWFpbCBjb2xsZWN0aW9uXG5cdCAqXG5cdCAqIFByb3ZpZGUgYW4gYXJyYXkgb2YgZW1haWwgb2JqZWN0cyBhbmQgdGhpcyBtZXRob2Qgd2lsbCBxdWV1ZSB0aGVtIHRvIHRoZSByZXF1ZXN0ZWRcblx0ICogVVJMIHRocm91Z2ggQUpBWCBQT1NULiBZb3UgY2FuIG9taXQgdGhlIHVybCBhbmQgdGhlIGRlZmF1bHQgRW1haWxzQ29udHJvbGxlciB3aWxsXG5cdCAqIGJlIHVzZWQuXG5cdCAqXG5cdCAqIEBwYXJhbSB7YXJyYXl9IGNvbGxlY3Rpb24gQXJyYXkgb2YgZW1haWwgb2JqZWN0cy5cblx0ICogQHBhcmFtIHtzdHJpbmd9IGFqYXhVcmwgKG9wdGlvbmFsKSBUaGUgQUpBWCBVUkwgZm9yIHRoZSBQT1NUIHJlcXVlc3QuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRoYXQgd2lsbCBwcm92aWRlIHRoZSBzZXJ2ZXIncyByZXNwb25zZS5cblx0ICovXG5cdGV4cG9ydHMucXVldWVDb2xsZWN0aW9uID0gZnVuY3Rpb24oY29sbGVjdGlvbiwgYWpheFVybCkge1xuXHRcdGFqYXhVcmwgPSBhamF4VXJsIHx8IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89RW1haWxzL1F1ZXVlJztcblx0XHRcblx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRkYXRhID0ge1xuXHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpLFxuXHRcdFx0XHRjb2xsZWN0aW9uOiBjb2xsZWN0aW9uXG5cdFx0XHR9O1xuXHRcdFxuXHRcdCQucG9zdChhamF4VXJsLCBkYXRhLCBmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0aWYgKHJlc3BvbnNlLmV4Y2VwdGlvbikge1xuXHRcdFx0XHRkZWZlcnJlZC5yZWplY3QocmVzcG9uc2UpO1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRkZWZlcnJlZC5yZXNvbHZlKHJlc3BvbnNlKTtcblx0XHR9LCAnanNvbicpO1xuXHRcdFxuXHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogRGVsZXRlcyBhbiBlbWFpbCBjb2xsZWN0aW9uXG5cdCAqXG5cdCAqIFByb3ZpZGUgYW4gYXJyYXkgb2YgZW1haWwgb2JqZWN0cyBhbmQgdGhpcyBtZXRob2Qgd2lsbCBkZWxldGUgdGhlbSB0byB0aGUgcmVxdWVzdGVkXG5cdCAqIFVSTCB0aHJvdWdoIEFKQVggUE9TVC4gWW91IGNhbiBvbWl0IHRoZSB1cmwgYW5kIHRoZSBkZWZhdWx0IEVtYWlsc0NvbnRyb2xsZXIgd2lsbFxuXHQgKiBiZSB1c2VkLlxuXHQgKlxuXHQgKiBAcGFyYW0ge2FycmF5fSBjb2xsZWN0aW9uIEFycmF5IG9mIGVtYWlsIG9iamVjdHMuXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBhamF4VXJsIChvcHRpb25hbCkgVGhlIEFKQVggVVJMIGZvciB0aGUgUE9TVCByZXF1ZXN0LlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdCB0aGF0IHdpbGwgcHJvdmlkZSB0aGUgc2VydmVyJ3MgcmVzcG9uc2UuXG5cdCAqL1xuXHRleHBvcnRzLmRlbGV0ZUNvbGxlY3Rpb24gPSBmdW5jdGlvbihjb2xsZWN0aW9uLCBhamF4VXJsKSB7XG5cdFx0YWpheFVybCA9IGFqYXhVcmwgfHwganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2FkbWluLnBocD9kbz1FbWFpbHMvRGVsZXRlJztcblx0XHRcblx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRkYXRhID0ge1xuXHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpLFxuXHRcdFx0XHRjb2xsZWN0aW9uOiBjb2xsZWN0aW9uXG5cdFx0XHR9O1xuXHRcdFxuXHRcdCQucG9zdChhamF4VXJsLCBkYXRhLCBmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0aWYgKHJlc3BvbnNlLmV4Y2VwdGlvbikge1xuXHRcdFx0XHRkZWZlcnJlZC5yZWplY3QocmVzcG9uc2UpO1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRkZWZlcnJlZC5yZXNvbHZlKHJlc3BvbnNlKTtcblx0XHR9LCAnanNvbicpO1xuXHRcdFxuXHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyBkZWZhdWx0IG1vZGFsIGJ1dHRvbnNcblx0ICpcblx0ICogVXNlZCBieSB2YXJpb3VzIHNlY3Rpb25zIG9mIHRoZSBhZG1pbi9lbWFpbHMgcGFnZS4gV2l0aCB0aGUgcHJvcGVyIHVzZSBvZiB2YWx1ZU1hcHBpbmcgb2JqZWN0XG5cdCAqIHlvdSBjYW4gdXNlIHRoaXMgbWV0aG9kIGluIG90aGVyIHBhZ2VzIHRvby5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRtb2RhbCBqUXVlcnkgc2VsZWN0b3IgZm9yIHRoZSBtb2RhbC5cblx0ICogQHBhcmFtIHtvYmplY3R9ICR0YWJsZSBqUXVlcnkgc2VsZWN0b3IgZm9yIHRoZSBtYWluIHRhYmxlLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgdGhlIGRpYWxvZyBtb2RhbCBidXR0b25zLlxuXHQgKi9cblx0ZXhwb3J0cy5nZXREZWZhdWx0TW9kYWxCdXR0b25zID0gZnVuY3Rpb24oJG1vZGFsLCAkdGFibGUpIHtcblx0XHR2YXIgYnV0dG9ucyA9IFtcblx0XHRcdHtcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0e1xuXHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncXVldWUnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JG1vZGFsLmZpbmQoJy50YWItY29udGVudC5kZXRhaWxzJykudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJyk7XG5cdFx0XHRcdFx0aWYgKCRtb2RhbC5maW5kKCcudGFiLWNvbnRlbnQuZGV0YWlscyAuZXJyb3InKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm47IC8vIFRoZXJlIGFyZSBmaWVsZHMgd2l0aCBlcnJvcnMuXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdHZhciBlbWFpbCA9IGpzZS5saWJzLmVtYWlscy5nZXRFbWFpbEZyb21Nb2RhbCgkbW9kYWwpO1xuXHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5xdWV1ZUNvbGxlY3Rpb24oW2VtYWlsXSlcblx0XHRcdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdCR0YWJsZS5EYXRhVGFibGUoKS5hamF4LnJlbG9hZCgpO1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuZ2V0QXR0YWNobWVudHNTaXplKCQoJyNhdHRhY2htZW50cy1zaXplJykpO1xuXHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKSxcblx0XHRcdFx0XHRcdFx0XHRjb250ZW50OiByZXNwb25zZS5tZXNzYWdlXG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHR7XG5cdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzZW5kJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCRtb2RhbC5maW5kKCcudGFiLWNvbnRlbnQuZGV0YWlscycpLnRyaWdnZXIoJ3ZhbGlkYXRvci52YWxpZGF0ZScpO1xuXHRcdFx0XHRcdGlmICgkbW9kYWwuZmluZCgnLnRhYi1jb250ZW50LmRldGFpbHMgLmVycm9yJykubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdFx0cmV0dXJuOyAvLyBUaGVyZSBhcmUgZmllbGRzIHdpdGggZXJyb3JzLlxuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHR2YXIgZW1haWwgPSBqc2UubGlicy5lbWFpbHMuZ2V0RW1haWxGcm9tTW9kYWwoJG1vZGFsKTtcblx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuc2VuZENvbGxlY3Rpb24oW2VtYWlsXSlcblx0XHRcdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdCR0YWJsZS5EYXRhVGFibGUoKS5hamF4LnJlbG9hZCgpO1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuZ2V0QXR0YWNobWVudHNTaXplKCQoJyNhdHRhY2htZW50cy1zaXplJykpO1xuXHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKSxcblx0XHRcdFx0XHRcdFx0XHRjb250ZW50OiByZXNwb25zZS5tZXNzYWdlXG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRdO1xuXHRcdFxuXHRcdHJldHVybiBidXR0b25zO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJldHVybnMgcHJldmlldyBtb2RhbCBidXR0b25zXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgcmV0dXJuIHRoZSBwcmV2aWV3IG1vZGFsIGJ1dHRvbnMgZm9yIHRoZSBqUXVlcnkgVUkgZGlhbG9nIHdpZGdldC4gV2l0aCB0aGUgcHJvcGVyXG5cdCAqIHVzZSBvZiB2YWx1ZU1hcHBpbmcgb2JqZWN0IHlvdSBjYW4gdXNlIHRoaXMgbWV0aG9kIGluIG90aGVyIHBhZ2VzIHRvby5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRtb2RhbCBqUXVlcnkgc2VsZWN0b3IgZm9yIHRoZSBtb2RhbC5cblx0ICogQHBhcmFtIHtvYmplY3R9ICR0YWJsZSBqUXVlcnkgc2VsZWN0b3IgZm9yIHRoZSBtYWluIHRhYmxlLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgdGhlIGRpYWxvZyBtb2RhbCBidXR0b25zLlxuXHQgKi9cblx0ZXhwb3J0cy5nZXRQcmV2aWV3TW9kYWxCdXR0b25zID0gZnVuY3Rpb24oJG1vZGFsLCAkdGFibGUpIHtcblx0XHR2YXIgYnV0dG9ucyA9IFtcblx0XHRcdHtcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0e1xuXHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZGVsZXRlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciBtb2RhbE9wdGlvbnMgPSB7XG5cdFx0XHRcdFx0XHR0aXRsZTogJ0RlbGV0ZSBFbWFpbCBSZWNvcmQnLFxuXHRcdFx0XHRcdFx0Y29udGVudDogJ0FyZSB5b3Ugc3VyZSB0aGF0IHlvdSB3YW50IHRvIGRlbGV0ZSB0aGlzIGVtYWlsIHJlY29yZD8nLFxuXHRcdFx0XHRcdFx0YnV0dG9uczogW1xuXHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0dmFyIGVtYWlsID0ganNlLmxpYnMuZW1haWxzLmdldEVtYWlsRnJvbU1vZGFsKCRtb2RhbCk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5kZWxldGVDb2xsZWN0aW9uKFtlbWFpbF0pXG5cdFx0XHRcdFx0XHRcdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHRhYmxlLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0anNlLmxpYnMuZW1haWxzLmdldEF0dGFjaG1lbnRzU2l6ZSgkKCcjYXR0YWNobWVudHMtc2l6ZScpKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdFx0XHRcdFx0LmZhaWwoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRqc2UubGlicy5tb2RhbC5tZXNzYWdlKHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnbWVzc2FnZXMnKSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQ6IHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdFx0XHRcdCRtb2RhbC5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ25vJywgJ2xpZ2h0Ym94X2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdF1cblx0XHRcdFx0XHR9O1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2UobW9kYWxPcHRpb25zKTtcblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdHtcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3F1ZXVlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciBlbWFpbCA9IGpzZS5saWJzLmVtYWlscy5nZXRFbWFpbEZyb21Nb2RhbCgkbW9kYWwpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIER1cGxpY2F0ZSByZWNvcmQgb25seSBpZiB0aGUgb3JpZ2luYWwgb25lIGlzIGFscmVhZHkgc2VudC5cblx0XHRcdFx0XHQvLyBPdGhlcndpc2Ugd2UganVzdCBuZWVkIHRvIHVwZGF0ZSB0aGUgZGF0YSBvZiB0aGUgY3VycmVudCBlbWFpbCByZWNvcmQuXG5cdFx0XHRcdFx0aWYgKCFlbWFpbC5pc19wZW5kaW5nKSB7XG5cdFx0XHRcdFx0XHRkZWxldGUgZW1haWwuZW1haWxfaWQ7IC8vIHdpbGwgZHVwbGljYXRlIHRoZSByZWNvcmRcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0anNlLmxpYnMuZW1haWxzLnF1ZXVlQ29sbGVjdGlvbihbZW1haWxdKVxuXHRcdFx0XHRcdFx0LmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdFx0JHRhYmxlLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XG5cdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5nZXRBdHRhY2htZW50c1NpemUoJCgnI2F0dGFjaG1lbnRzLXNpemUnKSk7XG5cdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0LmZhaWwoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG5cdFx0XHRcdFx0XHRcdFx0dGl0bGU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdlcnJvcicsICdtZXNzYWdlcycpLFxuXHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQ6IHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdHtcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3NlbmQnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyIGVtYWlsID0ganNlLmxpYnMuZW1haWxzLmdldEVtYWlsRnJvbU1vZGFsKCRtb2RhbCk7XG5cdFx0XHRcdFx0anNlLmxpYnMuZW1haWxzLnNlbmRDb2xsZWN0aW9uKFtlbWFpbF0pXG5cdFx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcblx0XHRcdFx0XHRcdFx0anNlLmxpYnMuZW1haWxzLmdldEF0dGFjaG1lbnRzU2l6ZSgkKCcjYXR0YWNobWVudHMtc2l6ZScpKTtcblx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHQuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy5tb2RhbC5tZXNzYWdlKHtcblx0XHRcdFx0XHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyksXG5cdFx0XHRcdFx0XHRcdFx0Y29udGVudDogcmVzcG9uc2UubWVzc2FnZVxuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XTtcblx0XHRcblx0XHRyZXR1cm4gYnV0dG9ucztcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBDb2xvcml6ZXMgbW9kYWwgYnV0dG9ucyBmb3IgdGhlIGVkaXQgbW9kZVxuXHQgKlxuXHQgKiBqUXVlcnkgVUkgZG9lcyBub3Qgc3VwcG9ydCBkaXJlY3QgYWRkaXRpb24gb2YgY2xhc3NlcyB0byB0aGUgZGlhbG9nIGJ1dHRvbnMsXG5cdCAqIHNvIHdlIG5lZWQgdG8gYXBwbHkgdGhlIGNsYXNzZXMgZHVyaW5nIHRoZSBcImNyZWF0ZVwiIGV2ZW50IG9mIHRoZSBkaWFsb2cuXG5cdCAqXG5cdCAqIEBwYXJhbSBldmVudCB7ZXZlbnR9IEV2ZW50IHRvIHRyaWdnZXIgdGhpcyBmdW5jdGlvbi5cblx0ICogQHBhcmFtIHVpIHtvYmplY3R9IERpYWxvZyBVSS5cblx0ICovXG5cdGV4cG9ydHMuY29sb3JpemVCdXR0b25zRm9yRWRpdE1vZGUgPSBmdW5jdGlvbihldmVudCwgdWkpIHtcblx0XHQkKHRoaXMpLmNsb3Nlc3QoJy51aS1kaWFsb2cnKS5maW5kKCcudWktYnV0dG9uJykuZXEoMykuYWRkQ2xhc3MoJ2J0bi1wcmltYXJ5Jyk7IC8vIFNlbmQgQnV0dG9uXG5cdH07XG5cdFxuXHQvKipcblx0ICogQ29sb3JpemVzIG1vZGFsIGJ1dHRvbnMgZm9yIHByZXZpZXcgbW9kZVxuXHQgKlxuXHQgKiBqUXVlcnkgVUkgZG9lcyBub3Qgc3VwcG9ydCBkaXJlY3QgYWRkaXRpb24gb2YgY2xhc3NlcyB0byB0aGUgZGlhbG9nIGJ1dHRvbnMsXG5cdCAqIHNvIHdlIG5lZWQgdG8gYXBwbHkgdGhlIGNsYXNzZXMgZHVyaW5nIHRoZSBcImNyZWF0ZVwiIGV2ZW50IG9mIHRoZSBkaWFsb2cuXG5cdCAqXG5cdCAqIEBwYXJhbSBldmVudCB7b2JqZWN0fSBFdmVudCB0byB0cmlnZ2VyIHRoaXMgZnVuY3Rpb24uXG5cdCAqIEBwYXJhbSB1aSB7b2JqZWN0fSBEaWFsb2cgVUkuXG5cdCAqL1xuXHRleHBvcnRzLmNvbG9yaXplQnV0dG9uc0ZvclByZXZpZXdNb2RlID0gZnVuY3Rpb24oZXZlbnQsIHVpKSB7XG5cdFx0JCh0aGlzKS5jbG9zZXN0KCcudWktZGlhbG9nJykuZmluZCgnLnVpLWJ1dHRvbicpLmVxKDQpLmFkZENsYXNzKCdidG4tcHJpbWFyeScpOyAvLyBTZW5kIEJ1dHRvblxuXHR9O1xuXHRcblx0LyoqXG5cdCAqIERlbGV0ZXMgb2xkIGF0dGFjaG1lbnRzIGZyb20gc2VsZWN0ZWQgcmVtb3ZhbCBkYXRlIGFuZCBiZWZvcmUuXG5cdCAqXG5cdCAqIEBwYXJhbSB7ZGF0ZX0gcmVtb3ZhbERhdGUgVGhlIGRhdGUgd2hlbiB0aGUgcmVtb3ZhbCBzaG91bGQgc3RhcnQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBhamF4VXJsIChvcHRpb25hbCkgU3BlY2lmaWMgYWpheFVybCB0byBiZSB1c2VkIGZvciB0aGUgcmVxdWVzdC5cblx0ICogQHJldHVybnMge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRvIGJlIHVzZWQgd2hlbiB0aGUgcmVxdWVzdHMgZW5kcy5cblx0ICovXG5cdGV4cG9ydHMuZGVsZXRlT2xkQXR0YWNobWVudHMgPSBmdW5jdGlvbihyZW1vdmFsRGF0ZSwgYWpheFVybCkge1xuXHRcdGFqYXhVcmwgPSBhamF4VXJsIHx8IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89RW1haWxzL0RlbGV0ZU9sZEF0dGFjaG1lbnRzJztcblx0XHRcblx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRkYXRhID0ge1xuXHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpLFxuXHRcdFx0XHRyZW1vdmFsRGF0ZTogcmVtb3ZhbERhdGVcblx0XHRcdH07XG5cdFx0XG5cdFx0JC5wb3N0KGFqYXhVcmwsIGRhdGEsIGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRpZiAocmVzcG9uc2UuZXhjZXB0aW9uKSB7XG5cdFx0XHRcdGRlZmVycmVkLnJlamVjdChyZXNwb25zZSk7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblx0XHRcdGRlZmVycmVkLnJlc29sdmUocmVzcG9uc2UpO1xuXHRcdH0sICdqc29uJyk7XG5cdFx0XG5cdFx0cmV0dXJuIGRlZmVycmVkLnByb21pc2UoKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXR1cm5zIHRoZSBhdHRhY2htZW50cyBzaXplIGluIE1CIGFuZCByZWZyZXNoZXMgdGhlIFVJLlxuXHQgKlxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIG1ha2UgYSBHRVQgcmVxdWVzdCB0byB0aGUgc2VydmVyIGluIG9yZGVyIHRvIGZldGNoIGFuZCBkaXNwbGF5XG5cdCAqIHRoZSB0b3RhbCBhdHRhY2htZW50cyBzaXplLCBzbyB0aGF0IHVzZXJzIGtub3cgd2hlbiBpdCBpcyB0aW1lIHRvIHJlbW92ZSBvbGRcblx0ICogYXR0YWNobWVudHMuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkdGFyZ2V0IGpRdWVyeSBzZWxlY3RvciBmb3IgdGhlIGVsZW1lbnQgdG8gY29udGFpbiB0aGUgc2l6ZSBpbmZvLlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gYWpheFVybCAob3B0aW9uYWwpIFNwZWNpZmljIGFqYXhVcmwgdG8gYmUgdXNlZCBmb3IgdGhlIHJlcXVlc3QuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyB0aGUgcHJvbWlzZSBvYmplY3QgZm9yIGNoYWluaW5nIGNhbGxiYWNrcy5cblx0ICovXG5cdGV4cG9ydHMuZ2V0QXR0YWNobWVudHNTaXplID0gZnVuY3Rpb24oJHRhcmdldCwgYWpheFVybCkge1xuXHRcdGFqYXhVcmwgPSBhamF4VXJsIHx8IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89RW1haWxzL0dldEF0dGFjaG1lbnRzU2l6ZSc7XG5cdFx0XG5cdFx0dmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXHRcdFxuXHRcdCQuZ2V0KGFqYXhVcmwsIGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRpZiAocmVzcG9uc2UuZXhjZXB0aW9uKSB7XG5cdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKSxcblx0XHRcdFx0XHRjb250ZW50OiByZXNwb25zZS5tZXNzYWdlXG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRkZWZlcnJlZC5yZWplY3QocmVzcG9uc2UpO1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHZhciBzaXplID0gKHJlc3BvbnNlLnNpemUubWVnYWJ5dGVzICE9PSAwKSA/IHJlc3BvbnNlLnNpemUubWVnYWJ5dGVzICsgJyBNQicgOiByZXNwb25zZS5zaXplXG5cdFx0XHRcdC5ieXRlcyArICcgYnl0ZXMnO1xuXHRcdFx0XG5cdFx0XHQkdGFyZ2V0LnRleHQoJygnICsgc2l6ZSArICcpJyk7XG5cdFx0XHRkZWZlcnJlZC5yZXNvbHZlKHJlc3BvbnNlKTtcblx0XHR9LCAnanNvbicpO1xuXHRcdFxuXHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogVXBkYXRlcyBtb2RhbCB0YWJzIGNvdW50ZXJzLlxuXHQgKlxuXHQgKiBEaXNwbGF5cyBpdGVtIG51bWJlciBvbiB0YWJzIHNvIHRoYXQgdXNlcnMga25vdyBob3cgbWFueSBpdGVtcyB0aGVyZSBhcmVcblx0ICogaW5jbHVkZWQgaW4gdGhlIGNvbnRhY3RzIGFuZCBhdHRhY2htZW50cyB0YWJsZXMuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkbW9kYWwgVGhlIG1vZGFsIHNlbGVjdG9yIHRvIGJlIHVwZGF0ZWQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkY29udGFjdHNUYWJsZSAob3B0aW9uYWwpIFRoZSBjb250YWN0cyB0YWJsZSBzZWxlY3RvciwgZGVmYXVsdCBzZWxlY3RvcjogJyNjb250YWN0cy10YWJsZScuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkY29udGFjdHNUYWIgKG9wdGlvbmFsKSBUaGUgY29udGFjdHMgdGFiIHNlbGVjdG9yLCBkZWZhdWx0IHNlbGVjdG9yOiAnLnRhYi1oZWFkbGluZS5iY2MtY2MnLlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJGF0dGFjaG1lbnRzVGFibGUgKG9wdGlvbmFsKSBUaGUgYXR0YWNobWVudHMgdGFibGUgc2VsZWN0b3IsIGRlZmF1bHRcblx0ICogc2VsZWN0b3I6ICcjYXR0YWNobWVudHMtdGFibGUnLlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJGF0dGFjaG1lbnRzVGFiIChvcHRpb25hbCkgVGhlIGF0dGFjaG1lbnRzIHRhYiBzZWxlY3RvciwgZGVmYXVsdFxuXHQgKiBzZWxlY3RvcjogJy50YWItaGVhZGxpbmUuYXR0YWNobWVudHMnLlxuXHQgKi9cblx0ZXhwb3J0cy51cGRhdGVUYWJDb3VudGVycyA9IGZ1bmN0aW9uKCRtb2RhbCwgJGNvbnRhY3RzVGFibGUsICRjb250YWN0c1RhYiwgJGF0dGFjaG1lbnRzVGFibGUsXG5cdFx0JGF0dGFjaG1lbnRzVGFiKSB7XG5cdFx0JGNvbnRhY3RzVGFibGUgPSAkY29udGFjdHNUYWJsZSB8fCAkbW9kYWwuZmluZCgnI2NvbnRhY3RzLXRhYmxlJyk7XG5cdFx0JGNvbnRhY3RzVGFiID0gJGNvbnRhY3RzVGFiIHx8ICRtb2RhbC5maW5kKCcudGFiLWhlYWRsaW5lLmJjYy1jYycpO1xuXHRcdCRhdHRhY2htZW50c1RhYmxlID0gJGF0dGFjaG1lbnRzVGFibGUgfHwgJG1vZGFsLmZpbmQoJyNhdHRhY2htZW50cy10YWJsZScpO1xuXHRcdCRhdHRhY2htZW50c1RhYiA9ICRhdHRhY2htZW50c1RhYiB8fCAkbW9kYWwuZmluZCgnLnRhYi1oZWFkbGluZS5hdHRhY2htZW50cycpO1xuXHRcdFxuXHRcdGlmICgkY29udGFjdHNUYWJsZS5sZW5ndGggPT09IDApIHtcblx0XHRcdHJldHVybjsgLy8gVGhlcmUgaXMgbm8gc3VjaCB0YWJsZSAoZW1haWxzLmpzIHVuaXQgdGVzdGluZykuXG5cdFx0fVxuXHRcdFxuXHRcdHZhciBjb250YWN0c0NvdW50ID0gJGNvbnRhY3RzVGFibGUuRGF0YVRhYmxlKCkucm93cygpLmRhdGEoKS5sZW5ndGgsXG5cdFx0XHRuZXdDb250YWN0c1RleHQgPSAkY29udGFjdHNUYWIudGV4dCgpLnJlcGxhY2UoL1xcKC4qXFwpL2csICcoJyArIGNvbnRhY3RzQ291bnQgKyAnKScpLFxuXHRcdFx0YXR0YWNobWVudHNDb3VudCA9ICRhdHRhY2htZW50c1RhYmxlLkRhdGFUYWJsZSgpLnJvd3MoKS5kYXRhKCkubGVuZ3RoLFxuXHRcdFx0bmV3QXR0YWNobWVudHNUZXh0ID0gJGF0dGFjaG1lbnRzVGFiLnRleHQoKS5yZXBsYWNlKC9cXCguKlxcKS9nLCAnKCcgKyBhdHRhY2htZW50c0NvdW50ICsgJyknKTtcblx0XHRcblx0XHRpZiAobmV3Q29udGFjdHNUZXh0LmluZGV4T2YoJygnKSA9PT0gLTEpIHtcblx0XHRcdG5ld0NvbnRhY3RzVGV4dCArPSAnICgnICsgY29udGFjdHNDb3VudCArICcpJztcblx0XHR9XG5cdFx0XG5cdFx0aWYgKG5ld0F0dGFjaG1lbnRzVGV4dC5pbmRleE9mKCcoJykgPT09IC0xKSB7XG5cdFx0XHRuZXdBdHRhY2htZW50c1RleHQgKz0gJyAoJyArIGF0dGFjaG1lbnRzQ291bnQgKyAnKSc7XG5cdFx0fVxuXHRcdFxuXHRcdCRjb250YWN0c1RhYi50ZXh0KG5ld0NvbnRhY3RzVGV4dCk7XG5cdFx0JGF0dGFjaG1lbnRzVGFiLnRleHQobmV3QXR0YWNobWVudHNUZXh0KTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXR1cm5zIGFuIG9iamVjdCBhcnJheSB3aXRoIHRoZSBzZWxlY3RlZCBlbWFpbHMgb2YgdGhlIG1haW4gZW1haWxzIHRhYmxlLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJHRhYmxlIChvcHRpb25hbCkgVGhlIG1haW4gdGFibGUgc2VsZWN0b3IsIGlmIG9taXR0ZWQgdGhlIFwiI2VtYWlscy10YWJsZVwiIHNlbGVjdG9yXG5cdCAqIHdpbGwgYmUgdXNlZC5cblx0ICpcblx0ICogQHJldHVybnMge2FycmF5fSBSZXR1cm5zIGFuIGFycmF5IHdpdGggdGhlIGVtYWlscyBkYXRhIChjb2xsZWN0aW9uKS5cblx0ICovXG5cdGV4cG9ydHMuZ2V0U2VsZWN0ZWRFbWFpbHMgPSBmdW5jdGlvbigkdGFibGUpIHtcblx0XHQkdGFibGUgPSAkdGFibGUgfHwgJCgnI2VtYWlscy10YWJsZScpO1xuXHRcdFxuXHRcdHZhciBjb2xsZWN0aW9uID0gW107XG5cdFx0XG5cdFx0JHRhYmxlXG5cdFx0XHQuZmluZCgndHIgdGQgaW5wdXQ6Y2hlY2tlZCcpXG5cdFx0XHQuZWFjaChmdW5jdGlvbihpbmRleCwgY2hlY2tib3gpIHtcblx0XHRcdFx0Y29sbGVjdGlvbi5wdXNoKCQoY2hlY2tib3gpLnBhcmVudHMoJ3RyJykuZGF0YSgpKTtcblx0XHRcdH0pO1xuXHRcdFxuXHRcdHJldHVybiBjb2xsZWN0aW9uO1xuXHR9O1xuXHRcbn0pKGpzZS5saWJzLmVtYWlscyk7XG4iXX0=
