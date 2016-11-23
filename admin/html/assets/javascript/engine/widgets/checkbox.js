'use strict';

/* --------------------------------------------------------------
 checkbox.js 2016-06-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Checkbox Widget
 *
 * This extension can serve multiple types of checkboxes (simple switchers, text switchers and gambio-styled
 * checkboxes, radio-button switcher). Apply the widget in a parent container and it will search and convert 
 * all the instances into fine checkboxes
 * 
 * ### Options 
 * 
 * **Filter | `data-checkbox-filter` | String | Optional**
 * 
 * Provide a jQuery selector string for filtering the children elements of the parent container. 
 * 
 * **Checked State URL | `data-checkbox-on_url` | String | Optional**
 * 
 * If provided the user will be navigated to the given URL once he clicks a checked instance of the widget. 
 * 
 * **Unchecked State URL | `dat-acheckbox-off_url` | String | Optional**
 * 
 * If provided the user will be navigated ot the given URL once he clicks an unchecked instance of the widget.
 * 
 * **Checked State Text | `data-checkbox-on_text` | String | Optional** 
 * 
 * If provided it will be displayed instead of the normal check icon. 
 *
 * **Unchecked State Text | `data-checkbox-off_text` | String | Optional**
 *
 * If provided it will be displayed instead of the normal X icon.
 * 
 * **Custom Checkbox Class | `data-checkbox-class` | String | Optional**
 * 
 * Provide additional custom classes to the checkbox element. 
 * 
 * **Check Status | `data-checkbox-check` | Boolean | Optional**
 * 
 * Defines whether the checkbox is checked or not. Use this option to override the original checkbox state.
 * 
 * ### Examples
 * 
 * **Single Checkbox Example**
 * 
 * A single checkbox is just a better styled checkbox that can be used for seamless integration into the 
 * Gambio Admin pages. 
 * 
 * ```html
 * <label for="my-checkbox">Single Checkbox (checked)</label>
 * <input type="checkbox" id="my-checkbox" title="Single Checkbox" data-single_checkbox checked />
 * ```
 * 
 * **Switcher Checkbox**
 * 
 * Displays a nice mobile-like switcher that is bound on the original checkbox. That means that any change done 
 * on the switcher will affect the original checkbox element. 
 * 
 * ```html 
 * <label for="my-checkbox">Receive Notifications</label>
 * <input type="checkbox" id="my-checkbox" title="Receive Notifications" />
 * ```
 * 
 * **Radio Checkbox**
 * 
 * The checkbox widget can also serve cases with two radio buttons that define a yes or no use case. Consider
 * the following example where the first radio element contains the "activate" and the second "deactivate" status.
 * 
 * ```html
 * <input type="radio" name="status" value="1" title="Activated" checked />
 * <input type="radio" name="status" value="0" title="Deactivated" />
 * ```
 * 
 * **URL Switcher**
 * 
 * If you need to change the status of something by navigating the user to a specific url use the "on_url" 
 * and "off_url" options which will forward the user to the required URL. 
 * 
 * ```html 
 * <div data-gx-widget="checkbox"
 *   data-checkbox-checked="true"
 *   data-checkbox-on_url="#installed"
 *   data-checkbox-off_url="#uninstalled"
 *   data-checkbox-on_label="Installed"
 *   data-checkbox-off_label="Uninstalled"
 *   data-checkbox-class="labeled"></div>
 * ```
 * 
 * **Notice:** This widget was highly modified for use in compatibility pages. It's complexity and performance
 * are not optimal anymore. Use the single_checkbox and switcher widgets instead.
 * 
 * @module Admin/Widgets/checkbox
 */
gx.widgets.module('checkbox', ['fallback'], function (data) {

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
  * Module Object
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

		$checkbox.prop('checked', $self.hasClass('checked')).trigger('checkbox:change');

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
	var _checkboxChangeHandler = function _checkboxChangeHandler() {
		if ($(this).hasClass('disabled')) {
			return false;
		}

		mouseDown = true;
		$(this).find('input:checkbox').focus();
	};

	/**
  * Imitate mouse up behaviour of the checkbox
  *
  * @private
  */
	var _checkboxMouseUpHandler = function _checkboxMouseUpHandler() {
		if ($(this).hasClass('disabled')) {
			return false;
		}

		$(this).toggleClass('checked');
		$(this).find('input:checkbox').focus();
		$(this).find('input:checkbox').trigger('click');
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
			    dataset = jse.libs.fallback._data($self, 'checkbox'),
			    className = dataset.className || '',
			    title = $self.prop('title'),
			    isChecked = $self.prop('checked') ? 'checked' : '',
			    isDisabled = $self.prop('disabled') ? 'disabled' : '';

			if (typeof $self.data('single_checkbox') !== 'undefined') {
				$self.css({
					'position': 'absolute',
					'left': '-100000px'
				}).wrap('<span class="single-checkbox ' + isChecked + ' ' + isDisabled + '" title="' + title + '"></span>').parent().append('<i class="fa fa-check"></i>');

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
  * build markup for the url switcher
  *
  * @private
  */
	var _initUrlSwitcher = function _initUrlSwitcher() {
		if (options.on_url !== '' && options.off_url !== '') {
			var dataset = jse.libs.fallback._data($this, 'checkbox'),
			    onLabel = options.on_label !== '' ? ' data-checkbox-label="' + options.on_label + '"' : '',
			    offLabel = options.off_label !== '' ? ' data-checkbox-label="' + options.off_label + '"' : '',
			    isChecked = options.checked ? 'checked' : '';

			$this.data('checkbox', dataset).addClass('switcher').addClass(isChecked).addClass(options.class).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on" title="' + options.off_url + '"' + onLabel + '><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off" title="' + options.on_url + '"' + offLabel + '><span class="fa fa-times"></span></div>' + '</div>').on('click', _switcherChangeHandler);
		}
	};

	/**
  * Bind events that change the checkbox or switcher
  *
  * @private
  */
	var _initEventHandlers = function _initEventHandlers() {
		$this.on('click', '.switcher', _switcherChangeHandler);

		$this.off('mousedown', '.single-checkbox');
		$this.on('mousedown', '.single-checkbox', _checkboxChangeHandler);
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

	var _initSelects = function _initSelects() {
		// Iterate over select fields
		$this.find('[data-convert-checkbox]').each(function (index, element) {
			// Shortcuts
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

		// sanitize url preventing cross site scripting
		options.on_url = options.on_url.replace('"', '');
		options.off_url = options.off_url.replace('"', '');

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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNoZWNrYm94LmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm1vdXNlRG93biIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc3dpdGNoZXJDaGFuZ2VIYW5kbGVyIiwiZXZlbnQiLCJoYXNDbGFzcyIsIiRzZWxmIiwiJGNoZWNrYm94IiwiZmluZCIsIiRvbkVsZW1lbnQiLCJmaXJzdCIsIiRvZmZFbGVtZW50IiwibGFzdCIsIiRzZWxlY3QiLCJkYXRhc2V0IiwicGFyZW50IiwidG9nZ2xlQ2xhc3MiLCJzaG93IiwiZmFkZU91dCIsInByb3AiLCJ0cmlnZ2VyIiwicmVtb3ZlQXR0ciIsInNlbGVjdE9wdGlvblRvU2VsZWN0IiwiYXR0ciIsIm9uX3VybCIsIm9mZl91cmwiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsImNoZWNrZWQiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJfY2hlY2tib3hDaGFuZ2VIYW5kbGVyIiwiZm9jdXMiLCJfY2hlY2tib3hNb3VzZVVwSGFuZGxlciIsIl9pbml0Q2hlY2tib3hlcyIsIiR0YXJnZXQiLCIkY29udGFpbmVyIiwiZmlsdGVyIiwiZWFjaCIsImpzZSIsImxpYnMiLCJmYWxsYmFjayIsIl9kYXRhIiwiY2xhc3NOYW1lIiwidGl0bGUiLCJpc0NoZWNrZWQiLCJpc0Rpc2FibGVkIiwiY3NzIiwid3JhcCIsImFwcGVuZCIsIm9uIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIm9uVGV4dCIsIm9mZlRleHQiLCJvbl90ZXh0Iiwib2ZmX3RleHQiLCJfaW5pdFJhZGlvT3B0aW9ucyIsImxlbmd0aCIsIm9uVGl0bGUiLCJvZmZUaXRsZSIsIm9uTGFiZWwiLCJvbl9sYWJlbCIsIm9mZkxhYmVsIiwib2ZmX2xhYmVsIiwiJHN3aXRjaGVyIiwiYWZ0ZXIiLCJhcHBlbmRUbyIsImNsYXNzIiwiX2luaXRVcmxTd2l0Y2hlciIsIl9pbml0RXZlbnRIYW5kbGVycyIsIm9mZiIsImUiLCJ0YXJnZXQiLCIkd3JhcHBlciIsImNsb3Nlc3QiLCJyZW1vdmUiLCJ1bndyYXAiLCJfaW5pdFNlbGVjdHMiLCJpbmRleCIsImVsZW1lbnQiLCIkb3B0aW9uVHJ1ZSIsIiRvcHRpb25GYWxzZSIsImlzIiwiaGlkZSIsImluaXQiLCJkb25lIiwicmVwbGFjZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlGQUEsR0FBR0MsT0FBSCxDQUFXQyxNQUFYLENBQ0MsVUFERCxFQUdDLENBQUMsVUFBRCxDQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVixZQUFVLEVBREEsRUFDSTs7QUFFZDtBQUNBLFlBQVUsRUFKQSxFQUlJO0FBQ2QsYUFBVyxFQUxELEVBS0s7QUFDZixjQUFZLEVBTkYsRUFNTTtBQUNoQixlQUFhLEVBUEgsRUFPTztBQUNqQixhQUFXLEVBUkQsRUFRSztBQUNmLGNBQVksRUFURixFQVNNO0FBQ2hCLFdBQVMsRUFWQyxFQVVHO0FBQ2IsYUFBVyxLQVhELENBV087QUFYUCxFQWJaOzs7QUEyQkM7Ozs7O0FBS0FDLGFBQVksS0FoQ2I7OztBQWtDQzs7Ozs7QUFLQUMsV0FBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkgsSUFBN0IsQ0F2Q1g7OztBQXlDQzs7Ozs7QUFLQUQsVUFBUyxFQTlDVjs7QUFnREE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFNQSxLQUFJUSx5QkFBeUIsU0FBekJBLHNCQUF5QixDQUFTQyxLQUFULEVBQWdCO0FBQzVDLE1BQUlOLEVBQUUsSUFB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QTNELENBQWhCO0FBQ0FtQixhQUNFYixRQURGLENBQ1d2RCxFQUFFbUYsT0FBRixFQUFXckYsSUFBWCxDQUFnQixVQUFoQixDQURYLEVBRUVBLElBRkYsQ0FFTyxVQUZQLEVBRW1CSyxPQUZuQixFQUdFaUQsTUFIRixDQUdTLHlDQUF5Qyw4QkFBekMsR0FDUCx3RUFETyxHQUVQLHlFQUZPLEdBRXFFLFFBTDlFOztBQVFBcEQsS0FBRW1GLE9BQUYsRUFDRWQsS0FERixDQUNRRCxTQURSLEVBRUVFLFFBRkYsQ0FFV0YsU0FGWCxFQUdFbUIsSUFIRjtBQUlBLEdBdkJEO0FBd0JBLEVBMUJEOztBQTRCQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBMUYsUUFBTzJGLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCO0FBQ0F0RixVQUFRdUIsTUFBUixHQUFpQnZCLFFBQVF1QixNQUFSLENBQWVnRSxPQUFmLENBQXVCLEdBQXZCLEVBQTRCLEVBQTVCLENBQWpCO0FBQ0F2RixVQUFRd0IsT0FBUixHQUFrQnhCLFFBQVF3QixPQUFSLENBQWdCK0QsT0FBaEIsQ0FBd0IsR0FBeEIsRUFBNkIsRUFBN0IsQ0FBbEI7O0FBRUFyRDtBQUNBdUI7QUFDQXFCO0FBQ0FUO0FBQ0FDOztBQUVBZ0I7QUFDQSxFQWJEOztBQWVBO0FBQ0EsUUFBTzVGLE1BQVA7QUFDQSxDQWpaRiIsImZpbGUiOiJjaGVja2JveC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY2hlY2tib3guanMgMjAxNi0wNi0wMVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQ2hlY2tib3ggV2lkZ2V0XG4gKlxuICogVGhpcyBleHRlbnNpb24gY2FuIHNlcnZlIG11bHRpcGxlIHR5cGVzIG9mIGNoZWNrYm94ZXMgKHNpbXBsZSBzd2l0Y2hlcnMsIHRleHQgc3dpdGNoZXJzIGFuZCBnYW1iaW8tc3R5bGVkXG4gKiBjaGVja2JveGVzLCByYWRpby1idXR0b24gc3dpdGNoZXIpLiBBcHBseSB0aGUgd2lkZ2V0IGluIGEgcGFyZW50IGNvbnRhaW5lciBhbmQgaXQgd2lsbCBzZWFyY2ggYW5kIGNvbnZlcnQgXG4gKiBhbGwgdGhlIGluc3RhbmNlcyBpbnRvIGZpbmUgY2hlY2tib3hlc1xuICogXG4gKiAjIyMgT3B0aW9ucyBcbiAqIFxuICogKipGaWx0ZXIgfCBgZGF0YS1jaGVja2JveC1maWx0ZXJgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICogXG4gKiBQcm92aWRlIGEgalF1ZXJ5IHNlbGVjdG9yIHN0cmluZyBmb3IgZmlsdGVyaW5nIHRoZSBjaGlsZHJlbiBlbGVtZW50cyBvZiB0aGUgcGFyZW50IGNvbnRhaW5lci4gXG4gKiBcbiAqICoqQ2hlY2tlZCBTdGF0ZSBVUkwgfCBgZGF0YS1jaGVja2JveC1vbl91cmxgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICogXG4gKiBJZiBwcm92aWRlZCB0aGUgdXNlciB3aWxsIGJlIG5hdmlnYXRlZCB0byB0aGUgZ2l2ZW4gVVJMIG9uY2UgaGUgY2xpY2tzIGEgY2hlY2tlZCBpbnN0YW5jZSBvZiB0aGUgd2lkZ2V0LiBcbiAqIFxuICogKipVbmNoZWNrZWQgU3RhdGUgVVJMIHwgYGRhdC1hY2hlY2tib3gtb2ZmX3VybGAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKiBcbiAqIElmIHByb3ZpZGVkIHRoZSB1c2VyIHdpbGwgYmUgbmF2aWdhdGVkIG90IHRoZSBnaXZlbiBVUkwgb25jZSBoZSBjbGlja3MgYW4gdW5jaGVja2VkIGluc3RhbmNlIG9mIHRoZSB3aWRnZXQuXG4gKiBcbiAqICoqQ2hlY2tlZCBTdGF0ZSBUZXh0IHwgYGRhdGEtY2hlY2tib3gtb25fdGV4dGAgfCBTdHJpbmcgfCBPcHRpb25hbCoqIFxuICogXG4gKiBJZiBwcm92aWRlZCBpdCB3aWxsIGJlIGRpc3BsYXllZCBpbnN0ZWFkIG9mIHRoZSBub3JtYWwgY2hlY2sgaWNvbi4gXG4gKlxuICogKipVbmNoZWNrZWQgU3RhdGUgVGV4dCB8IGBkYXRhLWNoZWNrYm94LW9mZl90ZXh0YCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqXG4gKiBJZiBwcm92aWRlZCBpdCB3aWxsIGJlIGRpc3BsYXllZCBpbnN0ZWFkIG9mIHRoZSBub3JtYWwgWCBpY29uLlxuICogXG4gKiAqKkN1c3RvbSBDaGVja2JveCBDbGFzcyB8IGBkYXRhLWNoZWNrYm94LWNsYXNzYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqIFxuICogUHJvdmlkZSBhZGRpdGlvbmFsIGN1c3RvbSBjbGFzc2VzIHRvIHRoZSBjaGVja2JveCBlbGVtZW50LiBcbiAqIFxuICogKipDaGVjayBTdGF0dXMgfCBgZGF0YS1jaGVja2JveC1jaGVja2AgfCBCb29sZWFuIHwgT3B0aW9uYWwqKlxuICogXG4gKiBEZWZpbmVzIHdoZXRoZXIgdGhlIGNoZWNrYm94IGlzIGNoZWNrZWQgb3Igbm90LiBVc2UgdGhpcyBvcHRpb24gdG8gb3ZlcnJpZGUgdGhlIG9yaWdpbmFsIGNoZWNrYm94IHN0YXRlLlxuICogXG4gKiAjIyMgRXhhbXBsZXNcbiAqIFxuICogKipTaW5nbGUgQ2hlY2tib3ggRXhhbXBsZSoqXG4gKiBcbiAqIEEgc2luZ2xlIGNoZWNrYm94IGlzIGp1c3QgYSBiZXR0ZXIgc3R5bGVkIGNoZWNrYm94IHRoYXQgY2FuIGJlIHVzZWQgZm9yIHNlYW1sZXNzIGludGVncmF0aW9uIGludG8gdGhlIFxuICogR2FtYmlvIEFkbWluIHBhZ2VzLiBcbiAqIFxuICogYGBgaHRtbFxuICogPGxhYmVsIGZvcj1cIm15LWNoZWNrYm94XCI+U2luZ2xlIENoZWNrYm94IChjaGVja2VkKTwvbGFiZWw+XG4gKiA8aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgaWQ9XCJteS1jaGVja2JveFwiIHRpdGxlPVwiU2luZ2xlIENoZWNrYm94XCIgZGF0YS1zaW5nbGVfY2hlY2tib3ggY2hlY2tlZCAvPlxuICogYGBgXG4gKiBcbiAqICoqU3dpdGNoZXIgQ2hlY2tib3gqKlxuICogXG4gKiBEaXNwbGF5cyBhIG5pY2UgbW9iaWxlLWxpa2Ugc3dpdGNoZXIgdGhhdCBpcyBib3VuZCBvbiB0aGUgb3JpZ2luYWwgY2hlY2tib3guIFRoYXQgbWVhbnMgdGhhdCBhbnkgY2hhbmdlIGRvbmUgXG4gKiBvbiB0aGUgc3dpdGNoZXIgd2lsbCBhZmZlY3QgdGhlIG9yaWdpbmFsIGNoZWNrYm94IGVsZW1lbnQuIFxuICogXG4gKiBgYGBodG1sIFxuICogPGxhYmVsIGZvcj1cIm15LWNoZWNrYm94XCI+UmVjZWl2ZSBOb3RpZmljYXRpb25zPC9sYWJlbD5cbiAqIDxpbnB1dCB0eXBlPVwiY2hlY2tib3hcIiBpZD1cIm15LWNoZWNrYm94XCIgdGl0bGU9XCJSZWNlaXZlIE5vdGlmaWNhdGlvbnNcIiAvPlxuICogYGBgXG4gKiBcbiAqICoqUmFkaW8gQ2hlY2tib3gqKlxuICogXG4gKiBUaGUgY2hlY2tib3ggd2lkZ2V0IGNhbiBhbHNvIHNlcnZlIGNhc2VzIHdpdGggdHdvIHJhZGlvIGJ1dHRvbnMgdGhhdCBkZWZpbmUgYSB5ZXMgb3Igbm8gdXNlIGNhc2UuIENvbnNpZGVyXG4gKiB0aGUgZm9sbG93aW5nIGV4YW1wbGUgd2hlcmUgdGhlIGZpcnN0IHJhZGlvIGVsZW1lbnQgY29udGFpbnMgdGhlIFwiYWN0aXZhdGVcIiBhbmQgdGhlIHNlY29uZCBcImRlYWN0aXZhdGVcIiBzdGF0dXMuXG4gKiBcbiAqIGBgYGh0bWxcbiAqIDxpbnB1dCB0eXBlPVwicmFkaW9cIiBuYW1lPVwic3RhdHVzXCIgdmFsdWU9XCIxXCIgdGl0bGU9XCJBY3RpdmF0ZWRcIiBjaGVja2VkIC8+XG4gKiA8aW5wdXQgdHlwZT1cInJhZGlvXCIgbmFtZT1cInN0YXR1c1wiIHZhbHVlPVwiMFwiIHRpdGxlPVwiRGVhY3RpdmF0ZWRcIiAvPlxuICogYGBgXG4gKiBcbiAqICoqVVJMIFN3aXRjaGVyKipcbiAqIFxuICogSWYgeW91IG5lZWQgdG8gY2hhbmdlIHRoZSBzdGF0dXMgb2Ygc29tZXRoaW5nIGJ5IG5hdmlnYXRpbmcgdGhlIHVzZXIgdG8gYSBzcGVjaWZpYyB1cmwgdXNlIHRoZSBcIm9uX3VybFwiIFxuICogYW5kIFwib2ZmX3VybFwiIG9wdGlvbnMgd2hpY2ggd2lsbCBmb3J3YXJkIHRoZSB1c2VyIHRvIHRoZSByZXF1aXJlZCBVUkwuIFxuICogXG4gKiBgYGBodG1sIFxuICogPGRpdiBkYXRhLWd4LXdpZGdldD1cImNoZWNrYm94XCJcbiAqICAgZGF0YS1jaGVja2JveC1jaGVja2VkPVwidHJ1ZVwiXG4gKiAgIGRhdGEtY2hlY2tib3gtb25fdXJsPVwiI2luc3RhbGxlZFwiXG4gKiAgIGRhdGEtY2hlY2tib3gtb2ZmX3VybD1cIiN1bmluc3RhbGxlZFwiXG4gKiAgIGRhdGEtY2hlY2tib3gtb25fbGFiZWw9XCJJbnN0YWxsZWRcIlxuICogICBkYXRhLWNoZWNrYm94LW9mZl9sYWJlbD1cIlVuaW5zdGFsbGVkXCJcbiAqICAgZGF0YS1jaGVja2JveC1jbGFzcz1cImxhYmVsZWRcIj48L2Rpdj5cbiAqIGBgYFxuICogXG4gKiAqKk5vdGljZToqKiBUaGlzIHdpZGdldCB3YXMgaGlnaGx5IG1vZGlmaWVkIGZvciB1c2UgaW4gY29tcGF0aWJpbGl0eSBwYWdlcy4gSXQncyBjb21wbGV4aXR5IGFuZCBwZXJmb3JtYW5jZVxuICogYXJlIG5vdCBvcHRpbWFsIGFueW1vcmUuIFVzZSB0aGUgc2luZ2xlX2NoZWNrYm94IGFuZCBzd2l0Y2hlciB3aWRnZXRzIGluc3RlYWQuXG4gKiBcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9jaGVja2JveFxuICovXG5neC53aWRnZXRzLm1vZHVsZShcblx0J2NoZWNrYm94Jyxcblx0XG5cdFsnZmFsbGJhY2snXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBXaWRnZXQgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdCdmaWx0ZXInOiAnJywgLy8gT25seSBzZWxlY3QgY2hlY2tib3hlcyB3aXRoIHRoZSBmb2xsb3dpbmcgc2VsZWN0b3Jcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFVybCBTd2l0Y2hlciBPcHRpb25zOlxuXHRcdFx0XHQnb25fdXJsJzogJycsIC8vIE9wZW4gdXJsIHdoZW4gc3dpdGNoZXIgaXMgdHVybmVkIG9uXG5cdFx0XHRcdCdvZmZfdXJsJzogJycsIC8vIE9wZW4gdXJsIHdoZW4gc3dpdGNoZXIgaXMgdHVybmVkIG9mZlxuXHRcdFx0XHQnb25fbGFiZWwnOiAnJywgLy8gVGV4dCBzaG93biBvbiB0aGUgc3dpdGNoZXIgd2hlbiB0dXJuZWQgb25cblx0XHRcdFx0J29mZl9sYWJlbCc6ICcnLCAvLyBUZXh0IHNob3duIG9uIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvZmZcblx0XHRcdFx0J29uX3RleHQnOiAnJywgLy8gVGV4dCBzaG93biBuZXh0IHRvIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvblxuXHRcdFx0XHQnb2ZmX3RleHQnOiAnJywgLy8gVGV4dCBzaG93biBuZXh0IHRvIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvZmZcblx0XHRcdFx0J2NsYXNzJzogJycsIC8vIEFkZCBjbGFzcyhlcykgdG8gdGhlIG9uIGFuZCBvZmYgc3dpdGNoZXJcblx0XHRcdFx0J2NoZWNrZWQnOiBmYWxzZSAvLyBJbml0aWFsIHN0YXR1cyBvZiB0aGUgc3dpdGNoZXI6IHRydWUgPSBvbiwgZmFsc2UgPSBvZmZcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogU3RhdHVzIG9mIG1vdXNlIGRvd24gZXZlbnRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7Ym9vbGVhbn1cblx0XHRcdCAqL1xuXHRcdFx0bW91c2VEb3duID0gZmFsc2UsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBDaGFuZ2UgdGhlIHN0eWxpbmcgb2YgdGhlIG5ldyBzd2l0Y2hlciBkZXBlbmRpbmcgb24gdGhlIG9yaWdpbmFsIGNoZWNrYm94L3JhZGlvIGJveCBzZXR0aW5nXG5cdFx0ICogQWRkaXRpb25hbGx5IHNldCB0aGUgbmV3IHN0YXRlIG9mIHRoZSBvcmlnaW5hbCBjaGVja2JveC9yYWRpbyBib3ggYW5kIHRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBvbiBpdC5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zd2l0Y2hlckNoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0aWYgKCQodGhpcykuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkY2hlY2tib3ggPSAkc2VsZi5maW5kKCdpbnB1dDpjaGVja2JveCcpLFxuXHRcdFx0XHQkb25FbGVtZW50ID0gJHNlbGYuZmluZCgnaW5wdXQ6cmFkaW8nKS5maXJzdCgpLFxuXHRcdFx0XHQkb2ZmRWxlbWVudCA9ICRzZWxmLmZpbmQoJ2lucHV0OnJhZGlvJykubGFzdCgpLFxuXHRcdFx0XHQkc2VsZWN0ID0gJHNlbGYuZmluZCgnc2VsZWN0JykuZmlyc3QoKSxcblx0XHRcdFx0ZGF0YXNldCA9ICRzZWxmLnBhcmVudCgpLmRhdGEoJ2NoZWNrYm94Jyk7XG5cdFx0XHRcblx0XHRcdCRzZWxmLnRvZ2dsZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcblx0XHRcdCRzZWxmLmZpbmQoJy5zdGF0ZS1kZXNjcmlwdGlvbicpLnNob3coKS5mYWRlT3V0KCdzbG93Jyk7XG5cdFx0XHRcblx0XHRcdCRjaGVja2JveFxuXHRcdFx0XHQucHJvcCgnY2hlY2tlZCcsICRzZWxmLmhhc0NsYXNzKCdjaGVja2VkJykpLnRyaWdnZXIoJ2NoZWNrYm94OmNoYW5nZScpO1xuXHRcdFx0XG5cdFx0XHQkb25FbGVtZW50XG5cdFx0XHRcdC5wcm9wKCdjaGVja2VkJywgJHNlbGYuaGFzQ2xhc3MoJ2NoZWNrZWQnKSk7XG5cdFx0XHRcblx0XHRcdCRvZmZFbGVtZW50XG5cdFx0XHRcdC5wcm9wKCdjaGVja2VkJywgISRzZWxmLmhhc0NsYXNzKCdjaGVja2VkJykpO1xuXHRcdFx0XG5cdFx0XHQkc2VsZWN0XG5cdFx0XHRcdC5maW5kKCdvcHRpb24nKVxuXHRcdFx0XHQucmVtb3ZlQXR0cignc2VsZWN0ZWQnKTtcblx0XHRcdFxuXHRcdFx0dmFyIHNlbGVjdE9wdGlvblRvU2VsZWN0ID0gJHNlbGYuaGFzQ2xhc3MoJ2NoZWNrZWQnKSA/IDEgOiAwO1xuXHRcdFx0XG5cdFx0XHQkc2VsZWN0XG5cdFx0XHRcdC5maW5kKCdvcHRpb25bdmFsdWU9XCInICsgc2VsZWN0T3B0aW9uVG9TZWxlY3QgKyAnXCJdJylcblx0XHRcdFx0LmF0dHIoJ3NlbGVjdGVkJywgdHJ1ZSk7XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLm9uX3VybCAhPT0gJycgJiYgb3B0aW9ucy5vZmZfdXJsICE9PSAnJykge1xuXHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChvcHRpb25zLmNoZWNrZWQpIHtcblx0XHRcdFx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IG9wdGlvbnMub2ZmX3VybDtcblx0XHRcdFx0XHRvcHRpb25zLmNoZWNrZWQgPSBmYWxzZTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gb3B0aW9ucy5vbl91cmw7XG5cdFx0XHRcdG9wdGlvbnMuY2hlY2tlZCA9IHRydWU7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENoYW5nZSB0aGUgc3R5bGluZyBvZiB0aGUgbmV3IGNoZWNrYm94IGRlcGVuZGluZyBvbiB0aGUgb3JpZ2luYWwgY2hlY2tib3ggc2V0dGluZ1xuXHRcdCAqIEFkZGl0aW9uYWxseSBzZXQgdGhlIG5ldyBzdGF0ZSBvZiB0aGUgb3JpZ2luYWwgY2hlY2tib3ggYW5kIHRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBvbiBpdC5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jaGVja2JveENoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdGlmICgkKHRoaXMpLmhhc0NsYXNzKCdkaXNhYmxlZCcpKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0bW91c2VEb3duID0gdHJ1ZTtcblx0XHRcdCQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5mb2N1cygpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW1pdGF0ZSBtb3VzZSB1cCBiZWhhdmlvdXIgb2YgdGhlIGNoZWNrYm94XG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hlY2tib3hNb3VzZVVwSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCQodGhpcykuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkKHRoaXMpLnRvZ2dsZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHQkKHRoaXMpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykuZm9jdXMoKTtcblx0XHRcdCQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0bW91c2VEb3duID0gZmFsc2U7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVNBVElPTiBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBXcmFwIHRoZSBjaGVja2JveGVzIGFuZCBnZW5lcmF0ZSBtYXJrdXAgZm9yIHRoZSBuZXcgY2hlY2tib3ggc3R5bGUuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaW5pdENoZWNrYm94ZXMgPSBmdW5jdGlvbigkdGFyZ2V0KSB7XG5cdFx0XHRcblx0XHRcdHZhciAkY29udGFpbmVyID0gJHRhcmdldCB8fCAkdGhpcztcblx0XHRcdFxuXHRcdFx0JGNvbnRhaW5lclxuXHRcdFx0XHQuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKVxuXHRcdFx0XHQuZmlsdGVyKG9wdGlvbnMuZmlsdGVyIHx8ICcqJylcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdGRhdGFzZXQgPSBqc2UubGlicy5mYWxsYmFjay5fZGF0YSgkc2VsZiwgJ2NoZWNrYm94JyksXG5cdFx0XHRcdFx0XHRjbGFzc05hbWUgPSBkYXRhc2V0LmNsYXNzTmFtZSB8fCAnJyxcblx0XHRcdFx0XHRcdHRpdGxlID0gJHNlbGYucHJvcCgndGl0bGUnKSxcblx0XHRcdFx0XHRcdGlzQ2hlY2tlZCA9ICgkc2VsZi5wcm9wKCdjaGVja2VkJykpID8gJ2NoZWNrZWQnIDogJycsXG5cdFx0XHRcdFx0XHRpc0Rpc2FibGVkID0gKCRzZWxmLnByb3AoJ2Rpc2FibGVkJykpID8gJ2Rpc2FibGVkJyA6ICcnO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmICh0eXBlb2YgJHNlbGYuZGF0YSgnc2luZ2xlX2NoZWNrYm94JykgIT09ICd1bmRlZmluZWQnKSB7XG5cdFx0XHRcdFx0XHQkc2VsZlxuXHRcdFx0XHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHRcdFx0XHQncG9zaXRpb24nOiAnYWJzb2x1dGUnLFxuXHRcdFx0XHRcdFx0XHRcdCdsZWZ0JzogJy0xMDAwMDBweCdcblx0XHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdFx0LndyYXAoJzxzcGFuIGNsYXNzPVwic2luZ2xlLWNoZWNrYm94ICcgKyBpc0NoZWNrZWQgKyAnICcgKyBpc0Rpc2FibGVkICsgJ1wiIHRpdGxlPVwiJyArXG5cdFx0XHRcdFx0XHRcdFx0dGl0bGUgKyAnXCI+PC9zcGFuPicpXG5cdFx0XHRcdFx0XHRcdC5wYXJlbnQoKVxuXHRcdFx0XHRcdFx0XHQuYXBwZW5kKCc8aSBjbGFzcz1cImZhIGZhLWNoZWNrXCI+PC9pPicpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkc2VsZi5vbignZm9jdXMnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JCgnLnNpbmdsZV9jaGVja2JveCcpLnJlbW92ZUNsYXNzKCdmb2N1c2VkJyk7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykucGFyZW50KCkuYWRkQ2xhc3MoJ2ZvY3VzZWQnKTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkc2VsZi5vbignYmx1cicsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdmb2N1c2VkJyk7XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0JHNlbGYub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRpZiAobW91c2VEb3duID09PSBmYWxzZSkge1xuXHRcdFx0XHRcdFx0XHRcdCQodGhpcykucGFyZW50KCkudG9nZ2xlQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0dmFyIG9uVGV4dCA9ICgkc2VsZi5hdHRyKCdkYXRhLWNoZWNrYm94LW9uX3RleHQnKSkgPyAkc2VsZi5hdHRyKCdkYXRhLWNoZWNrYm94LW9uX3RleHQnKSA6XG5cdFx0XHRcdFx0XHQgICAgICAgICAgICAgJzxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+Jztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0dmFyIG9mZlRleHQgPSAoJHNlbGYuYXR0cignZGF0YS1jaGVja2JveC1vbl90ZXh0JykpID8gJHNlbGYuYXR0cignZGF0YS1jaGVja2JveC1vZmZfdGV4dCcpIDpcblx0XHRcdFx0XHRcdCAgICAgICAgICAgICAgJzxzcGFuIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L3NwYW4+Jztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHRcdFx0LndyYXAoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlciAnICsgaXNDaGVja2VkICsgJyAnICsgaXNEaXNhYmxlZCArICdcIiB0aXRsZT1cIicgKyB0aXRsZSArXG5cdFx0XHRcdFx0XHRcdFx0J1wiPjwvZGl2PicpXG5cdFx0XHRcdFx0XHRcdC5wYXJlbnQoKVxuXHRcdFx0XHRcdFx0XHQuZGF0YSgnY2hlY2tib3gnLCBkYXRhc2V0KVxuXHRcdFx0XHRcdFx0XHQuYWRkQ2xhc3MoY2xhc3NOYW1lKVxuXHRcdFx0XHRcdFx0XHQuYXBwZW5kKCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItdG9nZ2xlclwiPjwvZGl2PicgKyAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLWlubmVyXCI+JyArXG5cdFx0XHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vblwiPicgKyBvblRleHQgKyAnPC9kaXY+JyArXG5cdFx0XHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vZmZcIj4nICsgb2ZmVGV4dCArICc8L2Rpdj4nICsgJzwvZGl2PicgK1xuXHRcdFx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItdGV4dC1vblwiPicgKyBvcHRpb25zLm9uX3RleHQgKyAnPC9kaXY+JyArXG5cdFx0XHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10ZXh0LW9mZlwiPicgKyBvcHRpb25zLm9mZl90ZXh0ICtcblx0XHRcdFx0XHRcdFx0XHQnPC9kaXY+J1xuXHRcdFx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBXcmFwIHRoZSByYWRpbyBib3hlcyBhbmQgZ2VuZXJhdGUgbWFya3VwIGZvciB0aGUgbmV3IGNoZWNrYm94IHN0eWxlLlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2luaXRSYWRpb09wdGlvbnMgPSBmdW5jdGlvbigpIHtcblx0XHRcdGlmICgkdGhpcy5maW5kKCdpbnB1dDpyYWRpbycpLmZpbHRlcihvcHRpb25zLmZpbHRlciB8fCAnKicpLmxlbmd0aCA9PT0gMikge1xuXHRcdFx0XHR2YXIgJG9uRWxlbWVudCA9ICR0aGlzLmZpbmQoJ2lucHV0OnJhZGlvJykuZmlsdGVyKG9wdGlvbnMuZmlsdGVyIHx8ICcqJykuZmlyc3QoKSxcblx0XHRcdFx0XHRvblRpdGxlID0gJG9uRWxlbWVudC5wcm9wKCd0aXRsZScpLFxuXHRcdFx0XHRcdCRvZmZFbGVtZW50ID0gJHRoaXMuZmluZCgnaW5wdXQ6cmFkaW8nKS5maWx0ZXIob3B0aW9ucy5maWx0ZXIgfHwgJyonKS5sYXN0KCksXG5cdFx0XHRcdFx0b2ZmVGl0bGUgPSAkb2ZmRWxlbWVudC5wcm9wKCd0aXRsZScpLFxuXHRcdFx0XHRcdG9uTGFiZWwgPSAob3B0aW9ucy5vbl9sYWJlbCAhPT0gJycpID8gJyBkYXRhLWNoZWNrYm94LWxhYmVsPVwiJyArIG9wdGlvbnMub25fbGFiZWwgKyAnXCInIDogJycsXG5cdFx0XHRcdFx0b2ZmTGFiZWwgPSAob3B0aW9ucy5vZmZfbGFiZWwgIT09ICcnKSA/ICcgZGF0YS1jaGVja2JveC1sYWJlbD1cIicgKyBvcHRpb25zLm9mZl9sYWJlbCArICdcIicgOlxuXHRcdFx0XHRcdCAgICAgICAgICAgJycsXG5cdFx0XHRcdFx0ZGF0YXNldCA9IG9wdGlvbnMsXG5cdFx0XHRcdFx0aXNDaGVja2VkID0gKCRvbkVsZW1lbnQucHJvcCgnY2hlY2tlZCcpKSA/ICdjaGVja2VkJyA6ICcnLFxuXHRcdFx0XHRcdGlzRGlzYWJsZWQgPSAoJG9uRWxlbWVudC5wcm9wKCdkaXNhYmxlZCcpKSA/ICdkaXNhYmxlZCcgOiAnJztcblx0XHRcdFx0XG5cdFx0XHRcdHZhciAkc3dpdGNoZXIgPSAkKCc8ZGl2IGNsYXNzPVwic3dpdGNoZXIgJyArIGlzQ2hlY2tlZCArICcgJyArIGlzRGlzYWJsZWQgKyAnXCI+PC9kaXY+Jyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkb25FbGVtZW50LmFmdGVyKCRzd2l0Y2hlcik7XG5cdFx0XHRcdFxuXHRcdFx0XHQkb25FbGVtZW50LmFwcGVuZFRvKCRzd2l0Y2hlcik7XG5cdFx0XHRcdCRvZmZFbGVtZW50LmFwcGVuZFRvKCRzd2l0Y2hlcik7XG5cdFx0XHRcdFxuXHRcdFx0XHQkc3dpdGNoZXJcblx0XHRcdFx0XHQuZGF0YSgnY2hlY2tib3gnLCBkYXRhc2V0KVxuXHRcdFx0XHRcdC5hZGRDbGFzcyhvcHRpb25zLmNsYXNzKVxuXHRcdFx0XHRcdC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcblx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb25cIiB0aXRsZT1cIicgKyBvblRpdGxlICsgJ1wiJyArIG9uTGFiZWwgK1xuXHRcdFx0XHRcdFx0Jz48c3BhbiBjbGFzcz1cImZhIGZhLWNoZWNrXCI+PC9zcGFuPjwvZGl2PicgK1xuXHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vZmZcIiB0aXRsZT1cIicgKyBvZmZUaXRsZSArICdcIicgKyBvZmZMYWJlbCArXG5cdFx0XHRcdFx0XHQnPjxzcGFuIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L3NwYW4+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItdGV4dC1vblwiPidcblx0XHRcdFx0XHRcdCsgb3B0aW9ucy5vbl90ZXh0ICtcblx0XHRcdFx0XHRcdCc8L2Rpdj4nICtcblx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItdGV4dC1vZmZcIj4nICsgb3B0aW9ucy5vZmZfdGV4dCArICc8L2Rpdj4nICsgJzwvZGl2Pidcblx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gdG9nZ2xlIHN3aXRjaGVyIGlmIGhpZGRlbiByYWRpbyBvcHRpb24gc3RhdHVzIGNoYW5nZXMgKHRoZXJlIGlzIG5vIGRlZmF1bHQgY2FzZSBmb3IgdGhhdClcblx0XHRcdFx0JG9uRWxlbWVudC5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoKS50b2dnbGVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIHRvZ2dsZSBzd2l0Y2hlciBpZiBoaWRkZW4gcmFkaW8gb3B0aW9uIHN0YXR1cyBjaGFuZ2VzICh0aGVyZSBpcyBubyBkZWZhdWx0IGNhc2UgZm9yIHRoYXQpXG5cdFx0XHRcdCRvZmZFbGVtZW50Lm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkKHRoaXMpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIGJ1aWxkIG1hcmt1cCBmb3IgdGhlIHVybCBzd2l0Y2hlclxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2luaXRVcmxTd2l0Y2hlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKG9wdGlvbnMub25fdXJsICE9PSAnJyAmJiBvcHRpb25zLm9mZl91cmwgIT09ICcnKSB7XG5cdFx0XHRcdHZhciBkYXRhc2V0ID0ganNlLmxpYnMuZmFsbGJhY2suX2RhdGEoJHRoaXMsICdjaGVja2JveCcpLFxuXHRcdFx0XHRcdG9uTGFiZWwgPSAob3B0aW9ucy5vbl9sYWJlbCAhPT0gJycpID8gJyBkYXRhLWNoZWNrYm94LWxhYmVsPVwiJyArIG9wdGlvbnMub25fbGFiZWwgKyAnXCInIDogJycsXG5cdFx0XHRcdFx0b2ZmTGFiZWwgPSAob3B0aW9ucy5vZmZfbGFiZWwgIT09ICcnKSA/ICcgZGF0YS1jaGVja2JveC1sYWJlbD1cIicgKyBvcHRpb25zLm9mZl9sYWJlbCArICdcIicgOlxuXHRcdFx0XHRcdCAgICAgICAgICAgJycsXG5cdFx0XHRcdFx0aXNDaGVja2VkID0gKG9wdGlvbnMuY2hlY2tlZCkgPyAnY2hlY2tlZCcgOiAnJztcblx0XHRcdFx0XG5cdFx0XHRcdCR0aGlzXG5cdFx0XHRcdFx0LmRhdGEoJ2NoZWNrYm94JywgZGF0YXNldClcblx0XHRcdFx0XHQuYWRkQ2xhc3MoJ3N3aXRjaGVyJylcblx0XHRcdFx0XHQuYWRkQ2xhc3MoaXNDaGVja2VkKVxuXHRcdFx0XHRcdC5hZGRDbGFzcyhvcHRpb25zLmNsYXNzKVxuXHRcdFx0XHRcdC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcblx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb25cIiB0aXRsZT1cIicgKyBvcHRpb25zLm9mZl91cmwgKyAnXCInICsgb25MYWJlbCArXG5cdFx0XHRcdFx0XHQnPjxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb2ZmXCIgdGl0bGU9XCInICtcblx0XHRcdFx0XHRcdG9wdGlvbnMub25fdXJsICsgJ1wiJyArXG5cdFx0XHRcdFx0XHRvZmZMYWJlbCArICc+PHNwYW4gY2xhc3M9XCJmYSBmYS10aW1lc1wiPjwvc3Bhbj48L2Rpdj4nICsgJzwvZGl2Pidcblx0XHRcdFx0XHQpXG5cdFx0XHRcdFx0Lm9uKCdjbGljaycsIF9zd2l0Y2hlckNoYW5nZUhhbmRsZXIpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQmluZCBldmVudHMgdGhhdCBjaGFuZ2UgdGhlIGNoZWNrYm94IG9yIHN3aXRjaGVyXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaW5pdEV2ZW50SGFuZGxlcnMgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLm9uKCdjbGljaycsICcuc3dpdGNoZXInLCBfc3dpdGNoZXJDaGFuZ2VIYW5kbGVyKTtcblx0XHRcdFxuXHRcdFx0JHRoaXMub2ZmKCdtb3VzZWRvd24nLCAnLnNpbmdsZS1jaGVja2JveCcpO1xuXHRcdFx0JHRoaXMub24oJ21vdXNlZG93bicsICcuc2luZ2xlLWNoZWNrYm94JywgX2NoZWNrYm94Q2hhbmdlSGFuZGxlcik7XG5cdFx0XHQkdGhpcy5vZmYoJ21vdXNldXAnLCAnLnNpbmdsZS1jaGVja2JveCcpO1xuXHRcdFx0JHRoaXMub24oJ21vdXNldXAnLCAnLnNpbmdsZS1jaGVja2JveCcsIF9jaGVja2JveE1vdXNlVXBIYW5kbGVyKTtcblx0XHRcdFxuXHRcdFx0JHRoaXMub24oJ21vdXNlZG93bicsICdsYWJlbCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRtb3VzZURvd24gPSB0cnVlO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCR0aGlzLm9uKCdtb3VzZXVwJywgJ2xhYmVsJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdG1vdXNlRG93biA9IGZhbHNlO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCR0aGlzLm9uKCdGT1JNX1VQREFURScsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dmFyICR0YXJnZXQgPSAkKGUudGFyZ2V0KTtcblx0XHRcdFx0JHRhcmdldFxuXHRcdFx0XHRcdC5maW5kKCdpbnB1dDpjaGVja2JveCcpXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHQkd3JhcHBlciA9ICRzZWxmLmNsb3Nlc3QoJy5zd2l0Y2hlcicpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRpZiAoJHdyYXBwZXIubGVuZ3RoKSB7XG5cdFx0XHRcdFx0XHRcdCR3cmFwcGVyXG5cdFx0XHRcdFx0XHRcdFx0LmZpbmQoJ2RpdicpXG5cdFx0XHRcdFx0XHRcdFx0LnJlbW92ZSgpO1xuXHRcdFx0XHRcdFx0XHQkc2VsZi51bndyYXAoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdF9pbml0Q2hlY2tib3hlcygkdGFyZ2V0KTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2luaXRTZWxlY3RzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBJdGVyYXRlIG92ZXIgc2VsZWN0IGZpZWxkc1xuXHRcdFx0JHRoaXMuZmluZCgnW2RhdGEtY29udmVydC1jaGVja2JveF0nKS5lYWNoKGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG5cdFx0XHRcdC8vIFNob3J0Y3V0c1xuXHRcdFx0XHR2YXIgJG9wdGlvblRydWUgPSAkKGVsZW1lbnQpLmZpbmQoJ29wdGlvblt2YWx1ZT1cIjFcIl0nKSxcblx0XHRcdFx0XHQkb3B0aW9uRmFsc2UgPSAkKGVsZW1lbnQpLmZpbmQoJ29wdGlvblt2YWx1ZT1cIjBcIl0nKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFN0YXRlc1xuXHRcdFx0XHR2YXIgaXNDaGVja2VkID0gJG9wdGlvblRydWUuaXMoJzpzZWxlY3RlZCcpID8gJ2NoZWNrZWQnIDogJycsXG5cdFx0XHRcdFx0aXNEaXNhYmxlZCA9ICQoZWxlbWVudCkuaXMoJzpkaXNhYmxlZCcpID8gJ2Rpc2FibGVkJyA6ICcnO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gU3dpdGNoZXIgVGVtcGxhdGVcblx0XHRcdFx0dmFyICRzd2l0Y2hlciA9ICQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlciAnICsgaXNDaGVja2VkICsgJyAnICsgaXNEaXNhYmxlZCArICdcIj48L2Rpdj4nKTtcblx0XHRcdFx0JHN3aXRjaGVyXG5cdFx0XHRcdFx0LmFkZENsYXNzKCQoZWxlbWVudCkuZGF0YSgnbmV3Q2xhc3MnKSlcblx0XHRcdFx0XHQuZGF0YSgnY2hlY2tib3gnLCBvcHRpb25zKVxuXHRcdFx0XHRcdC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcblx0XHRcdFx0XHRcdCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb25cIj48c3BhbiBjbGFzcz1cImZhIGZhLWNoZWNrXCI+PC9zcGFuPjwvZGl2PicgK1xuXHRcdFx0XHRcdFx0JzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vZmZcIj48c3BhbiBjbGFzcz1cImZhIGZhLXRpbWVzXCI+PC9zcGFuPjwvZGl2PicgKyAnPC9kaXY+J1xuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkKGVsZW1lbnQpXG5cdFx0XHRcdFx0LmFmdGVyKCRzd2l0Y2hlcilcblx0XHRcdFx0XHQuYXBwZW5kVG8oJHN3aXRjaGVyKVxuXHRcdFx0XHRcdC5oaWRlKCk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdC8vIHNhbml0aXplIHVybCBwcmV2ZW50aW5nIGNyb3NzIHNpdGUgc2NyaXB0aW5nXG5cdFx0XHRvcHRpb25zLm9uX3VybCA9IG9wdGlvbnMub25fdXJsLnJlcGxhY2UoJ1wiJywgJycpO1xuXHRcdFx0b3B0aW9ucy5vZmZfdXJsID0gb3B0aW9ucy5vZmZfdXJsLnJlcGxhY2UoJ1wiJywgJycpO1xuXHRcdFx0XG5cdFx0XHRfaW5pdENoZWNrYm94ZXMoKTtcblx0XHRcdF9pbml0UmFkaW9PcHRpb25zKCk7XG5cdFx0XHRfaW5pdFNlbGVjdHMoKTtcblx0XHRcdF9pbml0VXJsU3dpdGNoZXIoKTtcblx0XHRcdF9pbml0RXZlbnRIYW5kbGVycygpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
