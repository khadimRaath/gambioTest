/* --------------------------------------------------------------
 namespace.js 2016-05-17
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
	 * Class Namespace
	 *
	 * This class is used to handle multiple collections of modules. Every namespace has its own source URL 
	 * for loading the data. That means that JSE can load modules from multiple places at the same time. 
	 *
	 * @class JSE/Constructors/Namespace
	 */
	class Namespace {
		/**
		 * Class Constructor
		 *
		 * @param {String} name The namespace name must be unique within the app.
		 * @param {String} source Complete URL to the namespace modules directory (without trailing slash).
		 * @param {Array} collections Contains collection instances to be included in the namespace.
		 */
		constructor(name, source, collections) {
			this.name = name;
			this.source = source;
			this.collections = collections; // contains the default instances   		
		}
		
		/**
		 * Initialize the namespace collections.
		 *
		 * This method will create new collection instances based in the original ones.
		 *
		 * @return {jQuery.Promise} Returns a promise that will be resolved once every namespace collection
		 * is resolved.
		 */
		init() {
			let deferredCollection = [];
			
			for (let collection of this.collections) {
				let deferred = $.Deferred();
				
				deferredCollection.push(deferred);
				
				this[collection.name] = new jse.constructors.Collection(collection.name, collection.attribute, this);
				this[collection.name].init(null, deferred);
			}
			
			if (deferredCollection.length === 0) {
				return $.Deferred().resolve();
			}
			
			return $.when.apply(undefined, deferredCollection).promise();
		}
	}
	
	jse.constructors.Namespace = Namespace;
})();
