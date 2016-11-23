/* --------------------------------------------------------------
 tooltip_icon.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tooltip Icon Widget
 *
 * This widget will automatically transform the following markup to an icon widget.
 *
 * ### Options
 *
 * **Type | `data-tooltip_icon-type` | String | Optional**
 *
 * The type of the tooltip icon. Possible options are `'info'` and `'warning'`.
 *
 * ### Example
 *
 * ```html
 * <div class="gx-container" style="width:50px">
 *   <span data-gx-widget="tooltip_icon" data-tooltip_icon-type="warning">
 *     This is the tooltip content of the warning tooltip icon.
 *   </span>
 *   <span data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
 *     This is the tooltip content of the info tooltip icon.
 *   </span>
 * </div>
 * ```
 * **Note:** Currently, the wrapping `<div>` of the tooltip icon widget, has to have a CSS-Style
 * of `50px`.
 * 
 * @todo Make sure to set the width automatically. Currently, a style of 50px has to be applied manually.
 * @module Admin/Widgets/tooltip_icon
 */
gx.widgets.module(
	'tooltip_icon',
	
	[],
	
	/**  @lends module:Widgets/tooltip_icon */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				type: 'info'
			},
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Gets the content and tries to add the
		 * images at "Configuration > Image-Options" to the content.
		 * @returns {String | HTML}
		 */
		var _getContent = function() {
			// Is this from a configuration.php row?
			var $parentConfigRow = $this.parents('[data-config-key]:first');
			var isConfigRow = !!$parentConfigRow.length;
			
			// Try to get image and append it to the tooltip description
			if (isConfigRow) {
				var $image = $parentConfigRow.find('img:first');
				var hasImage = !!$image.length;
				
				if (hasImage) {
					$this.append('<br><br>');
					$this.append($image);
				}
			}
			
			return $this.html();
			
		};
		
		/**
		 * Get the image tag element selector for the widget.
		 *
		 * This method will return a different image depending on the provided type option.
		 */
		var _getImageElement = function() {
			var $icon;
			
			switch (options.type) {
				case 'warning':
					$icon = $('<span class="gx-container tooltip_icon pull-left ' + options.type + '">' +
						'<i class="fa fa-exclamation-triangle"></i>' +
						'</span>');
					break;
				case 'info':
					$icon = $('<span class="gx-container tooltip_icon ' + options.type + '">' +
						'<i class="fa fa-info-circle"></i>' +
						'</span>');
					break;
			}
			
			$icon.qtip({
				content: _getContent(),
				style: {
					classes: 'gx-container gx-qtip ' + options.type // use the type as a class for styling
				},
				position: {
					my: options.type === 'warning' ? 'bottom left' : 'left center',
					at: options.type === 'warning' ? 'top left' : 'right center'
				},
				hide: { // Delay the tooltip hide by 300ms so that users can interact with it.
					fixed: true,
					delay: 300
				}
			});
			
			return $icon;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			if ($this.text().replace(/\s+/, '') !== '') {
				var $icon = _getImageElement();
				
				$this.text('');
				
				$icon.appendTo($this);
			}
			
			done();
		};
		
		return module;
	});
