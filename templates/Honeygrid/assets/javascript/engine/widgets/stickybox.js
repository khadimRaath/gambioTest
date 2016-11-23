'use strict';

/* --------------------------------------------------------------
 stickybox.js 2016-07-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that keeps an element between the two elements in view
 * 
 * @todo Refactor the animation technique so that it works correctly in tablet devices ("landscape" orientation).
 */
gambio.widgets.module('stickybox', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $window = $(window),
	    $header = null,
	    $footer = null,
	    $outerWrapper = null,
	    bottom = null,
	    top = null,
	    elementHeight = null,
	    elementWidth = null,
	    elementOffset = null,
	    fixedTopPosition = null,
	    documentHeight = null,
	    headerFixed = null,
	    css = null,
	    timer = null,
	    initialOffset = null,
	    initialTop = null,
	    initialHeader = null,
	    initialMarginTop = null,
	    skipped = 0,
	    checkFit = true,
	    lastFit = null,
	    defaults = {
		// The breakpoint, since which this script calculates the position
		breakpoint: 60,
		// Selector to set the header's margin top
		outerWrapper: '#outer-wrapper',
		// Selector to set the header height
		header: 'header',
		// Selector to set the footer height
		footer: '.product-info-listings, footer',
		// Reference selector to set the top position of the sticky box
		offsetTopReferenceSelector: '#breadcrumb_navi, .product-info',
		// Add a space between header/footer and content container
		marginTop: 15,
		// Add a space between header/footer and content container
		marginBottom: 0,
		// Sets the z-index in fixed mode
		zIndex: 1000,
		// If set to true, the number of events in "smoothness" gets skipped
		cpuOptimization: false,
		// The higher the value, the more scroll events gets skipped
		smoothness: 10,
		// The delay after the last scroll event the cpu optimization fires an recalculate event
		smoothnessDelay: 150,
		// Selector to set teaser slider height
		stage: '#stage',
		// Selector to set error box height
		errorBox: 'table.box-error, table.box-warning'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Calculates all necessary positions,
  * offsets and dimensions
  * @private
  */
	var _calculateDimensions = function _calculateDimensions() {
		top = $header.outerHeight();
		bottom = $footer.offset().top;
		top += options.marginTop;
		bottom -= options.marginBottom;

		elementHeight = $this.outerHeight();
		elementWidth = $this.outerWidth();
		elementOffset = elementOffset || $this.offset();

		documentHeight = $(document).height();

		var cssTop = options.marginTop;
		if (headerFixed) {
			cssTop = top;
		}

		css = {
			'position': 'fixed',
			'top': cssTop + 'px',
			'left': elementOffset.left + 'px',
			'z-index': options.zIndex,
			'width': elementWidth
		};
	};

	/**
  * Checks if the available space between
  * the header & footer is enough to set
  * the container sticky
  * @return         {boolean}           If true, there is enough space to set it sticky
  * @private
  */
	var _fitInView = function _fitInView() {

		if (checkFit) {
			checkFit = false;

			_resetPosition();

			window.setTimeout(function () {
				checkFit = true;
			}, 100);

			lastFit = documentHeight - Math.abs(bottom - documentHeight) - top;
		}

		return lastFit > elementHeight;
	};

	/**
  * Helper function that gets called on scroll. In case
  * the content could be displayed without being sticky,
  * the sticky-styles were removed, else a check is
  * performed if the top of the element needs to be
  * adjusted in case that it would overlap with the
  * footer otherwise.
  * @param       {number}     scrollPosition      Current scroll position of the page
  * @private
  */
	var _calcPosition = function _calcPosition(scrollPosition) {
		var newTop = initialTop - (initialHeader - top) + scrollPosition;

		if (headerFixed) {
			var elementBottom = scrollPosition + top + elementHeight + options.marginBottom,
			    overlapping = elementBottom - bottom,
			    currentTop = parseFloat($this.css('top'));

			newTop = newTop < initialTop ? initialTop : newTop;
			newTop -= overlapping - top;

			if (top + scrollPosition <= elementOffset.top) {
				_resetPosition();
			} else if (overlapping > 0) {
				if (bottom - scrollPosition < elementHeight + initialHeader - initialTop) {
					newTop = bottom - elementHeight - initialHeader + initialTop - initialMarginTop;
					_resetPosition();
					$this.css({ top: newTop + 'px' });
				} else if (Math.abs(currentTop - newTop) >= 0.5) {
					_resetPosition();
					$this.css({ top: newTop + 'px' });
				}
			} else if ($this.css('position') !== 'fixed' || $this.css('top') !== css.top) {
				$this.css(css);
			}
		} else {
			if (scrollPosition <= elementOffset.top - options.marginTop) {
				_resetPosition();
			} else if (bottom - scrollPosition + options.marginTop < elementHeight - initialTop - options.marginTop) {
				newTop = bottom - elementHeight - initialHeader + initialTop - initialMarginTop;
				_resetPosition();
				$this.css({ top: newTop + 'px' });
			} else if ($this.css('position') !== 'fixed' || $this.css('top') !== css.top) {
				$this.css(css);
			}
		}
	};

	/**
  * In case that the CPU optimization
  * is enabled, skipp a certain count
  * of scroll events before recalculating
  * the position.
  * @return     {boolean}           True if this event shall be processed
  * @private
  */
	var _cpuOptimization = function _cpuOptimization() {
		skipped += 1;
		clearTimeout(timer);
		if (skipped < options.smoothness) {
			timer = setTimeout(function () {
				$window.trigger('scroll.stickybox', true);
			}, options.smoothnessDelay);
			return false;
		}
		skipped = 0;
		return true;
	};

	/**
  * Set the initial top position of the sticky box. A correction is necessary, if the breadcrumb is longer than 
  * one line.
  * 
  * @private
  */
	var _fixInitialTopPosition = function _fixInitialTopPosition() {
		var offsetTop = $this.offset().top,
		    targetOffsetTop = $(options.offsetTopReferenceSelector).first().offset().top,
		    offsetDifference = offsetTop - targetOffsetTop,
		    topPosition = parseFloat($this.css('top'));

		fixedTopPosition = topPosition - offsetDifference;

		_resetPosition();
	};

	/**
  * Restore initial position of the sticky box by removing its style attribute and setting the fixed 
  * top position.
  * 
  * @private
  */
	var _resetPosition = function _resetPosition() {
		$this.removeAttr('style');

		if (jse.libs.template.responsive.breakpoint().name === 'md' || jse.libs.template.responsive.breakpoint().name === 'lg') {
			$this.css('top', fixedTopPosition + 'px');
		}
	};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the scroll event. It gets the
  * upper border of the content element and calls
  * individual methods depending on the sticky state.
  * To perform better on low end CPUs it checks if
  * scroll events shall be skipped.
  * @private
  */
	var _checkPosition = function _checkPosition(e, d) {

		if (options.cpuOptimization && !d && !_cpuOptimization()) {
			return true;
		}

		if (jse.libs.template.responsive.breakpoint().id > options.breakpoint) {
			_calculateDimensions();
			var scrollPosition = $window.scrollTop(),
			    fit = _fitInView();

			if (fit) {
				_calcPosition(scrollPosition);
			}
		}
	};

	/**
  * Handler for the resize event. On browser
  * resize it is resetting the state to calculate
  * a new position
  * @private
  */
	var _resizeHandler = function _resizeHandler() {
		_resetPosition();
		elementOffset = null;
		skipped = 0;
		initialOffset = $this.offset().top;

		_checkPosition();
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		var sliderHeight = 0,
		    errorBoxHeight = 0,
		    marginTop = 0,
		    marginBottom = 0;

		$outerWrapper = $(options.outerWrapper);
		$header = $(options.header);
		$footer = $(options.footer);

		if ($(options.stage).length > 0) {
			sliderHeight = $(options.stage).outerHeight();
		}

		$(options.errorBox).each(function () {
			marginTop = parseInt($(this).css('margin-top'), 10);
			marginBottom = parseInt($(this).css('margin-bottom'), 10);

			errorBoxHeight += $(this).outerHeight();
			errorBoxHeight += marginTop;
			errorBoxHeight += marginBottom;
		});

		var errorBoxElements = $(options.errorBox).length;

		if (errorBoxElements >= 2) {
			errorBoxHeight = errorBoxHeight - marginTop * (errorBoxElements - 1);
		}

		_fixInitialTopPosition();

		initialOffset = $this.offset().top;
		initialTop = parseFloat($this.css('top'));
		initialHeader = $header.outerHeight() + options.marginTop + sliderHeight + errorBoxHeight;
		initialMarginTop = parseFloat($outerWrapper.css('margin-top').replace(/[^\d]/, ''));
		headerFixed = $header.css('position') === 'fixed';

		if (jse.core.config.get('mobile')) {
			return done();
		}

		_checkPosition();

		$window.on('resize', _resizeHandler).on('scroll.stickybox', _checkPosition).on(jse.libs.template.events.REPOSITIONS_STICKYBOX(), _resizeHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc3RpY2t5Ym94LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHdpbmRvdyIsIndpbmRvdyIsIiRoZWFkZXIiLCIkZm9vdGVyIiwiJG91dGVyV3JhcHBlciIsImJvdHRvbSIsInRvcCIsImVsZW1lbnRIZWlnaHQiLCJlbGVtZW50V2lkdGgiLCJlbGVtZW50T2Zmc2V0IiwiZml4ZWRUb3BQb3NpdGlvbiIsImRvY3VtZW50SGVpZ2h0IiwiaGVhZGVyRml4ZWQiLCJjc3MiLCJ0aW1lciIsImluaXRpYWxPZmZzZXQiLCJpbml0aWFsVG9wIiwiaW5pdGlhbEhlYWRlciIsImluaXRpYWxNYXJnaW5Ub3AiLCJza2lwcGVkIiwiY2hlY2tGaXQiLCJsYXN0Rml0IiwiZGVmYXVsdHMiLCJicmVha3BvaW50Iiwib3V0ZXJXcmFwcGVyIiwiaGVhZGVyIiwiZm9vdGVyIiwib2Zmc2V0VG9wUmVmZXJlbmNlU2VsZWN0b3IiLCJtYXJnaW5Ub3AiLCJtYXJnaW5Cb3R0b20iLCJ6SW5kZXgiLCJjcHVPcHRpbWl6YXRpb24iLCJzbW9vdGhuZXNzIiwic21vb3RobmVzc0RlbGF5Iiwic3RhZ2UiLCJlcnJvckJveCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2FsY3VsYXRlRGltZW5zaW9ucyIsIm91dGVySGVpZ2h0Iiwib2Zmc2V0Iiwib3V0ZXJXaWR0aCIsImRvY3VtZW50IiwiaGVpZ2h0IiwiY3NzVG9wIiwibGVmdCIsIl9maXRJblZpZXciLCJfcmVzZXRQb3NpdGlvbiIsInNldFRpbWVvdXQiLCJNYXRoIiwiYWJzIiwiX2NhbGNQb3NpdGlvbiIsInNjcm9sbFBvc2l0aW9uIiwibmV3VG9wIiwiZWxlbWVudEJvdHRvbSIsIm92ZXJsYXBwaW5nIiwiY3VycmVudFRvcCIsInBhcnNlRmxvYXQiLCJfY3B1T3B0aW1pemF0aW9uIiwiY2xlYXJUaW1lb3V0IiwidHJpZ2dlciIsIl9maXhJbml0aWFsVG9wUG9zaXRpb24iLCJvZmZzZXRUb3AiLCJ0YXJnZXRPZmZzZXRUb3AiLCJmaXJzdCIsIm9mZnNldERpZmZlcmVuY2UiLCJ0b3BQb3NpdGlvbiIsInJlbW92ZUF0dHIiLCJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJyZXNwb25zaXZlIiwibmFtZSIsIl9jaGVja1Bvc2l0aW9uIiwiZSIsImQiLCJpZCIsInNjcm9sbFRvcCIsImZpdCIsIl9yZXNpemVIYW5kbGVyIiwiaW5pdCIsImRvbmUiLCJzbGlkZXJIZWlnaHQiLCJlcnJvckJveEhlaWdodCIsImxlbmd0aCIsImVhY2giLCJwYXJzZUludCIsImVycm9yQm94RWxlbWVudHMiLCJyZXBsYWNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsIm9uIiwiZXZlbnRzIiwiUkVQT1NJVElPTlNfU1RJQ0tZQk9YIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFdBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLGNBRGpCLEVBRUNILE9BQU9HLE1BQVAsR0FBZ0Isa0JBRmpCLENBSEQsRUFRQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUY7O0FBRUUsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxVQUFVRCxFQUFFRSxNQUFGLENBRFg7QUFBQSxLQUVDQyxVQUFVLElBRlg7QUFBQSxLQUdDQyxVQUFVLElBSFg7QUFBQSxLQUlDQyxnQkFBZ0IsSUFKakI7QUFBQSxLQUtDQyxTQUFTLElBTFY7QUFBQSxLQU1DQyxNQUFNLElBTlA7QUFBQSxLQU9DQyxnQkFBZ0IsSUFQakI7QUFBQSxLQVFDQyxlQUFlLElBUmhCO0FBQUEsS0FTQ0MsZ0JBQWdCLElBVGpCO0FBQUEsS0FVQ0MsbUJBQW1CLElBVnBCO0FBQUEsS0FXQ0MsaUJBQWlCLElBWGxCO0FBQUEsS0FZQ0MsY0FBYyxJQVpmO0FBQUEsS0FhQ0MsTUFBTSxJQWJQO0FBQUEsS0FjQ0MsUUFBUSxJ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LFVBQVosTUFBNEIsT0FBMUM7O0FBRUEsTUFBSXVELElBQUlxQixJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFFBQXBCLENBQUosRUFBbUM7QUFDbEMsVUFBT1YsTUFBUDtBQUNBOztBQUVEUjs7QUFFQXpFLFVBQ0U0RixFQURGLENBQ0ssUUFETCxFQUNlYixjQURmLEVBRUVhLEVBRkYsQ0FFSyxrQkFGTCxFQUV5Qm5CLGNBRnpCLEVBR0VtQixFQUhGLENBR0t4QixJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0J1QixNQUFsQixDQUF5QkMscUJBQXpCLEVBSEwsRUFHdURmLGNBSHZEOztBQUtBRTtBQUNBLEVBakREOztBQW1EQTtBQUNBLFFBQU90RixNQUFQO0FBQ0EsQ0E1VUYiLCJmaWxlIjoid2lkZ2V0cy9zdGlja3lib3guanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHN0aWNreWJveC5qcyAyMDE2LTA3LTA4XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBrZWVwcyBhbiBlbGVtZW50IGJldHdlZW4gdGhlIHR3byBlbGVtZW50cyBpbiB2aWV3XG4gKiBcbiAqIEB0b2RvIFJlZmFjdG9yIHRoZSBhbmltYXRpb24gdGVjaG5pcXVlIHNvIHRoYXQgaXQgd29ya3MgY29ycmVjdGx5IGluIHRhYmxldCBkZXZpY2VzIChcImxhbmRzY2FwZVwiIG9yaWVudGF0aW9uKS5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnc3RpY2t5Ym94JyxcblxuXHRbXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvcmVzcG9uc2l2ZSdcblx0XSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdCR3aW5kb3cgPSAkKHdpbmRvdyksXG5cdFx0XHQkaGVhZGVyID0gbnVsbCxcblx0XHRcdCRmb290ZXIgPSBudWxsLFxuXHRcdFx0JG91dGVyV3JhcHBlciA9IG51bGwsXG5cdFx0XHRib3R0b20gPSBudWxsLFxuXHRcdFx0dG9wID0gbnVsbCxcblx0XHRcdGVsZW1lbnRIZWlnaHQgPSBudWxsLFxuXHRcdFx0ZWxlbWVudFdpZHRoID0gbnVsbCxcblx0XHRcdGVsZW1lbnRPZmZzZXQgPSBudWxsLFxuXHRcdFx0Zml4ZWRUb3BQb3NpdGlvbiA9IG51bGwsXG5cdFx0XHRkb2N1bWVudEhlaWdodCA9IG51bGwsXG5cdFx0XHRoZWFkZXJGaXhlZCA9IG51bGwsXG5cdFx0XHRjc3MgPSBudWxsLFxuXHRcdFx0dGltZXIgPSBudWxsLFxuXHRcdFx0aW5pdGlhbE9mZnNldCA9IG51bGwsXG5cdFx0XHRpbml0aWFsVG9wID0gbnVsbCxcblx0XHRcdGluaXRpYWxIZWFkZXIgPSBudWxsLFxuXHRcdFx0aW5pdGlhbE1hcmdpblRvcCA9IG51bGwsXG5cdFx0XHRza2lwcGVkID0gMCxcblx0XHRcdGNoZWNrRml0ID0gdHJ1ZSxcblx0XHRcdGxhc3RGaXQgPSBudWxsLFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdC8vIFRoZSBicmVha3BvaW50LCBzaW5jZSB3aGljaCB0aGlzIHNjcmlwdCBjYWxjdWxhdGVzIHRoZSBwb3NpdGlvblxuXHRcdFx0XHRicmVha3BvaW50OiA2MCxcblx0XHRcdFx0Ly8gU2VsZWN0b3IgdG8gc2V0IHRoZSBoZWFkZXIncyBtYXJnaW4gdG9wXG5cdFx0XHRcdG91dGVyV3JhcHBlcjogJyNvdXRlci13cmFwcGVyJyxcblx0XHRcdFx0Ly8gU2VsZWN0b3IgdG8gc2V0IHRoZSBoZWFkZXIgaGVpZ2h0XG5cdFx0XHRcdGhlYWRlcjogJ2hlYWRlcicsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIHRvIHNldCB0aGUgZm9vdGVyIGhlaWdodFxuXHRcdFx0XHRmb290ZXI6ICcucHJvZHVjdC1pbmZvLWxpc3RpbmdzLCBmb290ZXInLFxuXHRcdFx0XHQvLyBSZWZlcmVuY2Ugc2VsZWN0b3IgdG8gc2V0IHRoZSB0b3AgcG9zaXRpb24gb2YgdGhlIHN0aWNreSBib3hcblx0XHRcdFx0b2Zmc2V0VG9wUmVmZXJlbmNlU2VsZWN0b3I6ICcjYnJlYWRjcnVtYl9uYXZpLCAucHJvZHVjdC1pbmZvJyxcblx0XHRcdFx0Ly8gQWRkIGEgc3BhY2UgYmV0d2VlbiBoZWFkZXIvZm9vdGVyIGFuZCBjb250ZW50IGNvbnRhaW5lclxuXHRcdFx0XHRtYXJnaW5Ub3A6IDE1LFxuXHRcdFx0XHQvLyBBZGQgYSBzcGFjZSBiZXR3ZWVuIGhlYWRlci9mb290ZXIgYW5kIGNvbnRlbnQgY29udGFpbmVyXG5cdFx0XHRcdG1hcmdpbkJvdHRvbTogMCxcblx0XHRcdFx0Ly8gU2V0cyB0aGUgei1pbmRleCBpbiBmaXhlZCBtb2RlXG5cdFx0XHRcdHpJbmRleDogMTAwMCxcblx0XHRcdFx0Ly8gSWYgc2V0IHRvIHRydWUsIHRoZSBudW1iZXIgb2YgZXZlbnRzIGluIFwic21vb3RobmVzc1wiIGdldHMgc2tpcHBlZFxuXHRcdFx0XHRjcHVPcHRpbWl6YXRpb246IGZhbHNlLFxuXHRcdFx0XHQvLyBUaGUgaGlnaGVyIHRoZSB2YWx1ZSwgdGhlIG1vcmUgc2Nyb2xsIGV2ZW50cyBnZXRzIHNraXBwZWRcblx0XHRcdFx0c21vb3RobmVzczogMTAsXG5cdFx0XHRcdC8vIFRoZSBkZWxheSBhZnRlciB0aGUgbGFzdCBzY3JvbGwgZXZlbnQgdGhlIGNwdSBvcHRpbWl6YXRpb24gZmlyZXMgYW4gcmVjYWxjdWxhdGUgZXZlbnRcblx0XHRcdFx0c21vb3RobmVzc0RlbGF5OiAxNTAsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIHRvIHNldCB0ZWFzZXIgc2xpZGVyIGhlaWdodFxuXHRcdFx0XHRzdGFnZTogJyNzdGFnZScsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIHRvIHNldCBlcnJvciBib3ggaGVpZ2h0XG5cdFx0XHRcdGVycm9yQm94OiAndGFibGUuYm94LWVycm9yLCB0YWJsZS5ib3gtd2FybmluZycgXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIENhbGN1bGF0ZXMgYWxsIG5lY2Vzc2FyeSBwb3NpdGlvbnMsXG5cdFx0ICogb2Zmc2V0cyBhbmQgZGltZW5zaW9uc1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jYWxjdWxhdGVEaW1lbnNpb25zID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR0b3AgPSAkaGVhZGVyLm91dGVySGVpZ2h0KCk7XG5cdFx0XHRib3R0b20gPSAkZm9vdGVyLm9mZnNldCgpLnRvcDtcblx0XHRcdHRvcCArPSBvcHRpb25zLm1hcmdpblRvcDtcblx0XHRcdGJvdHRvbSAtPSBvcHRpb25zLm1hcmdpbkJvdHRvbTtcblxuXHRcdFx0ZWxlbWVudEhlaWdodCA9ICR0aGlzLm91dGVySGVpZ2h0KCk7XG5cdFx0XHRlbGVtZW50V2lkdGggPSAkdGhpcy5vdXRlcldpZHRoKCk7XG5cdFx0XHRlbGVtZW50T2Zmc2V0ID0gZWxlbWVudE9mZnNldCB8fCAkdGhpcy5vZmZzZXQoKTtcblxuXHRcdFx0ZG9jdW1lbnRIZWlnaHQgPSAkKGRvY3VtZW50KS5oZWlnaHQoKTtcblx0XHRcdFxuXHRcdFx0dmFyIGNzc1RvcCA9IG9wdGlvbnMubWFyZ2luVG9wOyBcblx0XHRcdGlmIChoZWFkZXJGaXhlZCkge1xuXHRcdFx0XHRjc3NUb3AgPSB0b3A7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGNzcyA9IHtcblx0XHRcdFx0J3Bvc2l0aW9uJzogJ2ZpeGVkJyxcblx0XHRcdFx0J3RvcCc6IGNzc1RvcCArICdweCcsXG5cdFx0XHRcdCdsZWZ0JzogZWxlbWVudE9mZnNldC5sZWZ0ICsgJ3B4Jyxcblx0XHRcdFx0J3otaW5kZXgnOiBvcHRpb25zLnpJbmRleCxcblx0XHRcdFx0J3dpZHRoJzogZWxlbWVudFdpZHRoXG5cdFx0XHR9O1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBDaGVja3MgaWYgdGhlIGF2YWlsYWJsZSBzcGFjZSBiZXR3ZWVuXG5cdFx0ICogdGhlIGhlYWRlciAmIGZvb3RlciBpcyBlbm91Z2ggdG8gc2V0XG5cdFx0ICogdGhlIGNvbnRhaW5lciBzdGlja3lcblx0XHQgKiBAcmV0dXJuICAgICAgICAge2Jvb2xlYW59ICAgICAgICAgICBJZiB0cnVlLCB0aGVyZSBpcyBlbm91Z2ggc3BhY2UgdG8gc2V0IGl0IHN0aWNreVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9maXRJblZpZXcgPSBmdW5jdGlvbigpIHtcblxuXHRcdFx0aWYgKGNoZWNrRml0KSB7XG5cdFx0XHRcdGNoZWNrRml0ID0gZmFsc2U7XG5cblx0XHRcdFx0X3Jlc2V0UG9zaXRpb24oKTtcblxuXHRcdFx0XHR3aW5kb3cuc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRjaGVja0ZpdCA9IHRydWU7XG5cdFx0XHRcdH0sIDEwMCk7XG5cblx0XHRcdFx0bGFzdEZpdCA9IGRvY3VtZW50SGVpZ2h0IC0gTWF0aC5hYnMoYm90dG9tIC0gZG9jdW1lbnRIZWlnaHQpIC0gdG9wO1xuXG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiBsYXN0Rml0ID4gZWxlbWVudEhlaWdodDtcblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBnZXRzIGNhbGxlZCBvbiBzY3JvbGwuIEluIGNhc2Vcblx0XHQgKiB0aGUgY29udGVudCBjb3VsZCBiZSBkaXNwbGF5ZWQgd2l0aG91dCBiZWluZyBzdGlja3ksXG5cdFx0ICogdGhlIHN0aWNreS1zdHlsZXMgd2VyZSByZW1vdmVkLCBlbHNlIGEgY2hlY2sgaXNcblx0XHQgKiBwZXJmb3JtZWQgaWYgdGhlIHRvcCBvZiB0aGUgZWxlbWVudCBuZWVkcyB0byBiZVxuXHRcdCAqIGFkanVzdGVkIGluIGNhc2UgdGhhdCBpdCB3b3VsZCBvdmVybGFwIHdpdGggdGhlXG5cdFx0ICogZm9vdGVyIG90aGVyd2lzZS5cblx0XHQgKiBAcGFyYW0gICAgICAge251bWJlcn0gICAgIHNjcm9sbFBvc2l0aW9uICAgICAgQ3VycmVudCBzY3JvbGwgcG9zaXRpb24gb2YgdGhlIHBhZ2Vcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2FsY1Bvc2l0aW9uID0gZnVuY3Rpb24oc2Nyb2xsUG9zaXRpb24pIHtcblx0XHRcdHZhciBuZXdUb3AgPSBpbml0aWFsVG9wIC0gKGluaXRpYWxIZWFkZXIgLSB0b3ApICsgc2Nyb2xsUG9zaXRpb247XG5cdFx0XHRcblx0XHRcdGlmIChoZWFkZXJGaXhlZCkge1xuXHRcdFx0XHR2YXIgZWxlbWVudEJvdHRvbSA9IHNjcm9sbFBvc2l0aW9uICsgdG9wICsgZWxlbWVudEhlaWdodCArIG9wdGlvbnMubWFyZ2luQm90dG9tLFxuXHRcdFx0XHRcdG92ZXJsYXBwaW5nID0gZWxlbWVudEJvdHRvbSAtIGJvdHRvbSxcblx0XHRcdFx0XHRjdXJyZW50VG9wID0gcGFyc2VGbG9hdCgkdGhpcy5jc3MoJ3RvcCcpKTtcblx0XHRcdFx0XG5cdFx0XHRcdG5ld1RvcCA9IChuZXdUb3AgPCBpbml0aWFsVG9wKSA/IGluaXRpYWxUb3AgOiBuZXdUb3A7XG5cdFx0XHRcdG5ld1RvcCAtPSBvdmVybGFwcGluZyAtIHRvcDtcblx0XHRcdFx0XG5cdFx0XHRcdGlmICh0b3AgKyBzY3JvbGxQb3NpdGlvbiA8PSBlbGVtZW50T2Zmc2V0LnRvcCkge1xuXHRcdFx0XHRcdF9yZXNldFBvc2l0aW9uKCk7XG5cdFx0XHRcdH0gZWxzZSBpZiAob3ZlcmxhcHBpbmcgPiAwKSB7XG5cdFx0XHRcdFx0aWYgKGJvdHRvbSAtIHNjcm9sbFBvc2l0aW9uIDwgZWxlbWVudEhlaWdodCArIGluaXRpYWxIZWFkZXIgLSBpbml0aWFsVG9wKSB7XG5cdFx0XHRcdFx0XHRuZXdUb3AgPSBib3R0b20gLSBlbGVtZW50SGVpZ2h0IC0gaW5pdGlhbEhlYWRlciArIGluaXRpYWxUb3AgLSBpbml0aWFsTWFyZ2luVG9wO1xuXHRcdFx0XHRcdFx0X3Jlc2V0UG9zaXRpb24oKTtcblx0XHRcdFx0XHRcdCR0aGlzLmNzcyh7dG9wOiBuZXdUb3AgKyAncHgnfSk7XG5cdFx0XHRcdFx0fSBlbHNlIGlmIChNYXRoLmFicyhjdXJyZW50VG9wIC0gbmV3VG9wKSA+PSAwLjUpIHtcblx0XHRcdFx0XHRcdF9yZXNldFBvc2l0aW9uKCk7XG5cdFx0XHRcdFx0XHQkdGhpcy5jc3Moe3RvcDogbmV3VG9wICsgJ3B4J30pO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSBlbHNlIGlmICgkdGhpcy5jc3MoJ3Bvc2l0aW9uJykgIT09ICdmaXhlZCcgfHwgJHRoaXMuY3NzKCd0b3AnKSAhPT0gY3NzLnRvcCkge1xuXHRcdFx0XHRcdCR0aGlzLmNzcyhjc3MpO1xuXHRcdFx0XHR9XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRpZiAoc2Nyb2xsUG9zaXRpb24gPD0gZWxlbWVudE9mZnNldC50b3AgLSBvcHRpb25zLm1hcmdpblRvcCkge1xuXHRcdFx0XHRcdF9yZXNldFBvc2l0aW9uKCk7XG5cdFx0XHRcdH0gZWxzZSBpZiAoYm90dG9tIC0gc2Nyb2xsUG9zaXRpb24gKyBvcHRpb25zLm1hcmdpblRvcCA8IGVsZW1lbnRIZWlnaHQgXG5cdFx0XHRcdFx0XHQtIGluaXRpYWxUb3AgLSBvcHRpb25zLm1hcmdpblRvcCkge1xuXHRcdFx0XHRcdG5ld1RvcCA9IGJvdHRvbSAtIGVsZW1lbnRIZWlnaHQgLSBpbml0aWFsSGVhZGVyICsgaW5pdGlhbFRvcCAtIGluaXRpYWxNYXJnaW5Ub3A7XG5cdFx0XHRcdFx0X3Jlc2V0UG9zaXRpb24oKTtcblx0XHRcdFx0XHQkdGhpcy5jc3Moe3RvcDogbmV3VG9wICsgJ3B4J30pO1xuXHRcdFx0XHR9IGVsc2UgaWYgKCR0aGlzLmNzcygncG9zaXRpb24nKSAhPT0gJ2ZpeGVkJyB8fCAkdGhpcy5jc3MoJ3RvcCcpICE9PSBjc3MudG9wKSB7XG5cdFx0XHRcdFx0JHRoaXMuY3NzKGNzcyk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBJbiBjYXNlIHRoYXQgdGhlIENQVSBvcHRpbWl6YXRpb25cblx0XHQgKiBpcyBlbmFibGVkLCBza2lwcCBhIGNlcnRhaW4gY291bnRcblx0XHQgKiBvZiBzY3JvbGwgZXZlbnRzIGJlZm9yZSByZWNhbGN1bGF0aW5nXG5cdFx0ICogdGhlIHBvc2l0aW9uLlxuXHRcdCAqIEByZXR1cm4gICAgIHtib29sZWFufSAgICAgICAgICAgVHJ1ZSBpZiB0aGlzIGV2ZW50IHNoYWxsIGJlIHByb2Nlc3NlZFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jcHVPcHRpbWl6YXRpb24gPSBmdW5jdGlvbigpIHtcblx0XHRcdHNraXBwZWQgKz0gMTtcblx0XHRcdGNsZWFyVGltZW91dCh0aW1lcik7XG5cdFx0XHRpZiAoc2tpcHBlZCA8IG9wdGlvbnMuc21vb3RobmVzcykge1xuXHRcdFx0XHR0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JHdpbmRvdy50cmlnZ2VyKCdzY3JvbGwuc3RpY2t5Ym94JywgdHJ1ZSk7XG5cdFx0XHRcdH0sIG9wdGlvbnMuc21vb3RobmVzc0RlbGF5KTtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXHRcdFx0c2tpcHBlZCA9IDA7XG5cdFx0XHRyZXR1cm4gdHJ1ZTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNldCB0aGUgaW5pdGlhbCB0b3AgcG9zaXRpb24gb2YgdGhlIHN0aWNreSBib3guIEEgY29ycmVjdGlvbiBpcyBuZWNlc3NhcnksIGlmIHRoZSBicmVhZGNydW1iIGlzIGxvbmdlciB0aGFuIFxuXHRcdCAqIG9uZSBsaW5lLlxuXHRcdCAqIFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9maXhJbml0aWFsVG9wUG9zaXRpb24gPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBvZmZzZXRUb3AgPSAkdGhpcy5vZmZzZXQoKS50b3AsXG5cdFx0XHRcdHRhcmdldE9mZnNldFRvcCA9ICQob3B0aW9ucy5vZmZzZXRUb3BSZWZlcmVuY2VTZWxlY3RvcikuZmlyc3QoKS5vZmZzZXQoKS50b3AsXG5cdFx0XHRcdG9mZnNldERpZmZlcmVuY2UgPSBvZmZzZXRUb3AgLSB0YXJnZXRPZmZzZXRUb3AsXG5cdFx0XHRcdHRvcFBvc2l0aW9uID0gcGFyc2VGbG9hdCgkdGhpcy5jc3MoJ3RvcCcpKTtcblx0XHRcdFxuXHRcdFx0Zml4ZWRUb3BQb3NpdGlvbiA9IHRvcFBvc2l0aW9uIC0gb2Zmc2V0RGlmZmVyZW5jZTtcblx0XHRcdFxuXHRcdFx0X3Jlc2V0UG9zaXRpb24oKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlc3RvcmUgaW5pdGlhbCBwb3NpdGlvbiBvZiB0aGUgc3RpY2t5IGJveCBieSByZW1vdmluZyBpdHMgc3R5bGUgYXR0cmlidXRlIGFuZCBzZXR0aW5nIHRoZSBmaXhlZCBcblx0XHQgKiB0b3AgcG9zaXRpb24uXG5cdFx0ICogXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Jlc2V0UG9zaXRpb24gPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG5cdFx0XHRcblx0XHRcdGlmIChqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5uYW1lID09PSAnbWQnXG5cdFx0XHRcdHx8IGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLm5hbWUgPT09ICdsZycpIHtcblx0XHRcdFx0JHRoaXMuY3NzKCd0b3AnLCBmaXhlZFRvcFBvc2l0aW9uICsgJ3B4Jyk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIHNjcm9sbCBldmVudC4gSXQgZ2V0cyB0aGVcblx0XHQgKiB1cHBlciBib3JkZXIgb2YgdGhlIGNvbnRlbnQgZWxlbWVudCBhbmQgY2FsbHNcblx0XHQgKiBpbmRpdmlkdWFsIG1ldGhvZHMgZGVwZW5kaW5nIG9uIHRoZSBzdGlja3kgc3RhdGUuXG5cdFx0ICogVG8gcGVyZm9ybSBiZXR0ZXIgb24gbG93IGVuZCBDUFVzIGl0IGNoZWNrcyBpZlxuXHRcdCAqIHNjcm9sbCBldmVudHMgc2hhbGwgYmUgc2tpcHBlZC5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hlY2tQb3NpdGlvbiA9IGZ1bmN0aW9uKGUsIGQpIHtcblxuXHRcdFx0aWYgKG9wdGlvbnMuY3B1T3B0aW1pemF0aW9uICYmICFkICYmICFfY3B1T3B0aW1pemF0aW9uKCkpIHtcblx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHR9XG5cblx0XHRcdGlmIChqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCkge1xuXHRcdFx0XHRfY2FsY3VsYXRlRGltZW5zaW9ucygpO1xuXHRcdFx0XHR2YXIgc2Nyb2xsUG9zaXRpb24gPSAkd2luZG93LnNjcm9sbFRvcCgpLFxuXHRcdFx0XHRcdGZpdCA9IF9maXRJblZpZXcoKTtcblxuXHRcdFx0XHRpZiAoZml0KSB7XG5cdFx0XHRcdFx0X2NhbGNQb3NpdGlvbihzY3JvbGxQb3NpdGlvbik7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlciBmb3IgdGhlIHJlc2l6ZSBldmVudC4gT24gYnJvd3NlclxuXHRcdCAqIHJlc2l6ZSBpdCBpcyByZXNldHRpbmcgdGhlIHN0YXRlIHRvIGNhbGN1bGF0ZVxuXHRcdCAqIGEgbmV3IHBvc2l0aW9uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Jlc2l6ZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdF9yZXNldFBvc2l0aW9uKCk7XG5cdFx0XHRlbGVtZW50T2Zmc2V0ID0gbnVsbDtcblx0XHRcdHNraXBwZWQgPSAwO1xuXHRcdFx0aW5pdGlhbE9mZnNldCA9ICR0aGlzLm9mZnNldCgpLnRvcDtcblxuXHRcdFx0X2NoZWNrUG9zaXRpb24oKTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHR2YXIgc2xpZGVySGVpZ2h0ID0gMCwgXG5cdFx0XHRcdGVycm9yQm94SGVpZ2h0ID0gMCxcblx0XHRcdFx0bWFyZ2luVG9wID0gMCxcblx0XHRcdFx0bWFyZ2luQm90dG9tID0gMDtcblx0XHRcdFxuXHRcdFx0JG91dGVyV3JhcHBlciA9ICQob3B0aW9ucy5vdXRlcldyYXBwZXIpO1xuXHRcdFx0JGhlYWRlciA9ICQob3B0aW9ucy5oZWFkZXIpO1xuXHRcdFx0JGZvb3RlciA9ICQob3B0aW9ucy5mb290ZXIpO1xuXHRcdFx0XG5cdFx0XHRpZiAoJChvcHRpb25zLnN0YWdlKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdHNsaWRlckhlaWdodCA9ICQob3B0aW9ucy5zdGFnZSkub3V0ZXJIZWlnaHQoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JChvcHRpb25zLmVycm9yQm94KS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRtYXJnaW5Ub3AgICAgPSBwYXJzZUludCgkKHRoaXMpLmNzcygnbWFyZ2luLXRvcCcpLCAxMCk7XG5cdFx0XHRcdG1hcmdpbkJvdHRvbSA9IHBhcnNlSW50KCQodGhpcykuY3NzKCdtYXJnaW4tYm90dG9tJyksIDEwKTtcblx0XHRcdFx0XG5cdFx0XHRcdGVycm9yQm94SGVpZ2h0ICs9ICQodGhpcykub3V0ZXJIZWlnaHQoKTtcblx0XHRcdFx0ZXJyb3JCb3hIZWlnaHQgKz0gbWFyZ2luVG9wO1xuXHRcdFx0XHRlcnJvckJveEhlaWdodCArPSBtYXJnaW5Cb3R0b207XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0dmFyIGVycm9yQm94RWxlbWVudHMgPSAkKG9wdGlvbnMuZXJyb3JCb3gpLmxlbmd0aDtcblx0XHRcdFxuXHRcdFx0aWYgKGVycm9yQm94RWxlbWVudHMgPj0gMikge1xuXHRcdFx0XHRlcnJvckJveEhlaWdodCA9IGVycm9yQm94SGVpZ2h0IC0gKG1hcmdpblRvcCAqIChlcnJvckJveEVsZW1lbnRzIC0gMSkpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRfZml4SW5pdGlhbFRvcFBvc2l0aW9uKCk7XG5cdFx0XHRcblx0XHRcdGluaXRpYWxPZmZzZXQgPSAkdGhpcy5vZmZzZXQoKS50b3A7XG5cdFx0XHRpbml0aWFsVG9wID0gcGFyc2VGbG9hdCgkdGhpcy5jc3MoJ3RvcCcpKTtcblx0XHRcdGluaXRpYWxIZWFkZXIgPSAkaGVhZGVyLm91dGVySGVpZ2h0KCkgKyBvcHRpb25zLm1hcmdpblRvcCArIHNsaWRlckhlaWdodCArIGVycm9yQm94SGVpZ2h0O1xuXHRcdFx0aW5pdGlhbE1hcmdpblRvcCA9IHBhcnNlRmxvYXQoJG91dGVyV3JhcHBlci5jc3MoJ21hcmdpbi10b3AnKS5yZXBsYWNlKC9bXlxcZF0vLCAnJykpO1xuXHRcdFx0aGVhZGVyRml4ZWQgPSAkaGVhZGVyLmNzcygncG9zaXRpb24nKSA9PT0gJ2ZpeGVkJztcblx0XHRcdFxuXHRcdFx0aWYgKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ21vYmlsZScpKSB7XG5cdFx0XHRcdHJldHVybiBkb25lKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdF9jaGVja1Bvc2l0aW9uKCk7XG5cblx0XHRcdCR3aW5kb3dcblx0XHRcdFx0Lm9uKCdyZXNpemUnLCBfcmVzaXplSGFuZGxlcilcblx0XHRcdFx0Lm9uKCdzY3JvbGwuc3RpY2t5Ym94JywgX2NoZWNrUG9zaXRpb24pXG5cdFx0XHRcdC5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuUkVQT1NJVElPTlNfU1RJQ0tZQk9YKCksIF9yZXNpemVIYW5kbGVyKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
