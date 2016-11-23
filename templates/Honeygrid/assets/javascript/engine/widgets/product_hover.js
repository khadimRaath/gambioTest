'use strict';

/* --------------------------------------------------------------
 product_hover.js 2016-06-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that is used for the hover functionality
 * of the product tiles. It includes the functionality
 * for the image gallery inside the tile
 */
gambio.widgets.module('product_hover', [gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $window = $(window),
	    $body = $('body'),
	    $container = null,
	    timer = null,
	    componentId = null,
	    clickTimer = 0,
	    defaults = {
		delay: 50, // Delay in ms after which a hovered element gets closed after mouseleave
		flyoverClass: 'flyover', // Class that gets added to every flyover
		scope: '', // Sets the scope selector for the mouseover events
		container: '#wrapper', // Container selector which is the boundary for the cloned element
		productUrlSelector: '.product-url' // a tag selector of product's url
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function to remove the opened flyovers that
  * were appended to the body by this component
  * @private
  */
	var _removeFlyover = function _removeFlyover(all) {
		var $flyover = $body.children('.' + options.flyoverClass);
		$flyover = all ? $flyover : $flyover.filter('.product-hover-' + componentId);

		$flyover.remove();
	};

	/**
  * Helper function that replaces the preloader
  * images with the real thumbnail images on
  * layer creation. This is needed to save
  * bandwidth
  * @param       {object}    $clone      jQuery selection of the layer
  * @private
  */
	var _loadImages = function _loadImages($clone) {
		$clone.find('.thumbnails img').each(function () {

			var $self = $(this),
			    $img = $('<img />'),
			    dataset = $self.data(),
			    src = dataset.thumbSrc || dataset.src,
			    $parentListItem = null;

			$img.on('load', function () {
				$parentListItem = $self.closest('li');
				$parentListItem.addClass('loaded').css({
					'background': '#FFFFFF url("' + src + '") no-repeat center',
					'background-size': 'contain'
				}).find('img, .align-helper').remove();
			}).attr('src', src);
		});
	};

	// ########## EVENT HANDLER ##########

	/**
  * Handler for the click event on the thumbnail
  * images. After a click on such an image the
  * main image of the hover element gets replaced
  * with the bigger version of the thumbnail image
  * @param       {object}        e       jQuery event object
  * @private
  */
	var _mouseEnterThumbHandler = function _mouseEnterThumbHandler(e) {
		e.preventDefault();

		var $img = $(this),
		    $container = $img.closest('.' + options.flyoverClass),
		    dataSrc = $img.css('background-image');

		dataSrc = dataSrc.replace('/thumbnail_images/', '/info_images/');

		if (dataSrc) {
			$container.find('.product-hover-main-image').css('background-image', dataSrc);
		}
	};

	/**
  * Event handler for the mouse leave event of the
  * hovered element. It sets a timer to remove the
  * hover element after a certain time
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _mouseLeaveHandler = function _mouseLeaveHandler(e) {
		e.stopPropagation();
		timer = timer ? clearTimeout(timer) : null;
		timer = window.setTimeout(_removeFlyover, options.delay);
	};

	/**
  * Event handler for the mouse enter event on both
  * elements (initial & hovered element).
  * It clones the initial element and adds the clone
  * to the body. It additionally adds functionality
  * for the image gallery inside the hovered element
  * @param       {object}        e       jQuery event object
  * @private
  */
	var _mouseEnterHandler = function _mouseEnterHandler(e) {
		e.stopPropagation();

		var $self = $(this),
		    $clone = null,
		    $target = $body,
		    uid = $self.data().uid || parseInt(Math.random() * 10000, 10),
		    $flyover = $target.children('.' + options.flyoverClass + '.product-hover-' + componentId + '[data-product_hover-uid="' + uid + '"]'),
		    offset = $self.offset();

		timer = timer ? clearTimeout(timer) : null;

		// Check if flyover needs to be created
		if (!$self.hasClass(options.flyoverClass) && !$flyover.length) {
			// Remove old opened flyovers
			_removeFlyover(true);
			$this.trigger(jse.libs.template.events.OPEN_FLYOUT(), $this);

			// Add a UID for identification to th hovered object
			$self.attr('data-product_hover-uid', uid).data('uid', uid);

			// Generate the markup
			$clone = $self.clone(true);

			// Replace the preloader images with the thumbnail images
			_loadImages($clone);

			// Set the positioning of the layer
			$clone.addClass(options.flyoverClass + ' product-hover-' + componentId).css({
				'position': 'absolute',
				'left': offset.left,
				'top': offset.top,
				'width': $self[0].getBoundingClientRect().width,
				'height': $self[0].getBoundingClientRect().height
			});

			// Add event listener to the hover elements
			$clone.on('mouseenter', _mouseEnterHandler).on('mouseleave', _mouseLeaveHandler).on('mouseenter', '.thumbnails', _mouseEnterThumbHandler).on('click', _clickHandler);

			// Add the element to the body element
			$body.append($clone);

			if ($container.offset().left > $clone.offset().left) {
				$clone.addClass('gallery-right');
			}
		}
	};

	/**
  * Handler for the window resize event. It
  * recalculates the position of the overlays
  * @private
  */
	var _resizeHandler = function _resizeHandler() {

		var $flyover = $body.children('.' + options.flyoverClass + '.product-hover-' + componentId);

		$flyover.each(function () {
			var $self = $(this),
			    uid = $self.data().uid,
			    $source = $this.find('[data-product_hover-uid="' + uid + '"]'),
			    offset = $source.offset();

			$self.css({
				left: offset.left,
				top: offset.top,
				width: 2 * $source.outerWidth()
			});
		});
	};

	/**
  * Event handler that closes the flyovers
  * if another flyover opens on the page
  * @param       {object}        e           jQuery event object
  * @param       {object}        d           jQuery selection of the event emitter
  * @private
  */
	var _closeLayers = function _closeLayers(e, d) {
		if ($this !== d) {
			_removeFlyover();
		}
	};

	/**
  * Event handler that makes the flyover and product image clickable linking to the product details page
  * 
  * @param       {object}        e           jQuery event object
  * @private
  */
	var _clickHandler = function _clickHandler(e) {
		var $container = $(this);

		if ($(this).hasClass('product-container') === false) {
			$container = $(this).closest('.product-container');
		}

		var $link = $container.find(options.productUrlSelector).first();

		if ($link.length) {
			var url = $link.attr('href');

			if (url !== undefined) {
				e.stopPropagation();
				e.preventDefault();

				// prevent double _clickHandler actions
				if (new Date().getTime() - clickTimer < 100) {
					return;
				} else {
					clickTimer = new Date().getTime();
				}

				switch (e.which) {
					// left click
					case 1:
						if (e.ctrlKey) {
							window.open(url, '_blank');
							return;
						}
						break;

					// middle click
					case 2:
						window.open(url, '_blank');
						return;

					// right click
					case 3:
						return;
				}

				location.href = url;
			}
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		componentId = parseInt(Math.random() * 10000, 10);
		$container = $(options.container);

		$this.on('touchstart', function () {
			// Workaround for tablet navigation problem
			$this.off('mouseenter mouseleave');
		}).on('touchend', function () {
			$this.off('mouseenter', options.scope + ' .product-container', _mouseEnterHandler).off('mouseleave', options.scope + ' .product-container', _mouseLeaveHandler);
		}).on('mouseenter', options.scope + ' .product-container', _mouseEnterHandler).on('mouseleave', options.scope + ' .product-container', _mouseLeaveHandler);

		$this.find('.product-container .product-image').on('click mouseup', _clickHandler);

		$body.on(jse.libs.template.events.OPEN_FLYOUT(), _closeLayers);

		$window.on('resize', _resizeHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9ob3Zlci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCIkYm9keSIsIiRjb250YWluZXIiLCJ0aW1lciIsImNvbXBvbmVudElkIiwiY2xpY2tUaW1lciIsImRlZmF1bHRzIiwiZGVsYXkiLCJmbHlvdmVyQ2xhc3MiLCJzY29wZSIsImNvbnRhaW5lciIsInByb2R1Y3RVcmxTZWxlY3RvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfcmVtb3ZlRmx5b3ZlciIsImFsbCIsIiRmbHlvdmVyIiwiY2hpbGRyZW4iLCJmaWx0ZXIiLCJyZW1vdmUiLCJfbG9hZEltYWdlcyIsIiRjbG9uZSIsImZpbmQiLCJlYWNoIiwiJHNlbGYiLCIkaW1nIiwiZGF0YXNldCIsInNyYyIsInRodW1iU3JjIiwiJHBhcmVudExpc3RJdGVtIiwib24iLCJjbG9zZXN0IiwiYWRkQ2xhc3MiLCJjc3MiLCJhdHRyIiwiX21vdXNlRW50ZXJUaHVtYkhhbmRsZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJkYXRhU3JjIiwicmVwbGFjZSIsIl9tb3VzZUxlYXZlSGFuZGxlciIsInN0b3BQcm9wYWdhdGlvbiIsImNsZWFyVGltZW91dCIsInNldFRpbWVvdXQiLCJfbW91c2VFbnRlckhhbmRsZXIiLCIkdGFyZ2V0IiwidWlkIiwicGFyc2VJbnQiLCJNYXRoIiwicmFuZG9tIiwib2Zmc2V0IiwiaGFzQ2xhc3MiLCJsZW5ndGgiLCJ0cmlnZ2VyIiwianNlIiwibGlicyIsInRlbXBsYXRlIiwiZXZlbnRzIiwiT1BFTl9GTFlPVVQiLCJjbG9uZSIsImxlZnQiLCJ0b3AiLCJnZXRCb3VuZGluZ0NsaWVudFJlY3QiLCJ3aWR0aCIsImhlaWdodCIsIl9jbGlja0hhbmRsZXIiLCJhcHBlbmQiLCJfcmVzaXplSGFuZGxlciIsIiRzb3VyY2UiLCJvdXRlcldpZHRoIiwiX2Nsb3NlTGF5ZXJzIiwiZCIsIiRsaW5rIiwiZmlyc3QiLCJ1cmwiLCJ1bmRlZmluZWQiLCJEYXRlIiwiZ2V0VGltZSIsIndoaWNoIiwiY3RybEtleSIsIm9wZW4iLCJsb2NhdGlvbiIsImhyZWYiLCJpbml0IiwiZG9uZSIsIm9mZiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxlQURELEVBR0MsQ0FDQ0YsT0FBT0csTUFBUCxHQUFnQixjQURqQixDQUhELEVBT0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVGOztBQUVFLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsVUFBVUQsRUFBRUUsTUFBRixDQURYO0FBQUEsS0FFQ0MsUUFBUUgsRUFBRSxNQUFGLENBRlQ7QUFBQSxLQUdDSSxhQUFhLElBSGQ7QUFBQSxLQUlDQyxRQUFRLElBSlQ7QUFBQSxLQUtDQyxjQUFjLElBTGY7QUFBQSxLQU1DQyxhQUFhLENBTmQ7QUFBQSxLQU9DQyxXQUFXO0FBQ1ZDLFNBQU8sRUFERyxFQUNPO0FBQ2pCQyxnQkFBYyxTQUZKLEVBRWdCO0FBQzFCQyxTQUFPLEVBSEcsRUFHVTtBQUNwQkMsYUFBVyxVQUpELEVBSWE7QUFDdkJDLHNCQUFvQixjQUxWLENBS3lCO0FBTHpCLEVBUFo7QUFBQSxLQWNDQyxVQUFVZCxFQUFFZSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJQLFFBQW5CLEVBQTZCVixJQUE3QixDQWRYO0FBQUEsS0FlQ0YsU0FBUyxFQWZWOztBQWlCRjs7QUFFRTs7Ozs7QUFLQSxLQUFJb0IsaUJBQWlCLFNBQWpCQSxjQUFpQixDQUFTQyxHQUFULEVBQWM7QUFDbEMsTUFBSUMsV0FBV2YsTUFBTWdCLFFBQU4sQ0FBZSxNQUFNTCxRQUFRSixZQUE3QixDQUFmO0FBQ0FRLGFBQVdELE1BQU1DLFFBQU4sR0FBaUJBLFNBQVNFLE1BQVQsQ0FBZ0Isb0JBQW9CZCxXQUFwQyxDQUE1Qjs7QUFFQVksV0FB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bUMsa0JBVjFELEVBV0VkLEVBWEYsQ0FXSyxZQVhMLEVBV21CbEIsUUFBUUgsS0FBUixHQUFnQixxQkFYbkMsRUFXMEQrQixrQkFYMUQ7O0FBYUEzQyxRQUFNeUIsSUFBTixDQUFXLG1DQUFYLEVBQWdEUSxFQUFoRCxDQUFtRCxlQUFuRCxFQUFvRW1DLGFBQXBFOztBQUVBaEUsUUFDRTZCLEVBREYsQ0FDS3dCLElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsTUFBbEIsQ0FBeUJDLFdBQXpCLEVBREwsRUFDNkNZLFlBRDdDOztBQUdBdkUsVUFDRStCLEVBREYsQ0FDSyxRQURMLEVBQ2VxQyxjQURmOztBQUdBaUI7QUFDQSxFQTNCRDs7QUE2QkE7QUFDQSxRQUFPMUYsTUFBUDtBQUNBLENBelRGIiwiZmlsZSI6IndpZGdldHMvcHJvZHVjdF9ob3Zlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9ob3Zlci5qcyAyMDE2LTA2LTAzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBpcyB1c2VkIGZvciB0aGUgaG92ZXIgZnVuY3Rpb25hbGl0eVxuICogb2YgdGhlIHByb2R1Y3QgdGlsZXMuIEl0IGluY2x1ZGVzIHRoZSBmdW5jdGlvbmFsaXR5XG4gKiBmb3IgdGhlIGltYWdlIGdhbGxlcnkgaW5zaWRlIHRoZSB0aWxlXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J3Byb2R1Y3RfaG92ZXInLFxuXG5cdFtcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cydcblx0XSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdCR3aW5kb3cgPSAkKHdpbmRvdyksXG5cdFx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHRcdCRjb250YWluZXIgPSBudWxsLFxuXHRcdFx0dGltZXIgPSBudWxsLFxuXHRcdFx0Y29tcG9uZW50SWQgPSBudWxsLFxuXHRcdFx0Y2xpY2tUaW1lciA9IDAsXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0ZGVsYXk6IDUwLCAgICAgICAvLyBEZWxheSBpbiBtcyBhZnRlciB3aGljaCBhIGhvdmVyZWQgZWxlbWVudCBnZXRzIGNsb3NlZCBhZnRlciBtb3VzZWxlYXZlXG5cdFx0XHRcdGZseW92ZXJDbGFzczogJ2ZseW92ZXInLCAgLy8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIGV2ZXJ5IGZseW92ZXJcblx0XHRcdFx0c2NvcGU6ICcnLCAgICAgICAgICAvLyBTZXRzIHRoZSBzY29wZSBzZWxlY3RvciBmb3IgdGhlIG1vdXNlb3ZlciBldmVudHNcblx0XHRcdFx0Y29udGFpbmVyOiAnI3dyYXBwZXInLCAvLyBDb250YWluZXIgc2VsZWN0b3Igd2hpY2ggaXMgdGhlIGJvdW5kYXJ5IGZvciB0aGUgY2xvbmVkIGVsZW1lbnRcblx0XHRcdFx0cHJvZHVjdFVybFNlbGVjdG9yOiAnLnByb2R1Y3QtdXJsJyAvLyBhIHRhZyBzZWxlY3RvciBvZiBwcm9kdWN0J3MgdXJsXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byByZW1vdmUgdGhlIG9wZW5lZCBmbHlvdmVycyB0aGF0XG5cdFx0ICogd2VyZSBhcHBlbmRlZCB0byB0aGUgYm9keSBieSB0aGlzIGNvbXBvbmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9yZW1vdmVGbHlvdmVyID0gZnVuY3Rpb24oYWxsKSB7XG5cdFx0XHR2YXIgJGZseW92ZXIgPSAkYm9keS5jaGlsZHJlbignLicgKyBvcHRpb25zLmZseW92ZXJDbGFzcyk7XG5cdFx0XHQkZmx5b3ZlciA9IGFsbCA/ICRmbHlvdmVyIDogJGZseW92ZXIuZmlsdGVyKCcucHJvZHVjdC1ob3Zlci0nICsgY29tcG9uZW50SWQpO1xuXG5cdFx0XHQkZmx5b3Zlci5yZW1vdmUoKTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgcmVwbGFjZXMgdGhlIHByZWxvYWRlclxuXHRcdCAqIGltYWdlcyB3aXRoIHRoZSByZWFsIHRodW1ibmFpbCBpbWFnZXMgb25cblx0XHQgKiBsYXllciBjcmVhdGlvbi4gVGhpcyBpcyBuZWVkZWQgdG8gc2F2ZVxuXHRcdCAqIGJhbmR3aWR0aFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAkY2xvbmUgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBsYXllclxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9sb2FkSW1hZ2VzID0gZnVuY3Rpb24oJGNsb25lKSB7XG5cdFx0XHQkY2xvbmVcblx0XHRcdFx0LmZpbmQoJy50aHVtYm5haWxzIGltZycpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdCRpbWcgPSAkKCc8aW1nIC8+JyksXG5cdFx0XHRcdFx0XHRkYXRhc2V0ID0gJHNlbGYuZGF0YSgpLFxuXHRcdFx0XHRcdFx0c3JjID0gZGF0YXNldC50aHVtYlNyYyB8fCBkYXRhc2V0LnNyYyxcblx0XHRcdFx0XHRcdCRwYXJlbnRMaXN0SXRlbSA9IG51bGw7XG5cblx0XHRcdFx0XHQkaW1nLm9uKCdsb2FkJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHQkcGFyZW50TGlzdEl0ZW0gPSAkc2VsZi5jbG9zZXN0KCdsaScpO1xuXHRcdFx0XHRcdFx0JHBhcmVudExpc3RJdGVtXG5cdFx0XHRcdFx0XHRcdC5hZGRDbGFzcygnbG9hZGVkJylcblx0XHRcdFx0XHRcdFx0LmNzcyh7XG5cdFx0XHRcdFx0XHRcdFx0ICAgICAnYmFja2dyb3VuZCc6ICcjRkZGRkZGIHVybChcIicgKyBzcmMgKyAnXCIpIG5vLXJlcGVhdCBjZW50ZXInLFxuXHRcdFx0XHRcdFx0XHRcdCAgICAgJ2JhY2tncm91bmQtc2l6ZSc6ICdjb250YWluJ1xuXHRcdFx0XHRcdFx0XHQgICAgIH0pXG5cdFx0XHRcdFx0XHRcdC5maW5kKCdpbWcsIC5hbGlnbi1oZWxwZXInKVxuXHRcdFx0XHRcdFx0XHQucmVtb3ZlKCk7XG5cdFx0XHRcdFx0fSkuYXR0cignc3JjJywgc3JjKTtcblxuXHRcdFx0XHR9KTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIGZvciB0aGUgY2xpY2sgZXZlbnQgb24gdGhlIHRodW1ibmFpbFxuXHRcdCAqIGltYWdlcy4gQWZ0ZXIgYSBjbGljayBvbiBzdWNoIGFuIGltYWdlIHRoZVxuXHRcdCAqIG1haW4gaW1hZ2Ugb2YgdGhlIGhvdmVyIGVsZW1lbnQgZ2V0cyByZXBsYWNlZFxuXHRcdCAqIHdpdGggdGhlIGJpZ2dlciB2ZXJzaW9uIG9mIHRoZSB0aHVtYm5haWwgaW1hZ2Vcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9tb3VzZUVudGVyVGh1bWJIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHR2YXIgJGltZyA9ICQodGhpcyksXG5cdFx0XHRcdCRjb250YWluZXIgPSAkaW1nLmNsb3Nlc3QoJy4nICsgb3B0aW9ucy5mbHlvdmVyQ2xhc3MpLFxuXHRcdFx0XHRkYXRhU3JjID0gJGltZy5jc3MoJ2JhY2tncm91bmQtaW1hZ2UnKTtcblx0XHRcdFxuXHRcdFx0ZGF0YVNyYyA9IGRhdGFTcmMucmVwbGFjZSgnL3RodW1ibmFpbF9pbWFnZXMvJywgJy9pbmZvX2ltYWdlcy8nKTtcblxuXHRcdFx0aWYgKGRhdGFTcmMpIHtcblx0XHRcdFx0JGNvbnRhaW5lclxuXHRcdFx0XHRcdC5maW5kKCcucHJvZHVjdC1ob3Zlci1tYWluLWltYWdlJylcblx0XHRcdFx0XHQuY3NzKCdiYWNrZ3JvdW5kLWltYWdlJywgZGF0YVNyYyk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZSBsZWF2ZSBldmVudCBvZiB0aGVcblx0XHQgKiBob3ZlcmVkIGVsZW1lbnQuIEl0IHNldHMgYSB0aW1lciB0byByZW1vdmUgdGhlXG5cdFx0ICogaG92ZXIgZWxlbWVudCBhZnRlciBhIGNlcnRhaW4gdGltZVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VMZWF2ZUhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0dGltZXIgPSB0aW1lciA/IGNsZWFyVGltZW91dCh0aW1lcikgOiBudWxsO1xuXHRcdFx0dGltZXIgPSB3aW5kb3cuc2V0VGltZW91dChfcmVtb3ZlRmx5b3Zlciwgb3B0aW9ucy5kZWxheSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZSBlbnRlciBldmVudCBvbiBib3RoXG5cdFx0ICogZWxlbWVudHMgKGluaXRpYWwgJiBob3ZlcmVkIGVsZW1lbnQpLlxuXHRcdCAqIEl0IGNsb25lcyB0aGUgaW5pdGlhbCBlbGVtZW50IGFuZCBhZGRzIHRoZSBjbG9uZVxuXHRcdCAqIHRvIHRoZSBib2R5LiBJdCBhZGRpdGlvbmFsbHkgYWRkcyBmdW5jdGlvbmFsaXR5XG5cdFx0ICogZm9yIHRoZSBpbWFnZSBnYWxsZXJ5IGluc2lkZSB0aGUgaG92ZXJlZCBlbGVtZW50XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VFbnRlckhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkY2xvbmUgPSBudWxsLFxuXHRcdFx0XHQkdGFyZ2V0ID0gJGJvZHksXG5cdFx0XHRcdHVpZCA9ICRzZWxmLmRhdGEoKS51aWQgfHwgcGFyc2VJbnQoTWF0aC5yYW5kb20oKSAqIDEwMDAwLCAxMCksXG5cdFx0XHRcdCRmbHlvdmVyID0gJHRhcmdldC5jaGlsZHJlbignLicgKyBvcHRpb25zLmZseW92ZXJDbGFzcyArICcucHJvZHVjdC1ob3Zlci0nICsgY29tcG9uZW50SWRcblx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgJ1tkYXRhLXByb2R1Y3RfaG92ZXItdWlkPVwiJyArIHVpZCArICdcIl0nKSxcblx0XHRcdFx0b2Zmc2V0ID0gJHNlbGYub2Zmc2V0KCk7XG5cblx0XHRcdHRpbWVyID0gdGltZXIgPyBjbGVhclRpbWVvdXQodGltZXIpIDogbnVsbDtcblxuXHRcdFx0Ly8gQ2hlY2sgaWYgZmx5b3ZlciBuZWVkcyB0byBiZSBjcmVhdGVkXG5cdFx0XHRpZiAoISRzZWxmLmhhc0NsYXNzKG9wdGlvbnMuZmx5b3ZlckNsYXNzKSAmJiAhJGZseW92ZXIubGVuZ3RoKSB7XG5cdFx0XHRcdC8vIFJlbW92ZSBvbGQgb3BlbmVkIGZseW92ZXJzXG5cdFx0XHRcdF9yZW1vdmVGbHlvdmVyKHRydWUpO1xuXHRcdFx0XHQkdGhpcy50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5PUEVOX0ZMWU9VVCgpLCAkdGhpcyk7XG5cblx0XHRcdFx0Ly8gQWRkIGEgVUlEIGZvciBpZGVudGlmaWNhdGlvbiB0byB0aCBob3ZlcmVkIG9iamVjdFxuXHRcdFx0XHQkc2VsZlxuXHRcdFx0XHRcdC5hdHRyKCdkYXRhLXByb2R1Y3RfaG92ZXItdWlkJywgdWlkKVxuXHRcdFx0XHRcdC5kYXRhKCd1aWQnLCB1aWQpO1xuXG5cdFx0XHRcdC8vIEdlbmVyYXRlIHRoZSBtYXJrdXBcblx0XHRcdFx0JGNsb25lID0gJHNlbGYuY2xvbmUodHJ1ZSk7XG5cblx0XHRcdFx0Ly8gUmVwbGFjZSB0aGUgcHJlbG9hZGVyIGltYWdlcyB3aXRoIHRoZSB0aHVtYm5haWwgaW1hZ2VzXG5cdFx0XHRcdF9sb2FkSW1hZ2VzKCRjbG9uZSk7XG5cblx0XHRcdFx0Ly8gU2V0IHRoZSBwb3NpdGlvbmluZyBvZiB0aGUgbGF5ZXJcblx0XHRcdFx0JGNsb25lXG5cdFx0XHRcdFx0LmFkZENsYXNzKG9wdGlvbnMuZmx5b3ZlckNsYXNzICsgJyBwcm9kdWN0LWhvdmVyLScgKyBjb21wb25lbnRJZClcblx0XHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHRcdCAgICAgJ3Bvc2l0aW9uJzogJ2Fic29sdXRlJyxcblx0XHRcdFx0XHRcdCAgICAgJ2xlZnQnOiBvZmZzZXQubGVmdCxcblx0XHRcdFx0XHRcdCAgICAgJ3RvcCc6IG9mZnNldC50b3AsXG5cdFx0XHRcdFx0XHQgICAgICd3aWR0aCc6ICRzZWxmWzBdLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLndpZHRoLFxuXHRcdFx0XHRcdFx0ICAgICAnaGVpZ2h0JzogJHNlbGZbMF0uZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkuaGVpZ2h0XG5cdFx0XHRcdFx0ICAgICB9KTtcblxuXHRcdFx0XHQvLyBBZGQgZXZlbnQgbGlzdGVuZXIgdG8gdGhlIGhvdmVyIGVsZW1lbnRzXG5cdFx0XHRcdCRjbG9uZVxuXHRcdFx0XHRcdC5vbignbW91c2VlbnRlcicsIF9tb3VzZUVudGVySGFuZGxlcilcblx0XHRcdFx0XHQub24oJ21vdXNlbGVhdmUnLCBfbW91c2VMZWF2ZUhhbmRsZXIpXG5cdFx0XHRcdFx0Lm9uKCdtb3VzZWVudGVyJywgJy50aHVtYm5haWxzJywgX21vdXNlRW50ZXJUaHVtYkhhbmRsZXIpXG5cdFx0XHRcdFx0Lm9uKCdjbGljaycsIF9jbGlja0hhbmRsZXIpO1xuXG5cdFx0XHRcdC8vIEFkZCB0aGUgZWxlbWVudCB0byB0aGUgYm9keSBlbGVtZW50XG5cdFx0XHRcdCRib2R5LmFwcGVuZCgkY2xvbmUpO1xuXG5cdFx0XHRcdGlmICgkY29udGFpbmVyLm9mZnNldCgpLmxlZnQgPiAkY2xvbmUub2Zmc2V0KCkubGVmdCkge1xuXHRcdFx0XHRcdCRjbG9uZS5hZGRDbGFzcygnZ2FsbGVyeS1yaWdodCcpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSB3aW5kb3cgcmVzaXplIGV2ZW50LiBJdFxuXHRcdCAqIHJlY2FsY3VsYXRlcyB0aGUgcG9zaXRpb24gb2YgdGhlIG92ZXJsYXlzXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Jlc2l6ZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblxuXHRcdFx0dmFyICRmbHlvdmVyID0gJGJvZHkuY2hpbGRyZW4oJy4nICsgb3B0aW9ucy5mbHlvdmVyQ2xhc3MgKyAnLnByb2R1Y3QtaG92ZXItJyArIGNvbXBvbmVudElkKTtcblxuXHRcdFx0JGZseW92ZXIuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHR1aWQgPSAkc2VsZi5kYXRhKCkudWlkLFxuXHRcdFx0XHRcdCRzb3VyY2UgPSAkdGhpcy5maW5kKCdbZGF0YS1wcm9kdWN0X2hvdmVyLXVpZD1cIicgKyB1aWQgKyAnXCJdJyksXG5cdFx0XHRcdFx0b2Zmc2V0ID0gJHNvdXJjZS5vZmZzZXQoKTtcblxuXHRcdFx0XHQkc2VsZi5jc3Moe1xuXHRcdFx0XHRcdCAgICAgICAgICBsZWZ0OiBvZmZzZXQubGVmdCxcblx0XHRcdFx0XHQgICAgICAgICAgdG9wOiBvZmZzZXQudG9wLFxuXHRcdFx0XHRcdCAgICAgICAgICB3aWR0aDogMiAqICRzb3VyY2Uub3V0ZXJXaWR0aCgpXG5cdFx0XHRcdCAgICAgICAgICB9KTtcblx0XHRcdH0pO1xuXG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgdGhhdCBjbG9zZXMgdGhlIGZseW92ZXJzXG5cdFx0ICogaWYgYW5vdGhlciBmbHlvdmVyIG9wZW5zIG9uIHRoZSBwYWdlXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBkICAgICAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBldmVudCBlbWl0dGVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2Nsb3NlTGF5ZXJzID0gZnVuY3Rpb24oZSwgZCkge1xuXHRcdFx0aWYgKCR0aGlzICE9PSBkKSB7XG5cdFx0XHRcdF9yZW1vdmVGbHlvdmVyKCk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHRcblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIHRoYXQgbWFrZXMgdGhlIGZseW92ZXIgYW5kIHByb2R1Y3QgaW1hZ2UgY2xpY2thYmxlIGxpbmtpbmcgdG8gdGhlIHByb2R1Y3QgZGV0YWlscyBwYWdlXG5cdFx0ICogXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdHZhciAkY29udGFpbmVyID0gJCh0aGlzKTtcblx0XHRcdFxuXHRcdFx0aWYgKCQodGhpcykuaGFzQ2xhc3MoJ3Byb2R1Y3QtY29udGFpbmVyJykgPT09IGZhbHNlKSB7XG5cdFx0XHRcdCRjb250YWluZXIgPSAkKHRoaXMpLmNsb3Nlc3QoJy5wcm9kdWN0LWNvbnRhaW5lcicpO1xuXHRcdFx0fSBcblx0XHRcdFxuXHRcdFx0dmFyICRsaW5rID0gJGNvbnRhaW5lci5maW5kKG9wdGlvbnMucHJvZHVjdFVybFNlbGVjdG9yKS5maXJzdCgpO1xuXHRcdFx0XG5cdFx0XHRpZiAoJGxpbmsubGVuZ3RoKSB7XG5cdFx0XHRcdHZhciB1cmwgPSAkbGluay5hdHRyKCdocmVmJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAodXJsICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBwcmV2ZW50IGRvdWJsZSBfY2xpY2tIYW5kbGVyIGFjdGlvbnNcblx0XHRcdFx0XHRpZiAobmV3IERhdGUoKS5nZXRUaW1lKCkgLSBjbGlja1RpbWVyIDwgMTAwKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdGNsaWNrVGltZXIgPSBuZXcgRGF0ZSgpLmdldFRpbWUoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0c3dpdGNoIChlLndoaWNoKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdC8vIGxlZnQgY2xpY2tcblx0XHRcdFx0XHRcdGNhc2UgMTpcblx0XHRcdFx0XHRcdFx0aWYgKGUuY3RybEtleSkge1xuXHRcdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19ibGFuaycpO1xuXHRcdFx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Ly8gbWlkZGxlIGNsaWNrXG5cdFx0XHRcdFx0XHRjYXNlIDI6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19ibGFuaycpO1xuXHRcdFx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdC8vIHJpZ2h0IGNsaWNrXG5cdFx0XHRcdFx0XHRjYXNlIDM6XG5cdFx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0bG9jYXRpb24uaHJlZiA9IHVybDtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRcdGNvbXBvbmVudElkID0gcGFyc2VJbnQoTWF0aC5yYW5kb20oKSAqIDEwMDAwLCAxMCk7XG5cdFx0XHQkY29udGFpbmVyID0gJChvcHRpb25zLmNvbnRhaW5lcik7XG5cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbigndG91Y2hzdGFydCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdC8vIFdvcmthcm91bmQgZm9yIHRhYmxldCBuYXZpZ2F0aW9uIHByb2JsZW1cblx0XHRcdFx0XHQkdGhpcy5vZmYoJ21vdXNlZW50ZXIgbW91c2VsZWF2ZScpO1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQub24oJ3RvdWNoZW5kJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JHRoaXNcblx0XHRcdFx0XHRcdC5vZmYoJ21vdXNlZW50ZXInLCBvcHRpb25zLnNjb3BlICsgJyAucHJvZHVjdC1jb250YWluZXInLCBfbW91c2VFbnRlckhhbmRsZXIpXG5cdFx0XHRcdFx0XHQub2ZmKCdtb3VzZWxlYXZlJywgb3B0aW9ucy5zY29wZSArICcgLnByb2R1Y3QtY29udGFpbmVyJywgX21vdXNlTGVhdmVIYW5kbGVyKTtcblx0XHRcdFx0fSlcblx0XHRcdFx0Lm9uKCdtb3VzZWVudGVyJywgb3B0aW9ucy5zY29wZSArICcgLnByb2R1Y3QtY29udGFpbmVyJywgX21vdXNlRW50ZXJIYW5kbGVyKVxuXHRcdFx0XHQub24oJ21vdXNlbGVhdmUnLCBvcHRpb25zLnNjb3BlICsgJyAucHJvZHVjdC1jb250YWluZXInLCBfbW91c2VMZWF2ZUhhbmRsZXIpO1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5maW5kKCcucHJvZHVjdC1jb250YWluZXIgLnByb2R1Y3QtaW1hZ2UnKS5vbignY2xpY2sgbW91c2V1cCcsIF9jbGlja0hhbmRsZXIpO1xuXHRcdFx0XG5cdFx0XHQkYm9keVxuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLk9QRU5fRkxZT1VUKCksIF9jbG9zZUxheWVycyk7XG5cblx0XHRcdCR3aW5kb3dcblx0XHRcdFx0Lm9uKCdyZXNpemUnLCBfcmVzaXplSGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
