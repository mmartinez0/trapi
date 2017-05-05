(function () {
    
    'use strict';

    angular.module('app.dialog')
        .factory('DialogService', Factory );

    Factory.$inject = [ '$rootScope', '$q' ];

    function Factory( $rootScope, $q ) {

        // I represent the currently active modal window instance.
        var dialog = {
            deferred: null,
            params: null
        };
                
        // Return the public API.
        return({
            open: _open,
            params: _params,
            proceedTo: _proceedTo,
            reject: _reject,
            resolve: _resolve
        });

        // I open a modal of the given type, with the given params. If a modal 
        // window is already open, you can optionally pipe the response of the
        // new modal window into the response of the current (cum previous) modal
        // window. Otherwise, the current modal will be rejected before the new
        // modal window is opened.
        function _open( type, params, pipeResponse ) {

            var previousDeferred = dialog.deferred;

            dialog.deferred = $q.defer();
            dialog.params = params;

            //
            // Pipe the new window response into the previous window's deferred value.
            //
            if ( previousDeferred && pipeResponse ) {

                dialog.deferred.promise.then( previousDeferred.resolve, previousDeferred.reject );
            } 
            else if ( previousDeferred ) {

                previousDeferred.reject();
            }

            $rootScope.$emit( "dialog.open", type );

            return dialog.deferred.promise;
        }

        function _params() {

            return dialog.params || {};
        }

        //
        // I open a modal window with the given type and pipe the new window's
        // response into the current window's response without rejecting it 
        // outright.
        // --
        // This is just a convenience method for .open() that enables the 
        // pipeResponse flag; it helps to make the workflow more intuitive. 
        //
        function _proceedTo( type, params ) {

            return _open( type, params, true );
        }

        function _reject( reason ) {

            if ( ! dialog.deferred )
                return;

            _close( dialog.deferred.reject, reason );
        }

        function _resolve( response ) {

            if ( ! dialog.deferred )
                return;

            _close( dialog.deferred.resolve, response );
        }

        function _close( callback, response ){

            callback( response );

            dialog.deferred = dialog.params = null;

            $rootScope.$emit( "dialog.close" );
        }
    }
})();
