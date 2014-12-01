( function( window, undefined ) {
	"use strict";
	var document = window.document;

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 */
	var EventManager = function() {
		/**
		 * Maintain a reference to the object scope so 'this' never becomes confusing.
		 */
		var SELF = this;

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};

		/**
		 * Adds an action to the event manager.
		 *
		 * @param action Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param priority Defaults to 10
		 */
		SELF.addAction = function( action, callback, priority ) {
			if( _validateNamespace( action ) === false || typeof callback !== 'function' ) {
				return SELF;
			}

			priority = parseInt( ( priority || 10 ), 10 );
			_addHook( 'actions', action, callback, priority );
			return SELF;
		};

		/**
		 * Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		 * that the first argument must always be the action.
		 */
		SELF.doAction = function( /* action, arg1, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var action = args.shift();

			if( _validateNamespace( action ) === false ) {
				return SELF;
			}

			_runHook( 'actions', action, args );

			return SELF;
		};

		/**
		 * Removes the specified action if it contains a namespace.identifier & exists.
		 *
		 * @param action The action to remove
		 */
		SELF.removeAction = function( action ) {
			if( _validateNamespace( action ) === false ) {
				return SELF;
			}

			_removeHook( 'actions', action );
			return SELF;
		};

		/**
		 * Adds a filter to the event manager.
		 *
		 * @param filter Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param priority Defaults to 10
		 */
		SELF.addFilter = function( filter, callback, priority ) {
			if( _validateNamespace( filter ) === false || typeof callback !== 'function' ) {
				return SELF;
			}

			priority = parseInt( ( priority || 10 ), 10 );
			_addHook( 'filters', filter, callback, priority );
			return SELF;
		};

		/**
		 * Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		 * the first argument must always be the filter.
		 */
		SELF.applyFilters = function( /* filter, filtered arg, arg2, ... */ ) {
			
			var args = Array.prototype.slice.call( arguments );
			var filter = args.shift();
			
			if( _validateNamespace( filter ) === false ) {
				return SELF;
			}

			return _runHook( 'filters', filter, args );
		};

		/**
		* Backwards compatible
		* @deprecated 2.1 wp.hooks.applyFilters
		* Remove EOv2.3+
		*/
		SELF.applyFilter = function( /* filter, filtered arg, arg2, ... */ ) {
			
			var args = Array.prototype.slice.call( arguments );
			var filter = args.shift();
			
			if( _validateNamespace( filter ) === false ) {
				return SELF;
			}

			return _runHook( 'filters', filter, args );
		};
		/**
		 * Removes the specified filter if it contains a namespace.identifier & exists.
		 *
		 * @param filter The action to remove
		 */
		SELF.removeFilter = function( filter ) {
			if( _validateNamespace( filter ) === false ) {
				return SELF;
			}

			_removeHook( 'filters', filter );
			return SELF;
		};

		/**
		 * Removes the specified hook by resetting the value of it.
		 *
		 * @param type Type of hook, either 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to remove
		 * @private
		 */
		var _removeHook = function( type, hook ) {
			if( STORAGE[ type ][ hook ] ) {
				STORAGE[ type ][ hook ] = [];
			}
		};

		/**
		 * Validates that the hook has both a namespace and an identifier.
		 *
		 * @param hook The hook we are checking for namespace and identifier for.
		 * @return {Boolean} False if it does not contain both or is incorrect. True if it has an appropriate namespace & identifier.
		 * @private
		 */
		var _validateNamespace = function( hook ) {
			if( typeof hook !== 'string' ) {
				return false;
			}
			var identifier = hook.replace( /^\s+|\s+$/i, '' ).split( '.' );
			var namespace = identifier.shift();
			identifier = identifier.join( '.' );

			return ( namespace !== '' && identifier !== '' );
		};

		/**
		 * Adds the hook to the appropriate storage container
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to add to our event manager
		 * @param callback The function that will be called when the hook is executed.
		 * @param priority The priority of this hook. Must be an integer.
		 * @private
		 */
		var _addHook = function( type, hook, callback, priority ) {
			var hookObject = {
				callback : callback,
				priority : priority
			};

			// Utilize 'prop itself' : http://jsperf.com/hasownproperty-vs-in-vs-undefined/19
			var hooks = STORAGE[ type ][ hook ];
			if( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			}
			else {
				hooks = [ hookObject ];
			}

			STORAGE[ type ][ hook ] = hooks;
		};

		/**
		 * Use an insert sort for keeping our hooks organized based on priority. This function is ridiculously faster
		 * than bubble sort, etc: http://jsperf.com/javascript-sort
		 *
		 * @param hooks The custom array containing all of the appropriate hooks to perform an insert sort on.
		 * @private
		 */
		var _hookInsertSort = function( hooks ) {
			var tmpHook, j, prevHook;
			for( var i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}

			return hooks;
		};

		/**
		 * Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook ( namespace.identifier ) to be ran.
		 * @param args Arguments to pass to the action/filter. If it's a filter, args is actually a single parameter.
		 * @private
		 */
		var _runHook = function( type, hook, args ) {
			var hooks = STORAGE[ type ][ hook ];
			if( typeof hooks === 'undefined' ) {
				if( type === 'filters' ) {
					return args[0];
				}
				return false;
			}

			for( var i = 0, len = hooks.length; i < len; i++ ) {
				if( type === 'actions' ) {
					hooks[ i ].callback.apply( undefined, args );
				}
				else {
					args[0] = hooks[ i ].callback.apply( undefined, args );
				}
			}

			if( type === 'actions' ) {
				return true;
			}

			return args[0];
		};

	};
	
	window.wp = window.wp || {};
	window.wp.hooks = new EventManager();

} )( window );
