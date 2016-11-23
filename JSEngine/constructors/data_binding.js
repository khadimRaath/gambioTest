/* --------------------------------------------------------------
 data_binding.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function() {
	
	'use strict';
	
	/**
	 * Data Binding Class 
	 * 
	 * Handles two-way data binding with UI elements. 
	 * 
	 * @class JSE/Constructors/DataBinding
	 */
	class DataBinding {
		/**
		 * Class Constructor 
		 * 
		 * @param {String} name The name of the binding. 
		 * @param {Object} $element Target element to be bond. 
		 */
		constructor(name, $element) {
			this.name = name;
			this.$element = $element;
			this.value = null;
			this.isMutable = $element.is('input, textarea, select');
			this.init();
		}
		
		/**
		 * Initialize the binding.
		 */
		init() {
			this.$element.on('change', () => {
				this.get();
			});
		}
		
		/**
		 * Get binding value. 
		 * 
		 * @returns {*}
		 */
		get() {
			this.value = this.isMutable ? this.$element.val() : this.$element.html();
			
			if (this.$element.is(':checkbox') ||  this.$element.is(':radio')) {
				this.value = this.$element.prop('checked');
			}
			
			return this.value;
		}
		
		/**
		 * Set binding value. 
		 * 
		 * @param {String} value
		 */
		set(value) {
			this.value = value;
			
			if (this.isMutable) {
				this.$element.val(value);
			} else {
				this.$element.html(value);
			}
		}
	}
	
	jse.constructors.DataBinding = DataBinding;
})();
