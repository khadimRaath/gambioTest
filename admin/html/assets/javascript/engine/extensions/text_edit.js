'use strict';

/* --------------------------------------------------------------
 text_edit.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Text Edit Extension
 *
 * This extension is used along with text_edit.js and ajax_search.js in the Gambio Admin
 * "Text Edit | Texte Anpassen" page.
 * 
 * @module Admin/Extensions/text_edit
 * @ignore
 */
gx.extensions.module('text_edit', ['xhr', 'modal', 'fallback'], function (data) {

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
	module = {},


	/**
  * Filter Selector
  *
  * @type {object}
  */
	$filter = null;

	// ------------------------------------------------------------------------
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
  * Reset Form Event Handler
  *
  * @param {object} $parent
  * @param {boolean} resetValue
  */
	var _resetForm = function _resetForm($parent, resetValue) {
		var $textarea = $parent.find('textarea'),
		    $buttons = $parent.find('ul.actions li'),
		    original = $textarea.data('data');

		$textarea.prop('disabled', true);

		if (resetValue) {
			$textarea.val(original);
		}

		$buttons.hide().filter('.edit').show();

		if ($textarea.data('texteditEdited')) {
			$buttons.filter('.reset').show();
		} else {
			$buttons.filter('.reset').hide();
		}
	};

	/**
  * Edit Event Handler
  */
	var _editHandler = function _editHandler() {
		var $self = $(this),
		    $parent = $self.closest('.dataTableRow'),
		    $textarea = $parent.find('textarea'),
		    $buttons = $parent.find('ul.actions li'),
		    value = $textarea.val();

		$textarea.data('data', value).val('').prop('disabled', false).focus().val(value);

		$self.hide().siblings().show();

		if ($textarea.data('texteditEdited')) {
			$buttons.filter('.reset').show();
		} else {
			$buttons.filter('.reset').hide();
		}
	};

	/**
  * Abort Event Handler
  */
	var _abortHandler = function _abortHandler() {
		var $self = $(this),
		    $parent = $self.closest('.dataTableRow'),
		    $textarea = $parent.find('textarea'),
		    value = $textarea.val(),
		    original = $textarea.data('data');

		if (value !== original) {
			jse.libs.modal.confirm({
				'content': jse.core.lang.translate('discard_changes_prompt', 'messages'),
				'title': jse.core.lang.translate('abort', 'buttons'),
				'position': {
					'my': 'center',
					'at': 'center',
					'of': $parent
				}
			}).done(function () {
				_resetForm($parent, true);
			});
		} else {
			_resetForm($parent);
		}
	};

	/**
  * Save Event Handler
  */
	var _saveHandler = function _saveHandler() {
		var $self = $(this),
		    $parent = $self.closest('.dataTableRow'),
		    $textarea = $parent.find('textarea'),
		    value = $textarea.val(),
		    original = $textarea.data('data'),
		    data = jse.libs.fallback._data($textarea, 'text_edit');

		data.value = value;
		if (!$self.hasClass('pending')) {
			if (value !== original) {
				$self.addClass('pending');

				jse.libs.xhr.ajax({
					'url': options.url,
					'data': data
				}).done(function (result) {
					$textarea.data('texteditEdited', result.edited);
					$parent.find('.searchSection').attr('title', result.source);
					_resetForm($parent);
				}).fail(function () {
					jse.libs.modal.error({
						'content': 'Error',
						'title': 'Error',
						'position': {
							'my': 'center',
							'at': 'center',
							'of': $parent
						}
					});
				}).always(function () {
					$self.removeClass('pending');
				});
			} else {
				_resetForm($parent);
			}
		}
	};

	/**
  * Reset Event Handler
  */
	var _resetHandler = function _resetHandler() {
		var $self = $(this),
		    $parent = $self.closest('.dataTableRow'),
		    $textarea = $parent.find('textarea');
		data = jse.libs.fallback._data($self, 'text_edit');

		if (!$self.hasClass('pending')) {
			$self.addClass('pending');

			jse.libs.xhr.ajax({
				'url': options.url,
				'data': data
			}).done(function (result) {
				if (result.success) {
					$parent.find('.searchSection').attr('title', result.source);
					$textarea.val(result.value);
					$textarea.data('texteditEdited', false);
					_resetForm($parent);
					$self.hide();
				}
			}).fail(function () {
				jse.libs.modal.error({
					'content': 'Error',
					'title': 'Error',
					'position': {
						'my': 'center',
						'at': 'center',
						'of': $parent
					}
				});
			}).always(function () {
				$self.removeClass('pending');
			});
		}
	};

	/**
  * Filter Event Handler
  */
	var _filterHandler = function _filterHandler() {
		var $self = $(this),
		    settings = jse.libs.fallback._data($(this), 'text_edit');

		$filter.trigger('submitform', [settings]);
		window.scrollTo(0, 0);
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the extension, called by the engine.
  */
	module.init = function (done) {
		$filter = $('#' + options.filter);

		$this.on('click', '.edit', _editHandler).on('click', '.save', _saveHandler).on('click', '.abort', _abortHandler).on('click', '.reset', _resetHandler);

		if ($filter.length) {
			$this.on('click', '.searchPhrase, .searchSection', _filterHandler);
		}

		$('#needle').focus();

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRleHRfZWRpdC5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiJGZpbHRlciIsIl9yZXNldEZvcm0iLCIkcGFyZW50IiwicmVzZXRWYWx1ZSIsIiR0ZXh0YXJlYSIsImZpbmQiLCIkYnV0dG9ucyIsIm9yaWdpbmFsIiwicHJvcCIsInZhbCIsImhpZGUiLCJmaWx0ZXIiLCJzaG93IiwiX2VkaXRIYW5kbGVyIiwiJHNlbGYiLCJjbG9zZXN0IiwidmFsdWUiLCJmb2N1cyIsInNpYmxpbmdzIiwiX2Fib3J0SGFuZGxlciIsImpzZSIsImxpYnMiLCJtb2RhbCIsImNvbmZpcm0iLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImRvbmUiLCJfc2F2ZUhhbmRsZXIiLCJmYWxsYmFjayIsIl9kYXRhIiwiaGFzQ2xhc3MiLCJhZGRDbGFzcyIsInhociIsImFqYXgiLCJ1cmwiLCJyZXN1bHQiLCJlZGl0ZWQiLCJhdHRyIiwic291cmNlIiwiZmFpbCIsImVycm9yIiwiYWx3YXlzIiwicmVtb3ZlQ2xhc3MiLCJfcmVzZXRIYW5kbGVyIiwic3VjY2VzcyIsIl9maWx0ZXJIYW5kbGVyIiwic2V0dGluZ3MiLCJ0cmlnZ2VyIiwid2luZG93Iiwic2Nyb2xsVG8iLCJpbml0Iiwib24iLCJsZW5ndGgiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7O0FBU0FBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLFdBREQsRUFHQyxDQUFDLEtBQUQsRUFBUSxPQUFSLEVBQWlCLFVBQWpCLENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFB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TixjQUFuRDtBQUNBOztBQUVEbEQsSUFBRSxTQUFGLEVBQWFxQixLQUFiOztBQUVBVTtBQUNBLEVBaEJEOztBQWtCQTtBQUNBLFFBQU9sQyxNQUFQO0FBQ0EsQ0ExUUYiLCJmaWxlIjoidGV4dF9lZGl0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB0ZXh0X2VkaXQuanMgMjAxNS0wOS0xNyBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgVGV4dCBFZGl0IEV4dGVuc2lvblxuICpcbiAqIFRoaXMgZXh0ZW5zaW9uIGlzIHVzZWQgYWxvbmcgd2l0aCB0ZXh0X2VkaXQuanMgYW5kIGFqYXhfc2VhcmNoLmpzIGluIHRoZSBHYW1iaW8gQWRtaW5cbiAqIFwiVGV4dCBFZGl0IHwgVGV4dGUgQW5wYXNzZW5cIiBwYWdlLlxuICogXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvdGV4dF9lZGl0XG4gKiBAaWdub3JlXG4gKi9cbmd4LmV4dGVuc2lvbnMubW9kdWxlKFxuXHQndGV4dF9lZGl0Jyxcblx0XG5cdFsneGhyJywgJ21vZGFsJywgJ2ZhbGxiYWNrJ10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgRXh0ZW5zaW9uIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmlsdGVyIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JGZpbHRlciA9IG51bGw7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRlVOQ1RJT05BTElUWVxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlc2V0IEZvcm0gRXZlbnQgSGFuZGxlclxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9ICRwYXJlbnRcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IHJlc2V0VmFsdWVcblx0XHQgKi9cblx0XHR2YXIgX3Jlc2V0Rm9ybSA9IGZ1bmN0aW9uKCRwYXJlbnQsIHJlc2V0VmFsdWUpIHtcblx0XHRcdHZhciAkdGV4dGFyZWEgPSAkcGFyZW50LmZpbmQoJ3RleHRhcmVhJyksXG5cdFx0XHRcdCRidXR0b25zID0gJHBhcmVudC5maW5kKCd1bC5hY3Rpb25zIGxpJyksXG5cdFx0XHRcdG9yaWdpbmFsID0gJHRleHRhcmVhLmRhdGEoJ2RhdGEnKTtcblx0XHRcdFxuXHRcdFx0JHRleHRhcmVhLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG5cdFx0XHRcblx0XHRcdGlmIChyZXNldFZhbHVlKSB7XG5cdFx0XHRcdCR0ZXh0YXJlYS52YWwob3JpZ2luYWwpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkYnV0dG9uc1xuXHRcdFx0XHQuaGlkZSgpXG5cdFx0XHRcdC5maWx0ZXIoJy5lZGl0Jylcblx0XHRcdFx0LnNob3coKTtcblx0XHRcdFxuXHRcdFx0aWYgKCR0ZXh0YXJlYS5kYXRhKCd0ZXh0ZWRpdEVkaXRlZCcpKSB7XG5cdFx0XHRcdCRidXR0b25zXG5cdFx0XHRcdFx0LmZpbHRlcignLnJlc2V0Jylcblx0XHRcdFx0XHQuc2hvdygpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JGJ1dHRvbnNcblx0XHRcdFx0XHQuZmlsdGVyKCcucmVzZXQnKVxuXHRcdFx0XHRcdC5oaWRlKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEVkaXQgRXZlbnQgSGFuZGxlclxuXHRcdCAqL1xuXHRcdHZhciBfZWRpdEhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdCRwYXJlbnQgPSAkc2VsZi5jbG9zZXN0KCcuZGF0YVRhYmxlUm93JyksXG5cdFx0XHRcdCR0ZXh0YXJlYSA9ICRwYXJlbnQuZmluZCgndGV4dGFyZWEnKSxcblx0XHRcdFx0JGJ1dHRvbnMgPSAkcGFyZW50LmZpbmQoJ3VsLmFjdGlvbnMgbGknKSxcblx0XHRcdFx0dmFsdWUgPSAkdGV4dGFyZWEudmFsKCk7XG5cdFx0XHRcblx0XHRcdCR0ZXh0YXJlYVxuXHRcdFx0XHQuZGF0YSgnZGF0YScsIHZhbHVlKVxuXHRcdFx0XHQudmFsKCcnKVxuXHRcdFx0XHQucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSlcblx0XHRcdFx0LmZvY3VzKClcblx0XHRcdFx0LnZhbCh2YWx1ZSk7XG5cdFx0XHRcblx0XHRcdCRzZWxmXG5cdFx0XHRcdC5oaWRlKClcblx0XHRcdFx0LnNpYmxpbmdzKClcblx0XHRcdFx0LnNob3coKTtcblx0XHRcdFxuXHRcdFx0aWYgKCR0ZXh0YXJlYS5kYXRhKCd0ZXh0ZWRpdEVkaXRlZCcpKSB7XG5cdFx0XHRcdCRidXR0b25zXG5cdFx0XHRcdFx0LmZpbHRlcignLnJlc2V0Jylcblx0XHRcdFx0XHQuc2hvdygpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JGJ1dHRvbnNcblx0XHRcdFx0XHQuZmlsdGVyKCcucmVzZXQnKVxuXHRcdFx0XHRcdC5oaWRlKCk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBBYm9ydCBFdmVudCBIYW5kbGVyXG5cdFx0ICovXG5cdFx0dmFyIF9hYm9ydEhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdCRwYXJlbnQgPSAkc2VsZi5jbG9zZXN0KCcuZGF0YVRhYmxlUm93JyksXG5cdFx0XHRcdCR0ZXh0YXJlYSA9ICRwYXJlbnQuZmluZCgndGV4dGFyZWEnKSxcblx0XHRcdFx0dmFsdWUgPSAkdGV4dGFyZWEudmFsKCksXG5cdFx0XHRcdG9yaWdpbmFsID0gJHRleHRhcmVhLmRhdGEoJ2RhdGEnKTtcblx0XHRcdFxuXHRcdFx0aWYgKHZhbHVlICE9PSBvcmlnaW5hbCkge1xuXHRcdFx0XHRqc2UubGlicy5tb2RhbC5jb25maXJtKHtcblx0XHRcdFx0XHQnY29udGVudCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdkaXNjYXJkX2NoYW5nZXNfcHJvbXB0JywgJ21lc3NhZ2VzJyksXG5cdFx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Fib3J0JywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHQncG9zaXRpb24nOiB7XG5cdFx0XHRcdFx0XHQnbXknOiAnY2VudGVyJyxcblx0XHRcdFx0XHRcdCdhdCc6ICdjZW50ZXInLFxuXHRcdFx0XHRcdFx0J29mJzogJHBhcmVudFxuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSkuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRfcmVzZXRGb3JtKCRwYXJlbnQsIHRydWUpO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdF9yZXNldEZvcm0oJHBhcmVudCk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBTYXZlIEV2ZW50IEhhbmRsZXJcblx0XHQgKi9cblx0XHR2YXIgX3NhdmVIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkcGFyZW50ID0gJHNlbGYuY2xvc2VzdCgnLmRhdGFUYWJsZVJvdycpLFxuXHRcdFx0XHQkdGV4dGFyZWEgPSAkcGFyZW50LmZpbmQoJ3RleHRhcmVhJyksXG5cdFx0XHRcdHZhbHVlID0gJHRleHRhcmVhLnZhbCgpLFxuXHRcdFx0XHRvcmlnaW5hbCA9ICR0ZXh0YXJlYS5kYXRhKCdkYXRhJyksXG5cdFx0XHRcdGRhdGEgPSBqc2UubGlicy5mYWxsYmFjay5fZGF0YSgkdGV4dGFyZWEsICd0ZXh0X2VkaXQnKTtcblx0XHRcdFxuXHRcdFx0ZGF0YS52YWx1ZSA9IHZhbHVlO1xuXHRcdFx0aWYgKCEkc2VsZi5oYXNDbGFzcygncGVuZGluZycpKSB7XG5cdFx0XHRcdGlmICh2YWx1ZSAhPT0gb3JpZ2luYWwpIHtcblx0XHRcdFx0XHQkc2VsZi5hZGRDbGFzcygncGVuZGluZycpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGpzZS5saWJzLnhoci5hamF4KHtcblx0XHRcdFx0XHRcdCd1cmwnOiBvcHRpb25zLnVybCxcblx0XHRcdFx0XHRcdCdkYXRhJzogZGF0YVxuXHRcdFx0XHRcdH0pLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdFx0XHQkdGV4dGFyZWEuZGF0YSgndGV4dGVkaXRFZGl0ZWQnLCByZXN1bHQuZWRpdGVkKTtcblx0XHRcdFx0XHRcdCRwYXJlbnQuZmluZCgnLnNlYXJjaFNlY3Rpb24nKS5hdHRyKCd0aXRsZScsIHJlc3VsdC5zb3VyY2UpO1xuXHRcdFx0XHRcdFx0X3Jlc2V0Rm9ybSgkcGFyZW50KTtcblx0XHRcdFx0XHR9KS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0anNlLmxpYnMubW9kYWwuZXJyb3Ioe1xuXHRcdFx0XHRcdFx0XHQnY29udGVudCc6ICdFcnJvcicsXG5cdFx0XHRcdFx0XHRcdCd0aXRsZSc6ICdFcnJvcicsXG5cdFx0XHRcdFx0XHRcdCdwb3NpdGlvbic6IHtcblx0XHRcdFx0XHRcdFx0XHQnbXknOiAnY2VudGVyJyxcblx0XHRcdFx0XHRcdFx0XHQnYXQnOiAnY2VudGVyJyxcblx0XHRcdFx0XHRcdFx0XHQnb2YnOiAkcGFyZW50XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdH0pLmFsd2F5cyhmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCRzZWxmLnJlbW92ZUNsYXNzKCdwZW5kaW5nJyk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0X3Jlc2V0Rm9ybSgkcGFyZW50KTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUmVzZXQgRXZlbnQgSGFuZGxlclxuXHRcdCAqL1xuXHRcdHZhciBfcmVzZXRIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkcGFyZW50ID0gJHNlbGYuY2xvc2VzdCgnLmRhdGFUYWJsZVJvdycpLFxuXHRcdFx0XHQkdGV4dGFyZWEgPSAkcGFyZW50LmZpbmQoJ3RleHRhcmVhJyk7XG5cdFx0XHRkYXRhID0ganNlLmxpYnMuZmFsbGJhY2suX2RhdGEoJHNlbGYsICd0ZXh0X2VkaXQnKTtcblx0XHRcdFxuXHRcdFx0aWYgKCEkc2VsZi5oYXNDbGFzcygncGVuZGluZycpKSB7XG5cdFx0XHRcdCRzZWxmLmFkZENsYXNzKCdwZW5kaW5nJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRqc2UubGlicy54aHIuYWpheCh7XG5cdFx0XHRcdFx0J3VybCc6IG9wdGlvbnMudXJsLFxuXHRcdFx0XHRcdCdkYXRhJzogZGF0YVxuXHRcdFx0XHR9KS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRcdGlmIChyZXN1bHQuc3VjY2Vzcykge1xuXHRcdFx0XHRcdFx0JHBhcmVudC5maW5kKCcuc2VhcmNoU2VjdGlvbicpLmF0dHIoJ3RpdGxlJywgcmVzdWx0LnNvdXJjZSk7XG5cdFx0XHRcdFx0XHQkdGV4dGFyZWEudmFsKHJlc3VsdC52YWx1ZSk7XG5cdFx0XHRcdFx0XHQkdGV4dGFyZWEuZGF0YSgndGV4dGVkaXRFZGl0ZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHRfcmVzZXRGb3JtKCRwYXJlbnQpO1xuXHRcdFx0XHRcdFx0JHNlbGYuaGlkZSgpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSkuZmFpbChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRqc2UubGlicy5tb2RhbC5lcnJvcih7XG5cdFx0XHRcdFx0XHQnY29udGVudCc6ICdFcnJvcicsXG5cdFx0XHRcdFx0XHQndGl0bGUnOiAnRXJyb3InLFxuXHRcdFx0XHRcdFx0J3Bvc2l0aW9uJzoge1xuXHRcdFx0XHRcdFx0XHQnbXknOiAnY2VudGVyJyxcblx0XHRcdFx0XHRcdFx0J2F0JzogJ2NlbnRlcicsXG5cdFx0XHRcdFx0XHRcdCdvZic6ICRwYXJlbnRcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fSkuYWx3YXlzKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCRzZWxmLnJlbW92ZUNsYXNzKCdwZW5kaW5nJyk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRmlsdGVyIEV2ZW50IEhhbmRsZXJcblx0XHQgKi9cblx0XHR2YXIgX2ZpbHRlckhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdHNldHRpbmdzID0ganNlLmxpYnMuZmFsbGJhY2suX2RhdGEoJCh0aGlzKSwgJ3RleHRfZWRpdCcpO1xuXHRcdFx0XG5cdFx0XHQkZmlsdGVyLnRyaWdnZXIoJ3N1Ym1pdGZvcm0nLCBbc2V0dGluZ3NdKTtcblx0XHRcdHdpbmRvdy5zY3JvbGxUbygwLCAwKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgZXh0ZW5zaW9uLCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCRmaWx0ZXIgPSAkKCcjJyArIG9wdGlvbnMuZmlsdGVyKTtcblx0XHRcdFxuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdjbGljaycsICcuZWRpdCcsIF9lZGl0SGFuZGxlcilcblx0XHRcdFx0Lm9uKCdjbGljaycsICcuc2F2ZScsIF9zYXZlSGFuZGxlcilcblx0XHRcdFx0Lm9uKCdjbGljaycsICcuYWJvcnQnLCBfYWJvcnRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5yZXNldCcsIF9yZXNldEhhbmRsZXIpO1xuXHRcdFx0XG5cdFx0XHRpZiAoJGZpbHRlci5sZW5ndGgpIHtcblx0XHRcdFx0JHRoaXMub24oJ2NsaWNrJywgJy5zZWFyY2hQaHJhc2UsIC5zZWFyY2hTZWN0aW9uJywgX2ZpbHRlckhhbmRsZXIpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkKCcjbmVlZGxlJykuZm9jdXMoKTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
