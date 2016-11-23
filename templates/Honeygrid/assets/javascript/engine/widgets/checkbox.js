'use strict';

/* --------------------------------------------------------------
 checkbox.js 2015-10-23 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Checkbox Widget
 *
 * Toggles the default checkboxes and 2-options radio boxes to a mobile like styling. This
 * widget can also be used to provide CSS style-able HTML markup so that we can have checkboxes
 * that look better.
 *
 * Important: Place the "data-use-glyphicons" to the widget element in HTML in order to use
 * glyphicons instead of the font-awesome icon library (applies currently only to "single-checkbox"
 * mode).
 *
 * @module Widgets/checkbox
 */
gambio.widgets.module('checkbox', [],

/** @lends module:Widgets/checkbox */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Widget Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Options for Widget
  *
  * @type {object}
  */
	defaults = {
		'filter': '', // Only select checkboxes with the following selector

		// Url Switcher Options:

		'on_url': '', // Open url when switcher is turned on
		'off_url': '', // Open url when switcher is turned off
		'on_label': '', // Text shown on the switcher when turned on
		'off_label': '', // Text shown on the switcher when turned off
		'on_text': '', // Text shown next to the switcher when turned on
		'off_text': '', // Text shown next to the switcher when turned off
		'class': '', // Add class(es) to the on and off switcher
		'checked': false // Initial status of the switcher: true = on, false = off
	},


	/**
  * Status of mouse down event
  *
  * @type {boolean}
  */
	mouseDown = false,


	/**
  * Final Widget Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Meta Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * Change the styling of the new switcher depending on the original checkbox/radio box setting
  * Additionally set the new state of the original checkbox/radio box and trigger the change event on it.
  *
  * @private
  */
	var _switcherChangeHandler = function _switcherChangeHandler(event) {
		if ($(this).hasClass('disabled')) {
			return false;
		}

		var $self = $(this),
		    $checkbox = $self.find('input:checkbox'),
		    $onElement = $self.find('input:radio').first(),
		    $offElement = $self.find('input:radio').last(),
		    $select = $self.find('select').first(),
		    dataset = $self.parent().data('checkbox');

		$self.toggleClass('checked');

		$self.find('.state-description').show().fadeOut('slow');

		$checkbox.prop('checked', $self.hasClass('checked'));

		$onElement.prop('checked', $self.hasClass('checked'));

		$offElement.prop('checked', !$self.hasClass('checked'));

		$select.find('option').removeAttr('selected');

		var selectOptionToSelect = $self.hasClass('checked') ? 1 : 0;

		$select.find('option[value="' + selectOptionToSelect + '"]').attr('selected', true);

		if (options.on_url !== '' && options.off_url !== '') {
			event.preventDefault();
			event.stopPropagation();

			if (options.checked) {
				window.location.href = options.off_url;
				options.checked = false;

				return false;
			}

			window.location.href = options.on_url;
			options.checked = true;
		}
	};

	/**
  * Change the styling of the new checkbox depending on the original checkbox setting
  * Additionally set the new state of the original checkbox and trigger the change event on it.
  *
  * @private
  */
	var _checkboxMouseDownHandler = function _checkboxMouseDownHandler(e) {
		//e.stopPropagation();

		if ($(this).hasClass('disabled')) {
			return false;
		}

		mouseDown = true;

		$(this).find('input:checkbox').focus();
	};

	/**
  * Imitate mouse up behaviour of the checkbox.
  *
  * @private
  */
	var _checkboxMouseUpHandler = function _checkboxMouseUpHandler(e) {
		//e.stopPropagation();

		if ($(this).hasClass('disabled')) {
			return false;
		}

		$(this).toggleClass('checked');
		$(this).find('input:checkbox').focus();
		//$(this).find('input:checkbox').trigger('click');

		mouseDown = false;
	};

	// ------------------------------------------------------------------------
	// INITIALISATION FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Wrap the checkboxes and generate markup for the new checkbox style.
  *
  * @private
  */
	var _initCheckboxes = function _initCheckboxes($target) {

		var $container = $target || $this;

		$container.find('input:checkbox').filter(options.filter || '*').each(function () {
			var $self = $(this),
			    dataset = $self.parseModuleData('checkbox'),
			    className = dataset.className || '',
			    title = $self.prop('title'),
			    isChecked = $self.prop('checked') ? 'checked' : '',
			    isDisabled = $self.prop('disabled') ? 'disabled' : '';

			if (typeof $self.data('single_checkbox') !== 'undefined') {
				$self.css({
					'position': 'absolute',
					'left': '-100000px'
				}).wrap('<span class="single-checkbox ' + isChecked + ' ' + isDisabled + '" title="' + title + '"></span>');

				var iconClass = options.useGlyphicons !== undefined ? 'glyphicon glyphicon-ok' : 'fa fa-check';

				$self.parent().append('<i class="' + iconClass + '"></i>');

				$self.on('focus', function () {
					$('.single_checkbox').removeClass('focused');
					$(this).parent().addClass('focused');
				});

				$self.on('blur', function () {
					$(this).parent().removeClass('focused');
				});

				$self.on('change', function () {
					if (mouseDown === false) {
						$(this).parent().toggleClass('checked');
					}
				});
			} else {
				var onText = $self.attr('data-checkbox-on_text') ? $self.attr('data-checkbox-on_text') : '<span class="fa fa-check"></span>';

				var offText = $self.attr('data-checkbox-on_text') ? $self.attr('data-checkbox-off_text') : '<span class="fa fa-times"></span>';

				$self.wrap('<div class="switcher ' + isChecked + ' ' + isDisabled + '" title="' + title + '"></div>').parent().data('checkbox', dataset).addClass(className).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on">' + onText + '</div>' + '<div class="switcher-state-off">' + offText + '</div>' + '</div>' + '<div class="switcher-text-on">' + options.on_text + '</div>' + '<div class="switcher-text-off">' + options.off_text + '</div>');
			}
		});
	};

	/**
  * Wrap the radio boxes and generate markup for the new checkbox style.
  *
  * @private
  */
	var _initRadioOptions = function _initRadioOptions() {
		if ($this.find('input:radio').filter(options.filter || '*').length === 2) {
			var $onElement = $this.find('input:radio').filter(options.filter || '*').first(),
			    onTitle = $onElement.prop('title'),
			    $offElement = $this.find('input:radio').filter(options.filter || '*').last(),
			    offTitle = $offElement.prop('title'),
			    onLabel = options.on_label !== '' ? ' data-checkbox-label="' + options.on_label + '"' : '',
			    offLabel = options.off_label !== '' ? ' data-checkbox-label="' + options.off_label + '"' : '',
			    dataset = options,
			    isChecked = $onElement.prop('checked') ? 'checked' : '',
			    isDisabled = $onElement.prop('disabled') ? 'disabled' : '';

			var $switcher = $('<div class="switcher ' + isChecked + ' ' + isDisabled + '"></div>');

			$onElement.after($switcher);

			$onElement.appendTo($switcher);
			$offElement.appendTo($switcher);

			$switcher.data('checkbox', dataset).addClass(options.class).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on" title="' + onTitle + '"' + onLabel + '><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off" title="' + offTitle + '"' + offLabel + '><span class="fa fa-times"></span></div>' + '<div class="switcher-text-on">' + options.on_text + '</div>' + '<div class="switcher-text-off">' + options.off_text + '</div>' + '</div>');

			// toggle switcher if hidden radio option status changes (there is no default case for that)
			$onElement.on('change', function () {
				$(this).parent().toggleClass('checked');
			});

			// toggle switcher if hidden radio option status changes (there is no default case for that)
			$offElement.on('change', function () {
				$(this).parent().toggleClass('checked');
			});
		}
	};

	/**
  * Build markup for the URL switcher.
  *
  * @private
  */
	var _initUrlSwitcher = function _initUrlSwitcher() {
		if (options.on_url !== '' && options.off_url !== '') {
			var dataset = $this.parseModuleData('checkbox'),
			    onLabel = options.on_label !== '' ? ' data-checkbox-label="' + options.on_label + '"' : '',
			    offLabel = options.off_label !== '' ? ' data-checkbox-label="' + options.off_label + '"' : '',
			    isChecked = options.checked ? 'checked' : '';

			$this.data('checkbox', dataset).addClass('switcher').addClass(isChecked).addClass(options.class).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on" title="' + options.off_url + '"' + onLabel + '><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off" title="' + options.on_url + '"' + offLabel + '><span class="fa fa-times"></span></div>' + '</div>').on('click', _switcherChangeHandler);
		}
	};

	/**
  * Bind events that change the checkbox or switcher.
  *
  * @private
  */
	var _initEventHandlers = function _initEventHandlers() {
		$this.on('click', '.switcher', _switcherChangeHandler);

		$this.off('mousedown', '.single-checkbox');
		$this.on('mousedown', '.single-checkbox', _checkboxMouseDownHandler);
		$this.off('mouseup', '.single-checkbox');
		$this.on('mouseup', '.single-checkbox', _checkboxMouseUpHandler);

		$this.on('mousedown', 'label', function () {
			mouseDown = true;
		});

		$this.on('mouseup', 'label', function () {
			mouseDown = false;
		});

		$this.on('FORM_UPDATE', function (e) {
			var $target = $(e.target);
			$target.find('input:checkbox').each(function () {
				var $self = $(this),
				    $wrapper = $self.closest('.switcher');

				if ($wrapper.length) {
					$wrapper.find('div').remove();
					$self.unwrap();
				}
			});

			_initCheckboxes($target);
		});
	};

	/**
  * Convert "yes/no" select elements to a switcher.
  *
  * The selects must have a "data-convert-checkbox" attribute in order to be processed by
  * this method.
  *
  * @private
  */
	var _initSelects = function _initSelects() {
		// Iterate over select fields
		$this.find('[data-convert-checkbox]').each(function (index, element) {
			// Selectors f
			var $optionTrue = $(element).find('option[value="1"]'),
			    $optionFalse = $(element).find('option[value="0"]');

			// States
			var isChecked = $optionTrue.is(':selected') ? 'checked' : '',
			    isDisabled = $(element).is(':disabled') ? 'disabled' : '';

			// Switcher Template
			var $switcher = $('<div class="switcher ' + isChecked + ' ' + isDisabled + '"></div>');
			$switcher.addClass($(element).data('newClass')).data('checkbox', options).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on"><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off"><span class="fa fa-times"></span></div>' + '</div>');

			$(element).after($switcher).appendTo($switcher).hide();
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		_initCheckboxes();
		_initRadioOptions();
		_initSelects();
		_initUrlSwitcher();
		_initEventHandlers();
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2hlY2tib3guanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm1vdXNlRG93biIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc3dpdGNoZXJDaGFuZ2VIYW5kbGVyIiwiZXZlbnQiLCJoYXNDbGFzcyIsIiRzZWxmIiwiJGNoZWNrYm94IiwiZmluZCIsIiRvbkVsZW1lbnQiLCJmaXJzdCIsIiRvZmZFbGVtZW50IiwibGFzdCIsIiRzZWxlY3QiLCJkYXRhc2V0IiwicGFyZW50IiwidG9nZ2xlQ2xhc3MiLCJzaG93IiwiZmFkZU91dCIsInByb3AiLCJyZW1vdmVBdHRyIiwic2VsZWN0T3B0aW9uVG9TZWxlY3QiLCJhdHRyIiwib25fdXJsIiwib2ZmX3VybCIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiY2hlY2tlZCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsIl9jaGVja2JveE1vdXNlRG93bkhhbmRsZXIiLCJlIiwiZm9jdXMiLCJfY2hlY2tib3hNb3VzZVVwSGFuZGxlciIsIl9pbml0Q2hlY2tib3hlcyIsIiR0YXJnZXQiLCIkY29udGFpbmVyIiwiZmlsdGVyIiwiZWFjaCIsInBhcnNlTW9kdWxlRGF0YSIsImNsYXNzTmFtZSIsInRpdGxlIiwiaXNDaGVja2VkIiwiaXNEaXNhYmxlZCIsImNzcyIsIndyYXAiLCJpY29uQ2xhc3MiLCJ1c2VHbHlwaGljb25zIiwidW5kZWZpbmVkIiwiYXBwZW5kIiwib24iLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwib25UZXh0Iiwib2ZmVGV4dCIsIm9uX3RleHQiLCJvZmZfdGV4dCIsIl9pbml0UmFkaW9PcHRpb25zIiwibGVuZ3RoIiwib25UaXRsZSIsIm9mZlRpdGxlIiwib25MYWJlbCIsIm9uX2xhYmVsIiwib2ZmTGFiZWwiLCJvZmZfbGFiZWwiLCIkc3dpdGNoZXIiLCJhZnRlciIsImFwcGVuZFRvIiwiY2xhc3MiLCJfaW5pdFVybFN3aXRjaGVyIiwiX2luaXRFdmVudEhhbmRsZXJzIiwib2ZmIiwidGFyZ2V0IiwiJHdyYXBwZXIiLCJjbG9zZXN0IiwicmVtb3ZlIiwidW53cmFwIiwiX2luaXRTZWxlY3RzIiwiaW5kZXgiLCJlbGVtZW50IiwiJG9wdGlvblRydWUiLCIkb3B0aW9uRmFsc2UiLCJpcyIsImhpZGUiLCJpbml0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7O0FBYUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFVBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXO0FBQ1YsWUFBVSxFQURBLEVBQ0k7O0FBRWQ7O0FBRUEsWUFBVSxFQUxBLEVBS0k7QUFDZCxhQUFXLEVBTkQsRUFNSztBQUNmLGNBQVksRUFQRixFQU9NO0FBQ2hCLGVBQWEsRUFSSCxFQVFPO0FBQ2pCLGFBQVcsRUFURCxFQVNLO0FBQ2YsY0FBWSxFQVZGLEVBVU07QUFDaEIsV0FBUyxFQVhDLEVBV0c7QUFDYixhQUFXLEtBWkQsQ0FZTztBQVpQLEVBYlo7OztBQTRCQzs7Ozs7QUFLQUMsYUFBWSxLQWpDYjs7O0FBbUNDOzs7OztBQUtBQyxXQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQXhDWDs7O0FBMENDOzs7OztBQUtBRCxVQUFTLEVBL0NWOztBQWlEQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQU1BLEtBQUlRLHlCQUF5QixTQUF6QkEsc0JBQXlCLENBQVNDLEtBQVQsRUFBZ0I7QUFDNUMsTUFBSU4sRUFBRSxJQUFGLEVBQVFPLFFBQVIsQ0FBaUIsVUFBakIsQ0FBSixFQUFrQztBQUNqQyxVQUFPLEtBQVA7QUFDQTs7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SUFGRixDQUVPLFVBRlAsRUFFbUJLLE9BRm5CLEVBR0VpRCxNQUhGLENBR1MseUNBQXlDLDhCQUF6QyxHQUNBLHdFQURBLEdBRUEseUVBRkEsR0FFNEUsUUFMckY7O0FBUUFwRCxLQUFFa0YsT0FBRixFQUNFYixLQURGLENBQ1FELFNBRFIsRUFFRUUsUUFGRixDQUVXRixTQUZYLEVBR0VrQixJQUhGO0FBSUEsR0F2QkQ7QUF3QkEsRUExQkQ7O0FBNEJBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0F6RixRQUFPMEYsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1Qm5EO0FBQ0F1QjtBQUNBb0I7QUFDQVI7QUFDQUM7QUFDQWU7QUFDQSxFQVBEOztBQVNBO0FBQ0EsUUFBTzNGLE1BQVA7QUFDQSxDQWhhRiIsImZpbGUiOiJ3aWRnZXRzL2NoZWNrYm94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjaGVja2JveC5qcyAyMDE1LTEwLTIzIGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBDaGVja2JveCBXaWRnZXRcbiAqXG4gKiBUb2dnbGVzIHRoZSBkZWZhdWx0IGNoZWNrYm94ZXMgYW5kIDItb3B0aW9ucyByYWRpbyBib3hlcyB0byBhIG1vYmlsZSBsaWtlIHN0eWxpbmcuIFRoaXNcbiAqIHdpZGdldCBjYW4gYWxzbyBiZSB1c2VkIHRvIHByb3ZpZGUgQ1NTIHN0eWxlLWFibGUgSFRNTCBtYXJrdXAgc28gdGhhdCB3ZSBjYW4gaGF2ZSBjaGVja2JveGVzXG4gKiB0aGF0IGxvb2sgYmV0dGVyLlxuICpcbiAqIEltcG9ydGFudDogUGxhY2UgdGhlIFwiZGF0YS11c2UtZ2x5cGhpY29uc1wiIHRvIHRoZSB3aWRnZXQgZWxlbWVudCBpbiBIVE1MIGluIG9yZGVyIHRvIHVzZVxuICogZ2x5cGhpY29ucyBpbnN0ZWFkIG9mIHRoZSBmb250LWF3ZXNvbWUgaWNvbiBsaWJyYXJ5IChhcHBsaWVzIGN1cnJlbnRseSBvbmx5IHRvIFwic2luZ2xlLWNoZWNrYm94XCJcbiAqIG1vZGUpLlxuICpcbiAqIEBtb2R1bGUgV2lkZ2V0cy9jaGVja2JveFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdjaGVja2JveCcsXG5cblx0W10sXG5cblx0LyoqIEBsZW5kcyBtb2R1bGU6V2lkZ2V0cy9jaGVja2JveCAqL1xuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBXaWRnZXQgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdCdmaWx0ZXInOiAnJywgLy8gT25seSBzZWxlY3QgY2hlY2tib3hlcyB3aXRoIHRoZSBmb2xsb3dpbmcgc2VsZWN0b3JcblxuXHRcdFx0XHQvLyBVcmwgU3dpdGNoZXIgT3B0aW9uczpcblxuXHRcdFx0XHQnb25fdXJsJzogJycsIC8vIE9wZW4gdXJsIHdoZW4gc3dpdGNoZXIgaXMgdHVybmVkIG9uXG5cdFx0XHRcdCdvZmZfdXJsJzogJycsIC8vIE9wZW4gdXJsIHdoZW4gc3dpdGNoZXIgaXMgdHVybmVkIG9mZlxuXHRcdFx0XHQnb25fbGFiZWwnOiAnJywgLy8gVGV4dCBzaG93biBvbiB0aGUgc3dpdGNoZXIgd2hlbiB0dXJuZWQgb25cblx0XHRcdFx0J29mZl9sYWJlbCc6ICcnLCAvLyBUZXh0IHNob3duIG9uIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvZmZcblx0XHRcdFx0J29uX3RleHQnOiAnJywgLy8gVGV4dCBzaG93biBuZXh0IHRvIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvblxuXHRcdFx0XHQnb2ZmX3RleHQnOiAnJywgLy8gVGV4dCBzaG93biBuZXh0IHRvIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvZmZcblx0XHRcdFx0J2NsYXNzJzogJycsIC8vIEFkZCBjbGFzcyhlcykgdG8gdGhlIG9uIGFuZCBvZmYgc3dpdGNoZXJcblx0XHRcdFx0J2NoZWNrZWQnOiBmYWxzZSAvLyBJbml0aWFsIHN0YXR1cyBvZiB0aGUgc3dpdGNoZXI6IHRydWUgPSBvbiwgZmFsc2UgPSBvZmZcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogU3RhdHVzIG9mIG1vdXNlIGRvd24gZXZlbnRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7Ym9vbGVhbn1cblx0XHRcdCAqL1xuXHRcdFx0bW91c2VEb3duID0gZmFsc2UsXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNZXRhIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHRcdC8qKlxuXHRcdCAqIENoYW5nZSB0aGUgc3R5bGluZyBvZiB0aGUgbmV3IHN3aXRjaGVyIGRlcGVuZGluZyBvbiB0aGUgb3JpZ2luYWwgY2hlY2tib3gvcmFkaW8gYm94IHNldHRpbmdcblx0XHQgKiBBZGRpdGlvbmFsbHkgc2V0IHRoZSBuZXcgc3RhdGUgb2YgdGhlIG9yaWdpbmFsIGNoZWNrYm94L3JhZGlvIGJveCBhbmQgdHJpZ2dlciB0aGUgY2hhbmdlIGV2ZW50IG9uIGl0LlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3N3aXRjaGVyQ2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRpZiAoJCh0aGlzKS5oYXNDbGFzcygnZGlzYWJsZWQnKSkge1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdCRjaGVja2JveCA9ICRzZWxmLmZpbmQoJ2lucHV0OmNoZWNrYm94JyksXG5cdFx0XHRcdCRvbkVsZW1lbnQgPSAkc2VsZi5maW5kKCdpbnB1dDpyYWRpbycpLmZpcnN0KCksXG5cdFx0XHRcdCRvZmZFbGVtZW50ID0gJHNlbGYuZmluZCgnaW5wdXQ6cmFkaW8nKS5sYXN0KCksXG5cdFx0XHRcdCRzZWxlY3QgPSAkc2VsZi5maW5kKCdzZWxlY3QnKS5maXJzdCgpLFxuXHRcdFx0XHRkYXRhc2V0ID0gJHNlbGYucGFyZW50KCkuZGF0YSgnY2hlY2tib3gnKTtcblxuXHRcdFx0JHNlbGYudG9nZ2xlQ2xhc3MoJ2NoZWNrZWQnKTtcblxuXHRcdFx0JHNlbGYuZmluZCgnLnN0YXRlLWRlc2NyaXB0aW9uJykuc2hvdygpLmZhZGVPdXQoJ3Nsb3cnKTtcblxuXHRcdFx0JGNoZWNrYm94XG5cdFx0XHRcdC5wcm9wKCdjaGVja2VkJywgJHNlbGYuaGFzQ2xhc3MoJ2NoZWNrZWQnKSk7XG5cblx0XHRcdCRvbkVsZW1lbnRcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCAkc2VsZi5oYXNDbGFzcygnY2hlY2tlZCcpKTtcblxuXHRcdFx0JG9mZkVsZW1lbnRcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCAhJHNlbGYuaGFzQ2xhc3MoJ2NoZWNrZWQnKSk7XG5cblx0XHRcdCRzZWxlY3Rcblx0XHRcdFx0LmZpbmQoJ29wdGlvbicpXG5cdFx0XHRcdC5yZW1vdmVBdHRyKCdzZWxlY3RlZCcpO1xuXG5cdFx0XHR2YXIgc2VsZWN0T3B0aW9uVG9TZWxlY3QgPSAkc2VsZi5oYXNDbGFzcygnY2hlY2tlZCcpID8gMSA6IDA7XG5cblx0XHRcdCRzZWxlY3Rcblx0XHRcdFx0LmZpbmQoJ29wdGlvblt2YWx1ZT1cIicgKyBzZWxlY3RPcHRpb25Ub1NlbGVjdCArICdcIl0nKVxuXHRcdFx0XHQuYXR0cignc2VsZWN0ZWQnLCB0cnVlKTtcblxuXHRcdFx0aWYgKG9wdGlvbnMub25fdXJsICE9PSAnJyAmJiBvcHRpb25zLm9mZl91cmwgIT09ICcnKSB7XG5cdFx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHRcdGlmIChvcHRpb25zLmNoZWNrZWQpIHtcblx0XHRcdFx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IG9wdGlvbnMub2ZmX3VybDtcblx0XHRcdFx0XHRvcHRpb25zLmNoZWNrZWQgPSBmYWxzZTtcblxuXHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gb3B0aW9ucy5vbl91cmw7XG5cdFx0XHRcdG9wdGlvbnMuY2hlY2tlZCA9IHRydWU7XG5cdFx0XHR9XG5cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogQ2hhbmdlIHRoZSBzdHlsaW5nIG9mIHRoZSBuZXcgY2hlY2tib3ggZGVwZW5kaW5nIG9uIHRoZSBvcmlnaW5hbCBjaGVja2JveCBzZXR0aW5nXG5cdFx0ICogQWRkaXRpb25hbGx5IHNldCB0aGUgbmV3IHN0YXRlIG9mIHRoZSBvcmlnaW5hbCBjaGVja2JveCBhbmQgdHJpZ2dlciB0aGUgY2hhbmdlIGV2ZW50IG9uIGl0LlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NoZWNrYm94TW91c2VEb3duSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdC8vZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuXHRcdFx0aWYgKCQodGhpcykuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXG5cdFx0XHRtb3VzZURvd24gPSB0cnVlO1xuXG5cdFx0XHQkKHRoaXMpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykuZm9jdXMoKTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSW1pdGF0ZSBtb3VzZSB1cCBiZWhhdmlvdXIgb2YgdGhlIGNoZWNrYm94LlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NoZWNrYm94TW91c2VVcEhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHQvL2Uuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdGlmICgkKHRoaXMpLmhhc0NsYXNzKCdkaXNhYmxlZCcpKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblxuXHRcdFx0JCh0aGlzKS50b2dnbGVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0JCh0aGlzKS5maW5kKCdpbnB1dDpjaGVja2JveCcpLmZvY3VzKCk7XG5cdFx0XHQvLyQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS50cmlnZ2VyKCdjbGljaycpO1xuXG5cdFx0XHRtb3VzZURvd24gPSBmYWxzZTtcblx0XHR9O1xuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElTQVRJT04gRlVOQ1RJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvKipcblx0XHQgKiBXcmFwIHRoZSBjaGVja2JveGVzIGFuZCBnZW5lcmF0ZSBtYXJrdXAgZm9yIHRoZSBuZXcgY2hlY2tib3ggc3R5bGUuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaW5pdENoZWNrYm94ZXMgPSBmdW5jdGlvbigkdGFyZ2V0KSB7XG5cblx0XHRcdHZhciAkY29udGFpbmVyID0gJHRhcmdldCB8fCAkdGhpcztcblxuXHRcdFx0JGNvbnRhaW5lclxuXHRcdFx0XHQuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKVxuXHRcdFx0XHQuZmlsdGVyKG9wdGlvbnMuZmlsdGVyIHx8ICcqJylcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdGRhdGFzZXQgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ2NoZWNrYm94JyksXG5cdFx0XHRcdFx0XHRjbGFzc05hbWUgPSBkYXRhc2V0LmNsYXNzTmFtZSB8fCAnJyxcblx0XHRcdFx0XHRcdHRpdGxlID0gJHNlbGYucHJvcCgndGl0bGUnKSxcblx0XHRcdFx0XHRcdGlzQ2hlY2tlZCA9ICgkc2VsZi5wcm9wKCdjaGVja2VkJykpID8gJ2NoZWNrZWQnIDogJycsXG5cdFx0XHRcdFx0XHRpc0Rpc2FibGVkID0gKCRzZWxmLnByb3AoJ2Rpc2FibGVkJykpID8gJ2Rpc2FibGVkJyA6ICcnO1xuXG5cdFx0XHRcdFx0aWYgKHR5cGVvZiAkc2VsZi5kYXRhKCdzaW5nbGVfY2hlY2tib3gnKSAhPT0gJ3VuZGVmaW5lZCcpIHtcblx0XHRcdFx0XHRcdCRzZWxmXG5cdFx0XHRcdFx0XHRcdC5jc3Moe1xuXHRcdFx0XHRcdFx0XHRcdCAgICAgJ3Bvc2l0aW9uJzogJ2Fic29sdXRlJyxcblx0XHRcdFx0XHRcdFx0XHQgICAgICdsZWZ0JzogJy0xMDAwMDBweCdcblx0XHRcdFx0XHRcdFx0ICAgICB9KVxuXHRcdFx0XHRcdFx0XHQud3JhcCgnPHNwYW4gY2xhc3M9XCJzaW5nbGUtY2hlY2tib3ggJyArIGlzQ2hlY2tlZCArICcgJyArIGlzRGlzYWJsZWQgKyAnXCIgdGl0bGU9XCInICtcblx0XHRcdFx0XHRcdFx0ICAgICAgdGl0bGUgKyAnXCI+PC9zcGFuPicpO1xuXG5cdFx0XHRcdFx0XHR2YXIgaWNvbkNsYXNzID0gKG9wdGlvbnMudXNlR2x5cGhpY29ucyAhPT0gdW5kZWZpbmVkKVxuXHRcdFx0XHRcdFx0XHQ/ICdnbHlwaGljb24gZ2x5cGhpY29uLW9rJ1xuXHRcdFx0XHRcdFx0XHQ6ICdmYSBmYS1jaGVjayc7XG5cblx0XHRcdFx0XHRcdCRzZWxmLnBhcmVudCgpLmFwcGVuZCgnPGkgY2xhc3M9XCInICsgaWNvbkNsYXNzICsgJ1wiPjwvaT4nKTtcblxuXHRcdFx0XHRcdFx0JHNlbGYub24oJ2ZvY3VzJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCQoJy5zaW5nbGVfY2hlY2tib3gnKS5yZW1vdmVDbGFzcygnZm9jdXNlZCcpO1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLnBhcmVudCgpLmFkZENsYXNzKCdmb2N1c2VkJyk7XG5cdFx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHRcdFx0JHNlbGYub24oJ2JsdXInLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnZm9jdXNlZCcpO1xuXHRcdFx0XHRcdFx0fSk7XG5cblx0XHRcdFx0XHRcdCRzZWxmLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0aWYgKG1vdXNlRG93biA9PT0gZmFsc2UpIHtcblx0XHRcdFx0XHRcdFx0XHQkKHRoaXMpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH0pO1xuXG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdHZhciBvblRleHQgPSAoJHNlbGYuYXR0cignZGF0YS1jaGVja2JveC1vbl90ZXh0JykpID8gJHNlbGYuYXR0cignZGF0YS1jaGVja2JveC1vbl90ZXh0JykgOlxuXHRcdFx0XHRcdFx0ICAgICAgICAgICAgICc8c3BhbiBjbGFzcz1cImZhIGZhLWNoZWNrXCI+PC9zcGFuPic7XG5cblx0XHRcdFx0XHRcdHZhciBvZmZUZXh0ID0gKCRzZWxmLmF0dHIoJ2RhdGEtY2hlY2tib3gtb25fdGV4dCcpKSA/ICRzZWxmLmF0dHIoJ2RhdGEtY2hlY2tib3gtb2ZmX3RleHQnKSA6XG5cdFx0XHRcdFx0XHQgICAgICAgICAgICAgICc8c3BhbiBjbGFzcz1cImZhIGZhLXRpbWVzXCI+PC9zcGFuPic7XG5cblx0XHRcdFx0XHRcdCRzZWxmXG5cdFx0XHRcdFx0XHRcdC53cmFwKCc8ZGl2IGNsYXNzPVwic3dpdGNoZXIgJyArIGlzQ2hlY2tlZCArICcgJyArIGlzRGlzYWJsZWQgKyAnXCIgdGl0bGU9XCInICsgdGl0bGUgK1xuXHRcdFx0XHRcdFx0XHQgICAgICAnXCI+PC9kaXY+Jylcblx0XHRcdFx0XHRcdFx0LnBhcmVudCgpXG5cdFx0XHRcdFx0XHRcdC5kYXRhKCdjaGVja2JveCcsIGRhdGFzZXQpXG5cdFx0XHRcdFx0XHRcdC5hZGRDbGFzcyhjbGFzc05hbWUpXG5cdFx0XHRcdFx0XHRcdC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcblx0XHRcdFx0XHRcdFx0ICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXN0YXRlLW9uXCI+JyArIG9uVGV4dCArICc8L2Rpdj4nICtcblx0XHRcdFx0XHRcdFx0ICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXN0YXRlLW9mZlwiPicgKyBvZmZUZXh0ICsgJzwvZGl2PicgKyAnPC9kaXY+JyArXG5cdFx0XHRcdFx0XHRcdCAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10ZXh0LW9uXCI+JyArIG9wdGlvbnMub25fdGV4dCArICc8L2Rpdj4nICtcblx0XHRcdFx0XHRcdFx0ICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXRleHQtb2ZmXCI+JyArIG9wdGlvbnMub2ZmX3RleHQgK1xuXHRcdFx0XHRcdFx0XHQgICAgICAgICc8L2Rpdj4nXG5cdFx0XHRcdFx0XHRcdCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogV3JhcCB0aGUgcmFkaW8gYm94ZXMgYW5kIGdlbmVyYXRlIG1hcmt1cCBmb3IgdGhlIG5ldyBjaGVja2JveCBzdHlsZS5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9pbml0UmFkaW9PcHRpb25zID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJHRoaXMuZmluZCgnaW5wdXQ6cmFkaW8nKS5maWx0ZXIob3B0aW9ucy5maWx0ZXIgfHwgJyonKS5sZW5ndGggPT09IDIpIHtcblx0XHRcdFx0dmFyICRvbkVsZW1lbnQgPSAkdGhpcy5maW5kKCdpbnB1dDpyYWRpbycpLmZpbHRlcihvcHRpb25zLmZpbHRlciB8fCAnKicpLmZpcnN0KCksXG5cdFx0XHRcdFx0b25UaXRsZSA9ICRvbkVsZW1lbnQucHJvcCgndGl0bGUnKSxcblx0XHRcdFx0XHQkb2ZmRWxlbWVudCA9ICR0aGlzLmZpbmQoJ2lucHV0OnJhZGlvJykuZmlsdGVyKG9wdGlvbnMuZmlsdGVyIHx8ICcqJykubGFzdCgpLFxuXHRcdFx0XHRcdG9mZlRpdGxlID0gJG9mZkVsZW1lbnQucHJvcCgndGl0bGUnKSxcblx0XHRcdFx0XHRvbkxhYmVsID0gKG9wdGlvbnMub25fbGFiZWwgIT09ICcnKSA/ICcgZGF0YS1jaGVja2JveC1sYWJlbD1cIicgKyBvcHRpb25zLm9uX2xhYmVsICsgJ1wiJyA6ICcnLFxuXHRcdFx0XHRcdG9mZkxhYmVsID0gKG9wdGlvbnMub2ZmX2xhYmVsICE9PSAnJykgPyAnIGRhdGEtY2hlY2tib3gtbGFiZWw9XCInICsgb3B0aW9ucy5vZmZfbGFiZWwgKyAnXCInIDpcblx0XHRcdFx0XHQgICAgICAgICAgICcnLFxuXHRcdFx0XHRcdGRhdGFzZXQgPSBvcHRpb25zLFxuXHRcdFx0XHRcdGlzQ2hlY2tlZCA9ICgkb25FbGVtZW50LnByb3AoJ2NoZWNrZWQnKSkgPyAnY2hlY2tlZCcgOiAnJyxcblx0XHRcdFx0XHRpc0Rpc2FibGVkID0gKCRvbkVsZW1lbnQucHJvcCgnZGlzYWJsZWQnKSkgPyAnZGlzYWJsZWQnIDogJyc7XG5cblx0XHRcdFx0dmFyICRzd2l0Y2hlciA9ICQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlciAnICsgaXNDaGVja2VkICsgJyAnICsgaXNEaXNhYmxlZCArICdcIj48L2Rpdj4nKTtcblxuXHRcdFx0XHQkb25FbGVtZW50LmFmdGVyKCRzd2l0Y2hlcik7XG5cblx0XHRcdFx0JG9uRWxlbWVudC5hcHBlbmRUbygkc3dpdGNoZXIpO1xuXHRcdFx0XHQkb2ZmRWxlbWVudC5hcHBlbmRUbygkc3dpdGNoZXIpO1xuXG5cdFx0XHRcdCRzd2l0Y2hlclxuXHRcdFx0XHRcdC5kYXRhKCdjaGVja2JveCcsIGRhdGFzZXQpXG5cdFx0XHRcdFx0LmFkZENsYXNzKG9wdGlvbnMuY2xhc3MpXG5cdFx0XHRcdFx0LmFwcGVuZCgnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXRvZ2dsZXJcIj48L2Rpdj4nICsgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1pbm5lclwiPicgK1xuXHRcdFx0XHRcdCAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vblwiIHRpdGxlPVwiJyArIG9uVGl0bGUgKyAnXCInICsgb25MYWJlbCArXG5cdFx0XHRcdFx0ICAgICAgICAnPjxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+PC9kaXY+JyArXG5cdFx0XHRcdFx0ICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXN0YXRlLW9mZlwiIHRpdGxlPVwiJyArIG9mZlRpdGxlICsgJ1wiJyArIG9mZkxhYmVsICtcblx0XHRcdFx0XHQgICAgICAgICc+PHNwYW4gY2xhc3M9XCJmYSBmYS10aW1lc1wiPjwvc3Bhbj48L2Rpdj4nICsgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10ZXh0LW9uXCI+J1xuXHRcdFx0XHRcdCAgICAgICAgKyBvcHRpb25zLm9uX3RleHQgK1xuXHRcdFx0XHRcdCAgICAgICAgJzwvZGl2PicgK1xuXHRcdFx0XHRcdCAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10ZXh0LW9mZlwiPicgKyBvcHRpb25zLm9mZl90ZXh0ICsgJzwvZGl2PicgKyAnPC9kaXY+J1xuXHRcdFx0XHRcdCk7XG5cblx0XHRcdFx0Ly8gdG9nZ2xlIHN3aXRjaGVyIGlmIGhpZGRlbiByYWRpbyBvcHRpb24gc3RhdHVzIGNoYW5nZXMgKHRoZXJlIGlzIG5vIGRlZmF1bHQgY2FzZSBmb3IgdGhhdClcblx0XHRcdFx0JG9uRWxlbWVudC5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS50b2dnbGVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0XHQvLyB0b2dnbGUgc3dpdGNoZXIgaWYgaGlkZGVuIHJhZGlvIG9wdGlvbiBzdGF0dXMgY2hhbmdlcyAodGhlcmUgaXMgbm8gZGVmYXVsdCBjYXNlIGZvciB0aGF0KVxuXHRcdFx0XHQkb2ZmRWxlbWVudC5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS50b2dnbGVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBCdWlsZCBtYXJrdXAgZm9yIHRoZSBVUkwgc3dpdGNoZXIuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaW5pdFVybFN3aXRjaGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAob3B0aW9ucy5vbl91cmwgIT09ICcnICYmIG9wdGlvbnMub2ZmX3VybCAhPT0gJycpIHtcblx0XHRcdFx0dmFyIGRhdGFzZXQgPSAkdGhpcy5wYXJzZU1vZHVsZURhdGEoJ2NoZWNrYm94JyksXG5cdFx0XHRcdFx0b25MYWJlbCA9IChvcHRpb25zLm9uX2xhYmVsICE9PSAnJykgPyAnIGRhdGEtY2hlY2tib3gtbGFiZWw9XCInICsgb3B0aW9ucy5vbl9sYWJlbCArICdcIicgOiAnJyxcblx0XHRcdFx0XHRvZmZMYWJlbCA9IChvcHRpb25zLm9mZl9sYWJlbCAhPT0gJycpID8gJyBkYXRhLWNoZWNrYm94LWxhYmVsPVwiJyArIG9wdGlvbnMub2ZmX2xhYmVsICsgJ1wiJyA6XG5cdFx0XHRcdFx0ICAgICAgICAgICAnJyxcblx0XHRcdFx0XHRpc0NoZWNrZWQgPSAob3B0aW9ucy5jaGVja2VkKSA/ICdjaGVja2VkJyA6ICcnO1xuXG5cdFx0XHRcdCR0aGlzXG5cdFx0XHRcdFx0LmRhdGEoJ2NoZWNrYm94JywgZGF0YXNldClcblx0XHRcdFx0XHQuYWRkQ2xhc3MoJ3N3aXRjaGVyJylcblx0XHRcdFx0XHQuYWRkQ2xhc3MoaXNDaGVja2VkKVxuXHRcdFx0XHRcdC5hZGRDbGFzcyhvcHRpb25zLmNsYXNzKVxuXHRcdFx0XHRcdC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcblx0XHRcdFx0XHQgICAgICAgICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb25cIiB0aXRsZT1cIicgKyBvcHRpb25zLm9mZl91cmwgKyAnXCInICsgb25MYWJlbCArXG5cdFx0XHRcdFx0ICAgICAgICAnPjxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb2ZmXCIgdGl0bGU9XCInICtcblx0XHRcdFx0XHQgICAgICAgIG9wdGlvbnMub25fdXJsICsgJ1wiJyArXG5cdFx0XHRcdFx0ICAgICAgICBvZmZMYWJlbCArICc+PHNwYW4gY2xhc3M9XCJmYSBmYS10aW1lc1wiPjwvc3Bhbj48L2Rpdj4nICsgJzwvZGl2Pidcblx0XHRcdFx0XHQpXG5cdFx0XHRcdFx0Lm9uKCdjbGljaycsIF9zd2l0Y2hlckNoYW5nZUhhbmRsZXIpO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBCaW5kIGV2ZW50cyB0aGF0IGNoYW5nZSB0aGUgY2hlY2tib3ggb3Igc3dpdGNoZXIuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaW5pdEV2ZW50SGFuZGxlcnMgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLm9uKCdjbGljaycsICcuc3dpdGNoZXInLCBfc3dpdGNoZXJDaGFuZ2VIYW5kbGVyKTtcblxuXHRcdFx0JHRoaXMub2ZmKCdtb3VzZWRvd24nLCAnLnNpbmdsZS1jaGVja2JveCcpO1xuXHRcdFx0JHRoaXMub24oJ21vdXNlZG93bicsICcuc2luZ2xlLWNoZWNrYm94JywgX2NoZWNrYm94TW91c2VEb3duSGFuZGxlcik7XG5cdFx0XHQkdGhpcy5vZmYoJ21vdXNldXAnLCAnLnNpbmdsZS1jaGVja2JveCcpO1xuXHRcdFx0JHRoaXMub24oJ21vdXNldXAnLCAnLnNpbmdsZS1jaGVja2JveCcsIF9jaGVja2JveE1vdXNlVXBIYW5kbGVyKTtcblxuXHRcdFx0JHRoaXMub24oJ21vdXNlZG93bicsICdsYWJlbCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRtb3VzZURvd24gPSB0cnVlO1xuXHRcdFx0fSk7XG5cblx0XHRcdCR0aGlzLm9uKCdtb3VzZXVwJywgJ2xhYmVsJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdG1vdXNlRG93biA9IGZhbHNlO1xuXHRcdFx0fSk7XG5cblx0XHRcdCR0aGlzLm9uKCdGT1JNX1VQREFURScsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dmFyICR0YXJnZXQgPSAkKGUudGFyZ2V0KTtcblx0XHRcdFx0JHRhcmdldFxuXHRcdFx0XHRcdC5maW5kKCdpbnB1dDpjaGVja2JveCcpXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHQkd3JhcHBlciA9ICRzZWxmLmNsb3Nlc3QoJy5zd2l0Y2hlcicpO1xuXG5cdFx0XHRcdFx0XHRpZiAoJHdyYXBwZXIubGVuZ3RoKSB7XG5cdFx0XHRcdFx0XHRcdCR3cmFwcGVyXG5cdFx0XHRcdFx0XHRcdFx0LmZpbmQoJ2RpdicpXG5cdFx0XHRcdFx0XHRcdFx0LnJlbW92ZSgpO1xuXHRcdFx0XHRcdFx0XHQkc2VsZi51bndyYXAoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHRfaW5pdENoZWNrYm94ZXMoJHRhcmdldCk7XG5cdFx0XHR9KTtcblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBDb252ZXJ0IFwieWVzL25vXCIgc2VsZWN0IGVsZW1lbnRzIHRvIGEgc3dpdGNoZXIuXG5cdFx0ICpcblx0XHQgKiBUaGUgc2VsZWN0cyBtdXN0IGhhdmUgYSBcImRhdGEtY29udmVydC1jaGVja2JveFwiIGF0dHJpYnV0ZSBpbiBvcmRlciB0byBiZSBwcm9jZXNzZWQgYnlcblx0XHQgKiB0aGlzIG1ldGhvZC5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9pbml0U2VsZWN0cyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gSXRlcmF0ZSBvdmVyIHNlbGVjdCBmaWVsZHNcblx0XHRcdCR0aGlzLmZpbmQoJ1tkYXRhLWNvbnZlcnQtY2hlY2tib3hdJykuZWFjaChmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuXHRcdFx0XHQvLyBTZWxlY3RvcnMgZlxuXHRcdFx0XHR2YXIgJG9wdGlvblRydWUgPSAkKGVsZW1lbnQpLmZpbmQoJ29wdGlvblt2YWx1ZT1cIjFcIl0nKSxcblx0XHRcdFx0XHQkb3B0aW9uRmFsc2UgPSAkKGVsZW1lbnQpLmZpbmQoJ29wdGlvblt2YWx1ZT1cIjBcIl0nKTtcblxuXHRcdFx0XHQvLyBTdGF0ZXNcblx0XHRcdFx0dmFyIGlzQ2hlY2tlZCA9ICRvcHRpb25UcnVlLmlzKCc6c2VsZWN0ZWQnKSA/ICdjaGVja2VkJyA6ICcnLFxuXHRcdFx0XHRcdGlzRGlzYWJsZWQgPSAkKGVsZW1lbnQpLmlzKCc6ZGlzYWJsZWQnKSA/ICdkaXNhYmxlZCcgOiAnJztcblxuXHRcdFx0XHQvLyBTd2l0Y2hlciBUZW1wbGF0ZVxuXHRcdFx0XHR2YXIgJHN3aXRjaGVyID0gJCgnPGRpdiBjbGFzcz1cInN3aXRjaGVyICcgKyBpc0NoZWNrZWQgKyAnICcgKyBpc0Rpc2FibGVkICsgJ1wiPjwvZGl2PicpO1xuXHRcdFx0XHQkc3dpdGNoZXJcblx0XHRcdFx0XHQuYWRkQ2xhc3MoJChlbGVtZW50KS5kYXRhKCduZXdDbGFzcycpKVxuXHRcdFx0XHRcdC5kYXRhKCdjaGVja2JveCcsIG9wdGlvbnMpXG5cdFx0XHRcdFx0LmFwcGVuZCgnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXRvZ2dsZXJcIj48L2Rpdj4nICsgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1pbm5lclwiPicgK1xuXHRcdFx0XHRcdCAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vblwiPjxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+PC9kaXY+JyArXG5cdFx0XHRcdFx0ICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXN0YXRlLW9mZlwiPjxzcGFuIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L3NwYW4+PC9kaXY+JyArICc8L2Rpdj4nXG5cdFx0XHRcdFx0KTtcblxuXHRcdFx0XHQkKGVsZW1lbnQpXG5cdFx0XHRcdFx0LmFmdGVyKCRzd2l0Y2hlcilcblx0XHRcdFx0XHQuYXBwZW5kVG8oJHN3aXRjaGVyKVxuXHRcdFx0XHRcdC5oaWRlKCk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0X2luaXRDaGVja2JveGVzKCk7XG5cdFx0XHRfaW5pdFJhZGlvT3B0aW9ucygpO1xuXHRcdFx0X2luaXRTZWxlY3RzKCk7XG5cdFx0XHRfaW5pdFVybFN3aXRjaGVyKCk7XG5cdFx0XHRfaW5pdEV2ZW50SGFuZGxlcnMoKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
